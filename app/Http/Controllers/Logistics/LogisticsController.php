<?php
// app/Http/Controllers/Logistics/LogisticsController.php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Models\DeliveryAgent;
use App\Models\AgentLocation;
use App\Models\CourierPartner;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class LogisticsController extends Controller
{
    /**
     * Display shipments listing
     */
    public function index(Request $request)
    {
        $query = Shipment::with(['customer', 'deliveryAgent', 'sale']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('courier_partner')) {
            $query->where('courier_partner', $request->courier_partner);
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
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

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $shipments = $query->paginate(15)->withQueryString();

        // Stats for dashboard
        $stats = [
            'total' => Shipment::count(),
            'pending' => Shipment::where('status', 'pending')->count(),
            'in_transit' => Shipment::whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->count(),
            'delivered' => Shipment::where('status', 'delivered')->count(),
            'delivered_today' => Shipment::whereDate('actual_delivery_date', today())->count(),
        ];

        return view('logistics.shipments.index', compact('shipments', 'stats'));
    }

    /**
     * Live tracking page (Rapido/Google Maps style)
     */
    public function liveTrack($trackingNumber)
    {
        $shipment = Shipment::with(['trackings', 'deliveryAgent'])
            ->where('tracking_number', $trackingNumber)
            ->orWhere('shipment_number', $trackingNumber)
            ->firstOrFail();

        // Get assigned agent
        $agent = null;
        if ($shipment->assigned_to) {
            $agent = DeliveryAgent::where('user_id', $shipment->assigned_to)->first();

            // Also get user details
            if ($agent) {
                $user = User::find($agent->user_id);
                if ($user) {
                    $agent->name = $user->name;
                    $agent->email = $user->email;
                    $agent->mobile = $user->mobile;
                }
            }
        }

        // Calculate progress
        $progress = 0;
        $distanceLeft = 0;
        $estimatedTime = 0;

        if ($agent && $agent->current_latitude && $agent->current_longitude && $shipment->destination_latitude) {
            $totalDist = $this->calculateDistance(
                env('WAREHOUSE_LAT', 22.524768),
                env('WAREHOUSE_LNG', 72.955568),
                $shipment->destination_latitude,
                $shipment->destination_longitude
            );

            $distanceLeft = $this->calculateDistance(
                $agent->current_latitude,
                $agent->current_longitude,
                $shipment->destination_latitude,
                $shipment->destination_longitude
            );

            $progress = $totalDist > 0 ? (($totalDist - $distanceLeft) / $totalDist) * 100 : 0;
            $estimatedTime = $distanceLeft * 2; // Rough estimate: 2 minutes per km
        }

        // Get tracking history for timeline
        $trackingHistory = $shipment->trackings()->orderBy('tracked_at', 'desc')->take(10)->get();

        return view('logistics.live-track', compact(
            'shipment',
            'agent',
            'progress',
            'distanceLeft',
            'estimatedTime',
            'trackingHistory'
        ));
    }

    /**
     * Get available agents for assignment (API)
     */
   // app/Http/Controllers/Logistics/LogisticsController.php

public function getAvailableAgents()
{
    try {
        Log::info('Fetching available agents...');

        // Fetch users with role 'delivery_agent' and active status
        $agents = User::where('role', 'delivery_agent')
            ->where('status', 'active')
            ->select(
                'id',
                'name',
                'mobile as phone',
                'email',
                'current_latitude',
                'current_longitude',
                DB::raw('COALESCE(rating, 4.5) as rating'),
                DB::raw('COALESCE(total_deliveries, 0) as total_deliveries'),
                DB::raw("CASE
                    WHEN current_latitude IS NOT NULL AND current_longitude IS NOT NULL AND current_latitude != 0 AND current_longitude != 0
                    THEN 'available'
                    ELSE 'busy'
                END as status"),
                // Add city from delivery_agents table
                DB::raw("(SELECT city FROM delivery_agents WHERE delivery_agents.user_id = users.id LIMIT 1) as city")
            )
            ->get();

        Log::info('Agents fetched successfully', ['count' => $agents->count()]);

        return response()->json($agents);

    } catch (\Exception $e) {
        Log::error('Error fetching agents: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => 'Failed to fetch agents: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get agent current location (API)
     */
    public function getAgentLocation($agentId)
    {
        try {
            $agent = User::find($agentId);

            if (!$agent) {
                return response()->json([
                    'success' => false,
                    'error' => 'Agent not found'
                ], 404);
            }

            // Get delivery agent record for complete location data
            $deliveryAgent = DeliveryAgent::where('user_id', $agentId)->first();

            if (!$deliveryAgent) {
                return response()->json([
                    'success' => false,
                    'error' => 'Delivery agent record not found'
                ], 404);
            }

            // Get latest location from agent_locations table if available
            $latestLocation = $deliveryAgent->agentLocations()
                ->latest('tracked_at')
                ->first();

            return response()->json([
                'success' => true,
                'latitude' => $deliveryAgent->current_latitude ?? $agent->current_latitude,
                'longitude' => $deliveryAgent->current_longitude ?? $agent->current_longitude,
                'speed' => $latestLocation?->speed_kmh ?? 0,
                'accuracy' => $latestLocation?->accuracy ?? $agent->gps_accuracy ?? 50,
                'battery' => $latestLocation?->battery_level ?? 100,
                'heading' => $latestLocation?->heading ?? 0,
                'last_update' => $latestLocation?->tracked_at ?? $deliveryAgent->updated_at,
                'status' => $deliveryAgent->status ?? 'active',
                'is_moving' => $latestLocation?->speed_kmh > 0 ?? false
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching agent location: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch location'
            ], 500);
        }
    }

    /**
     * Update shipment location (API)
     */
    public function updateShipmentLocation(Request $request, Shipment $shipment)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'accuracy' => 'nullable|numeric'
            ]);

            $shipment->current_latitude = $request->latitude;
            $shipment->current_longitude = $request->longitude;
            $shipment->location_accuracy = $request->accuracy ?? 50;
            $shipment->last_location_update = now();
            $shipment->save();

            // Also update tracking record
            $shipment->trackings()->create([
                'status' => $shipment->status,
                'location' => 'Location updated',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'tracked_at' => now(),
                'remarks' => 'Live location updated'
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error updating shipment location: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show shipment details
     */
    public function show($id)
    {
        $shipment = Shipment::with(['customer', 'deliveryAgent', 'sale', 'trackings', 'creator'])
            ->findOrFail($id);

        // Get assigned agent with user details
        $agent = null;
        if ($shipment->assigned_to) {
            $agent = User::find($shipment->assigned_to);
            if ($agent) {
                // Add delivery agent specific data
                $deliveryAgent = DeliveryAgent::where('user_id', $shipment->assigned_to)->first();
                if ($deliveryAgent) {
                    $agent->rating = $deliveryAgent->rating ?? 4.5;
                    $agent->total_deliveries = $deliveryAgent->successful_deliveries ?? 0;
                    $agent->current_latitude = $deliveryAgent->current_latitude;
                    $agent->current_longitude = $deliveryAgent->current_longitude;
                }
            }
        }

        // Calculate delivery progress
        $deliveryProgress = $this->calculateDeliveryProgress($shipment);

        // Get related shipments for same customer/sale
        $relatedShipments = Shipment::where('customer_id', $shipment->customer_id)
            ->where('id', '!=', $shipment->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('logistics.shipments.show', compact('shipment', 'agent', 'deliveryProgress', 'relatedShipments'));
    }




    // app/Http/Controllers/Logistics/LogisticsController.php

/**
 * Delete delivery agent
 */
public function destroy($id)
{
    try {
        DB::beginTransaction();

        // Find the delivery agent
        $deliveryAgent = DeliveryAgent::findOrFail($id);

        // Get the associated user
        $user = User::find($deliveryAgent->user_id);

        // Check if agent has any assigned shipments
        $assignedShipments = Shipment::where('assigned_to', $deliveryAgent->user_id)->count();

        if ($assignedShipments > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete agent. Agent has {$assignedShipments} assigned shipments. Please reassign or complete them first."
            ], 400);
        }

        // Delete delivery agent record
        $deliveryAgent->delete();

        // Delete user record
        if ($user) {
            $user->delete();
        }

        DB::commit();

        Log::info('Delivery agent deleted successfully', [
            'agent_id' => $id,
            'agent_name' => $deliveryAgent->name,
            'deleted_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Agent deleted successfully'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error deleting agent: ' . $e->getMessage(), [
            'agent_id' => $id,
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error deleting agent: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Assign delivery agent to shipment (API)
     */
  public function assignAgent(Request $request, $id)
{
    try {
        // Find shipment by ID
        $shipment = Shipment::find($id);

        if (!$shipment) {
            Log::warning('Shipment not found for assignment', ['shipment_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        Log::info('Assigning agent to shipment', [
            'shipment_id' => $shipment->id,
            'shipment_number' => $shipment->shipment_number,
            'agent_id' => $request->agent_id
        ]);

        $request->validate([
            'agent_id' => 'required|exists:users,id'
        ]);

        $agent = User::find($request->agent_id);

        if (!$agent) {
            return response()->json([
                'success' => false,
                'message' => 'Agent not found'
            ], 404);
        }

        // Check if agent is a delivery agent
        if ($agent->role !== 'delivery_agent') {
            return response()->json([
                'success' => false,
                'message' => 'Selected user is not a delivery agent'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Update shipment with assigned agent
            $shipment->assigned_to = $agent->id;
            $shipment->status = 'assigned';
            $shipment->save();

            Log::info('Shipment updated', [
                'shipment_id' => $shipment->id,
                'assigned_to' => $shipment->assigned_to,
                'status' => $shipment->status
            ]);

            // Update delivery_agent table if exists
            $deliveryAgent = DeliveryAgent::where('user_id', $agent->id)->first();
            $agentLat = null;
            $agentLng = null;

            if ($deliveryAgent) {
                // ========== WAREHOUSE INITIALIZATION ==========
                // Agent assigned for package pickup - initialize location to warehouse
                $warehouseLatitude = config('logistics.warehouse_latitude', 22.524768);
                $warehouseLongitude = config('logistics.warehouse_longitude', 72.955568);

                $deliveryAgent->status = 'busy';
                $deliveryAgent->current_latitude = $warehouseLatitude;
                $deliveryAgent->current_longitude = $warehouseLongitude;
                $deliveryAgent->current_location = 'Warehouse';
                $deliveryAgent->last_location_update = now();
                $deliveryAgent->save();

                // Create initial location record
                AgentLocation::create([
                    'agent_id' => $agent->id,
                    'latitude' => $warehouseLatitude,
                    'longitude' => $warehouseLongitude,
                    'speed' => 0,
                    'heading' => 0,
                    'accuracy' => 0,
                    'recorded_at' => now()
                ]);

                $agentLat = $warehouseLatitude;
                $agentLng = $warehouseLongitude;
                // =========================================

                Log::info('Delivery agent status updated & initialized at warehouse', [
                    'agent_id' => $agent->id,
                    'status' => 'busy',
                    'latitude' => $agentLat,
                    'longitude' => $agentLng
                ]);
            }

            // Get location from user table if not in delivery_agent
            if (!$agentLat && $agent->current_latitude) {
                $agentLat = $agent->current_latitude;
                $agentLng = $agent->current_longitude;
            }

            // Create tracking record
            $tracking = $shipment->trackings()->create([
                'status' => 'assigned',
                'location' => 'Warehouse',
                'tracked_at' => now(),
                'remarks' => "Assigned to delivery agent: {$agent->name} | Starting from Warehouse"
            ]);

            Log::info('Tracking record created', [
                'tracking_id' => $tracking->id,
                'shipment_id' => $shipment->id
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agent assigned successfully & initialized at warehouse',
                'agent_latitude' => $agentLat,
                'agent_longitude' => $agentLng,
                'agent_name' => $agent->name,
                'initial_location' => 'Warehouse',
                'shipment_id' => $shipment->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction error in assignAgent', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Error assigning agent: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'shipment_id' => $id ?? null,
            'agent_id' => $request->agent_id ?? null
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to assign agent: ' . $e->getMessage()
        ], 500);
    }
}




public function removeAgent(Request $request, $id)
{
    try {
        $shipment = Shipment::find($id);

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }

        if (!$shipment->assigned_to) {
            return response()->json([
                'success' => false,
                'message' => 'No agent assigned to this shipment'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $agentId = $shipment->assigned_to;

            $deliveryAgent = DeliveryAgent::where('user_id', $agentId)->first();
            if ($deliveryAgent) {
                $deliveryAgent->status = 'available';
                $deliveryAgent->save();
            }

            $shipment->assigned_to = null;
            $shipment->status = 'pending';
            $shipment->save();

            $shipment->trackings()->create([
                'status' => 'agent_removed',
                'location' => $shipment->city ?? 'Warehouse',
                'tracked_at' => now(),
                'remarks' => 'Delivery agent removed from shipment'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agent removed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    } catch (\Exception $e) {
        Log::error('Error removing agent: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to remove agent: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Assign delivery agent to shipment (Web - Form Submission)
     */
    public function assignAgentWeb(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        DB::beginTransaction();
        try {
            $shipment = Shipment::findOrFail($id);
            $agent = User::find($request->agent_id);

            if ($agent->role !== 'delivery_agent') {
                return redirect()->back()->with('error', 'Selected user is not a delivery agent');
            }

            $shipment->assigned_to = $request->agent_id;
            $shipment->save();

            // Update delivery_agent status
            $deliveryAgent = DeliveryAgent::where('user_id', $agent->id)->first();
            if ($deliveryAgent) {
                $deliveryAgent->status = 'busy';
                $deliveryAgent->save();
            }

            $shipment->updateTracking('assigned', null, "Assigned to delivery agent: " . $agent->name);

            DB::commit();

            Log::info('Delivery agent assigned', [
                'shipment_id' => $shipment->id,
                'agent_id' => $agent->id,
                'agent_name' => $agent->name
            ]);

            return redirect()->back()->with('success', 'Delivery agent assigned successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent assignment failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Error assigning agent: ' . $e->getMessage());
        }
    }

    /**
     * Show create shipment form
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();

        // Get sales that don't have shipments or have undelivered shipments
        $sales = Sale::with('customer')
            ->where(function($query) {
                $query->whereDoesntHave('shipments')
                    ->orWhereHas('shipments', function($q) {
                        $q->where('status', '!=', 'delivered');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $courierPartners = CourierPartner::where('is_active', true)->get();
        $deliveryAgents = User::where('role', 'delivery_agent')
            ->where('status', 'active')
            ->get();

        return view('logistics.shipments.create', compact('customers', 'sales', 'courierPartners', 'deliveryAgents'));
    }

    /**
     * Store new shipment
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'nullable|exists:sales,id',
            'customer_id' => 'required|exists:customers,id',
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'receiver_alternate_phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|string',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'country' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'package_type' => 'nullable|string|in:box,envelope,pallet',
            'declared_value' => 'required|numeric|min:0',
            'shipping_method' => 'required|in:standard,express,overnight',
            'courier_partner' => 'nullable|string',
            'payment_mode' => 'required|in:prepaid,cod',
            'estimated_delivery_date' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Calculate shipping charges
            $shippingCharge = $this->calculateShippingCharge($request);
            $codCharge = $request->payment_mode === 'cod' ? $this->calculateCODCharge($request) : 0;
            $insuranceCharge = $this->calculateInsuranceCharge($request);
            $totalCharge = $shippingCharge + $codCharge + $insuranceCharge;

            $shipment = new Shipment();
            $shipment->shipment_number = (new Shipment())->generateShipmentNumber();
            $shipment->fill($request->except(['_token', '_method']));
            $shipment->shipping_charge = $shippingCharge;
            $shipment->cod_charge = $codCharge;
            $shipment->insurance_charge = $insuranceCharge;
            $shipment->total_charge = $totalCharge;
            $shipment->created_by = auth()->id() ?? 1;
            $shipment->save();

            // Add initial tracking
            $shipment->updateTracking('pending', $request->city, 'Shipment created');

            // If this shipment is linked to a sale, update sale's shipping status
            if ($request->sale_id) {
                $sale = Sale::find($request->sale_id);
                if ($sale && !$sale->requires_shipping) {
                    $sale->requires_shipping = true;
                    $sale->save();
                }
            }

            DB::commit();

            Log::info('Shipment created successfully', [
                'shipment_id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
                'created_by' => auth()->id()
            ]);

            return redirect()->route('logistics.shipments.show', $shipment->id)
                ->with('success', 'Shipment created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Error creating shipment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update shipment status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,picked,in_transit,out_for_delivery,delivered,failed,returned',
            'location' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $shipment = Shipment::findOrFail($id);
            $oldStatus = $shipment->status;

            $shipment->updateTracking($request->status, $request->location, $request->remarks);

            // If status changed to delivered, update delivery agent stats
            if ($request->status === 'delivered' && $oldStatus !== 'delivered' && $shipment->assigned_to) {
                $agent = DeliveryAgent::where('user_id', $shipment->assigned_to)->first();
                if ($agent) {
                    $agent->total_deliveries++;
                    $agent->successful_deliveries++;
                    $agent->status = 'available';
                    $agent->save();
                }

                // Also update user
                $user = User::find($shipment->assigned_to);
                if ($user) {
                    $user->total_deliveries = ($user->total_deliveries ?? 0) + 1;
                    $user->save();
                }
            }

            // If status changed to failed/returned, free up the agent
            if (in_array($request->status, ['failed', 'returned']) && $shipment->assigned_to) {
                $agent = DeliveryAgent::where('user_id', $shipment->assigned_to)->first();
                if ($agent) {
                    $agent->status = 'available';
                    $agent->save();
                }
            }

            DB::commit();

            Log::info('Shipment status updated', [
                'shipment_id' => $shipment->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shipment status updated successfully',
                'shipment' => $shipment->load('trackings')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Shipment status update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload proof of delivery
     */
    public function uploadPOD(Request $request, $id)
    {
        // Log the request for debugging
        Log::info('POD upload started', [
            'shipment_id' => $id,
            'has_signature' => $request->hasFile('signature'),
            'has_photo' => $request->hasFile('photo'),
            'content_type' => $request->header('Content-Type')
        ]);

        $validator = Validator::make($request->all(), [
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'delivery_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            Log::warning('POD validation failed', ['errors' => $validator->errors()]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $shipment = Shipment::findOrFail($id);

            if ($shipment->status !== 'delivered') {
                $message = 'Shipment must be delivered before uploading POD';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 400);
                }

                return redirect()->back()->with('error', $message);
            }

            // Handle signature upload
            if ($request->hasFile('signature')) {
                $file = $request->file('signature');

                if (!$file->isValid()) {
                    throw new \Exception('Invalid signature file');
                }

                $filename = 'signature_' . $shipment->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('pod/signatures', $filename, 'public');

                if (!$path) {
                    throw new \Exception('Failed to store signature file');
                }

                if ($shipment->pod_signature && Storage::disk('public')->exists($shipment->pod_signature)) {
                    Storage::disk('public')->delete($shipment->pod_signature);
                }

                $shipment->pod_signature = $path;
                Log::info('Signature uploaded', ['path' => $path]);
            }

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');

                if (!$file->isValid()) {
                    throw new \Exception('Invalid photo file');
                }

                $filename = 'photo_' . $shipment->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('pod/photos', $filename, 'public');

                if (!$path) {
                    throw new \Exception('Failed to store photo file');
                }

                if ($shipment->pod_photo && Storage::disk('public')->exists($shipment->pod_photo)) {
                    Storage::disk('public')->delete($shipment->pod_photo);
                }

                $shipment->pod_photo = $path;
                Log::info('Photo uploaded', ['path' => $path]);
            }

            $shipment->delivery_notes = $request->delivery_notes;
            $shipment->save();

            $shipment->updateTracking('pod_uploaded', null, 'Proof of delivery uploaded');

            Log::info('POD uploaded successfully', [
                'shipment_id' => $shipment->id,
                'has_signature' => $request->hasFile('signature'),
                'has_photo' => $request->hasFile('photo'),
                'notes' => $request->delivery_notes
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Proof of delivery uploaded successfully!',
                    'data' => [
                        'signature_url' => $shipment->pod_signature ? asset('storage/' . $shipment->pod_signature) : null,
                        'photo_url' => $shipment->pod_photo ? asset('storage/' . $shipment->pod_photo) : null,
                        'notes' => $shipment->delivery_notes
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Proof of delivery uploaded successfully');

        } catch (\Exception $e) {
            Log::error('POD upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'shipment_id' => $id
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error uploading POD: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error uploading POD: ' . $e->getMessage());
        }
    }

    /**
     * Track shipment (Public API) - WITH LIVE LOCATION
     */
    public function track($trackingNumber)
    {
        try {
            $shipment = Shipment::with(['trackings' => function($q) {
                $q->orderBy('tracked_at', 'desc');
            }])->where('tracking_number', $trackingNumber)
              ->orWhere('shipment_number', $trackingNumber)
              ->firstOrFail();

            $latestTracking = $shipment->trackings()->latest()->first();

            return response()->json([
                'success' => true,
                'shipment_number' => $shipment->shipment_number,
                'tracking_number' => $shipment->tracking_number,
                'status' => $shipment->status,
                'status_badge' => $shipment->status_badge,
                'estimated_delivery' => $shipment->estimated_delivery_date?->format('Y-m-d'),
                'current_location' => $shipment->full_address,
                'latitude' => $shipment->current_latitude ?? $latestTracking?->latitude ?? 22.524768,
                'longitude' => $shipment->current_longitude ?? $latestTracking?->longitude ?? 72.955568,
                'accuracy' => $shipment->location_accuracy ?? $latestTracking?->accuracy ?? 100,
                'last_location_update' => $shipment->last_location_update?->diffForHumans() ?? 'Never',
                'receiver_name' => $shipment->receiver_name,
                'receiver_phone' => $shipment->receiver_phone,
                'destination' => $shipment->city . ', ' . $shipment->state . ' - ' . $shipment->pincode,
                'tracking_history' => $shipment->trackings->map(function($track) {
                    return [
                        'status' => $track->status,
                        'status_badge' => $this->getStatusBadge($track->status),
                        'location' => $track->location,
                        'remarks' => $track->remarks,
                        'tracked_at' => $track->tracked_at->format('Y-m-d H:i:s'),
                        'tracked_at_human' => $track->tracked_at->diffForHumans(),
                        'latitude' => $track->latitude,
                        'longitude' => $track->longitude
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Tracking lookup failed: ' . $e->getMessage(), [
                'tracking_number' => $trackingNumber,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Shipment not found'
            ], 404);
        }
    }

    /**
     * Update live location (Delivery boy API)
     */
    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric'
        ]);

        $shipment = Shipment::findOrFail($id);
        $shipment->current_latitude = $request->latitude;
        $shipment->current_longitude = $request->longitude;
        $shipment->location_accuracy = $request->accuracy;
        $shipment->last_location_update = now();
        $shipment->save();

        // Also update tracking record
        $shipment->trackings()->create([
            'status' => $shipment->status,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'tracked_at' => now(),
            'remarks' => 'Live location update'
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Calculate distance between two points
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return 0;

        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
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
        if ($request->payment_mode !== 'cod') {
            return 0;
        }

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
    private function calculateDeliveryProgress($shipment)
    {
        $statusOrder = [
            'pending' => 0,
            'picked' => 20,
            'in_transit' => 40,
            'out_for_delivery' => 70,
            'delivered' => 100,
            'failed' => 100,
            'returned' => 100
        ];

        return $statusOrder[$shipment->status] ?? 0;
    }

    /**
     * Helper function to get status badge color
     */
    private function getStatusBadge($status)
    {
        $badges = [
            'pending' => 'warning',
            'picked' => 'info',
            'in_transit' => 'primary',
            'out_for_delivery' => 'secondary',
            'delivered' => 'success',
            'failed' => 'danger',
            'returned' => 'dark'
        ];

        return $badges[$status] ?? 'secondary';
    }

    /**
     * Bulk shipment creation from sales
     */


    /**
     * Delivery agents management
     */
    public function agents(Request $request)
    {
        $query = DeliveryAgent::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('agent_code', 'like', "%{$search}%");
            });
        }

        $agents = $query->orderBy('name')->paginate(15)->withQueryString();

        $stats = [
            'total' => DeliveryAgent::count(),
            'available' => DeliveryAgent::where('status', 'available')->count(),
            'busy' => DeliveryAgent::where('status', 'busy')->count(),
            'offline' => DeliveryAgent::where('status', 'offline')->count(),
            'total_deliveries' => DeliveryAgent::sum('successful_deliveries')
        ];

        return view('logistics.agents.index', compact('agents', 'stats'));
    }

    /**
     * Shipment reports
     */
    public function reports(Request $request)
    {
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->endOfMonth()->format('Y-m-d'));

        $query = Shipment::whereBetween('created_at', [
            Carbon::parse($fromDate)->startOfDay(),
            Carbon::parse($toDate)->endOfDay()
        ]);

        $stats = [
            'total_shipments' => $query->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'in_transit' => (clone $query)->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->count(),
            'failed' => (clone $query)->whereIn('status', ['failed', 'returned'])->count(),
            'total_revenue' => (clone $query)->sum('total_charge'),
            'avg_delivery_time' => (clone $query)->where('status', 'delivered')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, actual_delivery_date)) as avg_time'))
                ->first()->avg_time ?? 0,
            'delivery_rate' => $query->count() > 0
                ? round((clone $query)->where('status', 'delivered')->count() / $query->count() * 100, 2)
                : 0
        ];

        $byCourier = (clone $query)
            ->select('courier_partner', DB::raw('count(*) as total'), DB::raw('sum(total_charge) as revenue'))
            ->whereNotNull('courier_partner')
            ->groupBy('courier_partner')
            ->orderBy('total', 'desc')
            ->get();

        $byCity = (clone $query)
            ->select('city', DB::raw('count(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $dailyTrend = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('logistics.reports.index', compact(
            'stats',
            'byCourier',
            'byCity',
            'dailyTrend',
            'fromDate',
            'toDate'
        ));
    }

    /**
     * Get shipment by tracking number (web view)
     */
    public function trackWeb($trackingNumber)
    {
        $shipment = Shipment::with(['trackings' => function($q) {
                $q->orderBy('tracked_at', 'desc');
            }])
            ->where('tracking_number', $trackingNumber)
            ->orWhere('shipment_number', $trackingNumber)
            ->firstOrFail();

          return view('logistics.live-track', compact('shipment', 'agent'));
    }

    /**
     * Get delivery agents for map
     */
    public function getAgentsForMap()
    {
        $agents = DeliveryAgent::where('is_active', true)
            ->whereNotNull('current_latitude')
            ->whereNotNull('current_longitude')
            ->get(['id', 'name', 'current_latitude', 'current_longitude', 'status']);

        return response()->json([
            'success' => true,
            'agents' => $agents
        ]);
    }

    /**
     * Edit shipment
     */
    public function edit($id)
    {
        $shipment = Shipment::findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $courierPartners = CourierPartner::where('is_active', true)->get();
        $deliveryAgents = User::where('role', 'delivery_agent')
            ->where('status', 'active')
            ->get();

        return view('logistics.shipments.edit', compact('shipment', 'customers', 'courierPartners', 'deliveryAgents'));
    }

    /**
     * Update shipment
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'weight' => 'nullable|numeric|min:0',
            'declared_value' => 'required|numeric|min:0',
            'shipping_method' => 'required|in:standard,express,overnight',
            'payment_mode' => 'required|in:prepaid,cod',
            'estimated_delivery_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $shipment = Shipment::findOrFail($id);
            $shipment->update($request->all());

            Log::info('Shipment updated successfully', [
                'shipment_id' => $shipment->id,
                'updated_by' => auth()->id()
            ]);

            return redirect()->route('logistics.shipments.show', $shipment->id)
                ->with('success', 'Shipment updated successfully!');

        } catch (\Exception $e) {
            Log::error('Shipment update failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating shipment: ' . $e->getMessage())
                ->withInput();
        }
    }

 // app/Http/Controllers/Logistics/LogisticsController.php

/**
 * Show bulk create shipment form
 */
public function bulkCreateForm()
{
    // Get sales that don't have shipments
    $sales = Sale::with('customer')
        ->whereDoesntHave('shipments')
        ->where('requires_shipping', true)
        ->orderBy('created_at', 'desc')
        ->get();

    // Get delivery agents
    $agents = DeliveryAgent::where('is_active', true)
        ->where('status', 'available')
        ->get();

    $courierPartners = CourierPartner::where('is_active', true)->get();

    return view('logistics.shipments.bulk-create', compact('sales', 'agents', 'courierPartners'));
}

/**
 * Process bulk shipment creation
 */
public function bulkCreate(Request $request)
{
    $validator = Validator::make($request->all(), [
        'sale_ids' => 'required|array',
        'sale_ids.*' => 'exists:sales,id',
        'shipping_method' => 'required|in:standard,express,overnight',
        'courier_partner' => 'nullable|string',
        'agent_id' => 'nullable|exists:delivery_agents,id'
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    DB::beginTransaction();

    try {
        $sales = Sale::with('customer')->whereIn('id', $request->sale_ids)->get();
        $created = [];
        $skipped = [];

        foreach ($sales as $sale) {
            // Check if shipment already exists
            if (Shipment::where('sale_id', $sale->id)->exists()) {
                $skipped[] = $sale->invoice_no;
                continue;
            }

            // Generate shipment number
            $shipmentNumber = 'SHIP' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $trackingNumber = 'TRK' . date('Ymd') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);

            // Calculate charges
            $shippingCharge = $this->calculateShippingChargeForSale($sale, $request->shipping_method);
            $totalCharge = $shippingCharge;

            // Create shipment
            $shipment = Shipment::create([
                'shipment_number' => $shipmentNumber,
                'tracking_number' => $trackingNumber,
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'receiver_name' => $sale->customer->name ?? 'Customer',
                'receiver_phone' => $sale->customer->mobile ?? '',
                'shipping_address' => $sale->shipping_address ?? $sale->customer->address ?? '',
                'city' => $sale->city ?? $sale->customer->city ?? '',
                'state' => $sale->state ?? $sale->customer->state ?? '',
                'pincode' => $sale->pincode ?? $sale->customer->pincode ?? '',
                'country' => $sale->country ?? 'India',
                'declared_value' => $sale->grand_total,
                'shipping_method' => $request->shipping_method,
                'courier_partner' => $request->courier_partner,
                'payment_mode' => $sale->payment_status === 'paid' ? 'prepaid' : 'cod',
                'shipping_charge' => $shippingCharge,
                'total_charge' => $totalCharge,
                'assigned_to' => $request->agent_id,
                'status' => 'pending',
                'created_by' => auth()->id()
            ]);

            // Add tracking record
            $shipment->updateTracking('pending', 'Shipment created', 'Bulk shipment created from sale');

            $created[] = $shipment->id;
        }

        DB::commit();

        $message = count($created) . ' shipments created successfully!';
        if (count($skipped) > 0) {
            $message .= ' Skipped ' . count($skipped) . ' sales (already have shipments): ' . implode(', ', $skipped);
        }

        return redirect()->route('logistics.shipments.index')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Bulk shipment creation failed: ' . $e->getMessage());

        return redirect()->back()
            ->with('error', 'Error creating bulk shipments: ' . $e->getMessage())
            ->withInput();
    }
}

/**
 * Calculate shipping charge for sale
 */
private function calculateShippingChargeForSale($sale, $shippingMethod)
{
    $baseRate = 50;

    if ($shippingMethod === 'express') {
        $rate = $baseRate * 1.5;
    } elseif ($shippingMethod === 'overnight') {
        $rate = $baseRate * 2;
    } else {
        $rate = $baseRate;
    }

    // Weight based calculation (if weight is available)
    $weight = $sale->total_weight ?? 1;

    return $rate * $weight;
}
}
