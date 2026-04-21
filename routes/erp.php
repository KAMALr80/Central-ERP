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
use App\Http\Controllers\ReportController;

// Payments & Wallet
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Controllers\Payments\EmiPaymentController;
use App\Http\Controllers\CustomerWalletController;

// Auth
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
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
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserAccessController;
use App\Http\Controllers\Admin\HrApprovalController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\TenantController;




/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// Root redirect for tenants
Route::get('/', function () {
    return redirect()->route('dashboard');
});

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
// Auth routes removed - handled by auth.php in central domain

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
        Route::post('/bulk-delete', [CustomerController::class, 'bulkDelete'])->name('bulk-delete');
        Route::get('/{customer}/sales', [CustomerController::class, 'sales'])->name('sales');
        Route::get('/{customer}/payments', [CustomerController::class, 'payments'])->name('payments');
        Route::get('/{customer}/wallet', [CustomerWalletController::class, 'customerReport'])->name('wallet');
        Route::get('/{id}/details', [CustomerController::class, 'getDetails'])->name('details');
    });

    /* ================= WALLET ================= */
    Route::middleware('permission:manage_customer_wallet')->prefix('wallet')->name('wallet.')->group(function () {
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
    Route::middleware('permission:manage_payments')->prefix('payments')->name('payments.')->group(function () {
        Route::get('/create/{sale}', [PaymentController::class, 'create'])->name('create');
        Route::post('/store', [PaymentController::class, 'store'])->name('store');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
        Route::delete('/bulk/{saleId}', [PaymentController::class, 'deleteBulk'])->name('delete-bulk');
        Route::delete('/customer/{customerId}/delete-all', [PaymentController::class, 'destroyAll'])->name('delete-all');
    });

    /* ================= EMI ================= */
    Route::middleware('permission:manage_emi')->prefix('emi')->name('emi.')->group(function () {
        Route::get('/{emi}', [EmiPaymentController::class, 'show'])->name('show');
        Route::post('/{emi}/pay', [EmiPaymentController::class, 'pay'])->name('pay');
    });

    /* ================= REPORTS ================= */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales')->middleware('permission:view_sales_reports');
        Route::get('/sales/excel', [ReportController::class, 'exportSalesCSV'])->name('sales.excel')->middleware('permission:export_sales');
        Route::get('/sales/pdf', [ReportController::class, 'exportSalesPDF'])->name('sales.pdf')->middleware('permission:export_sales');
        Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases')->middleware('permission:view_purchase_reports');
        Route::get('/purchases/excel', [ReportController::class, 'exportPurchasesCSV'])->name('purchases.excel')->middleware('permission:view_purchase_reports');
        Route::get('/purchases/pdf', [ReportController::class, 'exportPurchasesPDF'])->name('purchases.pdf')->middleware('permission:view_purchase_reports');
        Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance')->middleware('permission:view_attendance_reports');
        Route::get('/attendance/excel', [ReportController::class, 'exportAttendanceCSV'])->name('attendance.excel')->middleware('permission:export_attendance');
        Route::get('/attendance/pdf', [ReportController::class, 'exportAttendancePDF'])->name('attendance.pdf')->middleware('permission:export_attendance');
    });

    /* ================= AI ================= */
    Route::middleware('permission:view_dashboard')->prefix('ai')->name('ai.')->group(function () {
        Route::get('/sales-prediction', [AiController::class, 'salesPrediction'])->name('sales.prediction');
        Route::post('/ask', [AiAssistantController::class, 'ask'])->name('ask');
    });

    /* ================= EMPLOYEES (Based on Permission) ================= */
    Route::middleware('permission:view_employees')->prefix('employees')->name('employees.')->group(function () {
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

    /* ================= INVENTORY (Based on Permission) ================= */
    Route::middleware('permission:view_inventory')->prefix('inventory')->name('inventory.')->group(function () {
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
    Route::middleware('permission:view_employee_reports')->prefix('hr')->name('hr.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'hrDashboard'])->name('dashboard');
        Route::get('/analytics', [DashboardController::class, 'getHrAnalytics'])->name('analytics');
        Route::get('/department-stats', [DashboardController::class, 'getDepartmentStats'])->name('department.stats');
        Route::get('/monthly-attendance', [DashboardController::class, 'getMonthlyAttendance'])->name('monthly.attendance');
    });

    /* ================= LEAVE MANAGEMENT (Based on Permission) ================= */
    Route::middleware('permission:view_leaves')->prefix('admin/leaves')->name('leaves.')->group(function () {
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

    /* ================= ATTENDANCE MANAGEMENT (Based on Permission) ================= */
    Route::middleware('permission:view_attendance')->prefix('admin/attendance')->name('attendance.')->group(function () {
        Route::get('/manage', [AttendanceController::class, 'manage'])->name('manage');
        Route::get('/mark', [AttendanceController::class, 'markAttendance'])->name('mark');
        Route::post('/bulk', [AttendanceController::class, 'bulkAttendance'])->name('bulk');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
        Route::get('/edit/{id}', [AttendanceController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AttendanceController::class, 'update'])->name('update');
        Route::delete('/{id}', [AttendanceController::class, 'destroy'])->name('destroy');
        Route::get('/export', [AttendanceController::class, 'export'])->name('export');
    });

    /* ================= STAFF & AGENT APPROVAL ================= */
    Route::middleware('permission:view_approvals')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/staff-approval', [StaffApprovalController::class, 'index'])->name('staff.approval');
        Route::post('/staff-approve/{id}', [StaffApprovalController::class, 'approve'])->name('staff.approve')->middleware('permission:manage_approvals');
        Route::post('/staff-reject/{id}', [StaffApprovalController::class, 'reject'])->name('staff.reject')->middleware('permission:manage_approvals');

        Route::get('/hr-approval', [HrApprovalController::class, 'index'])->name('hr.approval');
        Route::post('/hr-approve/{id}', [HrApprovalController::class, 'approve'])->name('hr.approve')->middleware('permission:manage_approvals');
        Route::post('/hr-reject/{id}', [HrApprovalController::class, 'reject'])->name('hr.reject')->middleware('permission:manage_approvals');

        Route::get('/agent-approvals', [AgentApprovalController::class, 'index'])->name('agent.approvals');
        Route::post('/agent-approve/{id}', [AgentApprovalController::class, 'approve'])->name('agent.approve')->middleware('permission:manage_approvals');
        Route::post('/agent-reject/{id}', [AgentApprovalController::class, 'reject'])->name('agent.reject')->middleware('permission:manage_approvals');

        /* ================= ROLE MANAGEMENT (Based on Permission) ================= */
        Route::middleware('permission:manage_roles')->group(function() {
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });
    });

    /* ================= USER ACCESS & LOGIN DETAILS (Admin Only) ================= */
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/user-access', [UserAccessController::class, 'index'])->name('users.access');
        Route::get('/user-access/stop-impersonating', [UserAccessController::class, 'stopImpersonating'])->name('users.access.stop');
        Route::get('/user-access/{id}', [UserAccessController::class, 'show'])->name('users.access.show')->where('id', '[0-9]+');
        Route::post('/user-access/impersonate/{id}', [UserAccessController::class, 'impersonate'])->name('users.access.impersonate')->where('id', '[0-9]+');

        // Audit Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
        Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    });

    /* ================= LOGISTICS ROUTES ================= */
    Route::middleware('permission:view_logistics')->prefix('logistics')->name('logistics.')->group(function () {
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

// Agent Routes (Authenticated - All agent actions)
Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:delivery_agent'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AgentDashboardController::class, 'index'])->name('dashboard');
    Route::post('/status', [AgentDashboardController::class, 'updateStatus'])->name('status');

    // ========== DELIVERIES COLLECTION ==========
    Route::prefix('deliveries')->name('deliveries.')->group(function () {
        Route::get('/active', [AgentDeliveryController::class, 'active'])->name('active');
        Route::get('/history', [AgentDeliveryController::class, 'history'])->name('history');
        Route::get('/assigned', [AgentDeliveryController::class, 'assigned'])->name('assigned');
        Route::post('/bulk-start', [AgentDeliveryController::class, 'bulkStart'])->name('bulk-start');
        Route::get('/statistics', [AgentDeliveryController::class, 'statistics'])->name('statistics');
    });

    // ========== SINGLE DELIVERY ACTIONS ==========
    Route::prefix('delivery')->name('delivery.')->group(function () {
        Route::get('/{shipmentId}', [AgentDeliveryController::class, 'show'])->name('show');
        Route::post('/{shipmentId}/start', [AgentDeliveryController::class, 'start'])->name('start');
        Route::post('/{shipmentId}/complete', [AgentDeliveryController::class, 'complete'])->name('complete');
        Route::post('/{shipmentId}/status', [AgentDeliveryController::class, 'updateStatus'])->name('update-status');
        Route::get('/{shipmentId}/details', [AgentDeliveryController::class, 'details'])->name('details');
    });

    // ========== TRACKING (LIVE TRACKING WITH DUAL MARKERS) ==========
    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('/{shipmentId}', [AgentTrackingController::class, 'live'])->name('live');
        Route::get('/{shipmentId}/map', [AgentTrackingController::class, 'map'])->name('map');
        Route::post('/location', [AgentTrackingController::class, 'updateLocation'])->name('location.update');
    });

    // ========== PERFORMANCE ==========
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [AgentPerformanceController::class, 'index'])->name('index');
        Route::get('/weekly', [AgentPerformanceController::class, 'weekly'])->name('weekly');
        Route::get('/monthly', [AgentPerformanceController::class, 'monthly'])->name('monthly');
        Route::get('/export', [AgentPerformanceController::class, 'export'])->name('export');
    });

    // Backward compatibility route alias
    Route::get('/performance', [AgentPerformanceController::class, 'index'])->name('performance');

    // ========== EARNINGS ==========
    Route::prefix('earnings')->name('earnings.')->group(function () {
        Route::get('/', [AgentEarningsController::class, 'index'])->name('index');
        Route::get('/details', [AgentEarningsController::class, 'details'])->name('details');
        Route::get('/export', [AgentEarningsController::class, 'export'])->name('export');
        Route::get('/invoice', [AgentEarningsController::class, 'invoice'])->name('invoice');
    });

    // ========== PROFILE ==========
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [AgentProfileController::class, 'edit'])->name('edit');
        Route::put('/', [AgentProfileController::class, 'update'])->name('update');
        Route::post('/location', [AgentProfileController::class, 'updateLocation'])->name('update-location');
    });

    // ========== SUPPORT ==========
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [AgentSupportController::class, 'index'])->name('index');
        Route::post('/send', [AgentSupportController::class, 'send'])->name('send');
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN TRACKING & REAL-TIME ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Real-time agent tracking
    Route::get('/tracking/agents', [App\Http\Controllers\Admin\TrackingController::class, 'index'])->name('tracking.agents');
    Route::get('/tracking/active-agents', [App\Http\Controllers\Admin\TrackingController::class, 'activeAgents'])->name('tracking.active-agents');
    Route::get('/tracking/agent/{agentId}', [App\Http\Controllers\Admin\TrackingController::class, 'agentLocation'])->name('tracking.agent');
});

