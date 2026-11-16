<?php

namespace App\Console\Commands;

use App\Google\MerchandiseOrderGoogleSheetService;
use App\Models\MerchandiseOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExpireMerchandiseOrders extends Command
{
    protected $signature = 'merchandise:expire-orders';

    protected $description = 'Mark expired Merchandise invoices and sync their status to Google Sheets';

    public function handle(MerchandiseOrderGoogleSheetService $googleSheet): int
    {
        $expiredCount = 0;
        $cutoff = now();

        MerchandiseOrder::query()
            ->where('payment_status', 'PENDING')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $cutoff)
            ->orderBy('id')
            ->chunkById(100, function ($orders) use (&$expiredCount, $cutoff, $googleSheet) {
                foreach ($orders as $candidate) {
                    $orderId = DB::transaction(function () use ($candidate, $cutoff) {
                        $order = MerchandiseOrder::lockForUpdate()->find($candidate->id);
                        if (!$order
                            || $order->payment_status !== 'PENDING'
                            || !$order->expires_at
                            || $order->expires_at->gt($cutoff)) {
                            return null;
                        }

                        $changes = ['payment_status' => 'EXPIRED'];
                        if ($order->order_type === 'PREORDER') {
                            $changes['fulfillment_status'] = 'CANCELLED';
                        }
                        $order->update($changes);

                        return $order->id;
                    });

                    if (!$orderId) {
                        continue;
                    }

                    $expiredCount++;
                    try {
                        $googleSheet->syncOrder((int) $orderId);
                    } catch (Throwable $exception) {
                        Log::error('Expired Merchandise order Google Sheets sync failed.', [
                            'order_id' => $orderId,
                            'exception' => $exception->getMessage(),
                        ]);
                    }
                }
            });

        $this->info("Expired {$expiredCount} Merchandise order(s).");

        return Command::SUCCESS;
    }
}
