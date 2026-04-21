<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GeocodingController extends Controller
{
    protected $googleMaps;

    public function __construct(GoogleMapsService $googleMaps)
    {
        $this->googleMaps = $googleMaps;
    }

    /**
     * Geocode single address
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function geocode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
            'full_response' => 'nullable|boolean'
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
                    'message' => 'Could not geocode address'
                ], 404);
            }

            // If full_response is requested, get place details
            if ($request->boolean('full_response') && isset($result['place_id'])) {
                $details = $this->googleMaps->getPlaceDetails($result['place_id']);
                $result = array_merge($result, ['details' => $details]);
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Geocoding error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to geocode address',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reverse geocode coordinates
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
                    'message' => 'No address found'
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
     * Batch geocode multiple addresses
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function batch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'addresses' => 'required|array|min:1|max:50',
            'addresses.*' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $results = [];
            $failed = [];

            foreach ($request->addresses as $index => $address) {
                $result = $this->googleMaps->geocodeAddress($address);

                if ($result) {
                    $results[$index] = $result;
                } else {
                    $failed[$index] = $address;
                }

                // Small delay to avoid rate limiting
                if ($index < count($request->addresses) - 1) {
                    usleep(200000); // 200ms delay
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'successful' => $results,
                    'failed' => $failed,
                    'total' => count($request->addresses),
                    'success_count' => count($results),
                    'failed_count' => count($failed)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Batch geocoding error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to batch geocode',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Autocomplete address suggestions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'input' => 'required|string|min:2',
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
                $request->input,
                $request->country ?? 'IN'
            );

            return response()->json([
                'success' => true,
                'data' => $results,
                'count' => count($results)
            ]);

        } catch (\Exception $e) {
            Log::error('Autocomplete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get suggestions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
