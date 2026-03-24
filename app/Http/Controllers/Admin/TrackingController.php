<?php
// app/Http/Controllers/Admin/TrackingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrackingController extends Controller
{
    /**
     * Display all active agents on map
     */
    public function index()
    {
        $agents = DeliveryAgent::where('approval_status', 'approved')
            ->where('is_active', true)
            ->get();

        return view('admin.tracking.agents', compact('agents'));
    }

    /**
     * Display active agents list for admin
     */
    public function activeAgents()
    {
        $activeAgents = DeliveryAgent::where('approval_status', 'approved')
            ->where('is_active', true)
            ->where('status', '!=', 'offline')
            ->with(['currentShipment', 'user'])
            ->get();

        // Calculate additional stats for each agent
        foreach ($activeAgents as $agent) {
            $agent->current_speed = $this->getAgentCurrentSpeed($agent->user_id);
            $agent->current_location = $this->getAgentCurrentAddress($agent->current_latitude, $agent->current_longitude);

            if ($agent->currentShipment) {
                $remainingDistance = $this->calculateDistance(
                    $agent->current_latitude,
                    $agent->current_longitude,
                    $agent->currentShipment->destination_latitude,
                    $agent->currentShipment->destination_longitude
                );
                $agent->remaining_distance = round($remainingDistance, 1);
            }
        }

        return view('admin.tracking.active-agents', compact('activeAgents'));
    }

    /**
     * Track specific agent location
     */
    public function agentLocation($agentId)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)
            ->orWhere('id', $agentId)
            ->firstOrFail();

        $activeShipment = Shipment::where('assigned_to', $agent->user_id)
            ->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery'])
            ->first();

        return view('admin.tracking.agent', compact('agent', 'activeShipment'));
    }

    /**
     * Get agent live location via API
     */
    public function getAgentLiveLocation($agentId)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)
            ->orWhere('id', $agentId)
            ->first();

        if (!$agent) {
            return response()->json(['error' => 'Agent not found'], 404);
        }

        $data = [
            'latitude' => $agent->current_latitude,
            'longitude' => $agent->current_longitude,
            'speed' => $this->getAgentCurrentSpeed($agent->user_id),
            'last_update' => $agent->last_location_update,
            'is_online' => $agent->last_location_update &&
                           $agent->last_location_update->diffInMinutes(now()) <= 5,
            'status' => $agent->status
        ];

        return response()->json($data);
    }

    /**
     * Get all agents locations for map
     */
    public function getAllAgentsLocations()
    {
        $agents = DeliveryAgent::where('approval_status', 'approved')
            ->where('is_active', true)
            ->whereNotNull('current_latitude')
            ->whereNotNull('current_longitude')
            ->get();

        $locations = [];
        foreach ($agents as $agent) {
            $activeShipment = Shipment::where('assigned_to', $agent->user_id)
                ->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery'])
                ->first();

            $locations[] = [
                'id' => $agent->id,
                'user_id' => $agent->user_id,
                'name' => $agent->name,
                'agent_code' => $agent->agent_code,
                'phone' => $agent->phone,
                'latitude' => (float) $agent->current_latitude,
                'longitude' => (float) $agent->current_longitude,
                'status' => $agent->status,
                'speed' => $this->getAgentCurrentSpeed($agent->user_id),
                'last_update' => $agent->last_location_update?->diffForHumans(),
                'current_shipment' => $activeShipment ? [
                    'id' => $activeShipment->id,
                    'shipment_number' => $activeShipment->shipment_number,
                    'receiver_name' => $activeShipment->receiver_name
                ] : null
            ];
        }

        return response()->json([
            'success' => true,
            'agents' => $locations,
            'total' => count($locations)
        ]);
    }

    /**
     * Get agent route history
     */
    public function getAgentRouteHistory($agentId, Request $request)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)->first();
        if (!$agent) {
            return response()->json(['error' => 'Agent not found'], 404);
        }

        $startDate = $request->get('start_date', Carbon::now()->subDay()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $locations = \App\Models\AgentLocation::where('agent_id', $agent->user_id)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->orderBy('recorded_at', 'asc')
            ->get();

        $route = [];
        foreach ($locations as $location) {
            $route[] = [
                'lat' => (float) $location->latitude,
                'lng' => (float) $location->longitude,
                'time' => $location->recorded_at->toDateTimeString(),
                'speed' => $location->speed,
                'accuracy' => $location->accuracy
            ];
        }

        $stats = [
            'total_points' => count($route),
            'total_distance' => $this->calculateTotalDistance($route),
            'start_time' => $locations->first()?->recorded_at,
            'end_time' => $locations->last()?->recorded_at
        ];

        return response()->json([
            'success' => true,
            'route' => $route,
            'statistics' => $stats
        ]);
    }

    /**
     * Get agent current speed
     */
    private function getAgentCurrentSpeed($agentId)
    {
        $latestLocation = \App\Models\AgentLocation::where('agent_id', $agentId)
            ->orderBy('recorded_at', 'desc')
            ->first();

        return $latestLocation ? round($latestLocation->speed, 1) : 0;
    }

    /**
     * Get address from coordinates (reverse geocoding)
     */
    private function getAgentCurrentAddress($lat, $lng)
    {
        if (!$lat || !$lng) return null;

        // You can integrate Google Geocoding API here
        // For now, return coordinates
        return "{$lat}, {$lng}";
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
     * Calculate total distance from route points
     */
    private function calculateTotalDistance($route)
    {
        $total = 0;
        for ($i = 0; $i < count($route) - 1; $i++) {
            $total += $this->calculateDistance(
                $route[$i]['lat'], $route[$i]['lng'],
                $route[$i+1]['lat'], $route[$i+1]['lng']
            );
        }
        return round($total, 2);
    }
}