/*
|--------------------------------------------------------------------------
| ADMIN API ROUTES (Authenticated)
|--------------------------------------------------------------------------
*/
Route::prefix('api/admin')->name('api.admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/agents/locations', [App\Http\Controllers\Admin\TrackingController::class, 'getAllAgentsLocations']);
    Route::get('/agent/{agentId}/location', [App\Http\Controllers\Admin\TrackingController::class, 'getAgentLiveLocation']);
    Route::get('/agent/{agentId}/route', [App\Http\Controllers\Admin\TrackingController::class, 'getAgentRouteHistory']);
});



Route::prefix('agent')->name('agent.')->middleware(['auth', 'role:delivery_agent'])->group(function () {

    // ========== DASHBOARD & STATUS ==========
    Route::get('dashboard', [App\Http\Controllers\Agent\DashboardController::class, 'index'])->name('dashboard');
    Route::post('status', [App\Http\Controllers\Agent\DashboardController::class, 'updateStatus'])->name('status');
    Route::get('stats', [App\Http\Controllers\Agent\DashboardController::class, 'getStats'])->name('stats');
    Route::get('location-history', [App\Http\Controllers\Agent\DashboardController::class, 'getLocationHistory'])->name('location.history');

    // ========== LOCATION & TRACKING ==========
    Route::get('location/current', [App\Http\Controllers\Agent\TrackingController::class, 'getCurrentLocation'])->name('location.current');
    Route::post('location/update', [App\Http\Controllers\Agent\TrackingController::class, 'updateLocation'])->name('location.update');

    // ========== DELIVERIES & SHIPMENTS ==========
    Route::get('deliveries/check-new', [App\Http\Controllers\Agent\DeliveryController::class, 'checkNewAssignments'])->name('deliveries.check-new');
    Route::post('deliveries/check-new', [App\Http\Controllers\Agent\DeliveryController::class, 'checkNewAssignments']); // No name for POST
    Route::get('deliveries/statistics', [App\Http\Controllers\Agent\DeliveryController::class, 'statistics'])->name('deliveries.statistics');
    Route::get('deliveries/active', [App\Http\Controllers\Agent\DeliveryController::class, 'active'])->name('deliveries.active');

    // ========== EARNINGS ==========
    Route::prefix('earnings')->name('earnings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Agent\EarningsController::class, 'index'])->name('index');
        Route::get('export', [App\Http\Controllers\Agent\EarningsController::class, 'export'])->name('export');
        Route::get('invoice', [App\Http\Controllers\Agent\EarningsController::class, 'invoice'])->name('invoice');
        Route::get('details', [App\Http\Controllers\Agent\EarningsController::class, 'details'])->name('details');
    });
    Route::get('earnings', [App\Http\Controllers\Agent\EarningsController::class, 'index'])->name('earnings'); // Legacy

    // ========== PROFILE ==========
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [App\Http\Controllers\Agent\ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [App\Http\Controllers\Agent\ProfileController::class, 'update'])->name('update');
        Route::post('location', [App\Http\Controllers\Agent\ProfileController::class, 'updateLocation'])->name('update-location');
    });
    Route::get('profile', [App\Http\Controllers\Agent\ProfileController::class, 'edit'])->name('profile'); // Legacy
    Route::put('profile', [App\Http\Controllers\Agent\ProfileController::class, 'update'])->name('profile.update'); // Legacy

    // ========== PERFORMANCE ==========
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/', [App\Http\Controllers\Agent\PerformanceController::class, 'index'])->name('index');
        Route::get('weekly', [App\Http\Controllers\Agent\PerformanceController::class, 'weekly'])->name('weekly');
        Route::get('monthly', [App\Http\Controllers\Agent\PerformanceController::class, 'monthly'])->name('monthly');
        Route::get('export', [App\Http\Controllers\Agent\PerformanceController::class, 'export'])->name('export');
    });
    Route::get('performance', [App\Http\Controllers\Agent\PerformanceController::class, 'index'])->name('performance'); // Legacy

    // ========== SUPPORT ==========
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [App\Http\Controllers\Agent\SupportController::class, 'index'])->name('index');
        Route::post('send', [App\Http\Controllers\Agent\SupportController::class, 'send'])->name('send');
    });
    Route::get('support', [App\Http\Controllers\Agent\SupportController::class, 'index'])->name('support'); // Legacy

});


