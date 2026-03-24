<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Get public configuration for maps and location services
     * This endpoint is cached and suitable for frontend consumption
     */
    public function getMapsConfig(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'google_maps_api_key' => config('services.google.maps_api_key'),
                'default_latitude' => config('services.google.default_lat', 22.524768),
                'default_longitude' => config('services.google.default_lng', 72.955568),
                'warehouse_latitude' => env('WAREHOUSE_LAT', 22.524768),
                'warehouse_longitude' => env('WAREHOUSE_LNG', 72.955568),
                'osrm_url' => env('OSRM_URL', 'http://router.project-osrm.org'),
                'map_provider' => 'google_maps',
                'allowed_radius_km' => env('ALLOWED_RADIUS_KM', 1)
            ]
        ])->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Get configuration for authenticated users (allows refresh)
     */
    public function getLocationConfig()
    {
        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'config' => [
                'google_api_key' => config('services.google.maps_api_key'),
                'warehouse' => [
                    'lat' => env('WAREHOUSE_LAT', 28.6139),
                    'lng' => env('WAREHOUSE_LNG', 77.2090),
                    'name' => 'Main Warehouse'
                ],
                'default_map_center' => [
                    'lat' => config('services.google.default_lat', 28.6139),
                    'lng' => config('services.google.default_lng', 77.2090),
                ],
                'map_zoom_levels' => [
                    'street' => 18,
                    'city' => 13,
                    'region' => 10,
                    'country' => 6
                ]
            ]
        ]);
    }
}
