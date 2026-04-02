<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\DeliveryAgent;
use Carbon\Carbon;
use App\Helpers\HolidayHelper;

class DashboardController extends Controller
{
    /**
     * Main Dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // ==================== ROLE-BASED REDIRECTION ====================
        // Delivery Agent Dashboard
        if ($user->role === 'delivery_agent') {
            return redirect()->route('agent.dashboard');
        }

        // HR Dashboard
        if ($user->role === 'hr') {
            return redirect()->route('hr.dashboard');
        }

        // Staff Dashboard
        if ($user->role === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        // ==================== ADMIN DASHBOARD ====================
        if ($user->role === 'admin') {
            /* ===== COMMON DATA ===== */
            $totalProducts = Product::count();
            $totalRevenue  = Sale::sum('grand_total');
            $todaySales    = Sale::whereDate('sale_date', today())->sum('grand_total');
            $totalTransactions = Sale::count();
            $averageSale = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

            /* ===== EMPLOYEES ===== */
            $totalEmployees = Employee::count();

            $presentToday = Attendance::whereDate('attendance_date', today())
                ->where(function($query) {
                    $query->where('status', 'present')->orWhere('status', 'Present');
                })->count();

            $absentToday = Attendance::whereDate('attendance_date', today())
                ->where(function($query) {
                    $query->where('status', 'absent')->orWhere('status', 'Absent');
                })->count();

            $lateToday = Attendance::whereDate('attendance_date', today())
                ->where(function($query) {
                    $query->where('status', 'late')->orWhere('status', 'Late');
                })->count();

            $halfDayToday = Attendance::whereDate('attendance_date', today())
                ->where('status', 'Half Day')->count();

            $markedCount = $presentToday + $absentToday + $lateToday + $halfDayToday;
            $notMarkedToday = max(0, $totalEmployees - $markedCount);

            /* ===== LOW STOCK ===== */
            $lowStockProducts = Product::where('quantity', '<=', 10)
                ->orderBy('quantity', 'asc')
                ->get();

            /* ===== RECENT ACTIVITIES ===== */
            $recentActivities = $this->getRecentActivities();

            /* ===== AI SALES PREDICTION ===== */
            $aiPrediction = $this->getAIPredictionFromAPI();

            /* ===== PAST SALES DATA ===== */
            $pastSales = Sale::select(
                    DB::raw('DATE(sale_date) as date'),
                    DB::raw('SUM(grand_total) as total')
                )
                ->whereDate('sale_date', '>=', Carbon::now()->subDays(15))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $pastLabels = $pastSales->pluck('date')->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'));
            $pastData = $pastSales->pluck('total');

            // Future data
            $futureLabels = [];
            $futureData = [];

            if ($aiPrediction && isset($aiPrediction['data'])) {
                $futureLabels = $aiPrediction['data']['future_labels'] ?? [];
                $futureData = $aiPrediction['data']['future_data'] ?? [];
            } else {
                $futureLabels = collect(range(1, 15))->map(fn ($d) =>
                    Carbon::now()->addDays($d)->format('Y-m-d')
                )->toArray();
                $futureData = collect(range(1, 15))->map(fn () => $averageSale * 1.1)->toArray();
            }

            $attendanceData = [
                'labels' => ['Present', 'Absent', 'Late', 'Half Day', 'Not Marked'],
                'data' => [$presentToday, $absentToday, $lateToday, $halfDayToday, $notMarkedToday],
                'colors' => ['#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#6b7280'],
                'total' => $totalEmployees,
                'marked' => $markedCount,
                'percentage' => $totalEmployees > 0 ? round(($markedCount/$totalEmployees)*100) : 0
            ];

            Log::info('Recent Activities Count: ' . count($recentActivities));

