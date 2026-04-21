<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\DeliveryAgent;
use App\Services\ShipmentService;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ShipmentApiController extends Controller
{
    protected $shipmentService;
    protected $googleMaps;

    public function __construct(ShipmentService $shipmentService, GoogleMapsService $googleMaps)
    {
        $this->shipmentService = $shipmentService;
        $this->googleMaps = $googleMaps;
    }

    /**
     * List shipments with filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|string',
            'agent_id' => 'nullable|integer',
            'city' => 'nullable|string',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'search' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|in:created_at,shipment_number,estimated_delivery_date',
            'sort_order' => 'nullable|in:asc,desc'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = Shipment::with(['customer', 'deliveryAgent', 'trackings' => function($q) {
                $q->latest()->limit(1);
            }]);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('agent_id')) {
                $query->where('assigned_to', $request->agent_id);
            }

            if ($request->filled('city')) {
                $query->where('city', 'like', '%' . $request->city . '%');
            }

            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('shipment_number', 'like', "%{$search}%")
                      ->orWhere('tracking_number', 'like', "%{$search}%")
                      ->orWhere('receiver_name', 'like', "%{$search}%")
                      ->orWhere('receiver_phone', 'like', "%{$search}%");
                });
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $perPage = $request->get('per_page', 15);
            $shipments = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $shipments->items(),
                'pagination' => [
                    'current_page' => $shipments->currentPage(),
                    'last_page' => $shipments->lastPage(),
                    'per_page' => $shipments->perPage(),
                    'total' => $shipments->total(),
                    'next_page_url' => $shipments->nextPageUrl(),
                    'prev_page_url' => $shipments->previousPageUrl()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Shipment list error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch shipments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single shipment details
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $shipment = Shipment::with([
                'customer',
                'deliveryAgent',
                'sale',
                'trackings' => function($q) {
                    $q->orderBy('tracked_at', 'desc');
                }
            ])->find($id);

            if (!$shipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found'
                ], 404);
            }

            // Calculate progress
            $progress = $this->calculateProgress($shipment);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $shipment->id,
                    'shipment_number' => $shipment->shipment_number,
                    'tracking_number' => $shipment->tracking_number,
                    'status' => $shipment->status,
                    'status_display' => ucfirst(str_replace('_', ' ', $shipment->status)),
                    'status_badge' => $shipment->status_badge,

                    'receiver' => [
                        'name' => $shipment->receiver_name,
                        'phone' => $shipment->receiver_phone,
                        'alternate_phone' => $shipment->receiver_alternate_phone
                    ],

                    'address' => [
                        'full' => $shipment->full_address,
                        'address' => $shipment->shipping_address,
                        'landmark' => $shipment->landmark,
                        'city' => $shipment->city,
                        'state' => $shipment->state,
                        'pincode' => $shipment->pincode,
                        'country' => $shipment->country
                    ],

                    'package' => [
                        'weight' => $shipment->weight,
                        'dimensions' => [
                            'length' => $shipment->length,
                            'width' => $shipment->width,
                            'height' => $shipment->height
                        ],
                        'quantity' => $shipment->quantity,
                        'package_type' => $shipment->package_type,
                        'declared_value' => $shipment->declared_value
                    ],

                    'shipping' => [
                        'method' => $shipment->shipping_method,
                        'courier_partner' => $shipment->courier_partner,
                        'payment_mode' => $shipment->payment_mode
                    ],

                    'charges' => [
                        'shipping_charge' => $shipment->shipping_charge,
                        'cod_charge' => $shipment->cod_charge,
                        'insurance_charge' => $shipment->insurance_charge,
                        'total_charge' => $shipment->total_charge
                    ],

                    'dates' => [
                        'pickup' => $shipment->pickup_date?->format('Y-m-d'),
                        'estimated_delivery' => $shipment->estimated_delivery_date?->format('Y-m-d'),
                        'actual_delivery' => $shipment->actual_delivery_date?->format('Y-m-d H:i:s'),
                        'created_at' => $shipment->created_at->format('Y-m-d H:i:s'),
                        'last_location_update' => $shipment->last_location_update?->format('Y-m-d H:i:s')
                    ],

                    'current_location' => [
                        'lat' => $shipment->current_latitude,
                        'lng' => $shipment->current_longitude,
                        'accuracy' => $shipment->location_accuracy,
                        'last_updated' => $shipment->last_location_update?->diffForHumans()
                    ],

                    'agent' => $shipment->deliveryAgent ? [
                        'id' => $shipment->deliveryAgent->id,
                        'name' => $shipment->deliveryAgent->name,
                        'phone' => $shipment->deliveryAgent->phone,
                        'vehicle_type' => $shipment->deliveryAgent->vehicle_type
                    ] : null,

                    'customer' => $shipment->customer ? [
                        'id' => $shipment->customer->id,
                        'name' => $shipment->customer->name,
                        'mobile' => $shipment->customer->mobile
                    ] : null,

                    'sale' => $shipment->sale ? [
                        'id' => $shipment->sale->id,
                        'invoice_no' => $shipment->sale->invoice_no
                    ] : null,

                    'progress_percentage' => $progress,

                    'tracking_history' => $shipment->trackings->map(function($track) {
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
                            'longitude' => $track->longitude
                        ];
                    }),

                    'pod' => [
                        'signature' => $shipment->pod_signature ? asset('storage/' . $shipment->pod_signature) : null,
                        'photo' => $shipment->pod_photo ? asset('storage/' . $shipment->pod_photo) : null,
                        'delivery_notes' => $shipment->delivery_notes
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Shipment show error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch shipment details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new shipment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'nullable|exists:sales,id',
            'customer_id' => 'required|exists:customers,id',
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'declared_value' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'weight' => 'nullable|numeric|min:0',
            'shipping_method' => 'required|in:standard,express,overnight',
            'payment_mode' => 'required|in:prepaid,cod',
            'estimated_delivery_date' => 'nullable|date|after:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Calculate charges
            $shippingCharge = $this->calculateShippingCharge($request);
            $codCharge = $request->payment_mode === 'cod' ? $this->calculateCODCharge($request) : 0;
            $insuranceCharge = $this->calculateInsuranceCharge($request);
            $totalCharge = $shippingCharge + $codCharge + $insuranceCharge;

            $shipment = new Shipment();
            $shipment->shipment_number = (new Shipment())->generateShipmentNumber();
            $shipment->tracking_number = 'SHIP' . date('Ymd') . str_pad(Shipment::max('id') + 1, 6, '0', STR_PAD_LEFT);
            $shipment->fill($request->all());
            $shipment->shipping_charge = $shippingCharge;
            $shipment->cod_charge = $codCharge;
            $shipment->insurance_charge = $insuranceCharge;
            $shipment->total_charge = $totalCharge;
            $shipment->status = 'pending';
            $shipment->created_by = auth()->id() ?? 1;
            $shipment->save();

            // Add initial tracking
            $shipment->updateTracking('pending', $shipment->city, 'Shipment created');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shipment created successfully',
                'data' => [
                    'id' => $shipment->id,
                    'shipment_number' => $shipment->shipment_number,
                    'tracking_number' => $shipment->tracking_number
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update shipment
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        // Cannot update if in transit or delivered
        if (in_array($shipment->status, ['picked', 'in_transit', 'out_for_delivery', 'delivered'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update shipment in current status'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'declared_value' => 'required|numeric|min:0',
            'estimated_delivery_date' => 'nullable|date|after:today'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $shipment->update($request->all());

            // Add tracking for update
            $shipment->updateTracking(
                $shipment->status,
                $shipment->city,
                'Shipment details updated'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shipment updated successfully',
                'data' => $shipment
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update shipment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete shipment
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        // Cannot delete if in transit or delivered
        if (in_array($shipment->status, ['picked', 'in_transit', 'out_for_delivery', 'delivered'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete shipment in current status'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Delete tracking history
            $shipment->trackings()->delete();

            // Delete shipment
            $shipment->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shipment deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete shipment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update shipment status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,picked,in_transit,out_for_delivery,delivered,failed,returned,cancelled',
            'location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        $result = $this->shipmentService->updateStatus(
            $shipment,
            $request->status,
            $request->location,
            $request->remarks
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['shipment']
        ]);
    }

    /**
     * Assign delivery agent
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignAgent(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'required|exists:delivery_agents,user_id',
            'auto_assign' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        // If auto-assign is true, find nearest agent
        if ($request->boolean('auto_assign') && $shipment->destination_latitude) {
            $nearest = $this->shipmentService->findNearestAgent(
                $shipment->destination_latitude,
                $shipment->destination_longitude
            );

            if ($nearest) {
                $agentId = $nearest['agent']->user_id;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No available agent found nearby'
                ], 404);
            }
        } else {
            $agentId = $request->agent_id;
        }

        $result = $this->shipmentService->assignAgent($shipment, $agentId);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'shipment_id' => $shipment->id,
                'agent' => $result['agent']
            ]
        ]);
    }

    /**
     * Update live location (delivery boy API)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLiveLocation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        try {
            $shipment->current_latitude = $request->latitude;
            $shipment->current_longitude = $request->longitude;
            $shipment->location_accuracy = $request->accuracy;
            $shipment->last_location_update = now();
            $shipment->save();

            // Also add to tracking if needed
            if ($request->boolean('add_to_tracking')) {
                $shipment->updateTracking(
                    $shipment->status,
                    null,
                    'Location updated',
                    $request->latitude,
                    $request->longitude
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Live location update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload proof of delivery
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPOD(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'delivery_notes' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        if ($shipment->status !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Shipment must be delivered before uploading POD'
            ], 400);
        }

        try {
            // Handle signature upload
            if ($request->hasFile('signature')) {
                $path = $request->file('signature')->store('pod/signatures', 'public');
                $shipment->pod_signature = $path;
            }

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('pod/photos', 'public');
                $shipment->pod_photo = $path;
            }

            $shipment->delivery_notes = $request->delivery_notes;
            $shipment->save();

            // Add tracking event
            $shipment->updateTracking('pod_uploaded', null, 'Proof of delivery uploaded');

            return response()->json([
                'success' => true,
                'message' => 'Proof of delivery uploaded successfully',
                'data' => [
                    'signature_url' => $shipment->pod_signature ? asset('storage/' . $shipment->pod_signature) : null,
                    'photo_url' => $shipment->pod_photo ? asset('storage/' . $shipment->pod_photo) : null,
                    'notes' => $shipment->delivery_notes
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('POD upload error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload POD',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get proof of delivery
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPOD($id)
    {
        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        if ($shipment->status !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not delivered yet'
            ], 400);
        }

        $pod = $this->shipmentService->generatePOD($shipment);

        return response()->json([
            'success' => true,
            'data' => $pod
        ]);
    }

    /**
     * Cancel shipment
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        $result = $this->shipmentService->cancelShipment($shipment, $request->reason);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message']
        ]);
    }

    /**
     * Get tracking history
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackingHistory($id)
    {
        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        $timeline = $this->shipmentService->getTrackingTimeline($shipment);

        return response()->json([
            'success' => true,
            'data' => $timeline
        ]);
    }

    /**
     * Bulk status update
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkStatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipment_ids' => 'required|array|min:1',
            'shipment_ids.*' => 'exists:shipments,id',
            'status' => 'required|in:pending,picked,in_transit,out_for_delivery,delivered,failed,returned,cancelled',
            'remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $results = [
                'total' => count($request->shipment_ids),
                'success' => 0,
                'failed' => 0,
                'failed_ids' => []
            ];

            foreach ($request->shipment_ids as $id) {
                $shipment = Shipment::find($id);

                if ($shipment) {
                    $result = $this->shipmentService->updateStatus(
                        $shipment,
                        $request->status,
                        null,
                        $request->remarks
                    );

                    if ($result['success']) {
                        $results['success']++;
                    } else {
                        $results['failed']++;
                        $results['failed_ids'][] = $id;
                    }
                } else {
                    $results['failed']++;
                    $results['failed_ids'][] = $id;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Bulk status update completed: {$results['success']} successful, {$results['failed']} failed",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk status update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update bulk status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk assign agents
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipment_ids' => 'required|array|min:1',
            'shipment_ids.*' => 'exists:shipments,id',
            'agent_id' => 'required|exists:delivery_agents,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $results = [
                'total' => count($request->shipment_ids),
                'success' => 0,
                'failed' => 0,
                'failed_ids' => []
            ];

            foreach ($request->shipment_ids as $id) {
                $shipment = Shipment::find($id);

                if ($shipment && $shipment->status === 'pending') {
                    $result = $this->shipmentService->assignAgent($shipment, $request->agent_id);

                    if ($result['success']) {
                        $results['success']++;
                    } else {
                        $results['failed']++;
                        $results['failed_ids'][] = $id;
                    }
                } else {
                    $results['failed']++;
                    $results['failed_ids'][] = $id;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Bulk assignment completed: {$results['success']} successful, {$results['failed']} failed",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk assign error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk assign agents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete shipments
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipment_ids' => 'required|array|min:1',
            'shipment_ids.*' => 'exists:shipments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $results = [
                'total' => count($request->shipment_ids),
                'success' => 0,
                'failed' => 0,
                'failed_ids' => []
            ];

            foreach ($request->shipment_ids as $id) {
                $shipment = Shipment::find($id);

                if ($shipment && !in_array($shipment->status, ['picked', 'in_transit', 'out_for_delivery', 'delivered'])) {
                    $shipment->trackings()->delete();
                    $shipment->delete();
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['failed_ids'][] = $id;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Bulk delete completed: {$results['success']} successful, {$results['failed']} failed",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk delete shipments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardStats()
    {
        try {
            $stats = $this->shipmentService->getStats();

            // Add today's stats
            $stats['today'] = [
                'total' => Shipment::whereDate('created_at', today())->count(),
                'delivered' => Shipment::whereDate('actual_delivery_date', today())->count(),
                'pending' => Shipment::whereDate('created_at', today())->where('status', 'pending')->count()
            ];

            // Add this week stats
            $stats['this_week'] = Shipment::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get shipment trends for charts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function trends(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'nullable|integer|min:1|max:90'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $days = $request->get('days', 30);
            $startDate = Carbon::now()->subDays($days - 1);

            $trends = Shipment::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered'),
                    DB::raw('SUM(total_charge) as revenue')
                )
                ->whereDate('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $trends
            ]);

        } catch (\Exception $e) {
            Log::error('Trends error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch trends',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get revenue statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function revenueStats()
    {
        try {
            $revenue = [
                'today' => Shipment::whereDate('created_at', today())->sum('total_charge'),
                'this_week' => Shipment::whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])->sum('total_charge'),
                'this_month' => Shipment::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('total_charge'),
                'total' => Shipment::sum('total_charge'),
                'cod_total' => Shipment::where('payment_mode', 'cod')->sum('cod_charge'),
                'shipping_total' => Shipment::sum('shipping_charge')
            ];

            return response()->json([
                'success' => true,
                'data' => $revenue
            ]);

        } catch (\Exception $e) {
            Log::error('Revenue stats error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch revenue stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top cities by shipment volume
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topCities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $limit = $request->get('limit', 10);

            $cities = Shipment::select('city', DB::raw('COUNT(*) as total'))
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderBy('total', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cities
            ]);

        } catch (\Exception $e) {
            Log::error('Top cities error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch top cities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate shipping charge
     */
    private function calculateShippingCharge($request)
    {
        $baseRate = 50;
        $weight = $request->weight ?? 1;

        if ($request->shipping_method === 'express') {
            $rate = $baseRate * 1.5;
        } elseif ($request->shipping_method === 'overnight') {
            $rate = $baseRate * 2;
        } else {
            $rate = $baseRate;
        }

        return $rate * $weight;
    }

    /**
     * Calculate COD charge
     */
    private function calculateCODCharge($request)
    {
        $amount = $request->declared_value;
        if ($amount <= 5000) {
            return 30;
        } elseif ($amount <= 10000) {
            return 50;
        } elseif ($amount <= 25000) {
            return 100;
        } else {
            return $amount * 0.005;
        }
    }

    /**
     * Calculate insurance charge
     */
    private function calculateInsuranceCharge($request)
    {
        $amount = $request->declared_value;
        if ($amount > 10000) {
            return $amount * 0.001;
        }
        return 0;
    }

    /**
     * Calculate delivery progress percentage
     */
    private function calculateProgress($shipment)
    {
        if ($shipment->status === 'delivered') {
            return 100;
        }

        $statusOrder = [
            'pending' => 0,
            'picked' => 20,
            'in_transit' => 40,
            'out_for_delivery' => 70
        ];

        return $statusOrder[$shipment->status] ?? 0;
    }
}
