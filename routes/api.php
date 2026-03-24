<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\ShipmentApiController;
use App\Http\Controllers\Api\AgentApiController;
use App\Http\Controllers\Api\RouteOptimizationController;
use App\Http\Controllers\Api\GeocodingController;
use App\Http\Controllers\Api\AgentLocationController;
use App\Http\Controllers\Api\ConfigController;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================================
// TEST ROUTE - Check if API is working
// ============================================================
Route::middleware('api')->prefix('v1')->group(function () {
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'Logistics API is working',
            'timestamp' => now()->toDateTimeString(),
            'version' => '1.0.0'
        ]);
    });
});

// ============================================================
// CONFIGURATION API (Maps & Services Configuration)
// ============================================================
Route::prefix('config')->name('api.config.')->group(function () {
    // Get maps configuration (public, cached)
    Route::get('/maps', [ConfigController::class, 'getMapsConfig'])->name('maps');

    // Get location configuration (for authenticated users)
    Route::get('/location', [ConfigController::class, 'getLocationConfig'])
        ->middleware('auth:sanctum')
        ->name('location');
});

// ============================================================
// AGENT LOCATION TRACKING API (Live Location Updates)
// ============================================================
Route::prefix('agent-location')->name('api.agent-location.')->middleware(['auth:sanctum'])->group(function () {
    // Update agent's current location during delivery
    Route::post('/update', [AgentLocationController::class, 'updateLocation'])->name('update');

    // Get agent location for a shipment
    Route::get('/shipment/{shipmentId}', [AgentLocationController::class, 'getAgentLocation'])->name('shipment');

    // Get location history for agent during current delivery
    Route::get('/history/{shipmentId}', [AgentLocationController::class, 'getLocationHistory'])->name('history');
});

// ============================================================
// LOCATION SERVICES API (Google Maps Integration)
// ============================================================
Route::prefix('locations')->name('api.locations.')->group(function () {
    // Address search with autocomplete
    Route::get('/search', [LocationController::class, 'search'])->name('search');

    // Reverse geocoding (coordinates to address)
    Route::get('/reverse', [LocationController::class, 'reverse'])->name('reverse');

    // Calculate route between points
    Route::post('/route', [LocationController::class, 'calculateRoute'])->name('route');

    // Get distance matrix for multiple points
    Route::post('/distance-matrix', [LocationController::class, 'distanceMatrix'])->name('distance-matrix');

    // Validate address and get coordinates
    Route::post('/validate', [LocationController::class, 'validateAddress'])->name('validate');

    // Get place details by place_id
    Route::get('/place/{placeId}', [LocationController::class, 'placeDetails'])->name('place-details');
});

// ============================================================
// GEOCODING API (For address to coordinates)
// ============================================================
Route::prefix('geocode')->name('api.geocode.')->group(function () {
    // Geocode single address
    Route::get('/address', [GeocodingController::class, 'geocode'])->name('address');

    // Reverse geocode coordinates
    Route::get('/reverse', [GeocodingController::class, 'reverse'])->name('reverse');

    // Batch geocode multiple addresses
    Route::post('/batch', [GeocodingController::class, 'batch'])->name('batch');

    // Autocomplete address suggestions
    Route::get('/autocomplete', [GeocodingController::class, 'autocomplete'])->name('autocomplete');
});

// ============================================================
// SHIPMENT TRACKING API (Public - No Auth Required)
// ============================================================
Route::prefix('track')->name('api.track.')->group(function () {
    // Track by tracking number (public)
    Route::get('/{trackingNumber}', [TrackingController::class, 'track'])->name('shipment');

    // Track by shipment number
    Route::get('/shipment/{shipmentNumber}', [TrackingController::class, 'trackByShipment'])->name('by-shipment');

    // Get tracking timeline
    Route::get('/{trackingNumber}/timeline', [TrackingController::class, 'timeline'])->name('timeline');

    // Get current location
    Route::get('/{trackingNumber}/location', [TrackingController::class, 'currentLocation'])->name('location');
});

// ============================================================
// SHIPMENT API (Requires Authentication)
// ============================================================
Route::prefix('shipments')->name('api.shipments.')->middleware(['auth:sanctum'])->group(function () {
    // List shipments with filters
    Route::get('/', [ShipmentApiController::class, 'index'])->name('index');

    // Get single shipment details
    Route::get('/{id}', [ShipmentApiController::class, 'show'])->name('show');

    // Create new shipment
    Route::post('/', [ShipmentApiController::class, 'store'])->name('store');

    // Update shipment
    Route::put('/{id}', [ShipmentApiController::class, 'update'])->name('update');

    // Delete shipment
    Route::delete('/{id}', [ShipmentApiController::class, 'destroy'])->name('destroy');

    // ===== SHIPMENT ACTIONS =====

    // Update shipment status
    Route::post('/{id}/status', [ShipmentApiController::class, 'updateStatus'])->name('status');

    // Assign delivery agent
    Route::post('/{id}/assign-agent', [ShipmentApiController::class, 'assignAgent'])->name('assign-agent');

    // Update live location (for delivery boys)
    Route::post('/{id}/live-location', [ShipmentApiController::class, 'updateLiveLocation'])->name('live-location');

    // Upload proof of delivery
    Route::post('/{id}/upload-pod', [ShipmentApiController::class, 'uploadPOD'])->name('upload-pod');

    // Get proof of delivery
    Route::get('/{id}/pod', [ShipmentApiController::class, 'getPOD'])->name('get-pod');

    // Cancel shipment
    Route::post('/{id}/cancel', [ShipmentApiController::class, 'cancel'])->name('cancel');

    // Get tracking history
    Route::get('/{id}/tracking', [ShipmentApiController::class, 'trackingHistory'])->name('tracking');

    // ===== BULK OPERATIONS =====

    // Bulk status update
    Route::post('/bulk/status', [ShipmentApiController::class, 'bulkStatusUpdate'])->name('bulk-status');

    // Bulk assign agents
    Route::post('/bulk/assign', [ShipmentApiController::class, 'bulkAssign'])->name('bulk-assign');

    // Bulk delete
    Route::delete('/bulk', [ShipmentApiController::class, 'bulkDelete'])->name('bulk-delete');
});

