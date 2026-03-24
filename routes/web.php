<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\Purchase;
use App\Models\Product;

/* ================= CONTROLLERS ================= */
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

// Employee & HR
use App\Http\Controllers\Employees\EmployeeController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\Admin\StaffApprovalController;

// Inventory
use App\Http\Controllers\Inventory\InventoryController;

// Sales & Purchases
use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\Purchases\PurchaseController;
use App\Http\Controllers\Customers\CustomerController;
use App\Http\Controllers\Reports\ReportController;

// Payments & Wallet
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Payments\EmiPaymentController;
use App\Http\Controllers\CustomerWalletController;

// Auth
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\RegisterOtpController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\PasswordController;

// AI
use App\Http\Controllers\AiController;
use App\Http\Controllers\AiAssistantController;

// Logistics
use App\Http\Controllers\Logistics\LogisticsController;
use App\Http\Controllers\Logistics\MapController;
use App\Http\Controllers\Logistics\ServiceAreaController;
use App\Http\Controllers\Logistics\RouteController;
use App\Http\Controllers\Logistics\UpdateShipmentController;
use App\Http\Controllers\Logistics\AgentController;
use App\Http\Controllers\Logistics\ShipmentsController;

// API Controllers
use App\Http\Controllers\Api\AgentApiController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\ShipmentApiController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\GeocodingController;
use App\Http\Controllers\Api\RouteOptimizationController;

// Agent Controllers
use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Agent\DeliveryController as AgentDeliveryController;
use App\Http\Controllers\Agent\TrackingController as AgentTrackingController;
use App\Http\Controllers\Agent\PerformanceController as AgentPerformanceController;
use App\Http\Controllers\Agent\EarningsController as AgentEarningsController;
use App\Http\Controllers\Agent\ProfileController as AgentProfileController;
use App\Http\Controllers\Agent\SupportController as AgentSupportController;

// Admin Controllers
use App\Http\Controllers\Admin\AgentApprovalController;
use App\Http\Controllers\Admin\AdminStaffController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => redirect('/login'));

// Test Mail Route
Route::get('/test-mail', function () {
    Mail::raw('Hello 👋 Mailtrap test successful!', function ($message) {
        $message->to('test@demo.com')->subject('Mailtrap Test');
    });
    return 'Mail sent successfully!';
});

