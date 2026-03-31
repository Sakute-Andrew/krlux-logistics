<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Водій щойно призначений
        if ($order->wasChanged('driver_id') && $order->driver_id) {
            // Звільняємо попереднього водія якщо був
            $original = $order->getOriginal('driver_id');
            if ($original && $original !== $order->driver_id) {
                \App\Models\Driver::find($original)?->update(['status' => 'available']);
            }

            // Новий водій стає зайнятим
            \App\Models\Driver::find($order->driver_id)?->update(['status' => 'busy']);

            // Відправляємо лист клієнту
            if ($order->email) {
                $order->load('driver', 'vehicleType');
                \Illuminate\Support\Facades\Mail::to($order->email)
                    ->send(new \App\Mail\OrderConfirmed($order));
            }
        }

        // Замовлення завершено або скасовано — звільняємо водія
        if ($order->wasChanged('status') &&
            in_array($order->status, ['completed', 'cancelled']) &&
            $order->driver_id)
        {
            \App\Models\Driver::find($order->driver_id)?->update(['status' => 'available']);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
