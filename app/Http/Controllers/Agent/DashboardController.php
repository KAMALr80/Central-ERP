<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                'phone' => $user->mobile,
                'agent_code' => 'AG' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'status' => 'offline',
                'is_active' => true,
                'approval_status' => 'approved',
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

        // Get completed shipments count for today
        $todayDeliveries = Shipment::where('assigned_to', $user->id)
            ->whereDate('actual_delivery_date', today())
            ->where('status', 'delivered')
            ->count();

        // Get total deliveries count
        $totalDeliveries = Shipment::where('assigned_to', $user->id)
            ->where('status', 'delivered')
            ->count();

        // Get today's earnings (if commission based)
        $todayEarnings = 0;
        if ($agent && $agent->commission_type == 'fixed') {
            $todayEarnings = $todayDeliveries * ($agent->commission_value ?? 50);
        } elseif ($agent && $agent->commission_type == 'percentage') {
            // Calculate from delivered shipments value
            $todayValue = Shipment::where('assigned_to', $user->id)
                ->whereDate('actual_delivery_date', today())
                ->where('status', 'delivered')
                ->sum('declared_value');
            $todayEarnings = $todayValue * ($agent->commission_value / 100);
        }

        // Get rating
        $rating = $agent->rating ?? 4.5;

        // Get recent deliveries (last 5)
        $recentDeliveries = Shipment::where('assigned_to', $user->id)
            ->where('status', 'delivered')
            ->orderBy('actual_delivery_date', 'desc')
            ->take(5)
            ->get();

        // Get weekly stats
        $weeklyStats = $this->getWeeklyStats($user->id);

        return view('agent.dashboard', compact(
            'agent',
            'activeShipments',
            'todayDeliveries',
            'totalDeliveries',
            'todayEarnings',
            'rating',
            'recentDeliveries',
            'weeklyStats'
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
            $date = now()->startOfWeek()->addDays($index);
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
            $agent->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Agent not found'
        ], 404);
    }
}