Route::middleware('auth')->prefix('reports')->name('reports.')->group(function () {

    // Sales Reports
    Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
    Route::get('/sales/excel', [ReportController::class, 'exportSalesCSV'])->name('sales.excel');
    Route::get('/sales/pdf', [ReportController::class, 'exportSalesPDF'])->name('sales.pdf');

    // Customers Reports
    Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
    Route::get('/customers/excel', [ReportController::class, 'exportCustomersCSV'])->name('customers.excel');
    Route::get('/customers/pdf', [ReportController::class, 'exportCustomersPDF'])->name('customers.pdf');

    // Inventory Reports
    Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
    Route::get('/inventory/excel', [ReportController::class, 'exportInventoryCSV'])->name('inventory.excel');
    Route::get('/inventory/pdf', [ReportController::class, 'exportInventoryPDF'])->name('inventory.pdf');

    // Logistics Reports
    Route::get('/logistics', [ReportController::class, 'logistics'])->name('logistics');
    Route::get('/logistics/excel', [ReportController::class, 'exportLogisticsCSV'])->name('logistics.excel');
    Route::get('/logistics/pdf', [ReportController::class, 'exportLogisticsPDF'])->name('logistics.pdf');

    // Employee Reports
    Route::get('/employees', [ReportController::class, 'employees'])->name('employees');
    Route::get('/employees/excel', [ReportController::class, 'exportEmployeesCSV'])->name('employees.excel');
    Route::get('/employees/pdf', [ReportController::class, 'exportEmployeesPDF'])->name('employees.pdf');

    // Purchase Reports
    Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
    Route::get('/purchases/excel', [ReportController::class, 'exportPurchasesCSV'])->name('purchases.excel');
    Route::get('/purchases/pdf', [ReportController::class, 'exportPurchasesPDF'])->name('purchases.pdf');

    // Attendance Reports
    Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
    Route::get('/attendance/excel', [ReportController::class, 'exportAttendanceCSV'])->name('attendance.excel');
    Route::get('/attendance/pdf', [ReportController::class, 'exportAttendancePDF'])->name('attendance.pdf');

    // Financial Summary
    Route::middleware('admin')->group(function () {
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/financial/excel', [ReportController::class, 'exportFinancialCSV'])->name('financial.excel');
        Route::get('/financial/pdf', [ReportController::class, 'exportFinancialPDF'])->name('financial.pdf');
    });
});