// Brevo Test Mail
Route::get('/test-mail-brevo', function () {
    $apiKey = config('app.brevo_key');
    $response = Http::withHeaders([
        'accept' => 'application/json',
        'api-key' => $apiKey,
        'content-type' => 'application/json'
    ])->post('https://api.brevo.com/v3/smtp/email', [
        "sender" => [
            "name" => "INVOZA",
            "email" => "221240116017.it@gmail.com"
        ],
        "to" => [
            ["email" => "221240116017.it@gmail.com"]
        ],
        "subject" => "Test Mail",
        "htmlContent" => "<h1>OTP: 123456</h1>"
    ]);
    return $response->body();
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (Public)
|--------------------------------------------------------------------------
*/
// Login Routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Registration Routes
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Registration OTP Routes
Route::get('/register-otp', [RegisterOtpController::class, 'show'])->name('register.otp');
Route::post('/register-otp/verify', [RegisterOtpController::class, 'verify'])->name('register.otp.verify');
Route::post('/register-otp/resend', [RegisterOtpController::class, 'resend'])->name('register.otp.resend');

// Login OTP Routes
Route::get('/otp-verify', [OtpController::class, 'show'])->name('otp.verify');
Route::post('/otp-verify', [OtpController::class, 'verify'])->name('otp.verify.post');
Route::post('/otp-resend', [OtpController::class, 'resend'])->name('otp.resend');

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');

// Public Customer AJAX Routes
Route::get('/customers/ajax-search', [CustomerController::class, 'ajaxSearch'])->name('customers.ajax.search');
Route::post('/customers/store-ajax', [CustomerController::class, 'storeAjax'])->name('customers.store.ajax');

// Public Tracking Route
Route::get('/track/{trackingNumber}', [MapController::class, 'trackShipment'])->name('public.track');

// Test Relationship Route
Route::get('/test-relationship', function() {
    try {
        $productCount = Product::count();
        $purchaseCount = Purchase::count();
        $purchase = Purchase::with('product')->first();

        $results = [
            'product_model_exists' => 'Yes',
            'total_products' => $productCount,
            'purchase_model_exists' => 'Yes',
            'total_purchases' => $purchaseCount,
        ];

        if ($purchase) {
            $results['sample_purchase'] = [
                'id' => $purchase->id,
                'invoice' => $purchase->invoice_number,
                'product_loaded' => $purchase->product ? 'Yes' : 'No',
                'product_name' => $purchase->product ? $purchase->product->name : 'No product found',
                'product_id' => $purchase->product_id
            ];
        } else {
            $results['message'] = 'No purchases found. Create one first.';
        }

        return response()->json($results);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
    }
});

// Email Routes
Route::post('/sales/send-invoice', [SalesController::class, 'sendInvoice'])->name('sales.send-invoice');
Route::post('/sales/bulk-send-invoice', [SalesController::class, 'bulkSendInvoice'])->name('sales.bulk-send-invoice');
Route::post('/sales/send-due-reminder', [SalesController::class, 'sendDueReminder'])->name('sales.send-due-reminder');
Route::post('/sales/bulk-send-due-reminders', [SalesController::class, 'bulkSendDueReminders'])->name('sales.bulk-send-due-reminders');

/*
|--------------------------------------------------------------------------
| Auth Routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /* ================= DASHBOARD ================= */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* ================= PROFILE ================= */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Email Verification Routes
    Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')->name('verification.send');

    // Password Confirmation Routes
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Password Update Route
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    // Logout Route
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    /* ================= ATTENDANCE ROUTES ================= */
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', function () {
            if (Auth::user()->role === 'staff') {
                return redirect()->route('attendance.my');
            }
            if (in_array(Auth::user()->role, ['admin', 'hr'])) {
                return redirect()->route('attendance.manage');
            }
            abort(403);
        });
        Route::get('/my', [AttendanceController::class, 'myAttendance'])->name('my');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('checkin');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('checkout');
    });

    /* ================= LEAVE ROUTES ================= */
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/my', [LeaveController::class, 'myLeaves'])->name('my');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::post('/store', [LeaveController::class, 'store'])->name('store');
        Route::get('/{leave}', [LeaveController::class, 'show'])->name('show')->where('leave', '[0-9]+');
        Route::post('/{leave}/cancel', [LeaveController::class, 'cancel'])->name('cancel')->where('leave', '[0-9]+');
        Route::get('/{leave}/print', [LeaveController::class, 'printLeave'])->name('print')->where('leave', '[0-9]+');
        Route::get('/{leave}/pdf', [LeaveController::class, 'pdf'])->name('pdf')->where('leave', '[0-9]+');
        Route::get('/{leave}/download', [LeaveController::class, 'download'])->name('download')->where('leave', '[0-9]+');
    });

    /* ================= CUSTOMERS ================= */
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::get('/{customer}/sales', [CustomerController::class, 'sales'])->name('sales');
        Route::get('/{customer}/payments', [CustomerController::class, 'payments'])->name('payments');
        Route::get('/{customer}/wallet', [CustomerWalletController::class, 'customerReport'])->name('wallet');
        Route::get('/{id}/details', [CustomerController::class, 'getDetails'])->name('details');
    });

    /* ================= WALLET ================= */
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::post('/add', [CustomerWalletController::class, 'addAdvance'])->name('add');
        Route::post('/use', [CustomerWalletController::class, 'useAdvance'])->name('use');
        Route::delete('/{wallet}', [CustomerWalletController::class, 'destroy'])->name('delete');
        Route::get('/delete-impact/{wallet}', [CustomerWalletController::class, 'deleteImpact'])->name('delete.impact');
        Route::get('/history/{customer}', [CustomerWalletController::class, 'getHistory'])->name('history');
        Route::get('/report', [CustomerWalletController::class, 'report'])->name('report');
        Route::post('/recalculate/{customer}', [CustomerWalletController::class, 'recalculate'])->name('recalculate');
    });

    /* ================= SALES ================= */
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/create', [SalesController::class, 'create'])->name('create');
        Route::post('/', [SalesController::class, 'store'])->name('store');
        Route::get('/datatable', [SalesController::class, 'datatable'])->name('datatable');
        Route::get('/stats', [SalesController::class, 'stats'])->name('stats');
        Route::get('/{sale}', [SalesController::class, 'show'])->name('show');
        Route::get('/{sale}/invoice', [SalesController::class, 'invoice'])->name('invoice');
        Route::get('/{sale}/print', [SalesController::class, 'print'])->name('print');
        Route::get('/{sale}/edit', [SalesController::class, 'edit'])->name('edit');
        Route::put('/{sale}', [SalesController::class, 'update'])->name('update');
        Route::delete('/{sale}', [SalesController::class, 'destroy'])->name('destroy');
        Route::post('/{sale}/mark-due', [PaymentController::class, 'markAsDue'])->name('mark-due');
        Route::delete('/{saleId}/delete-with-payments', [SalesController::class, 'deleteWithPayments'])->name('delete-with-payments');
        Route::get('/{id}/delete-impact', [SalesController::class, 'deleteImpact'])->name('delete-impact');
        Route::get('/{sale}/create-shipment', [SalesController::class, 'createShipment'])->name('sales.create-shipment');
    });

    /* ================= PURCHASES ================= */
    Route::resource('purchases', PurchaseController::class);

    /* ================= PAYMENTS ================= */
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/create/{sale}', [PaymentController::class, 'create'])->name('create');
        Route::post('/store', [PaymentController::class, 'store'])->name('store');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
        Route::delete('/bulk/{saleId}', [PaymentController::class, 'deleteBulk'])->name('delete-bulk');
        Route::delete('/customer/{customerId}/delete-all', [PaymentController::class, 'destroyAll'])->name('delete-all');
    });

    /* ================= EMI ================= */
    Route::prefix('emi')->name('emi.')->group(function () {
        Route::get('/{emi}', [EmiPaymentController::class, 'show'])->name('show');
        Route::post('/{emi}/pay', [EmiPaymentController::class, 'pay'])->name('pay');
    });

    /* ================= REPORTS ================= */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/sales/excel', [ReportController::class, 'exportSalesCSV'])->name('sales.excel');
        Route::get('/sales/pdf', [ReportController::class, 'exportSalesPDF'])->name('sales.pdf');
        Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
        Route::get('/purchases/excel', [ReportController::class, 'exportPurchasesCSV'])->name('purchases.excel');
        Route::get('/purchases/pdf', [ReportController::class, 'exportPurchasesPDF'])->name('purchases.pdf');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/excel', [ReportController::class, 'exportAttendanceCSV'])->name('attendance.excel');
        Route::get('/attendance/pdf', [ReportController::class, 'exportAttendancePDF'])->name('attendance.pdf');
    });

    /* ================= AI ================= */
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/sales-prediction', [AiController::class, 'salesPrediction'])->name('sales.prediction');
        Route::post('/ask', [AiAssistantController::class, 'ask'])->name('ask');
    });

    /* ================= EMPLOYEES (Admin/HR Only) ================= */
    Route::middleware('hr')->prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::get('/search', [EmployeeController::class, 'search'])->name('search');
        Route::post('/{employee}/send-email', [EmployeeController::class, 'sendEmail'])->name('send.email');
    });

    /* ================= INVENTORY (Admin Only) ================= */
    Route::middleware('admin')->prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{inventory}', [InventoryController::class, 'show'])->name('show');
        Route::get('/{inventory}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{inventory}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{inventory}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::get('/search', [InventoryController::class, 'ajaxSearch'])->name('ajax.search');
        Route::post('/{id}/update-quantity', [InventoryController::class, 'updateQuantity'])->name('update.quantity');
        Route::post('/bulk-delete', [InventoryController::class, 'bulkDelete'])->name('bulk.delete');
        Route::post('/barcode-preview', [InventoryController::class, 'barcodePreview'])->name('barcode.preview');
        Route::post('/barcode-download', [InventoryController::class, 'barcodeDownload'])->name('barcode.download');
    });

    /* ================= HR DASHBOARD ================= */
    Route::middleware('hr')->prefix('hr')->name('hr.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'hrDashboard'])->name('dashboard');
        Route::get('/analytics', [DashboardController::class, 'getHrAnalytics'])->name('analytics');
        Route::get('/department-stats', [DashboardController::class, 'getDepartmentStats'])->name('department.stats');
        Route::get('/monthly-attendance', [DashboardController::class, 'getMonthlyAttendance'])->name('monthly.attendance');
    });

    /* ================= LEAVE MANAGEMENT (Admin/HR Only) ================= */
    Route::middleware('hr')->prefix('admin/leaves')->name('leaves.')->group(function () {
        Route::get('/manage', [LeaveController::class, 'manage'])->name('manage');
        Route::get('/{leave}', [LeaveController::class, 'adminShow'])->name('admin-show')->where('leave', '[0-9]+');
        Route::post('/{leave}/approve', [LeaveController::class, 'approve'])->name('approve')->where('leave', '[0-9]+');
        Route::post('/{leave}/reject', [LeaveController::class, 'reject'])->name('reject')->where('leave', '[0-9]+');
        Route::post('/bulk-approve', [LeaveController::class, 'bulkApprove'])->name('bulk.approve');
        Route::post('/bulk-reject', [LeaveController::class, 'bulkReject'])->name('bulk.reject');
        Route::get('/calendar-data', [LeaveController::class, 'calendarData'])->name('calendar.data');
        Route::get('/export', [LeaveController::class, 'export'])->name('export');
        Route::get('/balance/{employeeId?}', [LeaveController::class, 'getBalance'])->name('balance');
    });

    /* ================= ATTENDANCE MANAGEMENT (Admin/HR Only) ================= */
    Route::middleware('hr')->prefix('admin/attendance')->name('attendance.')->group(function () {
        Route::get('/manage', [AttendanceController::class, 'manage'])->name('manage');
        Route::get('/mark', [AttendanceController::class, 'markAttendance'])->name('mark');
        Route::post('/bulk', [AttendanceController::class, 'bulkAttendance'])->name('bulk');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
        Route::get('/edit/{id}', [AttendanceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AttendanceController::class, 'update'])->name('update');
        Route::delete('/{id}', [AttendanceController::class, 'destroy'])->name('destroy');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
    });

    /* ================= STAFF & AGENT APPROVAL (Admin Only) ================= */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/staff-approval', [StaffApprovalController::class, 'index'])->name('staff.approval');
        Route::post('/staff-approve/{id}', [StaffApprovalController::class, 'approve'])->name('staff.approve');

        Route::get('/agent-approvals', [AgentApprovalController::class, 'index'])->name('agent.approvals');
        Route::post('/agent-approve/{id}', [AgentApprovalController::class, 'approve'])->name('agent.approve');
        Route::post('/agent-reject/{id}', [AgentApprovalController::class, 'reject'])->name('agent.reject');
    });

    /* ================= LOGISTICS ROUTES ================= */
    Route::prefix('logistics')->name('logistics.')->group(function () {
        // Shipments
        Route::get('/shipments', [LogisticsController::class, 'index'])->name('shipments.index');
        Route::get('/shipments/create', [LogisticsController::class, 'create'])->name('shipments.create');
        Route::post('/shipments', [LogisticsController::class, 'store'])->name('shipments.store');
        Route::get('/shipments/{id}', [LogisticsController::class, 'show'])->name('shipments.show');
        Route::get('/shipments/{id}/edit', [LogisticsController::class, 'edit'])->name('shipments.edit');
        Route::put('/shipments/{id}', [LogisticsController::class, 'update'])->name('shipments.update');
        Route::delete('/shipments/{id}', [LogisticsController::class, 'destroy'])->name('shipments.destroy');

        // Shipment Actions
        Route::post('/shipments/{id}/status', [LogisticsController::class, 'updateStatus'])->name('shipments.status');
        Route::post('/shipments/{id}/assign-agent', [LogisticsController::class, 'assignAgent'])->name('shipments.assign-agent');
        Route::post('/shipments/{id}/remove-agent', [LogisticsController::class, 'removeAgent'])->name('shipments.remove-agent');
        Route::post('/shipments/{id}/upload-pod', [LogisticsController::class, 'uploadPOD'])->name('shipments.upload-pod');
        Route::get('/shipments/{id}/track', [LogisticsController::class, 'track'])->name('shipments.track');

        // Bulk Operations
        Route::post('/shipments/bulk-create', [LogisticsController::class, 'bulkCreate'])->name('shipments.bulk-create');
        Route::get('/shipments/bulk/create', [LogisticsController::class, 'bulkCreateForm'])->name('shipments.bulk.create');

        // Tracking
        Route::get('/track/{trackingNumber}', [LogisticsController::class, 'trackWeb'])->name('track');
        Route::get('/live-track/{trackingNumber}', [LogisticsController::class, 'liveTrack'])->name('live-track');
        Route::get('/track-web/{trackingNumber}', [LogisticsController::class, 'trackWeb'])->name('track-web');

        // Delivery Agents
        Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
        Route::get('/agents/create', [AgentController::class, 'create'])->name('agents.create');
        Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
        Route::get('/agents/{id}', [AgentController::class, 'show'])->name('agents.show');
        Route::get('/agents/{id}/edit', [AgentController::class, 'edit'])->name('agents.edit');
        Route::put('/agents/{id}', [AgentController::class, 'update'])->name('agents.update');
        Route::delete('/agents/{id}', [AgentController::class, 'destroy'])->name('agents.destroy');
        Route::post('/agents/{id}/status', [AgentController::class, 'updateStatus'])->name('agents.update-status');
        Route::post('/agents/{id}/upload-documents', [AgentController::class, 'uploadDocuments'])->name('agents.upload-documents');
        Route::get('/agents/{id}/performance', [AgentController::class, 'performanceReport'])->name('agents.performance');
        Route::get('/agents/{id}/location', [AgentController::class, 'getLocation'])->name('agents.location');

        // Service Areas
        Route::get('/service-areas', [ServiceAreaController::class, 'index'])->name('service-areas');
        Route::get('/service-areas/heatmap', [ServiceAreaController::class, 'heatmapData'])->name('service-areas.heatmap');

        // Route Planner
        Route::get('/route-planner', [RouteController::class, 'index'])->name('route-planner');
        Route::post('/route-calculate', [RouteController::class, 'calculate'])->name('route-calculate');
        Route::post('/route-assign', [RouteController::class, 'assign'])->name('route-assign');

        // Reports
        Route::get('/reports', [LogisticsController::class, 'reports'])->name('reports');

        // Maps
        Route::get('/map/{trackingNumber}', [MapController::class, 'trackShipment'])->name('map');
        Route::get('/agents-map', [LogisticsController::class, 'getAgentsForMap'])->name('agents-map');

        // API Routes
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/available-agents', [LogisticsController::class, 'getAvailableAgents'])->name('available-agents');
            Route::get('/agents/{agentId}/location', [LogisticsController::class, 'getAgentLocation'])->name('agents.location');
            Route::post('/shipments/{shipment}/location', [LogisticsController::class, 'updateShipmentLocation'])->name('shipments.location');
            Route::get('/track/{trackingNumber}', [ShipmentsController::class, 'track'])->name('track');
            Route::get('/agents/map', [AgentController::class, 'getAgentsForMap'])->name('agents.map');
            Route::get('/shipments/stats', [ShipmentsController::class, 'stats'])->name('shipments.stats');
        });
    });
});