            return view('dashboard.admin', compact(
                'totalProducts', 'totalRevenue', 'todaySales', 'totalTransactions', 'averageSale',
                'totalEmployees', 'presentToday', 'absentToday', 'lateToday', 'halfDayToday', 'notMarkedToday',
                'lowStockProducts', 'recentActivities', 'aiPrediction', 'pastLabels', 'pastData',
                'futureLabels', 'futureData', 'attendanceData'
            ));
        }

        /* =======================
           DEFAULT (GUEST / OTHERS)
        ======================= */
        $recentActivities = $this->getRecentActivities(5);

        return view('dashboard.staff', compact(
            'totalProducts',
            'todaySales',
            'recentActivities'
        ));
    }

    /**
     * Staff Dashboard - Dedicated method for staff role
     */
    public function staffDashboard()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();

        $myAttendanceToday = Attendance::where('employee_id', $employee->id ?? null)
            ->whereDate('attendance_date', today())
            ->first();

        // Get attendance stats
        $presentDays = Attendance::where('employee_id', $employee->id ?? null)
            ->whereMonth('attendance_date', now()->month)
            ->where(function($q) {
                $q->where('status', 'present')->orWhere('status', 'Present');
            })
            ->count();

        $absentDays = Attendance::where('employee_id', $employee->id ?? null)
            ->whereMonth('attendance_date', now()->month)
            ->where(function($q) {
                $q->where('status', 'absent')->orWhere('status', 'Absent');
            })
            ->count();

        $pendingLeaves = Leave::where('employee_id', $employee->id ?? null)
            ->where('status', 'pending')
            ->count();

        $totalProducts = Product::count();
        $todaySales = Sale::whereDate('sale_date', today())->sum('grand_total');

        // Get recent activities for staff
        $recentActivities = $this->getRecentActivities(5);

        return view('dashboard.staff', compact(
            'myAttendanceToday', 'presentDays', 'absentDays', 'pendingLeaves',
            'totalProducts', 'todaySales', 'recentActivities'
        ));
    }
    public function hrDashboard()
{
    // Allow both 'admin' and 'hr' roles
    if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
        return redirect()->route('dashboard')->with('error', 'Unauthorized access to HR Dashboard');
    }

    $totalEmployees = Employee::count();

    $presentToday = Attendance::whereDate('attendance_date', today())
        ->where(function($query) {
            $query->where('status', 'present')
                  ->orWhere('status', 'Present');
        })
        ->count();

    $absentToday = Attendance::whereDate('attendance_date', today())
        ->where(function($query) {
            $query->where('status', 'absent')
                  ->orWhere('status', 'Absent');
        })
        ->count();

    $lateToday = Attendance::whereDate('attendance_date', today())
        ->where(function($query) {
            $query->where('status', 'late')
                  ->orWhere('status', 'Late');
        })
        ->count();

    $halfDayToday = Attendance::whereDate('attendance_date', today())
        ->where('status', 'Half Day')
        ->count();

    $markedCount = $presentToday + $absentToday + $lateToday + $halfDayToday;
    $notMarkedToday = max(0, $totalEmployees - $markedCount);

    $pendingLeaves = Leave::where('status', 'pending')->count();
    $onLeaveToday = $this->getOnLeaveTodaySimple();

    $todayAttendance = Attendance::whereDate('attendance_date', today())
        ->with('employee')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    $employeesWithoutAttendance = Employee::whereDoesntHave('attendances', function($query) {
        $query->whereDate('attendance_date', today());
    })->get();

    $recentEmployees = Employee::latest()->take(5)->get();
    $recentLeaves = Leave::with('employee')
        ->where('status', 'pending')
        ->latest()
        ->take(5)
        ->get();

    $departmentStats = Employee::select('department', DB::raw('count(*) as count'))
        ->whereNotNull('department')
        ->groupBy('department')
        ->orderBy('count', 'desc')
        ->get()
        ->map(function($dept) {
            $colors = [
                'IT' => '#3b82f6', 'Sales' => '#10b981', 'Marketing' => '#8b5cf6',
                'HR' => '#f59e0b', 'Finance' => '#ef4444', 'Operations' => '#06b6d4',
                'Engineering' => '#6366f1', 'Management' => '#8b5cf6',
                'Support' => '#3b82f6', 'Production' => '#10b981',
            ];
            $icons = [
                'IT' => '💻', 'Sales' => '💰', 'Marketing' => '📢', 'HR' => '👥',
                'Finance' => '📊', 'Operations' => '⚙️', 'Engineering' => '🔧',
                'Management' => '👔', 'Support' => '🛟', 'Production' => '🏭',
            ];
            return [
                'name' => $dept->department,
                'count' => $dept->count,
                'color' => $colors[$dept->department] ?? '#6b7280',
                'icon' => $icons[$dept->department] ?? '👤'
            ];
        });

    $attendanceTrend = $this->getWeeklyAttendanceTrend();
    $attendancePercentage = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0;
    $activeEmployees = Employee::where('status', 'active')->count();
    $inactiveEmployees = Employee::where('status', 'inactive')->count();

    $attendanceIssues = Attendance::whereDate('attendance_date', today())
        ->where(function($query) {
            $query->where('remarks', 'like', '%late%')
                  ->orWhere('remarks', 'like', '%early%')
                  ->orWhere('remarks', 'like', '%absent%');
        })
        ->count();

    $monthlyLeaves = Leave::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $attendanceData = [
        'labels' => ['Present', 'Absent', 'Late', 'Half Day', 'Not Marked'],
        'data' => [$presentToday, $absentToday, $lateToday, $halfDayToday, $notMarkedToday],
        'colors' => ['#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#6b7280']
    ];

    $recentActivities = $this->getRecentActivities(10);