// Customer Reports Routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/customers', [ReportController::class, 'customerReport'])->name('customers');
    Route::get('/customers/export/csv', [ReportController::class, 'exportCustomerReportCSV'])->name('customers.excel');
    Route::get('/customers/export/pdf', [ReportController::class, 'exportCustomerReportPDF'])->name('customers.pdf');
    Route::get('/customer-sales', [ReportController::class, 'customerSalesReport'])->name('customer.sales');
    Route::get('/customer-sales/export/csv', [ReportController::class, 'exportCustomerSalesReportCSV'])->name('customer.sales.excel');
});



Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
    Route::get('/sales/export/csv', [ReportController::class, 'exportSalesCSV'])->name('sales.excel');
    // ... other routes
});



Route::prefix('reports')->name('reports.')->group(function () {
    // Sales Report
    Route::get('/sales', [ReportController::class, 'salesReport'])->name('sales');
    Route::get('/sales/export/csv', [ReportController::class, 'exportSalesReportCSV'])->name('sales.excel');
    Route::get('/sales/export/pdf', [ReportController::class, 'exportSalesReportPDF'])->name('sales.pdf');

    // Customer Report
    Route::get('/customers', [ReportController::class, 'customerReport'])->name('customers');
    Route::get('/customers/export/csv', [ReportController::class, 'exportCustomerReportCSV'])->name('customers.excel');
    Route::get('/customers/export/pdf', [ReportController::class, 'exportCustomerReportPDF'])->name('customers.pdf');

    // Customer Sales Report
    Route::get('/customer-sales', [ReportController::class, 'customerSalesReport'])->name('customer.sales');
    Route::get('/customer-sales/export/csv', [ReportController::class, 'exportCustomerSalesReportCSV'])->name('customer.sales.excel');
    Route::get('/customer-sales/export/pdf', [ReportController::class, 'exportCustomerSalesReportPDF'])->name('customer.sales.pdf');

    // Other reports
    Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
    Route::get('/inventory/export/csv', [ReportController::class, 'exportInventoryCSV'])->name('inventory.excel');
    Route::get('/inventory/export/pdf', [ReportController::class, 'exportInventoryPDF'])->name('inventory.pdf');

    Route::get('/logistics', [ReportController::class, 'logistics'])->name('logistics');
    Route::get('/logistics/export/csv', [ReportController::class, 'exportLogisticsCSV'])->name('logistics.excel');
    Route::get('/logistics/export/pdf', [ReportController::class, 'exportLogisticsPDF'])->name('logistics.pdf');

    Route::get('/employees', [ReportController::class, 'employees'])->name('employees');
    Route::get('/employees/export/csv', [ReportController::class, 'exportEmployeesCSV'])->name('employees.excel');
    Route::get('/employees/export/pdf', [ReportController::class, 'exportEmployeesPDF'])->name('employees.pdf');

    Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
    Route::get('/purchases/export/csv', [ReportController::class, 'exportPurchasesCSV'])->name('purchases.excel');
    Route::get('/purchases/export/pdf', [ReportController::class, 'exportPurchasesPDF'])->name('purchases.pdf');

    Route::get('/attendance', [ReportController::class, 'attendance'])->name('attendance');
    Route::get('/attendance/export/csv', [ReportController::class, 'exportAttendanceCSV'])->name('attendance.excel');
    Route::get('/attendance/export/pdf', [ReportController::class, 'exportAttendancePDF'])->name('attendance.pdf');

    Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
    Route::get('/financial/export/csv', [ReportController::class, 'exportFinancialCSV'])->name('financial.excel');
    Route::get('/financial/export/pdf', [ReportController::class, 'exportFinancialPDF'])->name('financial.pdf');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/staff/dashboard', [App\Http\Controllers\DashboardController::class, 'staffDashboard'])->name('staff.dashboard');
});