// ============================================================
// DELIVERY AGENT API
// ============================================================
Route::prefix('agents')->name('api.agents.')->middleware(['auth:sanctum'])->group(function () {
    // List agents with filters
    Route::get('/', [AgentApiController::class, 'index'])->name('index');

    // Get single agent
    Route::get('/{id}', [AgentApiController::class, 'show'])->name('show');

    // Create new agent
    Route::post('/', [AgentApiController::class, 'store'])->name('store');

    // Update agent
    Route::put('/{id}', [AgentApiController::class, 'update'])->name('update');

    // Delete agent
    Route::delete('/{id}', [AgentApiController::class, 'destroy'])->name('destroy');

    // ===== AGENT ACTIONS =====

    // Update agent status
    Route::post('/{id}/status', [AgentApiController::class, 'updateStatus'])->name('status');

    // Update agent location
    Route::post('/{id}/location', [AgentApiController::class, 'updateLocation'])->name('location');

    // Get agent location
    Route::get('/{id}/location', [AgentApiController::class, 'getLocation'])->name('get-location');

    // Get agent performance stats
    Route::get('/{id}/performance', [AgentApiController::class, 'performance'])->name('performance');

    // Get agent assigned shipments
    Route::get('/{id}/shipments', [AgentApiController::class, 'assignedShipments'])->name('shipments');

    // Upload agent documents
    Route::post('/{id}/documents', [AgentApiController::class, 'uploadDocuments'])->name('documents');

    // ===== AGENT MAP =====

    // Get all agents for map
    Route::get('/map/all', [AgentApiController::class, 'getAllForMap'])->name('map-all');

    // Find nearest available agents
    Route::get('/nearby', [AgentApiController::class, 'findNearby'])->name('nearby');

    // ===== BULK OPERATIONS =====

    // Bulk status update
    Route::post('/bulk/status', [AgentApiController::class, 'bulkStatusUpdate'])->name('bulk-status');

    // Bulk delete
    Route::delete('/bulk', [AgentApiController::class, 'bulkDelete'])->name('bulk-delete');
});

// ============================================================
// ROUTE OPTIMIZATION API
// ============================================================
Route::prefix('routes')->name('api.routes.')->middleware(['auth:sanctum'])->group(function () {
    // Optimize delivery route
    Route::post('/optimize', [RouteOptimizationController::class, 'optimize'])->name('optimize');

    // Calculate route between points
    Route::post('/calculate', [RouteOptimizationController::class, 'calculate'])->name('calculate');

    // Get distance matrix
    Route::post('/distance-matrix', [RouteOptimizationController::class, 'distanceMatrix'])->name('distance-matrix');

    // Assign optimized route to agent
    Route::post('/assign', [RouteOptimizationController::class, 'assign'])->name('assign');

    // Get route details by ID
    Route::get('/{routeId}', [RouteOptimizationController::class, 'show'])->name('show');
});

// ============================================================
// PUBLIC TRACKING PAGE (No Auth Required)
// ============================================================
Route::prefix('public')->name('api.public.')->group(function () {
    // Track shipment (public)
    Route::get('/track/{trackingNumber}', [TrackingController::class, 'publicTrack'])->name('track');

    // Get agent public info
    Route::get('/agent/{id}', [AgentApiController::class, 'publicInfo'])->name('agent');

    // Get delivery timeline
    Route::get('/timeline/{trackingNumber}', [TrackingController::class, 'publicTimeline'])->name('timeline');
});

