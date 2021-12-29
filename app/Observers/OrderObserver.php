<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        $lastInvoiceNumber = Order::getLastInvoiceNumber();

        if (!empty($lastInvoiceNumber))
        {
            $order->invoice_number = Order::INVOICE_PREFIX . '/' . str_pad($lastInvoiceNumber->id + 1, 4, '0', STR_PAD_LEFT) . '/' . now('Europe/Rome')->year;
        }
        else
        {
            $order->invoice_number = Order::INVOICE_PREFIX . '/' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . '/' . now('Europe/Rome')->year;
        }

        $order->locale = app()->getLocale();
        $order->saveQuietly();
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
