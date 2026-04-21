<?php
// app/Http/Controllers/API/DeliveryController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\DeliveryAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    /**
     * Get assigned shipments for delivery boy
     */
    public function assignedShipments(Request $request)
    {
        $agent = DeliveryAgent::where('user_id', auth()->id())->first();

        if (!$agent) {
            return response()->json(['error' => 'Not a delivery agent'], 403);
        }

        $shipments = Shipment::with(['customer'])
            ->where('assigned_to', auth()->id())
            ->whereIn('status', ['picked', 'in_transit', 'out_for_delivery'])
            ->orderBy('estimated_delivery_date')
            ->get()
            ->map(function($shipment) {
                return [
                    'id' => $shipment->id,
                    'shipment_number' => $shipment->shipment_number,
                    'tracking_number' => $shipment->tracking_number,
                    'receiver_name' => $shipment->receiver_name,
                    'receiver_phone' => $shipment->receiver_phone,
                    'address' => $shipment->full_address,
                    'status' => $shipment->status,
                    'estimated_delivery' => $shipment->estimated_delivery_date?->format('Y-m-d H:i:s'),
                    'amount' => $shipment->declared_value,
                    'payment_mode' => $shipment->payment_mode,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $shipments
        ]);
    }

    /**
     * Update shipment status from mobile
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:picked,in_transit,out_for_delivery,delivered,failed',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'remarks' => 'nullable|string',
            'photo' => 'nullable|image|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $shipment = Shipment::where('assigned_to', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        // Update location
        $shipment->current_latitude = $request->latitude;
        $shipment->current_longitude = $request->longitude;
        $shipment->last_location_update = now();

        // Handle delivery photo
        if ($request->hasFile('photo') && $request->status === 'delivered') {
            $path = $request->file('photo')->store('delivery-photos', 'public');
            $shipment->pod_photo = $path;
        }

        $shipment->save();

        // Add tracking
        $shipment->updateTracking(
            $request->status,
            "Lat: {$request->latitude}, Lng: {$request->longitude}",
            $request->remarks
        );

        // Update agent stats if delivered
        if ($request->status === 'delivered') {
            $agent = DeliveryAgent::where('user_id', auth()->id())->first();
            $agent->total_deliveries++;
            $agent->successful_deliveries++;
            $agent->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * Get delivery history
     */
    public function history(Request $request)
    {
        $agent = DeliveryAgent::where('user_id', auth()->id())->first();

        $shipments = Shipment::where('assigned_to', auth()->id())
            ->whereIn('status', ['delivered', 'failed', 'returned'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $shipments,
            'stats' => [
                'total_deliveries' => $agent->total_deliveries,
                'successful' => $agent->successful_deliveries,
                'success_rate' => $agent->success_rate . '%',
                'rating' => $agent->rating
            ]
        ]);
    }

    /**
     * Update live location
     */
    public function updateLiveLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $agent = DeliveryAgent::where('user_id', auth()->id())->first();
        $agent->current_latitude = $request->latitude;
        $agent->current_longitude = $request->longitude;
        $agent->last_location_update = now();
        $agent->save();

        return response()->json(['success' => true]);
    }

    /**
     * Mark availability
     */
    public function setAvailability(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:available,busy,offline',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $agent = DeliveryAgent::where('user_id', auth()->id())->first();
        $agent->status = $request->status;
        $agent->save();

        return response()->json([
            'success' => true,
            'message' => "Status updated to {$request->status}"
        ]);
    }
}
