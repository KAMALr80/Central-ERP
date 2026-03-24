<?php
// app/Http/Controllers/Agent/EarningsController.php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EarningsController extends Controller
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

        // Get completed shipments in date range
        $completedShipments = Shipment::where('assigned_to', $user->id)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->get();

        // Calculate earnings based on commission type
        $totalEarnings = 0;
        $shipmentEarnings = [];
        $totalDeliveries = 0;

        foreach ($completedShipments as $shipment) {
            $earnings = 0;

            if ($agent->commission_type == 'fixed') {
                $earnings = $agent->commission_value ?? 50;
            } elseif ($agent->commission_type == 'percentage') {
                $earnings = ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }

            $totalEarnings += $earnings;
            $totalDeliveries++;

            // Determine payout status (paid after 7 days, pending otherwise)
            $payoutStatus = $this->getPayoutStatus($shipment->actual_delivery_date);

            $shipmentEarnings[] = [
                'shipment' => $shipment,
                'earnings' => $earnings,
                'date' => $shipment->actual_delivery_date,
                'payout_status' => $payoutStatus
            ];
        }

        // Calculate average per delivery
        $avgPerDelivery = $totalDeliveries > 0 ? $totalEarnings / $totalDeliveries : 0;

        // Calculate weekly earnings
        $weeklyEarnings = $this->getWeeklyEarnings($agent, $startDate, $endDate);

        // Calculate monthly earnings
        $monthlyEarnings = $this->getMonthlyEarnings($agent, $startDate, $endDate);

        // Calculate monthly target (based on average of last 3 months)
        $monthlyTarget = $this->getMonthlyTarget($agent->id);

        // Calculate pending payout (earnings from last 7 days that are not yet paid)
        $pendingPayout = $this->getPendingPayout($agent->id);

        // Calculate earnings trend compared to last month
        $earningsTrend = $this->calculateEarningsTrend($agent->id, $startDate, $endDate);

        // Get deliveries this week
        $deliveriesThisWeek = $this->getDeliveriesThisWeek($agent->id);

        // Get best day and highest earning day
        $bestDay = $this->getBestDay($agent->id, $startDate, $endDate);
        $highestEarningDay = $this->getHighestEarningDay($agent->id, $startDate, $endDate);

        // Get daily earnings for chart
        $dailyEarnings = $this->getDailyEarnings($agent, $startDate, $endDate);

        // Get monthly summary with more details
        $monthlySummary = $this->getMonthlySummary($agent);

        // Get earnings breakdown (base, bonus, incentives, tips)
        $earningsBreakdown = $this->getEarningsBreakdown($agent, $startDate, $endDate);

        // Next payout date (next Friday)
        $nextPayoutDate = $this->getNextPayoutDate();

        // Get commission details
        $commissionDetails = $this->getCommissionDetails($agent);

        return view('agent.earnings.index', compact(
            'agent',
            'totalEarnings',
            'shipmentEarnings',
            'dailyEarnings',
            'monthlySummary',
            'startDate',
            'endDate',
            'totalDeliveries',
            'avgPerDelivery',
            'weeklyEarnings',
            'monthlyEarnings',
            'monthlyTarget',
            'pendingPayout',
            'earningsTrend',
            'deliveriesThisWeek',
            'bestDay',
            'highestEarningDay',
            'earningsBreakdown',
            'nextPayoutDate',
            'commissionDetails'
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
            case 'quarter':
                $startDate = $today->startOfQuarter()->toDateString();
                $endDate = $today->endOfQuarter()->toDateString();
                break;
            case 'year':
                $startDate = $today->startOfYear()->toDateString();
                $endDate = $today->endOfYear()->toDateString();
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
     * Get payout status based on delivery date
     * Paid after 7 days, pending otherwise
     */
    private function getPayoutStatus($deliveryDate)
    {
        if (!$deliveryDate) return 'pending';

        $deliveryDate = Carbon::parse($deliveryDate);
        $payoutDate = $deliveryDate->addDays(7);

        return $payoutDate->isPast() ? 'paid' : 'pending';
    }

    /**
     * Get weekly earnings
     */
    private function getWeeklyEarnings($agent, $startDate, $endDate)
    {
        $shipments = Shipment::where('assigned_to', $agent->user_id)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->get();

        $total = 0;
        foreach ($shipments as $shipment) {
            if ($agent->commission_type == 'fixed') {
                $total += $agent->commission_value ?? 50;
            } else {
                $total += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }
        }

        return $total;
    }

    /**
     * Get monthly earnings
     */
    private function getMonthlyEarnings($agent, $startDate, $endDate)
    {
        $shipments = Shipment::where('assigned_to', $agent->user_id)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [Carbon::parse($startDate)->startOfMonth(), Carbon::parse($endDate)->endOfMonth()])
            ->get();

        $total = 0;
        foreach ($shipments as $shipment) {
            if ($agent->commission_type == 'fixed') {
                $total += $agent->commission_value ?? 50;
            } else {
                $total += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }
        }

        return $total;
    }

    /**
     * Get monthly target (average of last 3 months)
     */
    private function getMonthlyTarget($agentId)
    {
        $months = [];
        for ($i = 1; $i <= 3; $i++) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();

            $earnings = $this->getEarningsForPeriod($agentId, $start, $end);
            $months[] = $earnings;
        }

        return !empty($months) ? array_sum($months) / count($months) : 0;
    }

    /**
     * Get earnings for a specific period
     */
    private function getEarningsForPeriod($agentId, $start, $end)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)->first();
        if (!$agent) return 0;

        $shipments = Shipment::where('assigned_to', $agentId)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$start, $end])
            ->get();

        $total = 0;
        foreach ($shipments as $shipment) {
            if ($agent->commission_type == 'fixed') {
                $total += $agent->commission_value ?? 50;
            } else {
                $total += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }
        }

        return $total;
    }

    /**
     * Get pending payout (earnings not yet paid)
     */
    private function getPendingPayout($agentId)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)->first();
        if (!$agent) return 0;

        $pendingDate = Carbon::now()->subDays(7);

        $shipments = Shipment::where('assigned_to', $agentId)
            ->where('status', 'delivered')
            ->where('actual_delivery_date', '>', $pendingDate)
            ->get();

        $total = 0;
        foreach ($shipments as $shipment) {
            if ($agent->commission_type == 'fixed') {
                $total += $agent->commission_value ?? 50;
            } else {
                $total += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }
        }

        return $total;
    }

    /**
     * Calculate earnings trend compared to previous period
     */
    private function calculateEarningsTrend($agentId, $startDate, $endDate)
    {
        $currentStart = Carbon::parse($startDate);
        $currentEnd = Carbon::parse($endDate);
        $diffDays = $currentStart->diffInDays($currentEnd);

        $previousStart = $currentStart->copy()->subDays($diffDays + 1);
        $previousEnd = $currentStart->copy()->subDay();

        $currentEarnings = $this->getEarningsForPeriod($agentId, $currentStart, $currentEnd);
        $previousEarnings = $this->getEarningsForPeriod($agentId, $previousStart, $previousEnd);

        if ($previousEarnings == 0) return 0;

        return round((($currentEarnings - $previousEarnings) / $previousEarnings) * 100, 1);
    }

    /**
     * Get deliveries count for current week
     */
    private function getDeliveriesThisWeek($agentId)
    {
        return Shipment::where('assigned_to', $agentId)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();
    }

    /**
     * Get best day (day with most deliveries)
     */
    private function getBestDay($agentId, $startDate, $endDate)
    {
        $shipments = Shipment::where('assigned_to', $agentId)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->select(DB::raw('DAYNAME(actual_delivery_date) as day, COUNT(*) as count'))
            ->groupBy('day')
            ->orderBy('count', 'desc')
            ->first();

        return $shipments ? $shipments->day : 'N/A';
    }

    /**
     * Get highest earning day
     */
    private function getHighestEarningDay($agentId, $startDate, $endDate)
    {
        $agent = DeliveryAgent::where('user_id', $agentId)->first();
        if (!$agent) return '₹0';

        $shipments = Shipment::where('assigned_to', $agentId)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->get();

        $dailyEarnings = [];
        foreach ($shipments as $shipment) {
            $date = $shipment->actual_delivery_date->format('Y-m-d');
            if (!isset($dailyEarnings[$date])) {
                $dailyEarnings[$date] = 0;
            }

            if ($agent->commission_type == 'fixed') {
                $dailyEarnings[$date] += $agent->commission_value ?? 50;
            } else {
                $dailyEarnings[$date] += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }
        }

        $maxEarning = !empty($dailyEarnings) ? max($dailyEarnings) : 0;
        return '₹' . number_format($maxEarning, 2);
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
     * Get monthly summary with average per delivery
     */
    private function getMonthlySummary($agent)
    {
        $months = [];

        for ($i = 0; $i < 6; $i++) {
            $month = Carbon::now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $shipments = Shipment::where('assigned_to', $agent->user_id)
                ->where('status', 'delivered')
                ->whereBetween('actual_delivery_date', [$start, $end])
                ->get();

            $total = 0;
            $count = $shipments->count();

            foreach ($shipments as $shipment) {
                if ($agent->commission_type == 'fixed') {
                    $total += $agent->commission_value ?? 50;
                } elseif ($agent->commission_type == 'percentage') {
                    $total += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
                }
            }

            $avgPerDelivery = $count > 0 ? $total / $count : 0;

            $months[] = [
                'month' => $month->format('M Y'),
                'earnings' => $total,
                'shipments' => $count,
                'avg_per_delivery' => $avgPerDelivery
            ];
        }

        return array_reverse($months);
    }

    /**
     * Get earnings breakdown
     */
    private function getEarningsBreakdown($agent, $startDate, $endDate)
    {
        $totalEarnings = 0;
        $bonus = 0;
        $incentives = 0;
        $tips = 0;

        $shipments = Shipment::where('assigned_to', $agent->user_id)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->get();

        foreach ($shipments as $shipment) {
            if ($agent->commission_type == 'fixed') {
                $baseEarnings = $agent->commission_value ?? 50;
            } else {
                $baseEarnings = ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }

            $totalEarnings += $baseEarnings;

            // Calculate bonus (5% of base for on-time deliveries)
            if ($shipment->actual_delivery_date && $shipment->estimated_delivery_date) {
                if ($shipment->actual_delivery_date <= $shipment->estimated_delivery_date) {
                    $bonus += $baseEarnings * 0.05;
                }
            }

            // Calculate incentives (extra for high-value deliveries)
            if (($shipment->declared_value ?? 0) > 5000) {
                $incentives += 20;
            }

            // Tips (simulate random tips for completed deliveries)
            if ($shipment->status == 'delivered') {
                $tips += rand(0, 50);
            }
        }

        $baseCommission = $totalEarnings - $bonus - $incentives - $tips;

        return [
            'base' => max(0, $baseCommission),
            'bonus' => $bonus,
            'incentives' => $incentives,
            'tips' => $tips
        ];
    }

    /**
     * Get next payout date (next Friday)
     */
    private function getNextPayoutDate()
    {
        $today = Carbon::now();
        $nextFriday = $today->copy()->next(Carbon::FRIDAY);

        return $nextFriday->format('d M Y');
    }

    /**
     * Get commission details
     */
    private function getCommissionDetails($agent)
    {
        if ($agent->commission_type == 'fixed') {
            return [
                'type' => 'fixed',
                'value' => $agent->commission_value ?? 50,
                'description' => '₹' . ($agent->commission_value ?? 50) . ' per successful delivery'
            ];
        } else {
            return [
                'type' => 'percentage',
                'value' => $agent->commission_value ?? 10,
                'description' => ($agent->commission_value ?? 10) . '% of order value'
            ];
        }
    }

    /**
     * Export earnings to CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $shipments = Shipment::where('assigned_to', $user->id)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->orderBy('actual_delivery_date', 'desc')
            ->get();

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="earnings-report-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($shipments, $agent) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Shipment #', 'Customer', 'Order Value', 'Commission Type', 'Commission Amount', 'Status']);

            foreach ($shipments as $shipment) {
                if ($agent->commission_type == 'fixed') {
                    $commission = $agent->commission_value ?? 50;
                } else {
                    $commission = ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
                }

                fputcsv($file, [
                    $shipment->actual_delivery_date->format('d M Y'),
                    $shipment->shipment_number,
                    $shipment->receiver_name,
                    number_format($shipment->declared_value ?? 0, 2),
                    ucfirst($agent->commission_type ?? 'fixed'),
                    number_format($commission, 2),
                    'Paid'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate invoice
     */
    public function invoice(Request $request)
    {
        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $shipments = Shipment::where('assigned_to', $user->id)
            ->where('status', 'delivered')
            ->whereBetween('actual_delivery_date', [$startDate, $endDate])
            ->get();

        $totalEarnings = 0;
        foreach ($shipments as $shipment) {
            if ($agent->commission_type == 'fixed') {
                $totalEarnings += $agent->commission_value ?? 50;
            } else {
                $totalEarnings += ($shipment->declared_value ?? 0) * (($agent->commission_value ?? 0) / 100);
            }
        }

        return view('agent.earnings.invoice', compact('agent', 'shipments', 'totalEarnings', 'startDate', 'endDate'));
    }

    /**
     * Get details for AJAX requests
     */
    public function details(Request $request)
    {
        $user = Auth::user();
        $agent = DeliveryAgent::where('user_id', $user->id)->first();

        $period = $request->get('period', 'month');
        list($startDate, $endDate) = $this->getDateRangeFromPeriod($period, $request);

        $dailyEarnings = $this->getDailyEarnings($agent, $startDate, $endDate);
        $breakdown = $this->getEarningsBreakdown($agent, $startDate, $endDate);

        return response()->json([
            'success' => true,
            'daily_earnings' => $dailyEarnings,
            'breakdown' => $breakdown,
            'total' => array_sum(array_column($dailyEarnings, 'earnings'))
        ]);
    }
}
