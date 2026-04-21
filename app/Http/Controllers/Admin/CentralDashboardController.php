<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class CentralDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::count(), // Add 'status' check if you have it
            'total_domains' => \Stancl\Tenancy\Database\Models\Domain::count(),
            'recent_logs' => AuditLog::with('user')->latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