// Get upcoming holidays automatically
    $upcomingHolidays = HolidayHelper::getUpcomingHolidays(6, true); // 6 upcoming holidays, include optional

    // Optional: Also get current month holidays
    $currentMonthHolidays = HolidayHelper::getAllHolidays(now()->year, true);
    $currentMonthHolidays = array_filter($currentMonthHolidays, function($holiday) {
        return $holiday['date']->month === now()->month;
    });

    // Return view with holidays
    return view('dashboard.hr', compact(
        'totalEmployees', 'presentToday', 'absentToday', 'lateToday', 'halfDayToday',
        'notMarkedToday', 'pendingLeaves', 'onLeaveToday', 'todayAttendance',
        'employeesWithoutAttendance', 'recentEmployees', 'recentLeaves', 'departmentStats',
        'attendanceTrend', 'attendancePercentage', 'activeEmployees', 'inactiveEmployees',
        'attendanceIssues', 'monthlyLeaves', 'attendanceData', 'recentActivities',
        'upcomingHolidays', 'currentMonthHolidays' // Add these
    ));


}

    /**
     * Get Recent Activities from all modules - FIXED VERSION
     */
    private function getRecentActivities($limit = 10)
    {
        $activities = [];

        try {
            // 1. Recent Sales
            $recentSales = Sale::latest('sale_date')->take(5)->get();

            foreach($recentSales as $sale) {
                $userName = 'System';
                if ($sale->created_by) {
                    $user = User::find($sale->created_by);
                    $userName = $user ? $user->name : 'System';
                }

                $customerName = $sale->customer ? $sale->customer->name : 'Walk-in Customer';

                $activities[] = [
                    'id' => 'sale_' . $sale->id,
                    'type' => 'sales',
                    'icon' => '💰',
                    'title' => 'New Sale',
                    'description' => $userName . ' created a sale of ₹' . number_format($sale->grand_total, 2) . ' for ' . $customerName,
                    'time' => $sale->created_at->diffForHumans(),
                    'user' => $userName,
                    'color' => 'green',
                    'created_at' => $sale->created_at
                ];
            }

            // 2. Recent Purchases
            if (class_exists('App\Models\Purchase')) {
                $recentPurchases = Purchase::latest('purchase_date')->take(5)->get();

                foreach($recentPurchases as $purchase) {
                    $userName = 'System';
                    if ($purchase->user_id) {
                        $user = User::find($purchase->user_id);
                        $userName = $user ? $user->name : 'System';
                    }

                    $supplierName = $purchase->supplier_name ?? 'Supplier';

                    $activities[] = [
                        'id' => 'purchase_' . $purchase->id,
                        'type' => 'purchases',
                        'icon' => '📦',
                        'title' => 'New Purchase',
                        'description' => $userName . ' created a purchase of ₹' . number_format($purchase->grand_total, 2) . ' from ' . $supplierName,
                        'time' => $purchase->created_at->diffForHumans(),
                        'user' => $userName,
                        'color' => 'purple',
                        'created_at' => $purchase->created_at
                    ];
                }
            }

            // 3. Recent Shipments
            if (class_exists('App\Models\Shipment')) {
                $recentShipments = Shipment::latest()->take(5)->get();

                foreach($recentShipments as $shipment) {
                    $userName = 'System';
                    if ($shipment->created_by) {
                        $user = User::find($shipment->created_by);
                        $userName = $user ? $user->name : 'System';
                    }

                    $statusIcon = [
                        'pending' => '⏳',
                        'picked' => '📦',
                        'in_transit' => '🚚',
                        'out_for_delivery' => '🚀',
                        'delivered' => '✅',
                        'failed' => '❌'
                    ];

                    $activities[] = [
                        'id' => 'shipment_' . $shipment->id,
                        'type' => 'shipments',
                        'icon' => $statusIcon[$shipment->status] ?? '📦',
                        'title' => 'Shipment ' . ucfirst(str_replace('_', ' ', $shipment->status)),
                        'description' => 'Shipment #' . $shipment->shipment_number . ' to ' . ($shipment->city ?? 'Destination'),
                        'time' => $shipment->created_at->diffForHumans(),
                        'user' => $userName,
                        'color' => $shipment->status == 'delivered' ? 'green' : ($shipment->status == 'failed' ? 'red' : 'blue'),
                        'created_at' => $shipment->created_at
                    ];
                }
            }

            // 4. Recent Attendance
            $recentAttendance = Attendance::with('employee')
                ->latest('attendance_date')
                ->take(5)
                ->get();

            foreach($recentAttendance as $attendance) {
                $statusIcons = [
                    'present' => '✅',
                    'Present' => '✅',
                    'absent' => '❌',
                    'Absent' => '❌',
                    'late' => '⏰',
                    'Late' => '⏰',
                    'Half Day' => '⚡'
                ];

                $statusColors = [
                    'present' => 'green',
                    'Present' => 'green',
                    'absent' => 'red',
                    'Absent' => 'red',
                    'late' => 'orange',
                    'Late' => 'orange',
                    'Half Day' => 'purple'
                ];

                $employeeName = $attendance->employee->name ?? 'Unknown Employee';

                $activities[] = [
                    'id' => 'attendance_' . $attendance->id,
                    'type' => 'attendance',
                    'icon' => $statusIcons[$attendance->status] ?? '👤',
                    'title' => 'Attendance Marked',
                    'description' => $employeeName . ' marked as ' . ucfirst($attendance->status),
                    'time' => $attendance->created_at->diffForHumans(),
                    'user' => 'HR System',
                    'color' => $statusColors[$attendance->status] ?? 'blue',
                    'created_at' => $attendance->created_at
                ];
            }

            // 5. New Employees
            $newEmployees = Employee::latest('created_at')->take(3)->get();

            foreach($newEmployees as $employee) {
                $activities[] = [
                    'id' => 'employee_' . $employee->id,
                    'type' => 'employees',
                    'icon' => '👤',
                    'title' => 'New Employee Added',
                    'description' => $employee->name . ' joined as ' . ($employee->position ?? 'Employee') . ' in ' . ($employee->department ?? 'General'),
                    'time' => $employee->created_at->diffForHumans(),
                    'user' => 'HR',
                    'color' => 'blue',
                    'created_at' => $employee->created_at
                ];
            }

            // 6. Low Stock Products
            $lowStockProducts = Product::where('quantity', '<=', 10)
                ->latest('updated_at')
                ->take(3)
                ->get();

            foreach($lowStockProducts as $product) {
                $activities[] = [
                    'id' => 'product_' . $product->id,
                    'type' => 'products',
                    'icon' => '⚠️',
                    'title' => 'Low Stock Alert',
                    'description' => $product->name . ' has only ' . $product->quantity . ' units left (Minimum: 10)',
                    'time' => $product->updated_at->diffForHumans(),
                    'user' => 'System',
                    'color' => 'red',
                    'created_at' => $product->updated_at
                ];
            }

            // 7. Recent Leave Requests
            if (class_exists('App\Models\Leave')) {
                $recentLeaves = Leave::with('employee')
                    ->latest('created_at')
                    ->take(3)
                    ->get();

                foreach($recentLeaves as $leave) {
                    $employeeName = $leave->employee->name ?? 'Employee';
                    $fromDate = $leave->from_date ? Carbon::parse($leave->from_date)->format('d M') : 'N/A';
                    $toDate = $leave->to_date ? Carbon::parse($leave->to_date)->format('d M') : 'N/A';

                    $activities[] = [
                        'id' => 'leave_' . $leave->id,
                        'type' => 'leaves',
                        'icon' => '🏖️',
                        'title' => 'Leave ' . ucfirst($leave->status),
                        'description' => $employeeName . ' requested leave from ' . $fromDate . ' to ' . $toDate,
                        'time' => $leave->created_at->diffForHumans(),
                        'user' => $employeeName,
                        'color' => $leave->status == 'pending' ? 'orange' : ($leave->status == 'approved' ? 'green' : 'red'),
                        'created_at' => $leave->created_at
                    ];
                }
            }

            // 8. Recent Payments
            if (class_exists('App\Models\Payment')) {
                $recentPayments = Payment::with(['sale', 'customer'])
                    ->latest('created_at')
                    ->take(3)
                    ->get();

                foreach($recentPayments as $payment) {
                    $customerName = $payment->customer->name ?? 'Customer';

                    $activities[] = [
                        'id' => 'payment_' . $payment->id,
                        'type' => 'payments',
                        'icon' => '💳',
                        'title' => 'Payment Received',
                        'description' => 'Payment of ₹' . number_format($payment->amount, 2) . ' received from ' . $customerName . ' via ' . ucfirst($payment->method),
                        'time' => $payment->created_at->diffForHumans(),
                        'user' => 'System',
                        'color' => 'green',
                        'created_at' => $payment->created_at
                    ];
                }
            }

            // Sort by created_at descending (newest first)
            usort($activities, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            // Return limited number of activities
            return array_slice($activities, 0, $limit);

        } catch (\Exception $e) {
            Log::error('Error fetching recent activities: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Debug function to check database data
     */
    public function debugActivities()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $sales = Sale::latest()->take(10)->get();
        $salesData = [];
        foreach($sales as $sale) {
            $salesData[] = [
                'id' => $sale->id,
                'invoice' => $sale->invoice_no,
                'amount' => $sale->grand_total,
                'created_by' => $sale->created_by,
                'user_name' => $sale->created_by ? (User::find($sale->created_by)?->name ?? 'NULL') : 'NULL'
            ];
        }

        $purchases = Purchase::latest()->take(10)->get();
        $purchasesData = [];
        foreach($purchases as $purchase) {
            $purchasesData[] = [
                'id' => $purchase->id,
                'invoice' => $purchase->invoice_number,
                'amount' => $purchase->grand_total,
                'user_id' => $purchase->user_id,
                'user_name' => $purchase->user_id ? (User::find($purchase->user_id)?->name ?? 'NULL') : 'NULL',
                'supplier_name' => $purchase->supplier_name ?? 'NULL'
            ];
        }

        $shipments = Shipment::latest()->take(10)->get();
        $shipmentsData = [];
        foreach($shipments as $shipment) {
            $shipmentsData[] = [
                'id' => $shipment->id,
                'shipment_number' => $shipment->shipment_number,
                'status' => $shipment->status,
                'created_by' => $shipment->created_by,
                'user_name' => $shipment->created_by ? (User::find($shipment->created_by)?->name ?? 'NULL') : 'NULL'
            ];
        }

        return response()->json([
            'sales_count' => Sale::count(),
            'sales_with_created_by' => Sale::whereNotNull('created_by')->count(),
            'purchases_count' => Purchase::count(),
            'purchases_with_user_id' => Purchase::whereNotNull('user_id')->count(),
            'shipments_count' => Shipment::count(),
            'shipments_with_created_by' => Shipment::whereNotNull('created_by')->count(),
            'users_count' => User::count(),
            'employees_count' => Employee::count(),
            'attendance_count' => Attendance::count(),
            'recent_sales' => $salesData,
            'recent_purchases' => $purchasesData,
            'recent_shipments' => $shipmentsData,
            'sample_activity' => $this->getRecentActivities(5)
        ]);
    }

    /**
     * Get AI Prediction from Python API
     */
    private function getAIPredictionFromAPI()
    {
        try {
            $response = Http::timeout(5)->get('http://localhost:5001/api/sales-forecast');

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('AI API returned non-success response', [
                'status' => $response->status()
            ]);

        } catch (\Exception $e) {
            Log::error('AI API connection failed: ' . $e->getMessage());
        }

        $todayActual = Sale::whereDate('sale_date', today())->sum('grand_total') ?: 1000;

        return [
            'success' => false,
            'data' => [
                'future_labels' => collect(range(1, 15))->map(fn ($d) =>
                    Carbon::now()->addDays($d)->format('Y-m-d')
                )->toArray(),
                'future_data' => collect(range(1, 15))->map(fn () => $todayActual * 1.05)->toArray(),
                'analysis' => [
                    'today_actual' => $todayActual,
                    'tomorrow_prediction' => $todayActual * 1.05,
                    'trend' => 'stable',
                    'percentage_change' => 5,
                    'confidence_score' => 75,
                ]
            ]
        ];
    }

    /**
     * Get weekly attendance trend
     */
    private function getWeeklyAttendanceTrend()
    {
        $days = [];
        $presentData = [];
        $absentData = [];
        $lateData = [];
        $halfDayData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('D');

            $presentData[] = Attendance::whereDate('attendance_date', $date)
                ->where(function($query) {
                    $query->where('status', 'present')->orWhere('status', 'Present');
                })->count();

            $absentData[] = Attendance::whereDate('attendance_date', $date)
                ->where(function($query) {
                    $query->where('status', 'absent')->orWhere('status', 'Absent');
                })->count();

            $lateData[] = Attendance::whereDate('attendance_date', $date)
                ->where(function($query) {
                    $query->where('status', 'late')->orWhere('status', 'Late');
                })->count();

            $halfDayData[] = Attendance::whereDate('attendance_date', $date)
                ->where('status', 'Half Day')->count();
        }

        return [
            'labels' => $days,
            'present' => $presentData,
            'absent' => $absentData,
            'late' => $lateData,
            'half_day' => $halfDayData,
            'present_total' => array_sum($presentData),
            'absent_total' => array_sum($absentData),
            'late_total' => array_sum($lateData),
            'half_day_total' => array_sum($halfDayData)
        ];
    }

    /**
     * Get employee turnover rate
     */
    private function getEmployeeTurnover()
    {
        $totalEmployees = Employee::count();
        if ($totalEmployees == 0) return 0;

        if (Schema::hasColumn('employees', 'deleted_at')) {
            $left = Employee::onlyTrashed()
                ->whereMonth('deleted_at', now()->month)
                ->whereYear('deleted_at', now()->year)->count();
        } elseif (Schema::hasColumn('employees', 'is_active')) {
            $left = Employee::where('is_active', false)
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)->count();
        } elseif (Schema::hasColumn('employees', 'status')) {
            $left = Employee::where('status', 'inactive')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)->count();
        } else {
            $left = 0;
        }

        return round(($left / $totalEmployees) * 100, 2);
    }

    /**
     * Get employees on leave today (simplified)
     */
    private function getOnLeaveTodaySimple()
    {
        try {
            if (!Schema::hasTable('leaves')) return 0;

            if (Schema::hasColumn('leaves', 'from_date') && Schema::hasColumn('leaves', 'to_date')) {
                return Leave::where('status', 'approved')
                    ->whereDate('from_date', '<=', today())
                    ->whereDate('to_date', '>=', today())->count();
            }

            if (Schema::hasColumn('leaves', 'leave_date')) {
                return Leave::where('status', 'approved')
                    ->whereDate('leave_date', today())->count();
            }

            if (Schema::hasColumn('leaves', 'date')) {
                return Leave::where('status', 'approved')
                    ->whereDate('date', today())->count();
            }

            return Leave::where('status', 'approved')
                ->whereDate('created_at', today())->count();

        } catch (\Exception $e) {
            Log::error('Error in getOnLeaveTodaySimple: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Common dashboard data for quick access
     */
    public function getCommonStats()
    {
        return [
            'total_products' => Product::count(),
            'total_revenue' => Sale::sum('grand_total'),
            'today_sales' => Sale::whereDate('sale_date', today())->sum('grand_total'),
            'total_employees' => Employee::count(),
            'date' => now()->format('l, F j, Y')
        ];
    }

    /**
     * Get HR analytics data (for AJAX requests)
     */
    public function getHrAnalytics(Request $request)
    {
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $totalEmployees = Employee::count();
        $presentToday = Attendance::whereDate('attendance_date', today())
            ->where(function($query) {
                $query->where('status', 'present')->orWhere('status', 'Present');
            })->count();

        $absentToday = Attendance::whereDate('attendance_date', today())
            ->where(function($query) {
                $query->where('status', 'absent')->orWhere('status', 'Absent');
            })->count();

        $lateToday = Attendance::whereDate('attendance_date', today())
            ->where(function($query) {
                $query->where('status', 'late')->orWhere('status', 'Late');
            })->count();

        $halfDayToday = Attendance::whereDate('attendance_date', today())
            ->where('status', 'Half Day')->count();

        $markedCount = $presentToday + $absentToday + $lateToday + $halfDayToday;
        $notMarkedToday = max(0, $totalEmployees - $markedCount);
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $onLeaveToday = $this->getOnLeaveTodaySimple();

        return response()->json([
            'total_employees' => $totalEmployees,
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
            'late_today' => $lateToday,
            'half_day_today' => $halfDayToday,
            'not_marked_today' => $notMarkedToday,
            'pending_leaves' => $pendingLeaves,
            'on_leave_today' => $onLeaveToday,
            'attendance_percentage' => $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Calculate overall attendance percentage
     */
    private function calculateAttendancePercentage()
    {
        $totalEmployees = Employee::count();
        if ($totalEmployees == 0) return 0;

        $presentToday = Attendance::whereDate('attendance_date', today())
            ->where(function($query) {
                $query->where('status', 'present')->orWhere('status', 'Present');
            })->count();

        return round(($presentToday / $totalEmployees) * 100, 1);
    }

    /**
     * Get department-wise employee count
     */
    public function getDepartmentStats()
    {
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json(
            Employee::select('department', DB::raw('count(*) as count'))
                ->whereNotNull('department')
                ->groupBy('department')
                ->orderBy('count', 'desc')
                ->get()
        );
    }

    /**
     * Get monthly attendance summary
     */
    public function getMonthlyAttendance(Request $request)
    {
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        return response()->json(Attendance::select(
                DB::raw('DATE(attendance_date) as date'),
                DB::raw('SUM(CASE WHEN status IN ("present", "Present") THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status IN ("absent", "Absent") THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN status IN ("late", "Late") THEN 1 ELSE 0 END) as late'),
                DB::raw('SUM(CASE WHEN status = "Half Day" THEN 1 ELSE 0 END) as half_day')
            )
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
        );
    }

    /**
     * HR Attendance Marking Page
     */
    public function hrAttendanceMark()
    {
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $employees = Employee::where('status', 'active')->get();
        $todayAttendance = Attendance::whereDate('attendance_date', today())
            ->with('employee')
            ->get()
            ->keyBy('employee_id');

        $totalEmployees = Employee::count();
        $presentToday = Attendance::whereDate('attendance_date', today())
            ->where(function($query) {
                $query->where('status', 'present')->orWhere('status', 'Present');
            })->count();

        $absentToday = Attendance::whereDate('attendance_date', today())
            ->where(function($query) {
                $query->where('status', 'absent')->orWhere('status', 'Absent');
            })->count();

        $lateToday = Attendance::whereDate('attendance_date', today())
            ->where(function($query) {
                $query->where('status', 'late')->orWhere('status', 'Late');
            })->count();

        $halfDayToday = Attendance::whereDate('attendance_date', today())
            ->where('status', 'Half Day')->count();

        $markedCount = $presentToday + $absentToday + $lateToday + $halfDayToday;
        $notMarkedToday = max(0, $totalEmployees - $markedCount);

        $attendanceCounts = [
            'present' => $presentToday,
            'absent' => $absentToday,
            'late' => $lateToday,
            'half_day' => $halfDayToday,
            'not_marked' => $notMarkedToday,
            'pending' => $notMarkedToday
        ];

        return view('attendance.hr_mark', compact('employees', 'todayAttendance', 'attendanceCounts'));
    }

    /**
     * Submit HR Bulk Attendance
     */
    public function hrAttendanceBulk(Request $request)
    {
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.employee_id' => 'required|exists:employees,id',
            'attendances.*.status' => 'required|in:present,absent,late,half_day,leave',
            'attendances.*.remarks' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['attendances'] as $attendanceData) {
                $status = ucfirst($attendanceData['status']);
                Attendance::updateOrCreate(
                    ['employee_id' => $attendanceData['employee_id'], 'attendance_date' => today()],
                    [
                        'status' => $status,
                        'remarks' => $attendanceData['remarks'] ?? null,
                        'check_in' => $attendanceData['status'] == 'present' ? now() : null,
                        'marked_by' => Auth::id()
                    ]
                );
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Attendance marked successfully', 'count' => count($validated['attendances'])]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to mark attendance', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get HR Dashboard Quick Stats
     */
    public function getHrQuickStats()
    {
        if (!Auth::check() || (Auth::user()->role !== 'admin' && Auth::user()->role !== 'hr')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $totalEmployees = Employee::count();
        $presentToday = Attendance::whereDate('attendance_date', today())
            ->where(function($query) {
                $query->where('status', 'present')->orWhere('status', 'Present');
            })->count();

        $absentToday = Attendance::whereDate('attendance_date', today())
            ->where(function($query) {
                $query->where('status', 'absent')->orWhere('status', 'Absent');
            })->count();

        $lateToday = Attendance::whereDate('attendance_date', today())
            ->where(function($query) {
                $query->where('status', 'late')->orWhere('status', 'Late');
            })->count();

        $halfDayToday = Attendance::whereDate('attendance_date', today())
            ->where('status', 'Half Day')->count();

        $markedCount = $presentToday + $absentToday + $lateToday + $halfDayToday;
        $notMarkedToday = max(0, $totalEmployees - $markedCount);
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $onLeaveToday = $this->getOnLeaveTodaySimple();

        return response()->json([
            'total_employees' => $totalEmployees,
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
            'late_today' => $lateToday,
            'half_day_today' => $halfDayToday,
            'not_marked_today' => $notMarkedToday,
            'pending_leaves' => $pendingLeaves,
            'on_leave_today' => $onLeaveToday,
            'attendance_percentage' => $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0,
            'updated_at' => now()->format('h:i A')
        ]);
    }

    /**
     * Get Recent Activities (API endpoint for AJAX refresh)
     */
    public function getRecentActivitiesApi()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json(['success' => true, 'activities' => $this->getRecentActivities(10)]);
    }
}
