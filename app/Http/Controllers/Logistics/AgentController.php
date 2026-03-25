<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AgentController extends Controller
{
    /**
     * Display a listing of delivery agents
     */
    public function index(Request $request)
    {


    
        $query = DeliveryAgent::with('user');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('agent_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // City filter
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Vehicle type filter
        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        // Sorting
        $sortField = $request->get('sort_field', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $agents = $query->paginate(15)->withQueryString();

        // Stats
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
     * Show form for creating new agent
     */
    public function create()
    {
        return view('logistics.agents.create');
    }

    /**
     * Store a newly created agent
     */
    /**
 * Store new agent
 */
public function store(Request $request)
{
    try {
        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:delivery_agents,phone',
            'email' => 'nullable|email|unique:delivery_agents,email',
            'vehicle_type' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'pincode' => 'nullable|string',
            'current_latitude' => 'nullable|numeric',
            'current_longitude' => 'nullable|numeric',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'joining_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'is_active' => 'boolean'
        ]);

        DB::beginTransaction();

        // Generate unique agent code
        $lastAgent = DeliveryAgent::orderBy('id', 'desc')->first();
        $lastNumber = $lastAgent ? (int) substr($lastAgent->agent_code, 2) : 0;
        $agentCode = 'AG' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        // Ensure uniqueness
        while (DeliveryAgent::where('agent_code', $agentCode)->exists()) {
            $lastNumber++;
            $agentCode = 'AG' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->phone,
            'password' => Hash::make('password123'),
            'role' => 'delivery_agent',
            'status' => 'active',
            'current_latitude' => $request->current_latitude,
            'current_longitude' => $request->current_longitude,
            'email_verified_at' => now()
        ]);

        // Create delivery agent
        $deliveryAgent = DeliveryAgent::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'agent_code' => $agentCode,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_number' => $request->vehicle_number,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'current_latitude' => $request->current_latitude,
            'current_longitude' => $request->current_longitude,
            'employment_type' => $request->employment_type,
            'joining_date' => $request->joining_date,
            'salary' => $request->salary,
            'status' => 'available',
            'is_active' => $request->is_active ?? true,
            'is_online' => true
        ]);

        DB::commit();

        return redirect()->route('logistics.agents.show', $deliveryAgent->id)
            ->with('success', 'Agent created successfully!');

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput();

    } catch (\Illuminate\Database\QueryException $e) {
        DB::rollBack();

        // Handle duplicate entry error
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            if (str_contains($e->getMessage(), 'agent_code')) {
                return redirect()->back()
                    ->with('error', 'Agent code already exists. Please try again.')
                    ->withInput();
            }
            if (str_contains($e->getMessage(), 'phone')) {
                return redirect()->back()
                    ->with('error', 'Phone number already registered. Please use a different number.')
                    ->withInput();
            }
            if (str_contains($e->getMessage(), 'email')) {
                return redirect()->back()
                    ->with('error', 'Email already registered. Please use a different email.')
                    ->withInput();
            }
        }

        return redirect()->back()
            ->with('error', 'Error creating agent: ' . $e->getMessage())
            ->withInput();

    } catch (\Exception $e) {
        DB::rollBack();

        return redirect()->back()
            ->with('error', 'Error creating agent: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Display agent details
     */
    public function show($id)
    {
        $agent = DeliveryAgent::with(['assignedShipments' => function($query) {
            $query->with('customer')
                  ->latest()
                  ->limit(10);
        }])->findOrFail($id);

        // Calculate performance metrics
        $totalDeliveries = $agent->total_deliveries ?? 0;
        $successfulDeliveries = $agent->successful_deliveries ?? 0;
        $successRate = $totalDeliveries > 0
            ? round(($successfulDeliveries / $totalDeliveries) * 100, 2)
            : 0;

        // Get today's deliveries
        $todaysDeliveries = $agent->assignedShipments()
            ->whereDate('created_at', today())
            ->count();

        // Get monthly stats
        $monthlyDeliveries = $agent->assignedShipments()
            ->whereMonth('created_at', now()->month)
            ->count();

        return view('logistics.agents.show', compact(
            'agent',
            'successRate',
            'todaysDeliveries',
            'monthlyDeliveries'
        ));
    }

    /**
     * Show form for editing agent
     */
    public function edit($id)
    {
        $agent = DeliveryAgent::findOrFail($id);
        return view('logistics.agents.edit', compact('agent'));
    }

    /**
     * Update agent details
     */
    public function update(Request $request, $id)
    {
        $agent = DeliveryAgent::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:delivery_agents,phone,' . $id,
            'email' => 'nullable|email|max:255|unique:delivery_agents,email,' . $id,
            'alternate_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'vehicle_type' => 'nullable|string|in:bike,cycle,van,truck',
            'vehicle_number' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'upi_id' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric|min:0',
            'commission_type' => 'nullable|in:fixed,percentage',
            'commission_value' => 'nullable|numeric|min:0',
            'service_areas' => 'nullable|array',
            'status' => 'nullable|in:available,busy,offline',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $agent->fill($request->except(['_token', '_method', 'aadhar_card', 'driving_license', 'photo']));

            // Update service areas as JSON
            if ($request->service_areas) {
                $agent->service_areas = json_encode($request->service_areas);
            }

            // Handle document uploads
            if ($request->hasFile('aadhar_card')) {
                $path = $request->file('aadhar_card')->store('agents/aadhar', 'public');
                $agent->aadhar_card = $path;
            }

            if ($request->hasFile('driving_license')) {
                $path = $request->file('driving_license')->store('agents/license', 'public');
                $agent->driving_license = $path;
            }

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('agents/photos', 'public');
                $agent->photo = $path;
            }

            $agent->save();

            DB::commit();

            Log::info('Agent updated', ['agent_id' => $agent->id]);

            return redirect()->route('logistics.agents.show', $agent->id)
                ->with('success', 'Agent updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent update failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error updating agent: ' . $e->getMessage())
                ->withInput();
        }
    }

