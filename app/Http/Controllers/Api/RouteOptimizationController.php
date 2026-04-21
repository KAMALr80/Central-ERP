<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\DeliveryAgent;
use App\Services\GoogleMapsService;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RouteOptimizationController extends Controller
{
    protected $googleMaps;
    protected $shipmentService;

    public function __construct(GoogleMapsService $googleMaps, ShipmentService $shipmentService)
    {
        $this->googleMaps = $googleMaps;
        $this->shipmentService = $shipmentService;
    }

    /**
     * Optimize delivery route
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function optimize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipment_ids' => 'required|array|min:1',
            'shipment_ids.*' => 'exists:shipments,id',
            'start_lat' => 'nullable|numeric|between:-90,90',
            'start_lng' => 'nullable|numeric|between:-180,180',
            'end_lat' => 'nullable|numeric|between:-90,90',
            'end_lng' => 'nullable|numeric|between:-180,180',
            'agent_id' => 'nullable|exists:delivery_agents,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // If agent_id provided, use agent's current location as start
            if ($request->filled('agent_id')) {
                $agent = DeliveryAgent::where('user_id', $request->agent_id)->first();
                if ($agent && $agent->current_latitude && $agent->current_longitude) {
                    $startLat = $agent->current_latitude;
                    $startLng = $agent->current_longitude;
                }
            }

            $result = $this->shipmentService->optimizeRoute(
                $request->shipment_ids,
                $request->start_lat ?? $startLat ?? null,
                $request->start_lng ?? $startLng ?? null
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Route optimization error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize route',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate route between points
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origin' => 'required|string',
            'destination' => 'required|string',
            'waypoints' => 'nullable|array',
            'waypoints.*' => 'string',
            'mode' => 'nullable|in:driving,walking,bicycling,transit'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->googleMaps->getDirections(
                $request->origin,
                $request->destination,
                $request->waypoints ?? [],
                $request->mode ?? 'driving'
            );

            if ($result['status'] !== 'OK') {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Route calculation failed'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Route calculation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate route',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get distance matrix
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function distanceMatrix(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origins' => 'required|array|min:1',
            'destinations' => 'required|array|min:1',
            'mode' => 'nullable|in:driving,walking,bicycling,transit'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->googleMaps->getDistanceMatrix(
                $request->origins,
                $request->destinations,
                $request->mode ?? 'driving'
            );

            if ($result['status'] !== 'OK') {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Distance matrix calculation failed'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Distance matrix error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate distance matrix',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign optimized route to agent
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'required|exists:delivery_agents,user_id',
            'shipment_ids' => 'required|array|min:1',
            'shipment_ids.*' => 'exists:shipments,id',
            'route_order' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $agent = DeliveryAgent::where('user_id', $request->agent_id)->first();

            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agent not found'
                ], 404);
            }

            if ($agent->status !== 'available') {
                return response()->json([
                    'success' => false,
                    'message' => 'Agent is not available'
                ], 400);
            }

            $results = [
                'total' => count($request->shipment_ids),
                'success' => 0,
                'failed' => 0,
                'failed_ids' => []
            ];

            // Assign shipments in order
            $order = $request->route_order ?? $request->shipment_ids;

            foreach ($order as $index => $shipmentId) {
                $shipment = Shipment::find($shipmentId);

                if ($shipment && $shipment->status === 'pending') {
                    $shipment->assigned_to = $request->agent_id;
                    $shipment->delivery_order = $index + 1;
                    $shipment->save();

                    $shipment->updateTracking(
                        'assigned',
                        null,
                        "Assigned to agent with delivery order #" . ($index + 1)
                    );

                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['failed_ids'][] = $shipmentId;
                }
            }

            // Update agent status
            if ($results['success'] > 0) {
                $agent->status = 'busy';
                $agent->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Route assigned: {$results['success']} shipments assigned to agent",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Route assign error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign route',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get route details by ID
     *
     * @param int $routeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($routeId)
    {
        // This would typically fetch a saved route from database
        // For now, return a placeholder
        return response()->json([
            'success' => true,
            'message' => 'Route details feature coming soon'
        ]);
    }
}