/*
|--------------------------------------------------------------------------
| API ROUTES (No Auth - Public)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    // Test route
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'Logistics API is working',
            'timestamp' => now()->toDateTimeString()
        ]);
    });

    // App login route
    Route::post('/app/login', [AgentApiController::class, 'appLogin']);

    // Tracking
    Route::prefix('track')->group(function () {
        Route::get('/{trackingNumber}', [TrackingController::class, 'track']);
        Route::get('/shipment/{shipmentNumber}', [TrackingController::class, 'trackByShipment']);
        Route::get('/{trackingNumber}/timeline', [TrackingController::class, 'timeline']);
        Route::get('/{trackingNumber}/location', [TrackingController::class, 'currentLocation']);
    });

    // Shipments
    Route::prefix('shipments')->group(function () {
        Route::get('/', [ShipmentApiController::class, 'index']);
        Route::get('/{id}', [ShipmentApiController::class, 'show']);
        Route::post('/', [ShipmentApiController::class, 'store']);
        Route::put('/{id}', [ShipmentApiController::class, 'update']);
        Route::delete('/{id}', [ShipmentApiController::class, 'destroy']);
        Route::post('/{id}/status', [ShipmentApiController::class, 'updateStatus']);
        Route::post('/{id}/assign-agent', [ShipmentApiController::class, 'assignAgent']);
        Route::post('/{id}/live-location', [ShipmentApiController::class, 'updateLiveLocation']);
        Route::post('/{id}/upload-pod', [ShipmentApiController::class, 'uploadPOD']);
        Route::get('/{id}/pod', [ShipmentApiController::class, 'getPOD']);
        Route::post('/{id}/cancel', [ShipmentApiController::class, 'cancel']);
        Route::get('/{id}/tracking', [ShipmentApiController::class, 'trackingHistory']);
    });

    // Agents
    Route::prefix('agents')->group(function () {
        Route::get('/', [AgentApiController::class, 'index']);
        Route::get('/{id}', [AgentApiController::class, 'show']);
        Route::post('/', [AgentApiController::class, 'store']);
        Route::put('/{id}', [AgentApiController::class, 'update']);
        Route::delete('/{id}', [AgentApiController::class, 'destroy']);
        Route::post('/{id}/status', [AgentApiController::class, 'updateStatus']);
        Route::post('/{id}/location', [AgentApiController::class, 'updateLocation']);
        Route::get('/{id}/location', [AgentApiController::class, 'getLocation']);
        Route::get('/{id}/performance', [AgentApiController::class, 'performance']);
        Route::get('/{id}/shipments', [AgentApiController::class, 'assignedShipments']);
        Route::get('/map/all', [AgentApiController::class, 'getAllForMap']);
        Route::get('/nearby', [AgentApiController::class, 'findNearby']);
    });

    // Locations
    Route::prefix('locations')->group(function () {
        Route::get('/search', [LocationController::class, 'search']);
        Route::get('/reverse', [LocationController::class, 'reverse']);
        Route::post('/route', [LocationController::class, 'calculateRoute']);
        Route::post('/distance-matrix', [LocationController::class, 'distanceMatrix']);
        Route::post('/validate', [LocationController::class, 'validateAddress']);
        Route::get('/place/{placeId}', [LocationController::class, 'placeDetails']);
    });

    // Routes
    Route::prefix('routes')->group(function () {
        Route::post('/optimize', [RouteOptimizationController::class, 'optimize']);
        Route::post('/calculate', [RouteOptimizationController::class, 'calculate']);
        Route::post('/distance-matrix', [RouteOptimizationController::class, 'distanceMatrix']);
        Route::post('/assign', [RouteOptimizationController::class, 'assign']);
    });

    // Public
    Route::prefix('public')->group(function () {
        Route::get('/track/{trackingNumber}', [TrackingController::class, 'publicTrack']);
        Route::get('/agent/{id}', [AgentApiController::class, 'publicInfo']);
        Route::get('/timeline/{trackingNumber}', [TrackingController::class, 'publicTimeline']);
    });
});

/*
|--------------------------------------------------------------------------
| AGENT REGISTRATION & ROUTES
|--------------------------------------------------------------------------
*/
// Agent Registration (Public)
Route::prefix('agent')->name('agent.')->group(function () {
    Route::get('/register', [App\Http\Controllers\Agent\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Agent\Auth\RegisterController::class, 'register']);
});

