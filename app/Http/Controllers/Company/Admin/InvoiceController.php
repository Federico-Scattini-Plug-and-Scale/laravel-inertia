<?php

namespace App\Http\Controllers\Company\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(User $user)
    {
        $filters = request()->has('filters') ? request()->get('filters') : [];
        $invoices = Order::getByUser($user->id, 20, $filters);

        $invoices->each(function ($item)
        {
            $item->date = $item->created_at->format('d-m-Y');
            $price = $item->amount/100;
            $item->total_price = "{$price} {$item->currency}";
        });

        return Inertia::render('Company/Invoices/Index', [
            'invoices' => $invoices,
            'company' => $user,
            'filters' => $filters
        ]);
    }

    public function download(User $user, Order $invoice)
    {
        return App::make('dompdf.wrapper')->loadView('invoices.invoice', [
            'invoice' => $invoice,
            'user' => $user->invoiceDetails
        ])->download();
    }

    public function preview(User $user, Order $invoice)
    {
        return App::make('dompdf.wrapper')->loadView('invoices.invoice', [
            'invoice' => $invoice,
            'user' => $user->invoiceDetails
        ])->stream();
    }
}
