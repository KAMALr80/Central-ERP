<?php
namespace App\Services;

use App\Models\Shipment;
use App\Models\Sale;
use App\Models\DeliveryAgent;
use App\Models\ShipmentTracking;
use App\Models\AgentLocation;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ShipmentService
{
    protected $googleMaps;

    /**
     * Constructor - Inject dependencies
     */
    public function __construct(GoogleMapsService $googleMaps)
    {
        $this->googleMaps = $googleMaps;
    }

    /* =========================================================
       1. CREATE SHIPMENT FROM SALE
    ========================================================= */

    /**
     * Auto-create shipment from sale
     *
     * @param Sale $sale
     * @param array $shippingData
     * @return Shipment|null
     */
    public function createFromSale(Sale $sale, array $shippingData = [])
    {
        try {
            DB::beginTransaction();

            // Check if shipment already exists
            if ($sale->shipments()->exists()) {
                Log::info("Shipment already exists for sale #{$sale->id}");
                return $sale->shipments()->first();
            }

            // Prepare receiver details
            $receiverName = $shippingData['receiver_name'] ?? $sale->customer->name;
            $receiverPhone = $shippingData['receiver_phone'] ?? $sale->customer->mobile;

            // Get coordinates if not provided
            $coordinates = $this->getCoordinatesFromAddress($shippingData, $sale);

            // Generate unique tracking number (Flipkart/Amazon style)
            $trackingNumber = $this->generateTrackingNumber($sale);

            // Create shipment
            $shipment = new Shipment();
            $shipment->shipment_number = $this->generateShipmentNumber();
            $shipment->tracking_number = $trackingNumber;
            $shipment->sale_id = $sale->id;
            $shipment->customer_id = $sale->customer_id;
            $shipment->receiver_name = $receiverName;
            $shipment->receiver_phone = $receiverPhone;
            $shipment->receiver_alternate_phone = $shippingData['alternate_phone'] ?? null;
            $shipment->shipping_address = $shippingData['shipping_address'] ?? $sale->shipping_address ?? $sale->customer->address;
            $shipment->landmark = $shippingData['landmark'] ?? null;
            $shipment->city = $shippingData['city'] ?? $sale->city ?? $sale->customer->city;
            $shipment->state = $shippingData['state'] ?? $sale->state ?? $sale->customer->state;
            $shipment->pincode = $shippingData['pincode'] ?? $sale->pincode ?? $sale->customer->pincode;
            $shipment->country = 'India';

            // Package details
            $shipment->declared_value = $sale->grand_total;
            $shipment->quantity = $sale->items->sum('quantity');
            $shipment->weight = $this->calculateTotalWeight($sale->items);
            $shipment->package_type = 'box'; // Default

            // Shipping method
            $shipment->shipping_method = $shippingData['shipping_method'] ?? 'standard';

            // Payment mode
            $shipment->payment_mode = $sale->payment_status === 'paid' ? 'prepaid' : 'cod';

            // Coordinates
            if ($coordinates) {
                $shipment->destination_latitude = $coordinates['lat'];
                $shipment->destination_longitude = $coordinates['lng'];
            } elseif ($sale->destination_latitude && $sale->destination_longitude) {
                $shipment->destination_latitude = $sale->destination_latitude;
                $shipment->destination_longitude = $sale->destination_longitude;
            }

            // Delivery instructions
            $shipment->delivery_instructions = $shippingData['delivery_instructions'] ?? $sale->delivery_instructions ?? null;

            // Status
            $shipment->status = 'pending';
            $shipment->created_by = auth()->id() ?? 1;

            // Calculate estimated delivery date
            $shipment->estimated_delivery_date = $this->calculateEstimatedDelivery($shipment->shipping_method);

            $shipment->save();

            // Add initial tracking
            $this->addTrackingEvent(
                $shipment,
                'pending',
                $shipment->city,
                'Shipment created from sale #' . $sale->invoice_no
            );

            DB::commit();

            Log::info("✅ Shipment auto-created from sale", [
                'sale_id' => $sale->id,
                'shipment_id' => $shipment->id,
                'tracking' => $shipment->tracking_number
            ]);

            return $shipment;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Failed to create shipment from sale: " . $e->getMessage(), [
                'sale_id' => $sale->id,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get coordinates from address using Google Maps
     */
    private function getCoordinatesFromAddress($shippingData, $sale)
    {
        // If coordinates already provided
        if (!empty($shippingData['destination_latitude']) && !empty($shippingData['destination_longitude'])) {
            return [
                'lat' => $shippingData['destination_latitude'],
                'lng' => $shippingData['destination_longitude']
            ];
        }

        // Build full address
        $address = $shippingData['shipping_address'] ?? $sale->shipping_address ?? $sale->customer->address;
        $city = $shippingData['city'] ?? $sale->city ?? $sale->customer->city;
        $state = $shippingData['state'] ?? $sale->state ?? $sale->customer->state;
        $pincode = $shippingData['pincode'] ?? $sale->pincode ?? $sale->customer->pincode;

        if (!$address) {
            return null;
        }

        $fullAddress = trim("{$address}, {$city}, {$state} - {$pincode}, India");

        // Use Google Maps service to geocode
        $result = $this->googleMaps->geocodeAddress($fullAddress);

        if ($result && isset($result['lat'], $result['lng'])) {
            return [
                'lat' => $result['lat'],
                'lng' => $result['lng']
            ];
        }

        return null;
    }

    /**
     * Calculate total weight from items (assuming 0.5kg per item default)
     */
    private function calculateTotalWeight($items)
    {
        $totalWeight = 0;
        foreach ($items as $item) {
            // If product has weight defined, use it, otherwise use default 0.5kg
            $productWeight = $item->product->weight ?? 0.5;
            $totalWeight += $productWeight * $item->quantity;
        }
        return round($totalWeight, 2);
    }

    /**
     * Generate unique shipment number
     * Format: SHIP-YYYYMMDD-XXXX
     */
    private function generateShipmentNumber()
    {
        $prefix = 'SHIP';
        $date = date('Ymd');
        $lastShipment = Shipment::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastShipment) {
            $lastNumber = intval(substr($lastShipment->shipment_number, -4));
            $sequence = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }

        return $prefix . '-' . $date . '-' . $sequence;
    }

    /**
     * Generate tracking number (Flipkart/Amazon style)
     * Format: INVYYYYMMDD-XXXXXX
     */
    private function generateTrackingNumber($sale)
    {
        $prefix = 'INV';
        $date = date('Ymd');
        $saleId = str_pad($sale->id, 6, '0', STR_PAD_LEFT);

        return $prefix . $date . '-' . $saleId;
    }

    /**
     * Calculate estimated delivery date based on shipping method
     */
    private function calculateEstimatedDelivery($shippingMethod)
    {
        $days = match ($shippingMethod) {
            'express' => 2,
            'overnight' => 1,
            default => 5 // standard
        };

        return Carbon::now()->addDays($days);
    }

    /* =========================================================
       2. TRACKING MANAGEMENT
    ========================================================= */

    /**
     * Add tracking event to shipment
     */
    public function addTrackingEvent(Shipment $shipment, $status, $location = null, $remarks = null, $latitude = null, $longitude = null)
    {
        try {
            $tracking = ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'status' => $status,
                'location' => $location ?? $shipment->city,
                'latitude' => $latitude ?? $shipment->current_latitude,
                'longitude' => $longitude ?? $shipment->current_longitude,
                'remarks' => $remarks,
                'updated_by' => auth()->id() ?? 'system',
                'tracked_at' => now()
            ]);

            // Update shipment status
            $shipment->status = $status;

            // If delivered, set actual delivery date
            if ($status === 'delivered') {
                $shipment->actual_delivery_date = now();
            }

            $shipment->save();

            Log::info("📍 Tracking event added", [
                'shipment_id' => $shipment->id,
                'status' => $status,
                'tracking_id' => $tracking->id
            ]);

            return $tracking;

        } catch (\Exception $e) {
            Log::error("❌ Failed to add tracking event: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get full tracking timeline
     */
    public function getTrackingTimeline(Shipment $shipment)
    {
        return $shipment->trackings()
            ->orderBy('tracked_at', 'desc')
            ->get()
            ->map(function($track) {
                return [
                    'status' => $track->status,
                    'status_display' => ucfirst(str_replace('_', ' ', $track->status)),
                    'location' => $track->location,
                    'remarks' => $track->remarks,
                    'time' => $track->tracked_at->format('h:i A'),
                    'date' => $track->tracked_at->format('d M Y'),
                    'full_datetime' => $track->tracked_at->format('d M Y, h:i A'),
                    'human_time' => $track->tracked_at->diffForHumans(),
                    'latitude' => $track->latitude,
                    'longitude' => $track->longitude,
                    'has_location' => !is_null($track->latitude) && !is_null($track->longitude)
                ];
            });
    }

    /* =========================================================
       3. AGENT ASSIGNMENT
    ========================================================= */

    /**
     * Assign delivery agent to shipment
     * IMPORTANT: Agent location starts from warehouse, then updates as they move
     */
    public function assignAgent(Shipment $shipment, $agentId)
    {
        try {
            DB::beginTransaction();

            $agent = DeliveryAgent::where('user_id', $agentId)->first();

            if (!$agent) {
                throw new \Exception("Delivery agent not found");
            }

            // Check if agent is available
            if ($agent->status !== 'available') {
                throw new \Exception("Agent is not available (Status: {$agent->status})");
            }

            // Update shipment
            $shipment->assigned_to = $agentId;
            $shipment->save();

            // Update agent status
            $agent->status = 'busy';

            // ========== WAREHOUSE INITIALIZATION ==========
            // When agent is assigned for pickup, initialize their location to warehouse
            // This is where they will pick up the package from
            $warehouseLatitude = config('logistics.warehouse_latitude', 22.524768);
            $warehouseLongitude = config('logistics.warehouse_longitude', 72.955568);

            $agent->current_latitude = $warehouseLatitude;
            $agent->current_longitude = $warehouseLongitude;
            $agent->current_location = 'Warehouse';
            $agent->last_location_update = now();
            $agent->save();

            // Create initial location record in agent_locations table
            // This establishes the starting point for real-time tracking
            AgentLocation::create([
                'agent_id' => $agent->user_id,
                'latitude' => $warehouseLatitude,
                'longitude' => $warehouseLongitude,
                'speed' => 0,
                'heading' => 0,
                'accuracy' => 0,
                'recorded_at' => now()
            ]);
            // =========================================

            // Add tracking event
            $this->addTrackingEvent(
                $shipment,
                'assigned',
                'Warehouse',
                "Assigned to delivery agent: {$agent->name} | Starting from Warehouse"
            );

            DB::commit();

            Log::info("✅ Agent assigned & initialized at warehouse", [
                'shipment_id' => $shipment->id,
                'agent_id' => $agent->id,
                'agent_name' => $agent->name,
                'initial_location' => 'Warehouse (' . $warehouseLatitude . ', ' . $warehouseLongitude . ')'
            ]);

            return [
                'success' => true,
                'message' => "Agent assigned successfully & initialized at warehouse",
                'agent' => $agent,
                'initial_location' => [
                    'latitude' => $warehouseLatitude,
                    'longitude' => $warehouseLongitude,
                    'location' => 'Warehouse'
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Agent assignment failed: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Find nearest available agent
     */
    public function findNearestAgent($latitude, $longitude, $radius = 10)
    {
        try {
            $agents = DeliveryAgent::where('status', 'available')
                ->where('is_active', true)
                ->whereNotNull('current_latitude')
                ->whereNotNull('current_longitude')
                ->get();

            if ($agents->isEmpty()) {
                return null;
            }

            $nearestAgent = null;
            $minDistance = PHP_FLOAT_MAX;

            foreach ($agents as $agent) {
                $distance = $this->googleMaps->calculateDistance(
                    $latitude,
                    $longitude,
                    $agent->current_latitude,
                    $agent->current_longitude
                );

                if ($distance < $minDistance && $distance <= $radius) {
                    $minDistance = $distance;
                    $nearestAgent = $agent;
                }
            }

            if ($nearestAgent) {
                return [
                    'agent' => $nearestAgent,
                    'distance' => $minDistance
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error("❌ Nearest agent search failed: " . $e->getMessage());
            return null;
        }
    }

    /* =========================================================
       4. STATUS UPDATES
    ========================================================= */

    /**
     * Update shipment status with validation
     */
    public function updateStatus(Shipment $shipment, $newStatus, $location = null, $remarks = null)
    {
        $allowedTransitions = [
            'pending' => ['picked', 'cancelled'],
            'picked' => ['in_transit', 'failed'],
            'in_transit' => ['out_for_delivery', 'failed'],
            'out_for_delivery' => ['delivered', 'failed'],
            'delivered' => [],
            'failed' => ['returned'],
            'returned' => [],
            'cancelled' => []
        ];

        $currentStatus = $shipment->status;

        // Validate transition
        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            return [
                'success' => false,
                'message' => "Cannot transition from {$currentStatus} to {$newStatus}"
            ];
        }

        try {
            DB::beginTransaction();

            // Add tracking event
            $this->addTrackingEvent($shipment, $newStatus, $location, $remarks);

            // Special handling for delivered
            if ($newStatus === 'delivered') {
                $shipment->actual_delivery_date = now();

                // Update agent stats if assigned
                if ($shipment->assigned_to) {
                    $agent = DeliveryAgent::where('user_id', $shipment->assigned_to)->first();
                    if ($agent) {
                        $agent->total_deliveries = ($agent->total_deliveries ?? 0) + 1;
                        $agent->successful_deliveries = ($agent->successful_deliveries ?? 0) + 1;
                        $agent->status = 'available';
                        $agent->save();
                    }
                }
            }

            // Special handling for failed/returned - free up agent
            if (in_array($newStatus, ['failed', 'returned', 'cancelled']) && $shipment->assigned_to) {
                $agent = DeliveryAgent::where('user_id', $shipment->assigned_to)->first();
                if ($agent) {
                    $agent->status = 'available';
                    $agent->save();
                }
            }

            $shipment->save();

            DB::commit();

            Log::info("✅ Shipment status updated", [
                'shipment_id' => $shipment->id,
                'old_status' => $currentStatus,
                'new_status' => $newStatus
            ]);

            return [
                'success' => true,
                'message' => "Status updated successfully",
                'shipment' => $shipment->fresh(['trackings'])
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Status update failed: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /* =========================================================
       5. BULK OPERATIONS
    ========================================================= */

    /**
     * Create bulk shipments from multiple sales
     */
    public function createBulkFromSales(array $saleIds, array $options = [])
    {
        $results = [
            'total' => count($saleIds),
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'shipments' => []
        ];

        foreach ($saleIds as $saleId) {
            try {
                $sale = Sale::with(['customer', 'items.product'])->find($saleId);

                if (!$sale) {
                    $results['failed']++;
                    $results['shipments'][] = [
                        'sale_id' => $saleId,
                        'status' => 'failed',
                        'message' => 'Sale not found'
                    ];
                    continue;
                }

                // Skip if already has shipment
                if ($sale->shipments()->exists()) {
                    $results['skipped']++;
                    $results['shipments'][] = [
                        'sale_id' => $saleId,
                        'status' => 'skipped',
                        'message' => 'Shipment already exists'
                    ];
                    continue;
                }

                $shipment = $this->createFromSale($sale, $options);

                if ($shipment) {
                    $results['success']++;
                    $results['shipments'][] = [
                        'sale_id' => $saleId,
                        'shipment_id' => $shipment->id,
                        'tracking' => $shipment->tracking_number,
                        'status' => 'success'
                    ];
                } else {
                    $results['failed']++;
                    $results['shipments'][] = [
                        'sale_id' => $saleId,
                        'status' => 'failed',
                        'message' => 'Shipment creation failed'
                    ];
                }

            } catch (\Exception $e) {
                $results['failed']++;
                $results['shipments'][] = [
                    'sale_id' => $saleId,
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ];
            }
        }

        Log::info("📦 Bulk shipment creation completed", [
            'total' => $results['total'],
            'success' => $results['success'],
            'failed' => $results['failed'],
            'skipped' => $results['skipped']
        ]);

        return $results;
    }

    /* =========================================================
       6. SHIPMENT STATISTICS
    ========================================================= */

    /**
     * Get shipment statistics for dashboard
     */
    public function getStats($fromDate = null, $toDate = null)
    {
        $query = Shipment::query();

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $total = $query->count();
        $delivered = (clone $query)->where('status', 'delivered')->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $inTransit = (clone $query)->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->count();
        $failed = (clone $query)->whereIn('status', ['failed', 'returned'])->count();

        $revenue = (clone $query)->sum('total_charge');
        $codAmount = (clone $query)->where('payment_mode', 'cod')->sum('cod_charge');

        // Daily trend
        $dailyTrend = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Status distribution
        $statusDistribution = (clone $query)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        return [
            'total' => $total,
            'delivered' => $delivered,
            'pending' => $pending,
            'in_transit' => $inTransit,
            'failed' => $failed,
            'delivery_rate' => $total > 0 ? round(($delivered / $total) * 100, 2) : 0,
            'revenue' => $revenue,
            'cod_amount' => $codAmount,
            'daily_trend' => $dailyTrend,
            'status_distribution' => $statusDistribution
        ];
    }

    /* =========================================================
       7. ROUTE OPTIMIZATION
    ========================================================= */

    /**
     * Optimize delivery route for multiple shipments
     */
    public function optimizeRoute(array $shipmentIds, $startLat = null, $startLng = null)
    {
        try {
            $shipments = Shipment::whereIn('id', $shipmentIds)->get();

            if ($shipments->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No shipments found'
                ];
            }

            // Use warehouse coordinates if start not provided
            if (!$startLat || !$startLng) {
                $startLat = config('services.google.default_lat', 22.524768);
                $startLng = config('services.google.default_lng', 72.955568);
            }

            // Build waypoints
            $waypoints = ["{$startLat},{$startLng}"];
            $waypointData = [];

            foreach ($shipments as $shipment) {
                $lat = $shipment->destination_latitude ?? $shipment->current_latitude;
                $lng = $shipment->destination_longitude ?? $shipment->current_longitude;

                if ($lat && $lng) {
                    $waypoints[] = "{$lat},{$lng}";
                    $waypointData[] = [
                        'id' => $shipment->id,
                        'number' => $shipment->shipment_number,
                        'address' => $shipment->full_address,
                        'receiver' => $shipment->receiver_name,
                        'lat' => $lat,
                        'lng' => $lng
                    ];
                }
            }

            if (count($waypoints) < 2) {
                return [
                    'success' => false,
                    'message' => 'Insufficient waypoints for route optimization'
                ];
            }

            // Get optimized route from Google Maps
            $origin = array_shift($waypoints);
            $destination = array_pop($waypoints);
            $intermediate = $waypoints;

            $result = $this->googleMaps->getDirections($origin, $destination, $intermediate);

            if ($result['status'] !== 'OK') {
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Route optimization failed'
                ];
            }

            // Reorder shipments based on optimized waypoints
            $optimizedOrder = [];
            if (!empty($result['waypoint_order'])) {
                foreach ($result['waypoint_order'] as $index) {
                    if (isset($waypointData[$index])) {
                        $optimizedOrder[] = $waypointData[$index];
                    }
                }
            }

            return [
                'success' => true,
                'total_distance' => $result['total_distance'],
                'total_duration' => $result['total_duration'],
                'waypoints' => $waypointData,
                'optimized_order' => $optimizedOrder,
                'legs' => $result['legs'] ?? [],
                'polyline' => $result['polyline'] ?? null
            ];

        } catch (\Exception $e) {
            Log::error("❌ Route optimization failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /* =========================================================
       8. CANCEL SHIPMENT
    ========================================================= */

    /**
     * Cancel shipment
     */
    public function cancelShipment(Shipment $shipment, $reason = null)
    {
        try {
            DB::beginTransaction();

            // Check if cancellation is allowed
            if (in_array($shipment->status, ['delivered', 'cancelled'])) {
                throw new \Exception("Cannot cancel shipment with status: {$shipment->status}");
            }

            // Free up agent if assigned
            if ($shipment->assigned_to) {
                $agent = DeliveryAgent::where('user_id', $shipment->assigned_to)->first();
                if ($agent) {
                    $agent->status = 'available';
                    $agent->save();
                }
            }

            // Update status
            $shipment->status = 'cancelled';
            $shipment->save();

            // Add tracking
            $this->addTrackingEvent(
                $shipment,
                'cancelled',
                $shipment->city,
                $reason ?? 'Shipment cancelled'
            );

            DB::commit();

            Log::info("✅ Shipment cancelled", [
                'shipment_id' => $shipment->id,
                'reason' => $reason
            ]);

            return [
                'success' => true,
                'message' => 'Shipment cancelled successfully'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Shipment cancellation failed: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /* =========================================================
       9. GENERATE POD (Proof of Delivery)
    ========================================================= */

    /**
     * Generate Proof of Delivery data
     */
    public function generatePOD(Shipment $shipment)
    {
        if ($shipment->status !== 'delivered') {
            return [
                'success' => false,
                'message' => 'Shipment not delivered yet'
            ];
        }

        return [
            'success' => true,
            'shipment_number' => $shipment->shipment_number,
            'tracking_number' => $shipment->tracking_number,
            'receiver_name' => $shipment->receiver_name,
            'delivered_at' => $shipment->actual_delivery_date?->format('d M Y, h:i A'),
            'signature' => $shipment->pod_signature ? asset('storage/' . $shipment->pod_signature) : null,
            'photo' => $shipment->pod_photo ? asset('storage/' . $shipment->pod_photo) : null,
            'delivery_notes' => $shipment->delivery_notes,
            'agent_name' => $shipment->deliveryAgent?->name,
            'timeline' => $this->getTrackingTimeline($shipment)
        ];
    }
}
