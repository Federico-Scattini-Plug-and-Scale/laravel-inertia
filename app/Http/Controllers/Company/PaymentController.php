<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function payment()
    {
        setStripeKey();

        $product = \Stripe\Product::create([
            'name' => 'T-shirt',
        ]);

        $price = \Stripe\Price::create([
            'product' => $product->id,
            'unit_amount' => 0100,
            'currency' => 'pln',
        ]);

        $paymentData = [
            'line_items' => [[
                'price' => $price->id,
                'quantity' => 1,
            ]],
            'payment_method_types' => [
                'card',
            ],
            'mode' => 'payment',
            'success_url' => route('company.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('company.cancel'),
        ];

        if (Auth::user()->stripe_customer_id)
        {
            $stripeCustomer = \Stripe\Customer::retrieve(Auth::user()->stripe_customer_id);
            $paymentData['customer'] = $stripeCustomer->id;
        }
        else
            $paymentData['customer_email'] = Auth::user()->email;

        $checkout_session = \Stripe\Checkout\Session::create($paymentData);

        return redirect($checkout_session->url);
    }

    public function success(Request $request)
    {
        setStripeKey();
        $data = \Stripe\Checkout\Session::retrieve($request->get('session_id'));

        Order::create([
            'stripe_id' => $data->id,
            'amount' => $data->amount_total,
            'currency' => $data->currency,
            'stripe_customer_id' => $data->customer,
            'stripe_customer_email' => $data->customer_email ? $data->customer_email : $data->customer_details->email,
            'stripe_payment_intent' => $data->payment_intent,
            'payment_status' => $data->payment_status,
            'user_id' => Auth::user()->id
        ]);

        $user = Auth::user();
        $user->stripe_customer_id = $data->customer;
        $user->save();

        return dd($data);
    }
}