// Agent Dashboard Route
Route::prefix('agent')->name('agent.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Agent\DashboardController::class, 'index'])->name('dashboard');
});

// HR Dashboard Route
Route::prefix('hr')->name('hr.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'hrDashboard'])->name('dashboard');
});


Route::prefix('leaves')->name('leaves.')->group(function () {
    Route::get('/', [LeaveController::class, 'index'])->name('index');
    Route::get('/my', [LeaveController::class, 'myLeaves'])->name('my');
    Route::get('/create', [LeaveController::class, 'create'])->name('create');

    // Add this line for the simple apply form
    Route::post('/apply', [LeaveController::class, 'apply'])->name('apply');  // 👈 ADD THIS LINE

    Route::post('/store', [LeaveController::class, 'store'])->name('store');
    Route::get('/{leave}', [LeaveController::class, 'show'])->name('show')->where('leave', '[0-9]+');
    Route::post('/{leave}/cancel', [LeaveController::class, 'cancel'])->name('cancel')->where('leave', '[0-9]+');
    Route::get('/{leave}/print', [LeaveController::class, 'printLeave'])->name('print')->where('leave', '[0-9]+');
    Route::get('/{leave}/pdf', [LeaveController::class, 'pdf'])->name('pdf')->where('leave', '[0-9]+');
    Route::get('/{leave}/download', [LeaveController::class, 'download'])->name('download')->where('leave', '[0-9]+');
});