// Agent Routes (Authenticated)
Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:delivery_agent'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
    Route::post('/status', [AgentDashboardController::class, 'updateStatus'])->name('status');

    // ========== DELIVERIES COLLECTION ==========
    Route::prefix('deliveries')->name('deliveries.')->group(function () {
        Route::get('active', [AgentDeliveryController::class, 'active'])->name('active');
        Route::get('history', [AgentDeliveryController::class, 'history'])->name('history');
        Route::get('assigned', [AgentDeliveryController::class, 'assigned'])->name('assigned');
        Route::post('bulk-start', [AgentDeliveryController::class, 'bulkStart'])->name('bulk-start');
        Route::get('statistics', [AgentDeliveryController::class, 'statistics'])->name('statistics');
    });

    // ========== SINGLE DELIVERY ACTIONS ==========
    Route::prefix('delivery')->name('delivery.')->group(function () {
        Route::get('{shipmentId}', [AgentDeliveryController::class, 'show'])->name('show');
        Route::post('{shipmentId}/start', [AgentDeliveryController::class, 'start'])->name('start');
        Route::post('{shipmentId}/complete', [AgentDeliveryController::class, 'complete'])->name('complete');
        Route::post('{shipmentId}/status', [AgentDeliveryController::class, 'updateStatus'])->name('update-status');
        Route::get('{shipmentId}/details', [AgentDeliveryController::class, 'details'])->name('details');
    });

    // ========== TRACKING ==========
    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('{shipmentId}', [AgentTrackingController::class, 'live'])->name('live');
        Route::get('{shipmentId}/map', [AgentTrackingController::class, 'map'])->name('map');
    });
    Route::post('tracking/location', [AgentTrackingController::class, 'updateLocation'])->name('location.update');

    // ========== PERFORMANCE ==========
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [AgentPerformanceController::class, 'index'])->name('index');
        Route::get('weekly', [AgentPerformanceController::class, 'weekly'])->name('weekly');
        Route::get('monthly', [AgentPerformanceController::class, 'monthly'])->name('monthly');
    });

    // ========== EARNINGS ==========
    Route::prefix('earnings')->name('earnings.')->group(function () {
        Route::get('/', [AgentEarningsController::class, 'index'])->name('index');
        Route::get('details', [AgentEarningsController::class, 'details'])->name('details');
    });

    // ========== PROFILE ==========
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [AgentProfileController::class, 'edit'])->name('edit');
        Route::put('/', [AgentProfileController::class, 'update'])->name('update');
        Route::post('location', [AgentProfileController::class, 'updateLocation'])->name('update-location');
    });

    // ========== SUPPORT ==========
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [AgentSupportController::class, 'index'])->name('index');
        Route::post('send', [AgentSupportController::class, 'send'])->name('send');
    });

    // ========== LEGACY/BACKWARD COMPATIBILITY ==========
    Route::get('tracking/{shipmentId}', [AgentTrackingController::class, 'live'])->name('tracking');
    Route::get('performance', [AgentPerformanceController::class, 'index'])->name('performance');
    Route::get('earnings', [AgentEarningsController::class, 'index'])->name('earnings');
    Route::get('profile', [AgentProfileController::class, 'edit'])->name('profile');
    Route::put('profile', [AgentProfileController::class, 'update'])->name('profile.update');
    Route::get('support', [AgentSupportController::class, 'index'])->name('support');
    Route::post('support', [AgentSupportController::class, 'send'])->name('support.send');
});

