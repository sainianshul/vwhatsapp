<?php

namespace App\Http\Controllers;

use App\Models\ScheduledOperation;
use Illuminate\Http\Request;

class ScheduledOperationController extends Controller
{
    public function index(Request $request)
    {
        $query = ScheduledOperation::where('created_by_id', auth()->id())
            ->with(['socialAccount.subject', 'post', 'template', 'assignedBot'])
            ->latest('scheduled_at');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $operations = $query->paginate(50);

        return view('automation.command_center.index', compact('operations'));
    }

    public function cancel(ScheduledOperation $operation)
    {
        $this->authorizeOperation($operation);

        if ($operation->status === ScheduledOperation::STATUS_PENDING) {
            $operation->update(['status' => ScheduledOperation::STATUS_CANCELLED]);
            return back()->with('success', 'Operation cancelled successfully.');
        }

        return back()->with('error', 'Cannot cancel an operation that is already ' . $operation->status);
    }

    public function destroy(ScheduledOperation $operation)
    {
        $this->authorizeOperation($operation);
        $operation->delete();

        return back()->with('success', 'Operation log deleted.');
    }

    // ── Private Helpers ─────────────────────────────────────────

    private function authorizeOperation(ScheduledOperation $operation): void
    {
        if (auth()->user()->role !== \App\Models\User::ROLE_ADMIN && $operation->created_by_id !== auth()->id()) {
            abort(403);
        }
    }
}
