<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    protected $googleMaps;

    public function __construct(GoogleMapsService $googleMaps)
    {
        $this->googleMaps = $googleMaps;
    }

    /**
     * Search locations with autocomplete
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:3',
            'country' => 'nullable|string|size:2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $results = $this->googleMaps->autocompleteAddress(
                $request->query,
                $request->country ?? 'IN'
            );

            return response()->json([
                'success' => true,
                'data' => $results,
                'count' => count($results)
            ]);

        } catch (\Exception $e) {
            Log::error('Location search error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to search locations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reverse geocoding - coordinates to address
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reverse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->googleMaps->reverseGeocode(
                $request->lat,
                $request->lng
            );

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'No address found for these coordinates'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Reverse geocoding error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to reverse geocode',
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
    public function calculateRoute(Request $request)
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
                    'message' => $result['message'] ?? 'No route found'
                ], 404);
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
     * Get distance matrix for multiple points
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
                    'message' => $result['message'] ?? 'No distance matrix found'
                ], 404);
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
     * Validate address and get coordinates
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->googleMaps->geocodeAddress($request->address);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not validate address'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Address validation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to validate address',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get place details by place_id
     *
     * @param string $placeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeDetails($placeId)
    {
        try {
            $result = $this->googleMaps->getPlaceDetails($placeId);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Place not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Place details error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get place details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
