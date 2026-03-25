<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\DeliveryAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    /**
     * Show delivery details
     */
    public function show($shipmentId)
    {

        $shipment = Shipment::where('id', $shipmentId)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        return view('agent.delivery.show', compact('shipment'));
    }

    /**
     * Start delivery
     */
    public function start($shipmentId)
    {
        $shipment = Shipment::where('id', $shipmentId)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $shipment->status = 'in_transit';
            $shipment->save();

            $shipment->trackings()->create([
                'status' => 'in_transit',
                'location' => 'Picked up from warehouse',
                'remarks' => 'Agent started delivery',
                'tracked_at' => now()
            ]);

            DB::commit();

            return redirect()->route('agent.tracking.live', $shipment->id)
                ->with('success', 'Delivery started!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to start delivery: ' . $e->getMessage());
            return back()->with('error', 'Failed to start delivery: ' . $e->getMessage());
        }
    }

    /**
     * Complete delivery
     */
    public function complete(Request $request, $shipmentId)
    {
        $shipment = Shipment::where('id', $shipmentId)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        $request->validate([
            'signature' => 'nullable|image|max:2048',
            'photo' => 'nullable|image|max:5120',
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $shipment->status = 'delivered';
            $shipment->actual_delivery_date = now();
            $shipment->delivery_notes = $request->notes;

            if ($request->hasFile('signature')) {
                $path = $request->file('signature')->store('pod/signatures', 'public');
                $shipment->pod_signature = $path;
            }

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('pod/photos', 'public');
                $shipment->pod_photo = $path;
            }

            $shipment->save();

            $shipment->trackings()->create([
                'status' => 'delivered',
                'location' => $shipment->city,
                'remarks' => $request->notes ?? 'Successfully delivered',
                'tracked_at' => now()
            ]);

            // Update agent stats
            $agent = DeliveryAgent::where('user_id', Auth::id())->first();
            if ($agent) {
                $agent->incrementDeliveries(true);
                $agent->updateRating(5);
            }

            DB::commit();

            return redirect()->route('agent.dashboard')
                ->with('success', 'Delivery completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete delivery: ' . $e->getMessage());
            return back()->with('error', 'Failed to complete delivery: ' . $e->getMessage());
        }
    }



    /**
     * Get delivery history with filters
     */
    public function history(Request $request)
    {
        $user = Auth::user();

        // Handle period selection
        $period = $request->get('period', 'month');
        list($startDate, $endDate) = $this->getDateRangeFromPeriod($period, $request);

        $query = Shipment::where('assigned_to', $user->id)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate]);

        // Search filter
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('shipment_number', 'LIKE', "%{$request->search}%")
                  ->orWhere('receiver_name', 'LIKE', "%{$request->search}%")
                  ->orWhere('receiver_phone', 'LIKE', "%{$request->search}%");
            });
        }

        // Sorting
        switch ($request->sort) {
            case 'date_asc':
                $query->orderBy('actual_delivery_date', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('declared_value', 'desc');
                break;
            case 'amount_asc':
                $query->orderBy('declared_value', 'asc');
                break;
            default:
                $query->orderBy('actual_delivery_date', 'desc');
        }

        $deliveries = $query->paginate(15);

        // Calculate statistics
        $totalDeliveries = $query->count();
        $totalEarnings = $this->calculateTotalEarnings($user->id, $startDate, $endDate);
        $avgPerDelivery = $totalDeliveries > 0 ? $totalEarnings / $totalDeliveries : 0;
        $bestDay = $this->getBestDeliveryDay($user->id, $startDate, $endDate);

        return view('agent.deliveries.history', compact(
            'deliveries', 'startDate', 'endDate', 'totalDeliveries',
            'totalEarnings', 'avgPerDelivery', 'bestDay'
        ));
    }