// Add this inside the admin middleware group
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // ... existing routes ...

    // Add tracking routes
    Route::get('/tracking/agents', [App\Http\Controllers\Admin\TrackingController::class, 'index'])->name('tracking.agents');
    Route::get('/tracking/agent/{agentId}', [App\Http\Controllers\Admin\TrackingController::class, 'agentLocation'])->name('tracking.agent');
});

Route::get('/agents/{id}/location', [AgentApiController::class, 'getLocation']);
Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:delivery_agent'])->group(function () {
    // ... existing routes ...

    Route::get('performance/export', [App\Http\Controllers\Agent\PerformanceController::class, 'export'])->name('performance.export');
});


Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:delivery_agent'])->group(function () {
    // ... existing routes ...

    Route::get('earnings/export', [App\Http\Controllers\Agent\EarningsController::class, 'export'])->name('earnings.export');
    Route::get('earnings/invoice', [App\Http\Controllers\Agent\EarningsController::class, 'invoice'])->name('earnings.invoice');
});
Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:delivery_agent'])->group(function () {
    // ... existing routes ...

    Route::get('earnings/export', [App\Http\Controllers\Agent\EarningsController::class, 'export'])->name('earnings.export');
    Route::get('earnings/invoice', [App\Http\Controllers\Agent\EarningsController::class, 'invoice'])->name('earnings.invoice');
    Route::get('earnings/details', [App\Http\Controllers\Agent\EarningsController::class, 'details'])->name('earnings.details');
});

