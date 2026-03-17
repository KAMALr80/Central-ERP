<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\Purchase;
use App\Models\Product;
use App\Http\Controllers\Logistics\LogisticsController;
use App\Http\Controllers\Logistics\MapController;
use App\Http\Controllers\Logistics\ServiceAreaController;
use App\Http\Controllers\Logistics\RouteController;
use App\Http\Controllers\Logistics\UpdateShipmentController;
use App\Http\Controllers\Logistics\AgentController;

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

// ==================== AUTH ROUTES (Public) ====================

// Login Routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');

// Registration Routes
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.post');

// Registration OTP Routes
Route::get('/register-otp', [RegisterOtpController::class, 'show'])->name('register.otp.show');
Route::post('/register-otp/verify', [RegisterOtpController::class, 'verify'])->name('register.otp.verify');
Route::post('/register-otp/resend', [RegisterOtpController::class, 'resend'])->name('register.otp.resend');

// Login OTP Routes (Shared)
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

// Public Tracking Route (No Auth Required)
Route::get('/track/{trackingNumber}', [MapController::class, 'trackShipment'])->name('public.track');

/*
|--------------------------------------------------------------------------
| Auth Routes (Laravel Breeze) - KEEP THIS
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

    // Email Verification Routes (Authenticated)
    Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')->name('verification.send');

    // Password Confirmation Routes
    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])->name('password.confirm.post');

    // Password Update Route
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    // Logout Route
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    /* ================= ATTENDANCE ROUTES (Employee) ================= */
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

    /* ================= LEAVE ROUTES (Employee) ================= */
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

    /* ================= STAFF APPROVAL (Admin Only) ================= */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/staff-approval', [StaffApprovalController::class, 'index'])->name('staff.approval');
        Route::post('/staff-approval/{id}', [StaffApprovalController::class, 'approve'])->name('staff.approve');
    });

    /* ================= LOGISTICS & SHIPPING ROUTES (COMPLETE) ================= */
    Route::prefix('logistics')->name('logistics.')->group(function () {

        // ========== SHIPMENTS ==========
        Route::get('/shipments', [LogisticsController::class, 'index'])->name('shipments.index');
        Route::get('/shipments/create', [LogisticsController::class, 'create'])->name('shipments.create');
        Route::post('/shipments', [LogisticsController::class, 'store'])->name('shipments.store');
        Route::get('/shipments/{id}', [LogisticsController::class, 'show'])->name('shipments.show');
        Route::get('/shipments/{id}/edit', [UpdateShipmentController::class, 'edit'])->name('shipments.edit');
        Route::put('/shipments/{id}', [UpdateShipmentController::class, 'update'])->name('shipments.update');
        Route::post('/shipments/bulk/create', [LogisticsController::class, 'bulkCreate'])->name('shipments.bulk.create');

        // Shipment Actions
        Route::post('/shipments/{id}/status', [LogisticsController::class, 'updateStatus'])->name('shipments.status');
        Route::post('/shipments/{id}/assign-agent', [LogisticsController::class, 'assignAgent'])->name('shipments.assign-agent');
        Route::post('/shipments/{id}/upload-pod', [LogisticsController::class, 'uploadPOD'])->name('shipments.upload-pod');

        // Tracking (Public) - Already defined above
        Route::get('/track/{trackingNumber}', [LogisticsController::class, 'track'])->name('track');

        // API Routes for Delivery Boy
        Route::post('/api/shipments/{id}/location', [LogisticsController::class, 'updateLocation'])->name('api.update-location');

        // ========== DELIVERY AGENTS - COMPLETE CRUD ==========
        Route::get('/agents', [LogisticsController::class, 'agents'])->name('agents.index');
        Route::get('/agents/create', [AgentController::class, 'create'])->name('agents.create');
        Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
        Route::get('/agents/{id}', [AgentController::class, 'show'])->name('agents.show');
        Route::get('/agents/{id}/edit', [AgentController::class, 'edit'])->name('agents.edit');
        Route::put('/agents/{id}', [AgentController::class, 'update'])->name('agents.update');
        Route::delete('/agents/{id}', [AgentController::class, 'destroy'])->name('agents.destroy');
        Route::post('/agents/{id}/status', [AgentController::class, 'updateStatus'])->name('agents.update-status');
        Route::post('/agents/{id}/upload-documents', [AgentController::class, 'uploadDocuments'])->name('agents.upload-documents');
        Route::get('/agents/map/locations', [LogisticsController::class, 'getAgentsForMap'])->name('agents.map');
        Route::get('/agents/{id}/performance', [AgentController::class, 'performanceReport'])->name('agents.performance');

        // ========== REPORTS ==========
        Route::get('/reports', [LogisticsController::class, 'reports'])->name('reports');

        // ========== SERVICE AREAS ==========
        Route::get('/service-areas', [ServiceAreaController::class, 'index'])->name('service-areas');
        Route::get('/service-areas/heatmap', [ServiceAreaController::class, 'heatmapData'])->name('heatmap');

        // ========== ROUTE PLANNER ==========
        Route::get('/route-planner', [RouteController::class, 'index'])->name('route-planner');
        Route::post('/route/calculate', [RouteController::class, 'calculate'])->name('route.calculate');
        Route::post('/route/assign', [RouteController::class, 'assign'])->name('route.assign');

        // ========== MAPS ==========
        Route::get('/map/{trackingNumber}', [MapController::class, 'trackShipment'])->name('map');
    });
});

