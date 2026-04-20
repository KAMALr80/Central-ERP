<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;

class HrApprovalController extends Controller
{
    public function index()
    {
        $hrList = User::where('role', 'hr')
                     ->where('status', '!=', 'approved')
                     ->where('status', '!=', 'rejected')
                     ->get();

        $approvedCount = User::where('role', 'hr')
                             ->where('status', 'approved')
                             ->count();

        $rejectedCount = User::where('role', 'hr')
                             ->where('status', 'rejected')
                             ->count();

        return view('admin.hr-approvals', compact('hrList', 'approvedCount', 'rejectedCount'));
    }

    private function generateEmployeeCode()
    {
        $lastEmployee = Employee::orderBy('id', 'desc')->first();

        if ($lastEmployee && $lastEmployee->employee_code) {
            $lastNumber = (int) substr($lastEmployee->employee_code, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'HR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function approve($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->status === 'approved') {
            return redirect()->back()->with('info', 'Already approved');
        }

        $employee = Employee::where('email', $user->email)->first();

        if (!$employee) {
            Employee::create([
                'user_id'        => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'employee_code'  => $this->generateEmployeeCode(),
            ]);
        }

        $user->update([
            'status' => 'approved',
            'role'   => 'hr'
        ]);

        return redirect()->back()->with('success', 'HR approved successfully');
    }

    public function reject($userId)
    {
        $user = User::findOrFail($userId);
        
        $user->update([
            'status' => 'rejected'
        ]);

        return redirect()->back()->with('success', 'HR registration rejected');
    }
}
