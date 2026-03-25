<?php
// app/Http/Controllers/Agent/TrackingController.php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\DeliveryAgent;
use App\Models\AgentLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TrackingController extends Controller
{
    /**
     * Live tracking page
     */
    public function live($shipmentId)
    {
        
        $shipment = Shipment::where('id', $shipmentId)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        // Get agent details for tracking
        $agent = DeliveryAgent::where('user_id', Auth::id())->first();

        // Calculate distance to destination if coordinates available
        $distance = null;
        $eta = null;

        if ($agent && $agent->current_latitude && $shipment->destination_latitude) {
            $distance = $this->calculateDistance(
                $agent->current_latitude,
                $agent->current_longitude,
                $shipment->destination_latitude,
                $shipment->destination_longitude
            );

            if ($agent->current_speed && $agent->current_speed > 0) {
                $eta = ($distance / $agent->current_speed) * 60;
            }
        }

        return view('agent.tracking.live', compact('shipment', 'agent', 'distance', 'eta'));
    }

    /**
     * Get current location of the agent (AJAX)
     */
    public function getCurrentLocation()
    {
        $user = Auth::user();

        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'error' => 'Agent not found'
            ], 404);
        }

        $isOnline = $agent->last_location_update &&
                    $agent->last_location_update->diffInMinutes(now()) <= 5;

        return response()->json([
            'success' => true,
            'latitude' => $agent->current_latitude ?? 22.524768,
            'longitude' => $agent->current_longitude ?? 72.955568,
            'speed' => $agent->current_speed ?? 0,
            'accuracy' => $agent->location_accuracy ?? null,
            'last_update' => $agent->last_location_update,
            'is_online' => $isOnline,
            'status' => $agent->status
        ]);
    }

    /**
     * Update agent location (AJAX)
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'accuracy' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|min:0|max:360',
            'shipment_id' => 'nullable|exists:shipments,id'
        ]);

        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'error' => 'Agent not found'
            ], 404);
        }

        // Check if agent is approved
        if ($agent->approval_status !== 'approved') {
            return response()->json([
                'success' => false,
                'error' => 'Agent not approved'
            ], 403);
        }

        // Update location
        $agent->current_latitude = $request->latitude;
        $agent->current_longitude = $request->longitude;
        $agent->last_location_update = now();

        if ($request->has('speed')) {
            $agent->current_speed = $request->speed;
        }

        if ($request->has('accuracy')) {
            $agent->location_accuracy = $request->accuracy;
        }

        // Auto set status to available if moving
        if ($agent->status === 'offline' && $request->speed > 0) {
            $agent->status = 'available';
            $agent->last_online_at = now();
        }

        $agent->save();

        // Save location history
        if (class_exists(AgentLocation::class)) {
            try {
                AgentLocation::create([
                    'agent_id' => $agent->user_id,
                    'shipment_id' => $request->shipment_id,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'speed' => $request->speed,
                    'accuracy' => $request->accuracy,
                    'heading' => $request->heading,
                    'recorded_at' => now()
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to save location history: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Location updated',
            'data' => [
                'latitude' => $agent->current_latitude,
                'longitude' => $agent->current_longitude,
                'speed' => $agent->current_speed,
                'updated_at' => $agent->last_location_update
            ]
        ]);
    }

    /**
     * Get location history for a shipment (for customer tracking)
     */
    public function getLocationHistory($shipmentId)
    {
        $shipment = Shipment::where('id', $shipmentId)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        $agent = DeliveryAgent::where('user_id', Auth::id())->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'error' => 'Agent not found'
            ], 404);
        }

        $locations = AgentLocation::where('agent_id', $agent->user_id)
            ->where('shipment_id', $shipmentId)
            ->orderBy('recorded_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'locations' => $locations->map(function ($location) {
                return [
                    'lat' => $location->latitude,
                    'lng' => $location->longitude,
                    'speed' => $location->speed,
                    'recorded_at' => $location->recorded_at,
                    'recorded_at_human' => $location->recorded_at->diffForHumans()
                ];
            }),
            'total' => $locations->count()
        ]);
    }

    /**
     * Get route between agent and destination
     */
    public function getRoute($shipmentId)
    {
        $shipment = Shipment::where('id', $shipmentId)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        $agent = DeliveryAgent::where('user_id', Auth::id())->first();

        if (!$agent || !$agent->current_latitude || !$shipment->destination_latitude) {
            return response()->json([
                'success' => false,
                'error' => 'Location data not available'
            ], 404);
        }

        // Calculate distance
        $distance = $this->calculateDistance(
            $agent->current_latitude,
            $agent->current_longitude,
            $shipment->destination_latitude,
            $shipment->destination_longitude
        );

        // Calculate ETA
        $eta = $agent->current_speed > 0 ? ($distance / $agent->current_speed) * 60 : 0;

        // Generate Google Maps URL
        $mapsUrl = "https://www.google.com/maps/dir/{$agent->current_latitude},{$agent->current_longitude}/{$shipment->destination_latitude},{$shipment->destination_longitude}";

        return response()->json([
            'success' => true,
            'data' => [
                'origin' => [
                    'lat' => $agent->current_latitude,
                    'lng' => $agent->current_longitude,
                    'address' => $agent->current_location ?? 'Current Location'
                ],
                'destination' => [
                    'lat' => $shipment->destination_latitude,
                    'lng' => $shipment->destination_longitude,
                    'address' => $shipment->shipping_address
                ],
                'distance_km' => round($distance, 1),
                'eta_minutes' => round($eta),
                'eta_human' => $this->formatETA($eta),
                'current_speed' => round($agent->current_speed ?? 0, 1),
                'maps_url' => $mapsUrl
            ]
        ]);
    }

    /**
     * Update agent online status
     */
    public function updateOnlineStatus(Request $request)
    {
        $request->validate([
            'is_online' => 'required|boolean'
        ]);

        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'error' => 'Agent not found'
            ], 404);
        }

        if ($request->is_online) {
            $agent->status = 'available';
            $agent->last_online_at = now();
        } else {
            $agent->status = 'offline';
            $agent->last_offline_at = now();

            // Calculate online minutes
            if ($agent->last_online_at) {
                $onlineMinutes = $agent->last_online_at->diffInMinutes(now());
                $agent->total_online_minutes = ($agent->total_online_minutes ?? 0) + $onlineMinutes;
            }
        }

        $agent->save();

        return response()->json([
            'success' => true,
            'is_online' => $request->is_online,
            'status' => $agent->status
        ]);
    }

    /**
     * Get agent statistics (speed, distance, etc.)
     */
    public function getStatistics()
    {
        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'error' => 'Agent not found'
            ], 404);
        }

        // Get today's distance from location history
        $todayDistance = 0;
        if (class_exists(AgentLocation::class)) {
            $todayLocations = AgentLocation::where('agent_id', $agent->user_id)
                ->whereDate('recorded_at', Carbon::today())
                ->orderBy('recorded_at', 'asc')
                ->get();

            for ($i = 0; $i < $todayLocations->count() - 1; $i++) {
                $current = $todayLocations[$i];
                $next = $todayLocations[$i + 1];
                $todayDistance += $this->calculateDistance(
                    $current->latitude, $current->longitude,
                    $next->latitude, $next->longitude
                );
            }
        }

        // Get active shipment
        $activeShipment = Shipment::where('assigned_to', $user->id)
            ->whereIn('status', ['pending', 'picked', 'in_transit', 'out_for_delivery'])
            ->first();

        // Calculate distance to destination
        $distanceToDest = null;
        if ($activeShipment && $agent->current_latitude && $activeShipment->destination_latitude) {
            $distanceToDest = $this->calculateDistance(
                $agent->current_latitude,
                $agent->current_longitude,
                $activeShipment->destination_latitude,
                $activeShipment->destination_longitude
            );
        }

        return response()->json([
            'success' => true,
            'data' => [
                'current_speed' => round($agent->current_speed ?? 0, 1),
                'today_distance' => round($todayDistance, 1),
                'total_deliveries' => $agent->total_deliveries ?? 0,
                'successful_deliveries' => $agent->successful_deliveries ?? 0,
                'rating' => $agent->rating ?? 0,
                'status' => $agent->status,
                'is_online' => $agent->last_location_update &&
                               $agent->last_location_update->diffInMinutes(now()) <= 5,
                'active_shipment' => $activeShipment ? [
                    'id' => $activeShipment->id,
                    'shipment_number' => $activeShipment->shipment_number,
                    'distance_km' => round($distanceToDest ?? 0, 1),
                    'eta_minutes' => $agent->current_speed > 0 && $distanceToDest
                        ? round(($distanceToDest / ($agent->current_speed ?? 1)) * 60)
                        : 0
                ] : null
            ]
        ]);
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
     * Format ETA for display
     */
    private function formatETA($minutes)
    {
        if ($minutes <= 0) return 'Arriving soon';
        if ($minutes < 60) return round($minutes) . ' minutes';

        $hours = floor($minutes / 60);
        $mins = round($minutes % 60);

        if ($mins == 0) return $hours . ' hour' . ($hours > 1 ? 's' : '');
        return $hours . 'h ' . $mins . 'm';
    }

    /**
     * Show map view for route visualization
     */
    public function map($shipmentId)
    {
        $shipment = Shipment::where('id', $shipmentId)
            ->where('assigned_to', Auth::id())
            ->firstOrFail();

        $agent = DeliveryAgent::where('user_id', Auth::id())->first();

        return view('agent.tracking.map', compact('shipment', 'agent'));
    }
}
