<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Shipment;
use App\Models\User;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AgentApiController extends Controller
{
    protected $googleMaps;

    public function __construct(GoogleMapsService $googleMaps)
    {
        $this->googleMaps = $googleMaps;
    }

    /**
     * List agents with filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:available,busy,offline',
            'city' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
            'search' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $query = DeliveryAgent::with('user');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('city')) {
                $query->where('city', 'like', '%' . $request->city . '%');
            }

            if ($request->filled('vehicle_type')) {
                $query->where('vehicle_type', $request->vehicle_type);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('agent_code', 'like', "%{$search}%");
                });
            }

            $perPage = $request->get('per_page', 15);
            $agents = $query->orderBy('name')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $agents->items(),
                'pagination' => [
                    'current_page' => $agents->currentPage(),
                    'last_page' => $agents->lastPage(),
                    'per_page' => $agents->perPage(),
                    'total' => $agents->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Agent list error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch agents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single agent details
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
          $agent = DeliveryAgent::with(['user', 'assignedShipments' => function($q) {  // ✅ Correct: array with []
    $q->latest()->limit(10);
}])->find($id);

            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agent not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $agent->id,
                    'agent_code' => $agent->agent_code,
                    'name' => $agent->name,
                    'email' => $agent->email,
                    'phone' => $agent->phone,
                    'alternate_phone' => $agent->alternate_phone,

                    'address' => [
                        'address' => $agent->address,
                        'city' => $agent->city,
                        'state' => $agent->state,
                        'pincode' => $agent->pincode
                    ],

                    'vehicle' => [
                        'type' => $agent->vehicle_type,
                        'number' => $agent->vehicle_number,
                        'license' => $agent->license_number
                    ],

                    'documents' => [
                        'aadhar' => $agent->aadhar_card ? asset('storage/' . $agent->aadhar_card) : null,
                        'driving_license' => $agent->driving_license ? asset('storage/' . $agent->driving_license) : null,
                        'photo' => $agent->photo ? asset('storage/' . $agent->photo) : null
                    ],

                    'bank' => [
                        'bank_name' => $agent->bank_name,
                        'account_number' => $agent->account_number ? 'XXXX' . substr($agent->account_number, -4) : null,
                        'ifsc_code' => $agent->ifsc_code,
                        'upi_id' => $agent->upi_id
                    ],

                    'employment' => [
                        'type' => $agent->employment_type,
                        'joining_date' => $agent->joining_date?->format('Y-m-d'),
                        'salary' => $agent->salary,
                        'commission_type' => $agent->commission_type,
                        'commission_value' => $agent->commission_value
                    ],

                    'service_areas' => $agent->service_areas ? json_decode($agent->service_areas) : [],

                    'current_location' => [
                        'lat' => $agent->current_latitude,
                        'lng' => $agent->current_longitude,
                        'last_update' => $agent->last_location_update?->diffForHumans(),
                        'last_update_raw' => $agent->last_location_update
                    ],

                    'status' => $agent->status,
                    'is_active' => $agent->is_active,

                    'performance' => [
                        'total_deliveries' => $agent->total_deliveries,
                        'successful_deliveries' => $agent->successful_deliveries,
                        'rating' => $agent->rating,
                        'success_rate' => $agent->total_deliveries > 0
                            ? round(($agent->successful_deliveries / $agent->total_deliveries) * 100, 2)
                            : 0
                    ],

                    'recent_shipments' => $agent->assignedShipments->map(function($shipment) {
                        return [
                            'id' => $shipment->id,
                            'shipment_number' => $shipment->shipment_number,
                            'receiver_name' => $shipment->receiver_name,
                            'city' => $shipment->city,
                            'status' => $shipment->status,
                            'created_at' => $shipment->created_at->format('Y-m-d H:i:s')
                        ];
                    }),

                    'created_at' => $agent->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $agent->updated_at->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Agent show error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch agent details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new agent
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:delivery_agents,phone',
            'email' => 'nullable|email|max:255|unique:delivery_agents,email',
            'city' => 'nullable|string|max:100',
            'vehicle_type' => 'nullable|in:bike,cycle,van,truck',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'joining_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Generate agent code
            $agentCode = $this->generateAgentCode();

            $agent = new DeliveryAgent();
            $agent->agent_code = $agentCode;
            $agent->name = $request->name;
            $agent->phone = $request->phone;
            $agent->email = $request->email;
            $agent->city = $request->city;
            $agent->vehicle_type = $request->vehicle_type;
            $agent->employment_type = $request->employment_type;
            $agent->joining_date = Carbon::parse($request->joining_date);
            $agent->status = 'available';
            $agent->is_active = true;
            $agent->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agent created successfully',
                'data' => [
                    'id' => $agent->id,
                    'agent_code' => $agent->agent_code,
                    'name' => $agent->name
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create agent',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update agent
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $agent = DeliveryAgent::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:delivery_agents,phone,' . $id,
            'email' => 'nullable|email|max:255|unique:delivery_agents,email,' . $id,
            'city' => 'nullable|string|max:100',
            'vehicle_type' => 'nullable|in:bike,cycle,van,truck',
            'status' => 'nullable|in:available,busy,offline',
            'is_active' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $agent->fill($request->only([
                'name', 'phone', 'email', 'city', 'vehicle_type',
                'vehicle_number', 'license_number', 'address', 'state', 'pincode'
            ]));

            if ($request->has('status')) {
                $agent->status = $request->status;
            }

            if ($request->has('is_active')) {
                $agent->is_active = $request->boolean('is_active');
            }

            $agent->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agent updated successfully',
                'data' => $agent
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update agent',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete agent
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $agent = DeliveryAgent::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        // Check if agent has active shipments
        if ($agent->assignedShipments()->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete agent with active shipments'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Delete associated user if exists
            if ($agent->user_id) {
                User::where('id', $agent->user_id)->delete();
            }

            $agent->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agent deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete agent',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update agent status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,busy,offline'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $agent = DeliveryAgent::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        try {
            $agent->status = $request->status;
            $agent->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => [
                    'id' => $agent->id,
                    'status' => $agent->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Agent status update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update agent location
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLocation(Request $request, $id)
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

        $agent = DeliveryAgent::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        try {
            $agent->current_latitude = $request->latitude;
            $agent->current_longitude = $request->longitude;
            $agent->location_accuracy = $request->accuracy;
            $agent->last_location_update = now();
            $agent->save();

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Agent location update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get agent location
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocation($id)
    {
        $agent = DeliveryAgent::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'latitude' => $agent->current_latitude,
                'longitude' => $agent->current_longitude,
                'accuracy' => $agent->location_accuracy,
                'last_update' => $agent->last_location_update?->diffForHumans(),
                'last_update_raw' => $agent->last_location_update
            ]
        ]);
    }

    /**
     * Get agent performance stats
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function performance(Request $request, $id)
    {
        $agent = DeliveryAgent::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        try {
            $fromDate = $request->get('from_date', Carbon::now()->startOfMonth());
            $toDate = $request->get('to_date', Carbon::now()->endOfMonth());

            $shipments = $agent->assignedShipments()
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->get();

            $stats = [
                'total' => $shipments->count(),
                'delivered' => $shipments->where('status', 'delivered')->count(),
                'failed' => $shipments->whereIn('status', ['failed', 'returned'])->count(),
                'pending' => $shipments->where('status', 'pending')->count(),
                'in_transit' => $shipments->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->count(),
                'success_rate' => $shipments->count() > 0
                    ? round(($shipments->where('status', 'delivered')->count() / $shipments->count()) * 100, 2)
                    : 0,
                'total_distance' => $shipments->sum('distance_travelled'),
                'avg_delivery_time' => $shipments->where('status', 'delivered')->avg(function($s) {
                    return $s->created_at->diffInHours($s->actual_delivery_date);
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Agent performance error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch performance stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assigned shipments for agent
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignedShipments($id)
    {
        $agent = DeliveryAgent::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        try {
            $shipments = $agent->assignedShipments()
                ->with('customer')
                ->latest()
                ->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $shipments->items(),
                'pagination' => [
                    'current_page' => $shipments->currentPage(),
                    'last_page' => $shipments->lastPage(),
                    'per_page' => $shipments->perPage(),
                    'total' => $shipments->total()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Agent shipments error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch shipments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload agent documents
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDocuments(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'documents' => 'required|array',
            'documents.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'type' => 'required|in:aadhar,license,photo,other'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $agent = DeliveryAgent::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        try {
            $uploadedFiles = [];

            foreach ($request->file('documents') as $file) {
                $path = $file->store('agents/' . $request->type . '/' . $id, 'public');

                switch ($request->type) {
                    case 'aadhar':
                        $agent->aadhar_card = $path;
                        break;
                    case 'license':
                        $agent->driving_license = $path;
                        break;
                    case 'photo':
                        $agent->photo = $path;
                        break;
                }

                $uploadedFiles[] = [
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'url' => asset('storage/' . $path)
                ];
            }

            $agent->save();

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' document(s) uploaded successfully',
                'data' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            Log::error('Document upload error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all agents for map
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllForMap()
    {
        try {
            $agents = DeliveryAgent::where('is_active', true)
                ->whereNotNull('current_latitude')
                ->whereNotNull('current_longitude')
                ->get(['id', 'name', 'current_latitude', 'current_longitude', 'status', 'phone', 'vehicle_type']);

            return response()->json([
                'success' => true,
                'data' => $agents->map(function($agent) {
                    return [
                        'id' => $agent->id,
                        'name' => $agent->name,
                        'latitude' => $agent->current_latitude,
                        'longitude' => $agent->current_longitude,
                        'status' => $agent->status,
                        'phone' => $agent->phone,
                        'vehicle' => $agent->vehicle_type,
                        'icon' => $this->getAgentIcon($agent->status)
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Agents for map error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch agents for map',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Find nearby available agents
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findNearby(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $radius = $request->get('radius', 10); // km

            $agents = DeliveryAgent::where('status', 'available')
                ->where('is_active', true)
                ->whereNotNull('current_latitude')
                ->whereNotNull('current_longitude')
                ->get();

            $nearbyAgents = [];

            foreach ($agents as $agent) {
                $distance = $this->googleMaps->calculateDistance(
                    $request->latitude,
                    $request->longitude,
                    $agent->current_latitude,
                    $agent->current_longitude
                );

                if ($distance <= $radius) {
                    $nearbyAgents[] = [
                        'id' => $agent->id,
                        'name' => $agent->name,
                        'phone' => $agent->phone,
                        'vehicle_type' => $agent->vehicle_type,
                        'distance' => round($distance, 2),
                        'latitude' => $agent->current_latitude,
                        'longitude' => $agent->current_longitude,
                        'rating' => $agent->rating,
                        'total_deliveries' => $agent->successful_deliveries
                    ];
                }
            }

            // Sort by distance
            usort($nearbyAgents, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });

            return response()->json([
                'success' => true,
                'data' => $nearbyAgents,
                'count' => count($nearbyAgents)
            ]);

        } catch (\Exception $e) {
            Log::error('Nearby agents error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to find nearby agents',
                'error' => $e->getMessage()
            ], 500);
        }
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
            'agent_ids' => 'required|array|min:1',
            'agent_ids.*' => 'exists:delivery_agents,id',
            'status' => 'required|in:available,busy,offline'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $updated = DeliveryAgent::whereIn('id', $request->agent_ids)
                ->update(['status' => $request->status]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$updated} agent(s) status updated successfully"
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
     * Bulk delete agents
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_ids' => 'required|array|min:1',
            'agent_ids.*' => 'exists:delivery_agents,id'
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
                'total' => count($request->agent_ids),
                'success' => 0,
                'failed' => 0,
                'failed_ids' => []
            ];

            foreach ($request->agent_ids as $id) {
                $agent = DeliveryAgent::find($id);

                if ($agent && !$agent->assignedShipments()->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->exists()) {
                    if ($agent->user_id) {
                        User::where('id', $agent->user_id)->delete();
                    }
                    $agent->delete();
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
                'message' => 'Failed to bulk delete agents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Public info (for tracking page)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicInfo($id)
    {
        $agent = DeliveryAgent::find($id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $agent->name,
                'phone' => $agent->phone,
                'vehicle_type' => $agent->vehicle_type,
                'rating' => $agent->rating,
                'total_deliveries' => $agent->successful_deliveries,
                'current_location' => [
                    'lat' => $agent->current_latitude,
                    'lng' => $agent->current_longitude,
                    'last_update' => $agent->last_location_update?->diffForHumans()
                ]
            ]
        ]);
    }

    /**
     * App login for delivery boys
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $agent = DeliveryAgent::where('phone', $request->phone)->first();

            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Agent not found'
                ], 404);
            }

            // If agent has user account, verify password
            if ($agent->user_id) {
                $user = User::find($agent->user_id);

                if (!$user || !Hash::check($request->password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid credentials'
                    ], 401);
                }

                $token = $user->createToken('delivery-app')->plainTextToken;
            } else {
                // Simple token for agents without user account
                $token = base64_encode($agent->id . '|' . time());
            }

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'agent' => [
                        'id' => $agent->id,
                        'name' => $agent->name,
                        'phone' => $agent->phone,
                        'status' => $agent->status
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('App login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assigned shipments for app
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appShipments(Request $request)
    {
        $agent = $this->getAgentFromRequest($request);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $shipments = $agent->assignedShipments()
                ->with('customer')
                ->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])
                ->orderBy('delivery_order')
                ->orderBy('estimated_delivery_date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $shipments->map(function($shipment) {
                    return [
                        'id' => $shipment->id,
                        'shipment_number' => $shipment->shipment_number,
                        'tracking_number' => $shipment->tracking_number,
                        'receiver_name' => $shipment->receiver_name,
                        'receiver_phone' => $shipment->receiver_phone, // ✅ FIXED: $hipment to $shipment
                        'address' => $shipment->full_address,
                        'city' => $shipment->city,
                        'status' => $shipment->status,
                        'delivery_order' => $shipment->delivery_order,
                        'estimated_delivery' => $shipment->estimated_delivery_date?->format('Y-m-d'),
                        'destination' => [
                            'lat' => $shipment->destination_latitude,
                            'lng' => $shipment->destination_longitude
                        ]
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('App shipments error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch shipments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update shipment status from app
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function appUpdateStatus(Request $request, $id)
    {
        $agent = $this->getAgentFromRequest($request);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:picked,in_transit,out_for_delivery,delivered,failed',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $shipment = Shipment::where('id', $id)
            ->where('assigned_to', $agent->user_id)
            ->first();

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found or not assigned to you'
            ], 404);
        }

        try {
            $shipment->updateTracking(
                $request->status,
                $shipment->city,
                $request->remarks,
                $request->latitude,
                $request->longitude
            );

            // Update current location if provided
            if ($request->latitude && $request->longitude) {
                $shipment->current_latitude = $request->latitude;
                $shipment->current_longitude = $request->longitude;
                $shipment->last_location_update = now();
                $shipment->save();

                // Also update agent location
                $agent->current_latitude = $request->latitude;
                $agent->current_longitude = $request->longitude;
                $agent->last_location_update = now();
                $agent->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('App status update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update location from app
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appUpdateLocation(Request $request)
    {
        $agent = $this->getAgentFromRequest($request);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

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

        try {
            $agent->current_latitude = $request->latitude;
            $agent->current_longitude = $request->longitude;
            $agent->location_accuracy = $request->accuracy;
            $agent->last_location_update = now();
            $agent->save();

            // Update assigned shipments' locations
            Shipment::where('assigned_to', $agent->user_id)
                ->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])
                ->update([
                    'current_latitude' => $request->latitude,
                    'current_longitude' => $request->longitude,
                    'last_location_update' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('App location update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get next delivery for app
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appNextDelivery(Request $request)
    {
        $agent = $this->getAgentFromRequest($request);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $nextShipment = $agent->assignedShipments()
                ->with('customer')
                ->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])
                ->orderBy('delivery_order')
                ->orderBy('estimated_delivery_date')
                ->first();

            if (!$nextShipment) {
                return response()->json([
                    'success' => true,
                    'message' => 'No pending deliveries',
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $nextShipment->id,
                    'shipment_number' => $nextShipment->shipment_number,
                    'receiver_name' => $nextShipment->receiver_name,
                    'receiver_phone' => $nextShipment->receiver_phone,
                    'address' => $nextShipment->full_address,
                    'city' => $nextShipment->city,
                    'destination' => [
                        'lat' => $nextShipment->destination_latitude,
                        'lng' => $nextShipment->destination_longitude
                    ],
                    'estimated_delivery' => $nextShipment->estimated_delivery_date?->format('Y-m-d H:i:s'),
                    'order' => $nextShipment->delivery_order
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('App next delivery error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get next delivery',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark as delivered from app
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function appDeliver(Request $request, $id)
    {
        $agent = $this->getAgentFromRequest($request);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'signature' => 'nullable|image|mimes:jpeg,png|max:2048',
            'photo' => 'nullable|image|mimes:jpeg,png|max:5120',
            'delivery_notes' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $shipment = Shipment::where('id', $id)
            ->where('assigned_to', $agent->user_id)
            ->first();

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found or not assigned to you'
            ], 404);
        }

        DB::beginTransaction();
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
            $shipment->status = 'delivered';
            $shipment->actual_delivery_date = now();

            if ($request->latitude && $request->longitude) {
                $shipment->current_latitude = $request->latitude;
                $shipment->current_longitude = $request->longitude;
                $shipment->last_location_update = now();
            }

            $shipment->save();

            // Add tracking
            $shipment->updateTracking('delivered', $shipment->city, 'Delivered by agent');

            // Update agent stats
            $agent->total_deliveries = ($agent->total_deliveries ?? 0) + 1;
            $agent->successful_deliveries = ($agent->successful_deliveries ?? 0) + 1;
            $agent->status = 'available';
            $agent->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shipment delivered successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('App deliver error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark as delivered',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get route for today (app)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appRoute(Request $request)
    {
        $agent = $this->getAgentFromRequest($request);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $shipments = $agent->assignedShipments()
                ->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])
                ->orderBy('delivery_order')
                ->get();

            $waypoints = [];

            if ($shipments->isNotEmpty()) {
                // Start from current location
                if ($agent->current_latitude && $agent->current_longitude) {
                    $waypoints[] = [
                        'lat' => $agent->current_latitude,
                        'lng' => $agent->current_longitude,
                        'type' => 'current'
                    ];
                }

                // Add shipments
                foreach ($shipments as $shipment) {
                    if ($shipment->destination_latitude && $shipment->destination_longitude) {
                        $waypoints[] = [
                            'id' => $shipment->id,
                            'number' => $shipment->shipment_number,
                            'lat' => $shipment->destination_latitude,
                            'lng' => $shipment->destination_longitude,
                            'address' => $shipment->full_address,
                            'receiver' => $shipment->receiver_name,
                            'type' => 'delivery',
                            'order' => $shipment->delivery_order
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'waypoints' => $waypoints,
                    'total_stops' => $shipments->count(),
                    'current_status' => $agent->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('App route error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get route',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get agent profile (app)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appProfile(Request $request)
    {
        $agent = $this->getAgentFromRequest($request);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $agent->id,
                'name' => $agent->name,
                'phone' => $agent->phone,
                'email' => $agent->email,
                'vehicle_type' => $agent->vehicle_type,
                'vehicle_number' => $agent->vehicle_number,
                'city' => $agent->city,
                'photo' => $agent->photo ? asset('storage/' . $agent->photo) : null,
                'status' => $agent->status,
                'total_deliveries' => $agent->total_deliveries,
                'successful_deliveries' => $agent->successful_deliveries,
                'rating' => $agent->rating
            ]
        ]);
    }

    /**
     * Update agent profile (app)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appUpdateProfile(Request $request)
    {
        $agent = $this->getAgentFromRequest($request);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'vehicle_number' => 'nullable|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->has('name')) {
                $agent->name = $request->name;
            }

            if ($request->has('email')) {
                $agent->email = $request->email;
            }

            if ($request->has('vehicle_number')) {
                $agent->vehicle_number = $request->vehicle_number;
            }

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('agents/photos', 'public');
                $agent->photo = $path;
            }

            $agent->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('App profile update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance chart data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function performanceChart(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $startDate = Carbon::now()->subDays($days - 1);

            $data = DeliveryAgent::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(successful_deliveries) as deliveries'),
                    DB::raw('AVG(rating) as avg_rating')
                )
                ->whereDate('created_at', '>=', $startDate)
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Performance chart error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get performance chart data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate unique agent code
     */
    private function generateAgentCode()
    {
        $prefix = 'AG';
        $year = date('y');
        $month = date('m');

        $lastAgent = DeliveryAgent::whereYear('created_at', date('Y'))
            ->orderBy('id', 'desc')
            ->first();

        if ($lastAgent) {
            $lastCode = substr($lastAgent->agent_code, -4);
            $sequence = intval($lastCode) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get agent icon based on status
     */
    private function getAgentIcon($status)
    {
        $icons = [
            'available' => 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
            'busy' => 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
            'offline' => 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
        ];

        return $icons[$status] ?? 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png';
    }

    /**
     * Get agent from request (for app)
     */
    private function getAgentFromRequest($request)
    {
        // For token-based auth (sanctum)
        if ($request->user() && $request->user()->role === 'delivery_agent') {
            return DeliveryAgent::where('user_id', $request->user()->id)->first();
        }

        // For simple token
        $token = $request->bearerToken();
        if ($token) {
            $data = explode('|', base64_decode($token));
            if (count($data) >= 2) {
                return DeliveryAgent::find($data[0]);
            }
        }

        return null;
    }
}
