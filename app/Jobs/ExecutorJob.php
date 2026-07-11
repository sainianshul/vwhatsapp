<?php

namespace App\Jobs;

use App\Models\ScheduledOperation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class ExecutorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("[Executor] Starting operation check...");

        // 1. Recover operations stuck in 'processing' for more than 15 minutes
        $stuckOperations = ScheduledOperation::where('status', ScheduledOperation::STATUS_PROCESSING)
            ->where('updated_at', '<', now()->subMinutes(15))
            ->get();

        foreach ($stuckOperations as $stuckOp) {
            Log::warning("[Executor] Recovering stuck Operation ID: {$stuckOp->id}. Marking as failed due to timeout.");
            $stuckOp->update([
                'status' => ScheduledOperation::STATUS_FAILED,
                'error_log' => 'Timeout: The operation was stuck in processing state for too long.',
            ]);
        }

        // 2. Fetch pending operations that are ready to run
        $operations = ScheduledOperation::readyToExecute()
            ->limit(20) // Dispatch up to 20 per minute
            ->get();

        if ($operations->isEmpty()) {
            return;
        }

        foreach ($operations as $op) {
            // Mark as processing immediately so the next minute's Executor doesn't grab it again
            $op->update(['status' => ScheduledOperation::STATUS_PROCESSING]);

            // Dispatch the actual execution to a separate queued job
            ProcessOperationJob::dispatch($op->id);
            Log::info("[Executor] Dispatched ProcessOperationJob for Operation ID: {$op->id}");
        }
    }
}