// routes/web.php - Add these inside the agent routes group

Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:delivery_agent'])->group(function () {
    // ... existing routes ...

    // ========== EARNINGS ROUTES ==========
    Route::prefix('earnings')->name('earnings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Agent\EarningsController::class, 'index'])->name('index');
        Route::get('export', [App\Http\Controllers\Agent\EarningsController::class, 'export'])->name('export');
        Route::get('invoice', [App\Http\Controllers\Agent\EarningsController::class, 'invoice'])->name('invoice');
        Route::get('details', [App\Http\Controllers\Agent\EarningsController::class, 'details'])->name('details');
    });

    // Legacy route for backward compatibility
    Route::get('earnings', [App\Http\Controllers\Agent\EarningsController::class, 'index'])->name('earnings');
});


Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // ... existing routes ...

    Route::get('/active-agents', [App\Http\Controllers\Admin\TrackingController::class, 'activeAgents'])->name('active.agents');
    Route::get('/tracking/agent/{agentId}', [App\Http\Controllers\Admin\TrackingController::class, 'agentLocation'])->name('tracking.agent');
});

// Admin Tracking Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/tracking/agents', [App\Http\Controllers\Admin\TrackingController::class, 'index'])->name('tracking.agents');
    Route::get('/tracking/active-agents', [App\Http\Controllers\Admin\TrackingController::class, 'activeAgents'])->name('tracking.active-agents');
    Route::get('/tracking/agent/{agentId}', [App\Http\Controllers\Admin\TrackingController::class, 'agentLocation'])->name('tracking.agent');
});

