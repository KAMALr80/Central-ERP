<?php
// app/Http/Controllers/Api/AgentLocationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Shipment;
use App\Models\AgentLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AgentLocationController extends Controller
{
    /**
     * Update agent's current location
     */
    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'speed' => 'nullable|numeric|min:0|max:200',
            'heading' => 'nullable|integer|min:0|max:360',
            'battery_level' => 'nullable|integer|min:0|max:100',
            'shipment_id' => 'nullable|exists:shipments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Get agent profile
        $agent = DeliveryAgent::where('user_id', $user->id)
            ->orWhere('id', $user->id)
            ->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'error' => 'Agent profile not found'
            ], 404);
        }

        // Check if agent is approved
        if ($agent->approval_status !== 'approved') {
            return response()->json([
                'success' => false,
                'error' => 'Agent account not approved yet'
            ], 403);
        }

        // Update location in delivery_agents table
        $agent->current_latitude = $request->latitude;
        $agent->current_longitude = $request->longitude;
        $agent->last_location_update = now();
        $agent->is_online = true;
        $agent->status = 'online';

        // Store speed and battery if provided
        if ($request->filled('speed')) {
            $agent->current_speed = $request->speed;
        }
        if ($request->filled('battery_level')) {
            $agent->battery_level = $request->battery_level;
        }
        if ($request->filled('heading')) {
            $agent->heading = $request->heading;
        }

        $agent->save();

        // Save to location history for tracking map visualization
        try {
            AgentLocation::create([
                'agent_id' => $agent->user_id ?? $agent->id,
                'shipment_id' => $request->shipment_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy,
                'speed' => $request->speed,
                'heading' => $request->heading,
                'recorded_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save location history: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => [
                'latitude' => $agent->current_latitude,
                'longitude' => $agent->current_longitude,
                'speed' => $agent->current_speed ?? 0,
                'battery_level' => $agent->battery_level ?? 0,
                'updated_at' => $agent->last_location_update,
                'is_online' => true
            ]
        ]);
    }

    /**
     * Get agent's current location for a shipment (public tracking)
     */
    public function getAgentLocation($shipmentId)
    {
        $shipment = Shipment::with('assignedAgent')->findOrFail($shipmentId);

        if (!$shipment->assignedAgent) {
            return response()->json([
                'success' => false,
                'error' => 'No agent assigned to this shipment'
            ], 404);
        }

        $agent = $shipment->assignedAgent;

        // Check if location is recent (within 5 minutes)
        $isRecent = $agent->last_location_update &&
                    $agent->last_location_update->diffInMinutes(now()) <= 5;

        return response()->json([
            'success' => true,
            'data' => [
                'agent' => [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'phone' => $agent->phone,
                    'photo' => $agent->photo,
                    'vehicle_type' => $agent->vehicle_type,
                    'vehicle_number' => $agent->vehicle_number,
                    'rating' => $agent->rating
                ],
                'current_location' => [
                    'lat' => (float) $agent->current_latitude,
                    'lng' => (float) $agent->current_longitude,
                    'updated_at' => $agent->last_location_update,
                    'is_recent' => $isRecent,
                    'last_update_human' => $agent->last_location_update?->diffForHumans()
                ],
                'shipment' => [
                    'id' => $shipment->id,
                    'tracking_number' => $shipment->tracking_number,
                    'status' => $shipment->status,
                    'destination' => [
                        'lat' => (float) $shipment->destination_latitude,
                        'lng' => (float) $shipment->destination_longitude,
                        'address' => $shipment->shipping_address
                    ]
                ]
            ]
        ]);
    }

    /**
     * Get location history for an agent (admin)
     */
    public function getLocationHistory(Request $request, $agentId)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $query = AgentLocation::where('agent_id', $agentId);

        // Filter by date
        if ($request->date) {
            $query->whereDate('recorded_at', $request->date);
        } elseif ($request->start_date && $request->end_date) {
            $query->whereBetween('recorded_at', [$request->start_date, $request->end_date]);
        } elseif ($request->start_date) {
            $query->whereDate('recorded_at', '>=', $request->start_date);
        } elseif ($request->end_date) {
            $query->whereDate('recorded_at', '<=', $request->end_date);
        } else {
            // Default to last 24 hours
            $query->where('recorded_at', '>=', now()->subHours(24));
        }

        // Apply limit
        if ($request->limit) {
            $query->limit($request->limit);
        }

        $locations = $query->orderBy('recorded_at', 'desc')->get();

        // Calculate statistics
        $stats = AgentLocation::getStatisticsForAgent($agentId, $request->date);

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $locations->count(),
                'statistics' => $stats,
                'locations' => $locations->map(function ($location) {
                    return [
                        'id' => $location->id,
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                        'accuracy' => $location->accuracy,
                        'speed' => $location->formatted_speed,
                        'battery' => $location->battery_level,
                        'recorded_at' => $location->recorded_at,
                        'recorded_at_human' => $location->recorded_at->diffForHumans(),
                        'google_maps_url' => $location->google_maps_url
                    ];
                })
            ]
        ]);
    }

    /**
     * Update agent's online status
     */
    public function updateOnlineStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_online' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        if (!$agent) {
            return response()->json([
                'success' => false,
                'error' => 'Agent not found'
            ], 404);
        }

        $agent->is_online = $request->is_online;

        if ($request->is_online) {
            $agent->last_online_at = now();
        } else {
            $agent->last_offline_at = now();
        }

        $agent->save();

        return response()->json([
            'success' => true,
            'is_online' => $agent->is_online,
            'message' => $request->is_online ? 'You are now online' : 'You are now offline'
        ]);
    }

    /**
     * Get nearby agents (for admin/dispatch)
     */
    public function getNearbyAgents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:50',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $radius = $request->radius ?? 10;
        $limit = $request->limit ?? 10;

        $agents = DeliveryAgent::available()
            ->withLiveLocation()
            ->nearby($request->latitude, $request->longitude, $radius)
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'center' => [
                    'lat' => $request->latitude,
                    'lng' => $request->longitude
                ],
                'radius' => $radius,
                'total' => $agents->count(),
                'agents' => $agents->map(function ($agent) {
                    return [
                        'id' => $agent->id,
                        'name' => $agent->name,
                        'phone' => $agent->phone,
                        'vehicle_type' => $agent->vehicle_type,
                        'vehicle_number' => $agent->vehicle_number,
                        'rating' => $agent->rating,
                        'current_location' => [
                            'lat' => $agent->current_latitude,
                            'lng' => $agent->current_longitude
                        ],
                        'distance_km' => round($agent->distance, 2),
                        'last_update' => $agent->last_location_update?->diffForHumans()
                    ];
                })
            ]
        ]);
    }

    /**
     * Get real-time agent location with speed for shipment display
     */
    public function getShipmentAgentLocation($shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);

        if (!$shipment->assigned_to) {
            return response()->json([
                'success' => false,
                'error' => 'No agent assigned to this shipment'
            ], 404);
        }

        // Get latest location from AgentLocation table for this shipment
        $latestLocation = AgentLocation::where('agent_id', $shipment->assigned_to)
            ->where('shipment_id', $shipmentId)
            ->latest('recorded_at')
            ->first();

        // If no location for this shipment, get latest overall location
        if (!$latestLocation) {
            $latestLocation = AgentLocation::where('agent_id', $shipment->assigned_to)
                ->latest('recorded_at')
                ->first();
        }

        $agent = $shipment->agent;

        if (!$agent) {
            return response()->json([
                'success' => false,
                'error' => 'Agent information not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'agent' => [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'phone' => $agent->phone,
                    'email' => $agent->email,
                    'photo' => $agent->photo,
                    'vehicle_type' => $agent->vehicle_type,
                    'vehicle_number' => $agent->vehicle_number,
                    'rating' => $agent->rating,
                    'total_deliveries' => $agent->total_deliveries,
                    'successful_deliveries' => $agent->successful_deliveries,
                    'status' => $agent->status,
                    'is_online' => $agent->status === 'online' || $agent->status === 'busy',
                ],
                'location' => [
                    'latitude' => (float)($latestLocation->latitude ?? $agent->current_latitude ?? 0),
                    'longitude' => (float)($latestLocation->longitude ?? $agent->current_longitude ?? 0),
                    'accuracy' => (float)($latestLocation->accuracy ?? 0),
                    'speed' => (float)($latestLocation->speed ?? 0),
                    'speed_kmh' => (float)($latestLocation->speed ?? 0), // Speed is already in km/h
                    'heading' => (int)($latestLocation->heading ?? 0),
                    'battery_level' => (int)($latestLocation->battery_level ?? 0),
                    'recorded_at' => $latestLocation->recorded_at ?? $agent->last_location_update,
                    'recorded_at_human' => $latestLocation?->recorded_at?->diffForHumans() ?? $agent->last_location_update?->diffForHumans(),
                    'is_recent' => $latestLocation && $latestLocation->recorded_at->diffInMinutes(now()) <= 5,
                ],
                'shipment' => [
                    'id' => $shipment->id,
                    'tracking_number' => $shipment->tracking_number,
                    'status' => $shipment->status,
                    'destination' => [
                        'latitude' => (float)($shipment->destination_latitude ?? 22.524768),
                        'longitude' => (float)($shipment->destination_longitude ?? 72.955568),
                        'address' => $shipment->shipping_address
                    ]
                ]
            ]
        ]);
    }
}