Route::post('/employee/{id}/send-email', [EmployeeController::class, 'sendEmail'])
    ->name('employee.send.email');



use App\Http\Controllers\Auth\TwoFactorController;

// ==================== PROFILE ROUTES ====================
Route::middleware(['auth'])->group(function () {
    // Profile Management
      Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
 Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.index');
 Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');

    // Password Management
    Route::get('/profile/change-password', [ProfileController::class, 'showChangePassword'])->name('profile.change-password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Security & 2FA
    Route::get('/profile/security', [ProfileController::class, 'showSecurity'])->name('profile.security');

    // Activity Log
    Route::get('/profile/activity', [ProfileController::class, 'activityLog'])->name('profile.activity');
});

// ==================== TWO FACTOR AUTHENTICATION ROUTES ====================
Route::middleware(['auth'])->group(function () {
    // 2FA Setup & Management
    Route::get('/2fa/setup', [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::get('/2fa/recovery/generate', [TwoFactorController::class, 'generateRecoveryCodes'])->name('2fa.recovery.generate');
});

// 2FA Verification Routes (no auth middleware - these are accessed before login)
Route::middleware(['web'])->group(function () {
    Route::get('/2fa/verify', [TwoFactorController::class, 'showVerify'])->name('2fa.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify.post');
    Route::get('/2fa/recovery', [TwoFactorController::class, 'showRecovery'])->name('2fa.recovery');
    Route::post('/2fa/recovery', [TwoFactorController::class, 'verifyRecovery'])->name('2fa.recovery.verify');
});