public function checkNewAssignments(Request $request)
{
    $user = Auth::user();
    $lastChecked = $request->get('last_checked', Carbon::now()->subMinutes(5));

    // Find shipments assigned after last check
    $newShipments = Shipment::where('assigned_to', $user->id)
        ->where('created_at', '>', $lastChecked)
        ->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery'])
        ->get();

    return response()->json([
        'success' => true,
        'new_shipments' => $newShipments,
        'count' => $newShipments->count()
    ]);
}
    /**
     * Get assigned shipments
     */
  public function assigned()
{
    $user = Auth::user();

    // Get ALL shipments assigned to agent (including delivered)
    $assignedShipments = Shipment::where('assigned_to', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('agent.deliveries.assigned', compact('assignedShipments'));
}

    /**
     * Track delivery (redirect to live tracking)
     */
    public function track($shipmentId)
    {
        $shipment = Shipment::where('id', $shipmentId)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        return redirect()->route('agent.tracking.live', $shipmentId);
    }

    /**
     * Get delivery details with location
     */
    public function details($shipmentId)
    {
        $shipment = Shipment::where('id', $shipmentId)
            ->where('assigned_to', Auth::id())
            ->with(['trackings', 'assignedAgent'])
            ->firstOrFail();

        return view('agent.delivery.details', compact('shipment'));
    }

    /**
     * Update delivery status via AJAX
     */
    public function updateStatus(Request $request, $shipmentId)
    {
        $shipment = Shipment::where('id', $shipmentId)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:picked,in_transit,out_for_delivery,delivered,failed',
            'remarks' => 'nullable|string',
            'location' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $shipment->status;
            $shipment->status = $request->status;

            if ($request->status == 'delivered' && !$shipment->actual_delivery_date) {
                $shipment->actual_delivery_date = now();
            }

            $shipment->save();

            $shipment->trackings()->create([
                'status' => $request->status,
                'location' => $request->location ?? $shipment->city,
                'remarks' => $request->remarks ?? 'Status updated to ' . str_replace('_', ' ', $request->status),
                'tracked_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $shipment->status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk start deliveries
     */
    public function bulkStart(Request $request)
    {
        $request->validate([
            'shipment_ids' => 'required|array',
            'shipment_ids.*' => 'exists:shipments,id'
        ]);

        $user = Auth::user();
        $shipments = Shipment::whereIn('id', $request->shipment_ids)
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'picked'])
            ->get();

        DB::beginTransaction();
        try {
            foreach ($shipments as $shipment) {
                $shipment->status = 'in_transit';
                $shipment->save();

                $shipment->trackings()->create([
                    'status' => 'in_transit',
                    'location' => 'Picked up from warehouse',
                    'remarks' => 'Bulk start - Agent started multiple deliveries',
                    'tracked_at' => now()
                ]);
            }
            DB::commit();

            return redirect()->back()->with('success', count($shipments) . ' deliveries started successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk start deliveries: ' . $e->getMessage());
            return back()->with('error', 'Failed to start deliveries: ' . $e->getMessage());
        }
    }

    /**
     * Get delivery statistics for agent
     */
    public function statistics()
    {
        $user = Auth::user();

        $stats = [
            'total_assigned' => Shipment::where('assigned_to', $user->id)->count(),
            'completed' => Shipment::where('assigned_to', $user->id)->where('status', 'delivered')->count(),
            'in_progress' => Shipment::where('assigned_to', $user->id)
                ->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])
                ->count(),
            'pending' => Shipment::where('assigned_to', $user->id)->where('status', 'pending')->count(),
            'failed' => Shipment::where('assigned_to', $user->id)->whereIn('status', ['failed', 'returned'])->count(),
            'today_completed' => Shipment::where('assigned_to', $user->id)
                ->whereDate('actual_delivery_date', Carbon::today())
                ->where('status', 'delivered')
                ->count(),
            'this_week' => Shipment::where('assigned_to', $user->id)
                ->whereBetween('actual_delivery_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->where('status', 'delivered')
                ->count(),
            'this_month' => Shipment::where('assigned_to', $user->id)
                ->whereMonth('actual_delivery_date', Carbon::now()->month)
                ->where('status', 'delivered')
                ->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }



public function active()
{
    $user = Auth::user();

    // Get all shipments that are assigned to agent AND not yet delivered
    // This includes pending, picked, in_transit, out_for_delivery
    $activeShipments = Shipment::where('assigned_to', $user->id)
        ->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery'])
        ->orderBy('created_at', 'desc')
        ->get();

    // Get agent's current location for distance calculation
    $agent = DeliveryAgent::where('user_id', $user->id)->first();

    // Calculate distance and ETA for each shipment
    foreach ($activeShipments as $shipment) {
        if ($agent && $agent->current_latitude && $shipment->destination_latitude) {
            $distance = $this->calculateDistance(
                $agent->current_latitude,
                $agent->current_longitude,
                $shipment->destination_latitude,
                $shipment->destination_longitude
            );
            $shipment->distance_from_agent = $distance;

            // Calculate ETA based on current speed
            if ($agent->current_speed && $agent->current_speed > 0) {
                $shipment->eta_minutes = ($distance / $agent->current_speed) * 60;
            } else {
                $shipment->eta_minutes = 0;
            }
        } else {
            $shipment->distance_from_agent = 0;
            $shipment->eta_minutes = 0;
        }
    }

    $todayCompleted = Shipment::where('assigned_to', $user->id)
        ->whereDate('actual_delivery_date', Carbon::today())
        ->where('status', 'delivered')
        ->count();

    return view('agent.deliveries.active', compact('activeShipments', 'todayCompleted'));
}

private function calculateDistance($lat1, $lng1, $lat2, $lng2)
{
    if (!$lat1 || !$lng1 || !$lat2 || !$lng2) return 0;

    $earthRadius = 6371; // km
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLng/2) * sin($dLng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    return $earthRadius * $c;
}
    /* ==================== PRIVATE HELPER METHODS ==================== */

    /**
     * Get date range from period selection
     */
    private function getDateRangeFromPeriod($period, $request)
    {
        $today = Carbon::today();

        switch ($period) {
            case 'today':
                $startDate = $today->toDateString();
                $endDate = $today->toDateString();
                break;
            case 'week':
                $startDate = $today->startOfWeek()->toDateString();
                $endDate = $today->endOfWeek()->toDateString();
                break;
            case 'month':
                $startDate = $today->startOfMonth()->toDateString();
                $endDate = $today->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $lastMonth = $today->copy()->subMonth();
                $startDate = $lastMonth->startOfMonth()->toDateString();
                $endDate = $lastMonth->endOfMonth()->toDateString();
                break;
            case 'quarter':
                $startDate = $today->startOfQuarter()->toDateString();
                $endDate = $today->endOfQuarter()->toDateString();
                break;
            case 'year':
                $startDate = $today->startOfYear()->toDateString();
                $endDate = $today->endOfYear()->toDateString();
                break;
            default:
                $startDate = $request->get('start_date', $today->startOfMonth()->toDateString());
                $endDate = $request->get('end_date', $today->toDateString());
        }

        return [$startDate, $endDate];
    }

    /**
     * Calculate total earnings for period
     */
    private function calculateTotalEarnings($agentId, $startDate, $endDate)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)->first();
        if (!$agent) return 0;

        $shipments = Shipment::where('assigned_to', $agentId)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->get();

        $total = 0;
        foreach ($shipments as $shipment) {
            if ($agent->commission_type == 'fixed') {
                $total += $agent->commission_value ?? 50;
            } else {
                $total += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }
        }
        return $total;
    }

    /**
     * Get best delivery day
     */
    private function getBestDeliveryDay($agentId, $startDate, $endDate)
    {
        $shipments = Shipment::where('assigned_to', $agentId)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->select(DB::raw('DAYNAME(actual_delivery_date) as day, COUNT(*) as count'))
            ->groupBy('day')
            ->orderBy('count', 'desc')
            ->first();

        return $shipments ? $shipments->day : 'N/A';
    }
}
