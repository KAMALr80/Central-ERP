<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgentApprovalController extends Controller
{
    /**
     * Display pending delivery agents for approval
     */
    public function index()
    {
        // Get all pending agents (approval_status = 'pending_approval')
        $pendingAgents = DeliveryAgent::where('approval_status', 'pending_approval')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get statistics
        $totalAgents = DeliveryAgent::count();
        $approvedCount = DeliveryAgent::where('approval_status', 'approved')->count();
        $rejectedCount = DeliveryAgent::where('approval_status', 'rejected')->count();

        return view('admin.agent-approvals', compact(
            'pendingAgents',
            'totalAgents',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Approve a delivery agent
     */
    public function approve($id)
    {
        DB::beginTransaction();

        try {
            $agent = DeliveryAgent::findOrFail($id);

            // Update agent approval status
            $agent->approval_status = 'approved';
            $agent->status = 'available';
            $agent->is_active = true;
            $agent->is_online = false;
            $agent->approved_at = now();
            $agent->approved_by = auth()->id();
            $agent->save();

            // Update associated user
            if ($agent->user_id) {
                $user = User::find($agent->user_id);
                if ($user) {
                    $user->status = 'active';
                    $user->save();
                }
            }

            DB::commit();

            Log::info('Delivery agent approved', [
                'agent_id' => $agent->id,
                'agent_name' => $agent->name,
                'approved_by' => auth()->id()
            ]);

            return redirect()->back()->with('success', "✅ Agent {$agent->name} has been approved successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent approval failed', [
                'agent_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', '❌ Error approving agent: ' . $e->getMessage());
        }
    }

    /**
     * Reject a delivery agent
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            $agent = DeliveryAgent::findOrFail($id);

            // Update agent approval status
            $agent->approval_status = 'rejected';
            $agent->status = 'offline';
            $agent->is_active = false;
            $agent->rejection_reason = $request->reason;
            $agent->save();

            // Update associated user
            if ($agent->user_id) {
                $user = User::find($agent->user_id);
                if ($user) {
                    $user->status = 'inactive';
                    $user->save();
                }
            }

            DB::commit();

            Log::info('Delivery agent rejected', [
                'agent_id' => $agent->id,
                'agent_name' => $agent->name,
                'reason' => $request->reason,
                'rejected_by' => auth()->id()
            ]);

            $message = "❌ Agent {$agent->name} has been rejected.";
            if ($request->reason) {
                $message .= " Reason: {$request->reason}";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Agent rejection failed', [
                'agent_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', '❌ Error rejecting agent: ' . $e->getMessage());
        }
    }
}
