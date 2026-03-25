<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Shipment;
use App\Models\AgentLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get agent details
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        if (!$agent) {
            // If no agent profile exists, create one
            $agent = DeliveryAgent::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->mobile ?? null,
                'agent_code' => DeliveryAgent::generateAgentCode(),
                'status' => 'offline',
                'is_active' => true,
                'approval_status' => 'pending_approval',
            ]);
        }

        // Check if agent is approved
        if ($agent->approval_status !== 'approved') {
            return view('agent.pending-approval', compact('agent'));
        }

        // Get assigned shipments (active deliveries)
        $activeShipments = Shipment::where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Add distance and ETA for each active shipment
        foreach ($activeShipments as $shipment) {
            if ($agent->current_latitude && $shipment->destination_latitude) {
                $distance = $this->calculateDistance(
                    $agent->current_latitude,
                    $agent->current_longitude,
                    $shipment->destination_latitude,
                    $shipment->destination_longitude
                );
                $shipment->distance = round($distance, 1);

                if ($agent->current_speed && $agent->current_speed > 0) {
                    $shipment->eta_minutes = round(($distance / $agent->current_speed) * 60);
                } else {
                    $shipment->eta_minutes = round($distance * 2); // Rough estimate
                }
            } else {
                $shipment->distance = 0;
                $shipment->eta_minutes = 0;
            }
        }

        // Get completed shipments count for today
        $todayDeliveries = Shipment::where('assigned_to', $user->id)
            ->whereDate('actual_delivery_date', Carbon::today())
            ->where('status', 'delivered')
            ->count();

        // Get total deliveries count
        $totalDeliveries = Shipment::where('assigned_to', $user->id)
            ->where('status', 'delivered')
            ->count();

        // Get today's earnings (if commission based)
        $todayEarnings = $this->calculateTodayEarnings($user->id);

        // Get this week's earnings
        $weeklyEarnings = $this->calculateWeeklyEarnings($user->id);

        // Get this month's earnings
        $monthlyEarnings = $this->calculateMonthlyEarnings($user->id);

        // Get total earnings (all time)
        $totalEarnings = $this->calculateTotalEarnings($user->id);

        // Get rating
        $rating = $agent->rating ?? 4.5;

        // Get recent deliveries (last 5)
        $recentDeliveries = Shipment::where('assigned_to', $user->id)
            ->where('status', 'delivered')
            ->orderBy('actual_delivery_date', 'desc')
            ->take(5)
            ->get();

        // Get weekly stats for chart
        $weeklyStats = $this->getWeeklyStats($user->id);

        // Get today's distance traveled
        $todayDistance = $this->getTodayDistance($user->id);

        // Get location status
        $locationStatus = $this->getLocationStatus($agent);

        // Get completion rate
        $totalAssigned = Shipment::where('assigned_to', $user->id)->count();
        $completionRate = $totalAssigned > 0 ? round(($totalDeliveries / $totalAssigned) * 100, 1) : 0;

        // Get pending approvals count (for admin info)
        $pendingApprovals = DeliveryAgent::where('approval_status', 'pending_approval')->count();

        return view('agent.dashboard', compact(
            'agent',
            'activeShipments',
            'todayDeliveries',
            'totalDeliveries',
            'todayEarnings',
            'weeklyEarnings',
            'monthlyEarnings',
            'totalEarnings',
            'rating',
            'recentDeliveries',
            'weeklyStats',
            'todayDistance',
            'locationStatus',
            'completionRate',
            'pendingApprovals'
        ));
    }

    /**
     * Get weekly delivery statistics
     */
    private function getWeeklyStats($agentId)
    {
        $stats = [];
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        foreach ($days as $index => $day) {
            $date = Carbon::now()->startOfWeek()->addDays($index);
            $count = Shipment::where('assigned_to', $agentId)
                ->whereDate('actual_delivery_date', $date)
                ->where('status', 'delivered')
                ->count();

            $stats[] = [
                'day' => $day,
                'count' => $count,
                'date' => $date->format('d M')
            ];
        }

        return $stats;
    }

    /**
     * Calculate today's earnings
     */
    private function calculateTodayEarnings($agentId)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)->first();
        if (!$agent) return 0;

        $shipments = Shipment::where('assigned_to', $agentId)
            ->whereDate('actual_delivery_date', Carbon::today())
            ->where('status', 'delivered')
            ->get();

        return $this->calculateEarningsForShipments($agent, $shipments);
    }

    /**
     * Calculate weekly earnings
     */
    private function calculateWeeklyEarnings($agentId)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)->first();
        if (!$agent) return 0;

        $shipments = Shipment::where('assigned_to', $agentId)
            ->whereBetween('actual_delivery_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('status', 'delivered')
            ->get();

        return $this->calculateEarningsForShipments($agent, $shipments);
    }

    /**
     * Calculate monthly earnings
     */
    private function calculateMonthlyEarnings($agentId)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)->first();
        if (!$agent) return 0;

        $shipments = Shipment::where('assigned_to', $agentId)
            ->whereMonth('actual_delivery_date', Carbon::now()->month)
            ->where('status', 'delivered')
            ->get();

        return $this->calculateEarningsForShipments($agent, $shipments);
    }

    /**
     * Calculate total earnings (all time)
     */
    private function calculateTotalEarnings($agentId)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)->first();
        if (!$agent) return 0;

        $shipments = Shipment::where('assigned_to', $agentId)
            ->where('status', 'delivered')
            ->get();

        return $this->calculateEarningsForShipments($agent, $shipments);
    }

    /**
     * Calculate earnings for given shipments
     */
    private function calculateEarningsForShipments($agent, $shipments)
    {
        $total = 0;
        foreach ($shipments as $shipment) {
            if ($agent->commission_type == 'fixed') {
                $total += $agent->commission_value ?? 50;
            } elseif ($agent->commission_type == 'percentage') {
                $total += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }
        }
        return $total;
    }

    /**
     * Calculate distance between two points (Haversine formula)
     */
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

    /**
     * Get today's distance traveled
     */
    private function getTodayDistance($agentId)
    {
        if (!class_exists(AgentLocation::class)) return 0;

        $locations = AgentLocation::where('agent_id', $agentId)
            ->whereDate('recorded_at', Carbon::today())
            ->orderBy('recorded_at', 'asc')
            ->get();

        if ($locations->count() < 2) return 0;

        $totalDistance = 0;
        for ($i = 0; $i < $locations->count() - 1; $i++) {
            $current = $locations[$i];
            $next = $locations[$i + 1];
            $totalDistance += $this->calculateDistance(
                $current->latitude, $current->longitude,
                $next->latitude, $next->longitude
            );
        }

        return round($totalDistance, 1);
    }

    /**
     * Get location status for agent
     */
    private function getLocationStatus($agent)
    {
        if (!$agent->current_latitude || !$agent->current_longitude) {
            return [
                'is_active' => false,
                'message' => 'Location not enabled',
                'last_update' => null
            ];
        }

        $isRecent = $agent->last_location_update &&
                    $agent->last_location_update->diffInMinutes(Carbon::now()) <= 5;

        return [
            'is_active' => $isRecent,
            'message' => $isRecent ? 'Live tracking active' : 'Location update delayed',
            'last_update' => $agent->last_location_update,
            'latitude' => $agent->current_latitude,
            'longitude' => $agent->current_longitude,
            'speed' => $agent->current_speed ?? 0
        ];
    }

    /**
     * Update agent status (available/busy/offline)
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:available,busy,offline'
        ]);

        $agent = DeliveryAgent::where('user_id', Auth::id())->first();

        if ($agent) {
            $agent->status = $request->status;

            if ($request->status == 'available') {
                $agent->last_online_at = Carbon::now();
            } elseif ($request->status == 'offline') {
                $agent->last_offline_at = Carbon::now();

                if ($agent->last_online_at) {
                    $onlineMinutes = $agent->last_online_at->diffInMinutes(Carbon::now());
                    $agent->total_online_minutes = ($agent->total_online_minutes ?? 0) + $onlineMinutes;
                }
            }

            $agent->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $agent->status
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Agent not found'
        ], 404);
    }

    /**
     * Get dashboard statistics for AJAX
     */
    public function getStats()
    {
        $user = Auth::user();

        $stats = [
            'active_deliveries' => Shipment::where('assigned_to', $user->id)
                ->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery'])
                ->count(),
            'today_deliveries' => Shipment::where('assigned_to', $user->id)
                ->whereDate('actual_delivery_date', Carbon::today())
                ->where('status', 'delivered')
                ->count(),
            'today_earnings' => $this->calculateTodayEarnings($user->id),
            'weekly_earnings' => $this->calculateWeeklyEarnings($user->id),
            'completion_rate' => $this->getCompletionRate($user->id),
            'rating' => DeliveryAgent::where('user_id', $user->id)->first()->rating ?? 4.5
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get completion rate
     */
    private function getCompletionRate($agentId)
    {
        $totalAssigned = Shipment::where('assigned_to', $agentId)->count();
        $completed = Shipment::where('assigned_to', $agentId)
            ->where('status', 'delivered')
            ->count();

        return $totalAssigned > 0 ? round(($completed / $totalAssigned) * 100, 1) : 0;
    }

    /**
     * Get recent location history for map
     */
    public function getLocationHistory(Request $request)
    {
        $user = Auth::user();
        $hours = $request->get('hours', 24);

        $locations = AgentLocation::where('agent_id', $user->id)
            ->where('recorded_at', '>=', Carbon::now()->subHours($hours))
            ->orderBy('recorded_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'locations' => $locations->map(function($loc) {
                return [
                    'lat' => $loc->latitude,
                    'lng' => $loc->longitude,
                    'speed' => $loc->speed,
                    'time' => $loc->recorded_at->toDateTimeString()
                ];
            }),
            'count' => $locations->count()
        ]);
    }
}