// ============================================================
// DELIVERY BOY APP API (Simple Auth)
// ============================================================
Route::prefix('app')->name('api.app.')->group(function () {
    // Delivery boy login
    Route::post('/login', [AgentApiController::class, 'appLogin'])->name('login');

    // Delivery boy routes (require token)
    Route::middleware(['auth:sanctum'])->group(function () {
        // Get assigned shipments
        Route::get('/shipments', [AgentApiController::class, 'appShipments'])->name('shipments');

        // Update shipment status
        Route::post('/shipment/{id}/status', [AgentApiController::class, 'appUpdateStatus'])->name('update-status');

        // Update live location
        Route::post('/location', [AgentApiController::class, 'appUpdateLocation'])->name('update-location');

        // Get next delivery
        Route::get('/next-delivery', [AgentApiController::class, 'appNextDelivery'])->name('next-delivery');

        // Mark as delivered with POD
        Route::post('/shipment/{id}/deliver', [AgentApiController::class, 'appDeliver'])->name('deliver');

        // Get route for today
        Route::get('/route', [AgentApiController::class, 'appRoute'])->name('route');

        // Get agent profile
        Route::get('/profile', [AgentApiController::class, 'appProfile'])->name('profile');

        // Update profile
        Route::post('/profile', [AgentApiController::class, 'appUpdateProfile'])->name('update-profile');
    });
});

// ============================================================
// WEBHOOKS FOR COURIER PARTNERS
// ============================================================
Route::prefix('webhooks')->name('api.webhooks.')->group(function () {
    // Delhivery webhook
    Route::post('/delhivery', [TrackingController::class, 'delhiveryWebhook'])->name('delhivery');

    // BlueDart webhook
    Route::post('/bluedart', [TrackingController::class, 'bluedartWebhook'])->name('bluedart');

    // DTDC webhook
    Route::post('/dtdc', [TrackingController::class, 'dtdcWebhook'])->name('dtdc');

    // Generic courier webhook
    Route::post('/courier/{code}', [TrackingController::class, 'courierWebhook'])->name('courier');
});

// ============================================================
// DASHBOARD STATS API
// ============================================================
Route::prefix('dashboard')->name('api.dashboard.')->middleware(['auth:sanctum'])->group(function () {
    // Get dashboard stats
    Route::get('/stats', [ShipmentApiController::class, 'dashboardStats'])->name('stats');

    // Get shipment trend chart data
    Route::get('/trends', [ShipmentApiController::class, 'trends'])->name('trends');

    // Get agent performance chart
    Route::get('/agent-performance', [AgentApiController::class, 'performanceChart'])->name('agent-performance');

    // Get revenue stats
    Route::get('/revenue', [ShipmentApiController::class, 'revenueStats'])->name('revenue');

    // Get top cities
    Route::get('/top-cities', [ShipmentApiController::class, 'topCities'])->name('top-cities');
});

// ============================================================
// HEALTH CHECK & MONITORING
// ============================================================
Route::prefix('health')->name('api.health.')->group(function () {
    // Basic health check
    Route::get('/', function () {
        return response()->json([
            'status' => 'healthy',
            'services' => [
                'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected',
                'api' => 'operational'
            ],
            'timestamp' => now()->toDateTimeString()
        ]);
    })->name('check');

    // Detailed health check (authenticated)
    Route::middleware(['auth:sanctum'])->get('/detailed', function () {
        return response()->json([
            'status' => 'healthy',
            'database' => DB::connection()->getDatabaseName(),
            'services' => [
                'google_maps' => config('services.google.maps_api_key') ? 'configured' : 'missing',
                'cache' => 'operational',
                'queue' => 'operational'
            ],
            'stats' => [
                'total_shipments' => App\Models\Shipment::count(),
                'total_agents' => App\Models\DeliveryAgent::count(),
                'pending_shipments' => App\Models\Shipment::where('status', 'pending')->count()
            ],
            'timestamp' => now()->toDateTimeString()
        ]);
    })->name('detailed');
});

// ============================================================
// FALLBACK ROUTE FOR 404
// ============================================================
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
        'status_code' => 404
    ], 404);
});



// Agent API Routes (For AJAX calls)
Route::prefix('agent')->middleware(['auth:sanctum', 'agent'])->group(function () {
    // Location Tracking (High Priority)
    Route::post('/location', [Agent\TrackingController::class, 'updateLocation']);
    Route::get('/location/history/{shipment}', [Agent\TrackingController::class, 'getLocationHistory']);

    // Dashboard Data
    Route::get('/dashboard/stats', [Agent\DashboardController::class, 'getStats']);
    Route::get('/deliveries/assigned', [Agent\DeliveryController::class, 'getAssigned']);

    // Notifications
    Route::get('/notifications/unread-count', [Agent\NotificationController::class, 'getUnreadCount']);
    Route::get('/notifications/latest', [Agent\NotificationController::class, 'getLatest']);

    // Performance
    Route::get('/performance/today', [Agent\PerformanceController::class, 'getTodayStats']);
});

// routes/api.php

use App\Http\Controllers\Api\AgentLocationController;

Route::middleware('auth:sanctum')->group(function () {
    // Agent location routes
    Route::post('/agent/location/update', [AgentLocationController::class, 'updateLocation']);
    Route::post('/agent/online-status', [AgentLocationController::class, 'updateOnlineStatus']);
    Route::get('/agent/location/history/{agentId}', [AgentLocationController::class, 'getLocationHistory']);
    Route::get('/agent/nearby', [AgentLocationController::class, 'getNearbyAgents']);
});

// Public tracking route (no auth)
Route::get('/public/track/{shipmentId}', [AgentLocationController::class, 'getAgentLocation']);
