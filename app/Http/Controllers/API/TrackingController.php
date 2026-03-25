<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentTracking;
use App\Services\ShipmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TrackingController extends Controller
{
    protected $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    /**
     * Track shipment by tracking number (public)
     *
     * @param string $trackingNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function track($trackingNumber)
    {
        dd('kamal');
        try {
            $shipment = Shipment::with(['trackings' => function($q) {
                    $q->orderBy('tracked_at', 'desc');
                }])
                ->where('tracking_number', $trackingNumber)
                ->orWhere('shipment_number', $trackingNumber)
                ->first();

            if (!$shipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found'
                ], 404);
            }

            // Get latest tracking
            $latestTracking = $shipment->trackings->first();

            // Calculate progress percentage
            $progress = $this->calculateProgress($shipment);

            return response()->json([
                'success' => true,
                'data' => [
                    'shipment_number' => $shipment->shipment_number,
                    'tracking_number' => $shipment->tracking_number,
                    'status' => $shipment->status,
                    'status_display' => ucfirst(str_replace('_', ' ', $shipment->status)),
                    'status_badge' => $shipment->status_badge,
                    'receiver_name' => $shipment->receiver_name,
                    'receiver_phone' => $shipment->receiver_phone,
                    'destination' => $shipment->full_address,
                    'city' => $shipment->city,
                    'estimated_delivery' => $shipment->estimated_delivery_date?->format('d M Y'),
                    'actual_delivery' => $shipment->actual_delivery_date?->format('d M Y, h:i A'),

                    // Live location
                    'current_location' => [
                        'lat' => $shipment->current_latitude ?? $latestTracking?->latitude,
                        'lng' => $shipment->current_longitude ?? $latestTracking?->longitude,
                        'accuracy' => $shipment->location_accuracy,
                        'last_updated' => $shipment->last_location_update?->diffForHumans(),
                        'last_updated_raw' => $shipment->last_location_update
                    ],

                    // Progress
                    'progress_percentage' => $progress,

                    // Tracking history
                    'tracking_history' => $shipment->trackings->map(function($track) {
                        return [
                            'id' => $track->id,
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
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Tracking error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to track shipment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track by shipment number
     *
     * @param string $shipmentNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackByShipment($shipmentNumber)
    {
        return $this->track($shipmentNumber);
    }

    /**
     * Get tracking timeline
     *
     * @param string $trackingNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function timeline($trackingNumber)
    {
        try {
            $shipment = Shipment::where('tracking_number', $trackingNumber)
                ->orWhere('shipment_number', $trackingNumber)
                ->first();

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

        } catch (\Exception $e) {
            Log::error('Timeline error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get timeline',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current location
     *
     * @param string $trackingNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function currentLocation($trackingNumber)
    {
        try {
            $shipment = Shipment::where('tracking_number', $trackingNumber)
                ->orWhere('shipment_number', $trackingNumber)
                ->first();

            if (!$shipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found'
                ], 404);
            }

            $latestTracking = $shipment->trackings()->latest()->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'latitude' => $shipment->current_latitude ?? $latestTracking?->latitude,
                    'longitude' => $shipment->current_longitude ?? $latestTracking?->longitude,
                    'accuracy' => $shipment->location_accuracy,
                    'last_updated' => $shipment->last_location_update?->diffForHumans(),
                    'last_updated_raw' => $shipment->last_location_update,
                    'address' => $shipment->city . ', ' . $shipment->state
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Current location error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get current location',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Public track (for website widget)
     *
     * @param string $trackingNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicTrack($trackingNumber)
    {
        try {
            $shipment = Shipment::with(['trackings' => function($q) {
                    $q->orderBy('tracked_at', 'desc')->limit(10);
                }])
                ->where('tracking_number', $trackingNumber)
                ->orWhere('shipment_number', $trackingNumber)
                ->first();

            if (!$shipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'tracking_number' => $shipment->tracking_number,
                    'status' => $shipment->status,
                    'status_display' => ucfirst(str_replace('_', ' ', $shipment->status)),
                    'estimated_delivery' => $shipment->estimated_delivery_date?->format('d M Y'),
                    'current_city' => $shipment->city,
                    'receiver_name' => $shipment->receiver_name,
                    'tracking_history' => $shipment->trackings->map(function($track) {
                        return [
                            'status' => $track->status,
                            'location' => $track->location,
                            'time' => $track->tracked_at->format('d M Y, h:i A')
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Public track error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to track shipment'
            ], 500);
        }
    }

    /**
     * Public timeline
     *
     * @param string $trackingNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicTimeline($trackingNumber)
    {
        return $this->timeline($trackingNumber);
    }

    /**
     * Delhivery webhook handler
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delhiveryWebhook(Request $request)
    {
        return $this->handleCourierWebhook('delhivery', $request);
    }

    /**
     * BlueDart webhook handler
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bluedartWebhook(Request $request)
    {
        return $this->handleCourierWebhook('bluedart', $request);
    }

    /**
     * DTDC webhook handler
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dtdcWebhook(Request $request)
    {
        return $this->handleCourierWebhook('dtdc', $request);
    }

    /**
     * Generic courier webhook handler
     *
     * @param string $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function courierWebhook($code, Request $request)
    {
        return $this->handleCourierWebhook($code, $request);
    }

    /**
     * Handle courier webhook
     */
    private function handleCourierWebhook($courier, Request $request)
    {
        try {
            Log::info("{$courier} webhook received", $request->all());

            // Extract tracking number based on courier
            $trackingNumber = $this->extractTrackingNumber($courier, $request);

            if (!$trackingNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tracking number not found'
                ], 400);
            }

            // Find shipment
            $shipment = Shipment::where('tracking_number', $trackingNumber)->first();

            if (!$shipment) {
                Log::warning("Shipment not found for tracking: {$trackingNumber}");
                return response()->json([
                    'success' => false,
                    'message' => 'Shipment not found'
                ], 404);
            }

            // Extract status and location
            $statusData = $this->extractStatusData($courier, $request);

            if ($statusData) {
                // Add tracking event
                $this->shipmentService->addTrackingEvent(
                    $shipment,
                    $statusData['status'],
                    $statusData['location'] ?? $shipment->city,
                    $statusData['remarks'] ?? "Status updated via {$courier} webhook",
                    $statusData['latitude'] ?? null,
                    $statusData['longitude'] ?? null
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("{$courier} webhook error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to process webhook'
            ], 500);
        }
    }

    /**
     * Extract tracking number from courier webhook
     */
    private function extractTrackingNumber($courier, $request)
    {
        switch ($courier) {
            case 'delhivery':
                return $request->input('waybill') ?? $request->input('tracking_number');

            case 'bluedart':
                return $request->input('AWBNo') ?? $request->input('tracking_number');

            case 'dtdc':
                return $request->input('cn_no') ?? $request->input('tracking_number');

            default:
                return $request->input('tracking_number') ?? $request->input('tracking_id');
        }
    }

    /**
     * Extract status data from courier webhook
     */
    private function extractStatusData($courier, $request)
    {
        switch ($courier) {
            case 'delhivery':
                $status = $request->input('status');
                return [
                    'status' => $this->mapCourierStatus($status),
                    'location' => $request->input('location'),
                    'remarks' => $request->input('remarks'),
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude')
                ];

            default:
                $status = $request->input('status');
                return [
                    'status' => $this->mapCourierStatus($status),
                    'location' => $request->input('location') ?? $request->input('city'),
                    'remarks' => $request->input('remarks') ?? $request->input('message'),
                    'latitude' => $request->input('lat') ?? $request->input('latitude'),
                    'longitude' => $request->input('lng') ?? $request->input('longitude')
                ];
        }
    }

    /**
     * Map courier status to internal status
     */
    private function mapCourierStatus($status)
    {
        $statusMap = [
            'Delivered' => 'delivered',
            'Out for Delivery' => 'out_for_delivery',
            'In Transit' => 'in_transit',
            'Picked Up' => 'picked',
            'Failed' => 'failed',
            'Returned' => 'returned',
            'Cancelled' => 'cancelled',
            'Pending' => 'pending'
        ];

        return $statusMap[$status] ?? 'in_transit';
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
