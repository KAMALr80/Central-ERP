<?php
// app/Http/Controllers/Agent/PerformanceController.php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Shipment;
use App\Models\AgentPerformanceLog;
use App\Models\AgentLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get agent details
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        if (!$agent) {
            return redirect()->route('agent.dashboard')->with('error', 'Agent profile not found');
        }

        // Handle period selection
        $period = $request->get('period', 'month');
        list($startDate, $endDate) = $this->getDateRangeFromPeriod($period, $request);

        // Get shipments in date range
        $shipments = Shipment::where('assigned_to', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Calculate performance metrics
        $totalShipments = $shipments->count();
        $completedShipments = $shipments->where('status', 'delivered')->count();
        $failedShipments = $shipments->whereIn('status', ['failed', 'returned'])->count();
        $completionRate = $totalShipments > 0 ? round(($completedShipments / $totalShipments) * 100, 2) : 0;

        // Calculate average delivery time (in minutes)
        $deliveryTimes = [];
        foreach ($shipments->where('status', 'delivered') as $shipment) {
            if ($shipment->actual_delivery_date && $shipment->created_at) {
                $deliveryTimes[] = $shipment->created_at->diffInMinutes($shipment->actual_delivery_date);
            }
        }
        $avgDeliveryTime = !empty($deliveryTimes) ? round(array_sum($deliveryTimes) / count($deliveryTimes), 0) : 0;

        // Get daily performance for chart
        $dailyPerformance = $this->getDailyPerformance($agent->user_id, $startDate, $endDate);

        // Get daily earnings for chart
        $dailyEarnings = $this->getDailyEarnings($agent, $startDate, $endDate);

        // Get delivery times for chart
        $deliveryTimesChart = $this->getDeliveryTimesForChart($agent->user_id, $startDate, $endDate);

        // Get rating distribution
        $ratingDistribution = $this->getRatingDistribution($agent->user_id, $startDate, $endDate);

        // Get performance logs
        $performanceLogs = AgentPerformanceLog::where('agent_id', $agent->id)
            ->whereBetween('log_date', [$startDate, $endDate])
            ->orderBy('log_date', 'desc')
            ->get();

        // Add on-time rate to logs
        foreach ($performanceLogs as $log) {
            $log->on_time_rate = $this->calculateOnTimeRateForDate($agent->user_id, $log->log_date);
        }

        // Calculate total distance traveled
        $totalDistance = $this->getTotalDistance($agent->user_id, $startDate, $endDate);

        // Calculate on-time delivery rate
        $onTimeRate = $this->calculateOnTimeRate($agent->user_id, $startDate, $endDate);

        // Calculate success rate
        $successRate = $agent->success_rate;

        // Calculate customer satisfaction (average rating * 20 to get percentage)
        $customerSatisfaction = min(100, round(($agent->rating ?? 0) * 20, 0));

        // Calculate total earnings
        $totalEarnings = $this->calculateTotalEarnings($agent, $startDate, $endDate);

        // Calculate trends
        $previousPeriod = $this->getPreviousPeriod($startDate, $endDate);
        $shipmentTrend = $this->calculateShipmentTrend($agent->user_id, $startDate, $endDate, $previousPeriod);
        $completionTrend = $this->calculateCompletionTrend($agent->user_id, $startDate, $endDate, $previousPeriod);
        $timeTrend = $this->calculateTimeTrend($agent->user_id, $startDate, $endDate, $previousPeriod);

        // Get monthly summary
        $monthlySummary = $this->getMonthlySummary($agent->user_id);

        // Get weekly performance
        $weeklyPerformance = $this->getWeeklyPerformance($agent->user_id, $startDate, $endDate);

        return view('agent.performance.index', compact(
            'agent',
            'totalShipments',
            'completedShipments',
            'failedShipments',
            'completionRate',
            'avgDeliveryTime',
            'dailyPerformance',
            'dailyEarnings',
            'deliveryTimesChart',
            'ratingDistribution',
            'performanceLogs',
            'startDate',
            'endDate',
            'totalDistance',
            'onTimeRate',
            'successRate',
            'customerSatisfaction',
            'totalEarnings',
            'shipmentTrend',
            'completionTrend',
            'timeTrend',
            'monthlySummary',
            'weeklyPerformance'
        ));
    }

    /**
     * Get date range from period selection
     */
    private function getDateRangeFromPeriod($period, $request)
    {
        $today = Carbon::today();

        switch ($period) {
            case 'today':
                $startDate = $today->toDateString();
                $endDate = $today->toDateString();
                break;
            case 'week':
                $startDate = $today->startOfWeek()->toDateString();
                $endDate = $today->endOfWeek()->toDateString();
                break;
            case 'month':
                $startDate = $today->startOfMonth()->toDateString();
                $endDate = $today->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $lastMonth = $today->subMonth();
                $startDate = $lastMonth->startOfMonth()->toDateString();
                $endDate = $lastMonth->endOfMonth()->toDateString();
                break;
            case 'custom':
            default:
                $startDate = $request->get('start_date', $today->startOfMonth()->toDateString());
                $endDate = $request->get('end_date', $today->toDateString());
                break;
        }

        return [$startDate, $endDate];
    }

    /**
     * Get previous period for trend calculation
     */
    private function getPreviousPeriod($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $diffDays = $start->diffInDays($end);

        $previousStart = $start->copy()->subDays($diffDays + 1);
        $previousEnd = $start->copy()->subDay();

        return ['start' => $previousStart->toDateString(), 'end' => $previousEnd->toDateString()];
    }

    /**
     * Calculate shipment trend
     */
    private function calculateShipmentTrend($agentId, $startDate, $endDate, $previousPeriod)
    {
        $currentTotal = Shipment::where('assigned_to', $agentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $previousTotal = Shipment::where('assigned_to', $agentId)
            ->whereBetween('created_at', [$previousPeriod['start'], $previousPeriod['end']])
            ->count();

        if ($previousTotal == 0) return 0;

        return round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1);
    }

    /**
     * Calculate completion trend
     */
    private function calculateCompletionTrend($agentId, $startDate, $endDate, $previousPeriod)
    {
        $currentCompleted = Shipment::where('assigned_to', $agentId)
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->count();

        $previousCompleted = Shipment::where('assigned_to', $agentId)
            ->whereBetween('actual_delivery_date', [$previousPeriod['start'], $previousPeriod['end']])
            ->where('status', 'delivered')
            ->count();

        if ($previousCompleted == 0) return 0;

        return round((($currentCompleted - $previousCompleted) / $previousCompleted) * 100, 1);
    }

    /**
     * Calculate time trend
     */
    private function calculateTimeTrend($agentId, $startDate, $endDate, $previousPeriod)
    {
        $currentAvg = $this->getAverageDeliveryTime($agentId, $startDate, $endDate);
        $previousAvg = $this->getAverageDeliveryTime($agentId, $previousPeriod['start'], $previousPeriod['end']);

        if ($previousAvg == 0) return 0;

        return round($currentAvg - $previousAvg, 1);
    }

    /**
     * Get average delivery time
     */
    private function getAverageDeliveryTime($agentId, $startDate, $endDate)
    {
        $shipments = Shipment::where('assigned_to', $agentId)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->get();

        $times = [];
        foreach ($shipments as $shipment) {
            if ($shipment->actual_delivery_date && $shipment->created_at) {
                $times[] = $shipment->created_at->diffInMinutes($shipment->actual_delivery_date);
            }
        }

        return !empty($times) ? array_sum($times) / count($times) : 0;
    }

    /**
     * Get daily performance data
     */
    private function getDailyPerformance($agentId, $startDate, $endDate)
    {
        $dates = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current <= $end) {
            $date = $current->toDateString();
            $count = Shipment::where('assigned_to', $agentId)
                ->whereDate('actual_delivery_date', $date)
                ->where('status', 'delivered')
                ->count();

            $dates[] = [
                'date' => $current->format('d M'),
                'count' => $count
            ];

            $current->addDay();
        }

        return $dates;
    }

    /**
     * Get daily earnings for chart
     */
    private function getDailyEarnings($agent, $startDate, $endDate)
    {
        $dates = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current <= $end) {
            $date = $current->toDateString();
            $shipments = Shipment::where('assigned_to', $agent->user_id)
                ->whereDate('actual_delivery_date', $date)
                ->where('status', 'delivered')
                ->get();

            $total = 0;
            foreach ($shipments as $shipment) {
                if ($agent->commission_type == 'fixed') {
                    $total += $agent->commission_value ?? 50;
                } elseif ($agent->commission_type == 'percentage') {
                    $total += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
                }
            }

            $dates[] = [
                'date' => $current->format('d M'),
                'earnings' => $total
            ];

            $current->addDay();
        }

        return $dates;
    }

    /**
     * Get delivery times for chart
     */
    private function getDeliveryTimesForChart($agentId, $startDate, $endDate)
    {
        $dates = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current <= $end) {
            $date = $current->toDateString();
            $shipments = Shipment::where('assigned_to', $agentId)
                ->whereDate('actual_delivery_date', $date)
                ->where('status', 'delivered')
                ->get();

            $times = [];
            foreach ($shipments as $shipment) {
                if ($shipment->actual_delivery_date && $shipment->created_at) {
                    $times[] = $shipment->created_at->diffInMinutes($shipment->actual_delivery_date);
                }
            }

            $avgTime = !empty($times) ? round(array_sum($times) / count($times), 0) : 0;

            $dates[] = [
                'date' => $current->format('d M'),
                'time' => $avgTime
            ];

            $current->addDay();
        }

        return $dates;
    }

    /**
     * Get rating distribution
     */
    private function getRatingDistribution($agentId, $startDate, $endDate)
    {
        // This would typically come from a ratings table
        // For now, return sample distribution based on agent rating
        $rating = DeliveryAgent::where('user_id', $agentId)->first()->rating ?? 4.5;

        // Generate distribution based on average rating
        $distribution = [0, 0, 0, 0, 0];
        $base = floor($rating);
        $fraction = $rating - $base;

        if ($base >= 5) {
            $distribution[4] = 80;
            $distribution[3] = 15;
            $distribution[2] = 5;
        } elseif ($base >= 4) {
            $distribution[4] = 60;
            $distribution[3] = 25;
            $distribution[2] = 10;
            $distribution[1] = 5;
        } elseif ($base >= 3) {
            $distribution[3] = 40;
            $distribution[4] = 20;
            $distribution[2] = 25;
            $distribution[1] = 10;
            $distribution[0] = 5;
        } else {
            $distribution[2] = 30;
            $distribution[3] = 20;
            $distribution[1] = 25;
            $distribution[0] = 15;
            $distribution[4] = 10;
        }

        return $distribution;
    }

    /**
     * Calculate on-time rate for a specific date
     */
    private function calculateOnTimeRateForDate($agentId, $date)
    {
        $shipments = Shipment::where('assigned_to', $agentId)
            ->whereDate('actual_delivery_date', $date)
            ->where('status', 'delivered')
            ->get();

        if ($shipments->count() == 0) return 100;

        $onTime = 0;
        foreach ($shipments as $shipment) {
            if ($shipment->estimated_delivery_date && $shipment->actual_delivery_date) {
                if ($shipment->actual_delivery_date <= $shipment->estimated_delivery_date) {
                    $onTime++;
                }
            } else {
                $onTime++; // Assume on-time if no estimate
            }
        }

        return round(($onTime / $shipments->count()) * 100, 0);
    }

    /**
     * Calculate overall on-time rate
     */
    private function calculateOnTimeRate($agentId, $startDate, $endDate)
    {
        $shipments = Shipment::where('assigned_to', $agentId)
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->get();

        if ($shipments->count() == 0) return 100;

        $onTime = 0;
        foreach ($shipments as $shipment) {
            if ($shipment->estimated_delivery_date && $shipment->actual_delivery_date) {
                if ($shipment->actual_delivery_date <= $shipment->estimated_delivery_date) {
                    $onTime++;
                }
            } else {
                $onTime++; // Assume on-time if no estimate
            }
        }

        return round(($onTime / $shipments->count()) * 100, 0);
    }

    /**
     * Calculate total earnings for period
     */
    private function calculateTotalEarnings($agent, $startDate, $endDate)
    {
        $shipments = Shipment::where('assigned_to', $agent->user_id)
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->get();

        $total = 0;
        foreach ($shipments as $shipment) {
            if ($agent->commission_type == 'fixed') {
                $total += $agent->commission_value ?? 50;
            } elseif ($agent->commission_type == 'percentage') {
                $total += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }
        }

        return $total;
    }

    /**
     * Get total distance traveled
     */
    private function getTotalDistance($agentId, $startDate, $endDate)
    {
        if (class_exists(AgentLocation::class)) {
            return AgentLocation::getTotalDistanceForAgent($agentId, $startDate, $endDate);
        }
        return 0;
    }

    /**
     * Get monthly summary
     */
    private function getMonthlySummary($agentId)
    {
        $months = [];

        for ($i = 0; $i < 6; $i++) {
            $month = Carbon::now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $shipments = Shipment::where('assigned_to', $agentId)
                ->where('status', 'delivered')
                ->whereBetween('actual_delivery_date', [$start, $end])
                ->get();

            $months[] = [
                'month' => $month->format('M Y'),
                'shipments' => $shipments->count(),
                'earnings' => $shipments->sum('declared_value')
            ];
        }

        return array_reverse($months);
    }

    /**
     * Get weekly performance
     */
    private function getWeeklyPerformance($agentId, $startDate, $endDate)
    {
        $weeks = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($start <= $end) {
            $weekStart = $start->copy()->startOfWeek();
            $weekEnd = $start->copy()->endOfWeek();

            $shipments = Shipment::where('assigned_to', $agentId)
                ->where('status', 'delivered')
                ->whereBetween('actual_delivery_date', [$weekStart, $weekEnd])
                ->get();

            $weeks[] = [
                'week' => 'Week ' . $start->weekOfMonth,
                'shipments' => $shipments->count(),
                'dates' => $weekStart->format('d M') . ' - ' . $weekEnd->format('d M')
            ];

            $start->addWeek();
        }

        return $weeks;
    }

    /**
     * Export performance report
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $performanceLogs = AgentPerformanceLog::where('agent_id', $agent->id)
            ->whereBetween('log_date', [$startDate, $endDate])
            ->orderBy('log_date', 'desc')
            ->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="performance-report-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($performanceLogs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Assigned', 'Delivered', 'Failed', 'On-Time Rate', 'Rating', 'Distance (km)', 'Earnings (₹)']);

            foreach ($performanceLogs as $log) {
                fputcsv($file, [
                    $log->log_date->format('d M Y'),
                    $log->shipments_assigned ?? 0,
                    $log->shipments_delivered ?? 0,
                    $log->shipments_failed ?? 0,
                    $log->on_time_rate ?? 100,
                    number_format($log->average_rating ?? 0, 1),
                    number_format($log->total_distance_km ?? 0, 1),
                    number_format($log->total_earnings ?? 0, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get weekly performance data (AJAX)
     */
    public function weekly(Request $request)
    {
        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        $startDate = $request->get('start_date', now()->startOfWeek()->toDateString());
        $endDate = $request->get('end_date', now()->endOfWeek()->toDateString());

        $weeklyData = $this->getDailyPerformance($agent->user_id, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $weeklyData
        ]);
    }

    /**
     * Get monthly performance data (AJAX)
     */
    public function monthly(Request $request)
    {
        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        $month = $request->get('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth()->toDateString();
        $endDate = Carbon::parse($month)->endOfMonth()->toDateString();

        $monthlyData = $this->getDailyPerformance($agent->user_id, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $monthlyData,
            'summary' => [
                'total' => array_sum(array_column($monthlyData, 'count')),
                'average' => count($monthlyData) > 0 ? round(array_sum(array_column($monthlyData, 'count')) / count($monthlyData), 1) : 0
            ]
        ]);
    }
}
