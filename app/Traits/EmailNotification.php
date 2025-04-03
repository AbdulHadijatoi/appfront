<?php

namespace App\Traits;

use App\Jobs\SendPriceChangeNotification;
use Illuminate\Support\Facades\Log;

trait EmailNotification
{
    public function sendPriceChangeEmailNotification($oldPrice, $product)
    {
        // Check if price has changed & send notification
        if ($oldPrice != $product->price) {
            // log message for price change notification
            Log::info('Price change detected for product: ' . $product->name);
            $notificationEmail = env('PRICE_NOTIFICATION_EMAIL', 'admin@example.com');

            try {
                SendPriceChangeNotification::dispatch(
                    $product,
                    $oldPrice,
                    $product->price,
                    $notificationEmail
                );
            } catch (\Exception $e) {
                Log::error('Failed to dispatch price change notification: ' . $e->getMessage());
            }
        }

    }
}