// API Routes for Admin
Route::prefix('api/admin')->name('api.admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/agents/locations', [App\Http\Controllers\Admin\TrackingController::class, 'getAllAgentsLocations']);
    Route::get('/agent/{agentId}/location', [App\Http\Controllers\Admin\TrackingController::class, 'getAgentLiveLocation']);
    Route::get('/agent/{agentId}/route', [App\Http\Controllers\Admin\TrackingController::class, 'getAgentRouteHistory']);
});


Route::prefix('logistics')->name('logistics.')->middleware(['auth'])->group(function () {

    // ========== ROUTE PLANNER ROUTES ==========
    Route::get('/route-planner', [App\Http\Controllers\Logistics\RouteController::class, 'index'])->name('route-planner');
    Route::post('/route/calculate', [App\Http\Controllers\Logistics\RouteController::class, 'calculate'])->name('route.calculate');
    Route::post('/route/assign', [App\Http\Controllers\Logistics\RouteController::class, 'assign'])->name('route.assign');
    Route::get('/route/optimize', [App\Http\Controllers\Logistics\RouteController::class, 'optimize'])->name('route.optimize');

    // ========== ADD THESE FOR COMPATIBILITY ==========
    Route::post('/route-calculate', [App\Http\Controllers\Logistics\RouteController::class, 'calculate'])->name('route-calculate');
    Route::post('/route-assign', [App\Http\Controllers\Logistics\RouteController::class, 'assign'])->name('route-assign');
});



Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:delivery_agent'])->group(function () {

    // ========== LOCATION ROUTES ==========
    Route::get('location/current', [App\Http\Controllers\Agent\TrackingController::class, 'getCurrentLocation'])->name('location.current');
    Route::post('location/update', [App\Http\Controllers\Agent\TrackingController::class, 'updateLocation'])->name('location.update');

    // ========== DELIVERIES ROUTES ==========
    Route::get('deliveries/check-new', [App\Http\Controllers\Agent\DeliveryController::class, 'checkNewAssignments'])->name('deliveries.check-new');
    Route::get('deliveries/statistics', [App\Http\Controllers\Agent\DeliveryController::class, 'statistics'])->name('deliveries.statistics');

    // ========== ACTIVE DELIVERIES ==========
    Route::get('deliveries/active', [App\Http\Controllers\Agent\DeliveryController::class, 'active'])->name('deliveries.active');

    // ... other existing routes ...
});

Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:delivery_agent'])->group(function () {
    // ... other routes ...

    Route::post('deliveries/check-new', [App\Http\Controllers\Agent\DeliveryController::class, 'checkNewAssignments'])->name('deliveries.check-new');
});