/* ================= TEST ROUTES (Remove in Production) ================= */
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

// Leave Routes (Additional - Already covered above, but keeping for compatibility)
Route::middleware(['auth'])->group(function () {
    // Staff routes
    Route::get('/leaves/my', [LeaveController::class, 'myLeaves'])->name('leaves.my');
    Route::post('/leaves/apply', [LeaveController::class, 'apply'])->name('leaves.apply');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{id}', [LeaveController::class, 'show'])->name('leaves.show');
    Route::post('/leaves/{id}/cancel', [LeaveController::class, 'cancel'])->name('leaves.cancel');

    // Admin routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/leaves/dashboard', [LeaveController::class, 'dashboard'])->name('leaves.dashboard');
        Route::get('/admin/leaves/manage', [LeaveController::class, 'manage'])->name('leaves.manage');
        Route::get('/admin/leaves/{id}', [LeaveController::class, 'adminShow'])->name('leaves.admin-show');
        Route::post('/admin/leaves/{id}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('/admin/leaves/{id}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    });
});

// Employee view (restricted to own leaves)
Route::get('/leaves/{id}', [LeaveController::class, 'show'])->name('leaves.show');

// Admin view (can view any leave)
Route::get('/admin/leaves/{id}', [LeaveController::class, 'adminShow'])->name('leaves.admin-show');

// Email routes
Route::post('/sales/send-invoice', [App\Http\Controllers\Sales\SalesController::class, 'sendInvoice'])->name('sales.send-invoice');
Route::post('/sales/bulk-send-invoice', [App\Http\Controllers\Sales\SalesController::class, 'bulkSendInvoice'])->name('sales.bulk-send-invoice');
Route::post('/sales/send-due-reminder', [App\Http\Controllers\Sales\SalesController::class, 'sendDueReminder'])->name('sales.send-due-reminder');
Route::post('/sales/bulk-send-due-reminders', [App\Http\Controllers\Sales\SalesController::class, 'bulkSendDueReminders'])->name('sales.bulk-send-due-reminders');


Route::get('/shipments/{id}/track', [LogisticsController::class, 'track'])->name('shipments.track');



// Add these routes to your web.php file

// Route Planner Routes
Route::prefix('logistics')->name('logistics.')->group(function () {
    Route::get('/route-planner', [App\Http\Controllers\Logistics\RouteController::class, 'index'])->name('route-planner');
    Route::post('/route/calculate', [App\Http\Controllers\Logistics\RouteController::class, 'calculate'])->name('route.calculate');
    Route::post('/route/assign', [App\Http\Controllers\Logistics\RouteController::class, 'assign'])->name('route.assign');
});

use Illuminate\Support\Facades\Http;

Route::get('/test-mail', function () {

    $apiKey = config('app.brevo_key'); // 🔥 better than env()

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