/**
 * Get agent's current location (for API)
 */
public function getLocation($id)
{
    $agent = DeliveryAgent::find($id);

    if (!$agent) {
        return response()->json(['error' => 'Agent not found'], 404);
    }

    return response()->json([
        'latitude' => $agent->current_latitude,
        'longitude' => $agent->current_longitude,
        'accuracy' => $agent->location_accuracy,
        'last_update' => $agent->last_location_update,
        'status' => $agent->status
    ]);
}

    /**
     * Delete agent
     */
    public function destroy($id)
    {
        try {
            $agent = DeliveryAgent::findOrFail($id);

            // Check if agent has active shipments
            if ($agent->assignedShipments()->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete agent with active shipments');
            }

            $agent->delete();

            Log::info('Agent deleted', ['agent_id' => $id]);

            return redirect()->route('logistics.agents.index')
                ->with('success', 'Agent deleted successfully');

        } catch (\Exception $e) {
            Log::error('Agent deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error deleting agent: ' . $e->getMessage());
        }
    }

    /**
     * Update agent status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,busy,offline',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $agent = DeliveryAgent::findOrFail($id);
            $agent->status = $request->status;
            $agent->save();

            Log::info('Agent status updated', [
                'agent_id' => $agent->id,
                'new_status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $agent->status
            ]);

        } catch (\Exception $e) {
            Log::error('Status update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload documents for agent
     */
    public function uploadDocuments(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'documents.*' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'type' => 'required|in:aadhar,license,photo,other'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $agent = DeliveryAgent::findOrFail($id);
            $uploadedFiles = [];

            foreach ($request->file('documents') as $file) {
                $path = $file->store('agents/' . $request->type . '/' . $id, 'public');

                // Update the specific field based on type
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

                $uploadedFiles[] = $path;
            }

            $agent->save();

            Log::info('Documents uploaded for agent', [
                'agent_id' => $agent->id,
                'type' => $request->type,
                'count' => count($uploadedFiles)
            ]);

            return response()->json([
                'success' => true,
                'message' => count($uploadedFiles) . ' document(s) uploaded successfully',
                'files' => $uploadedFiles
            ]);

        } catch (\Exception $e) {
            Log::error('Document upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading documents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get agents for map
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
     * Create user account for agent
     */
    private function createUserAccount($agent, $request)
    {
        // Create user account if needed
        if ($request->filled('email') && $request->filled('password')) {
            $user = User::create([
                'name' => $agent->name,
                'email' => $agent->email,
                'password' => Hash::make($request->password),
                'role' => 'delivery_agent',
                'status' => 'approved',
            ]);

            $agent->user_id = $user->id;
            $agent->save();
        }
    }

    /**
     * Get agent performance report
     */
    public function performanceReport(Request $request, $id)
    {
        $agent = DeliveryAgent::findOrFail($id);

        $fromDate = $request->get('from_date', now()->startOfMonth());
        $toDate = $request->get('to_date', now()->endOfMonth());

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
            'avg_delivery_time' => $shipments->where('status', 'delivered')
                ->avg(function($s) {
                    return $s->created_at->diffInHours($s->actual_delivery_date);
                })
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
