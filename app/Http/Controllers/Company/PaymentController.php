<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('currentUser');
    }

    public function payment(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status != 'active')
        {
            setStripeKey();

            $price = retrieveStripePrice($jobOffer->jobOfferType->stripe_price_id);

            $paymentData = [
                'line_items' => [[
                    'price' => $price->id,
                    'quantity' => 1,
                ]],
                'payment_method_types' => [
                    'card',
                ],
                'mode' => 'payment',
                'success_url' => route('company.payment.success', [$user, $jobOffer]) . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('company.cancel', [$user, $jobOffer]),
            ];

            if ($user->stripe_customer_id)
            {
                $stripeCustomer = \Stripe\Customer::retrieve($user->stripe_customer_id);
                $paymentData['customer'] = $stripeCustomer->id;
            }
            else
            {
                $paymentData['customer_email'] = $user->email;
            }

            $checkoutSession = \Stripe\Checkout\Session::create($paymentData);

            return Inertia::location($checkoutSession->url);
        }

        return redirect()->route('company.joboffers.index', $user)->with('info', __('The order has been already paid.'));
    }

    public function success(User $user, JobOffer $jobOffer, Request $request)
    {
        if ($request->has('session_id'))
        {
            $order = Order::getByStripeId($request->get('session_id'));

            if (empty($order))
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
                    'user_id' => $user->id,
                    'job_offer_id' => $jobOffer->id,
                    'locale' => $jobOffer->locale
                ]);
        
                $jobOffer->status = JobOffer::STATUS_ACTIVE;
                $jobOffer->save();
        
                $user->stripe_customer_id = $data->customer;
                $user->save();

                return redirect()->route('company.joboffers.index', $user)->with('success', __('The order has been successfully paid.'));
            }

            return redirect()->route('company.joboffers.index', $user)->with('info', __('The order has been already paid.'));
        }

        return redirect()->route('company.joboffers.index', $user)->with('info', __('No stripe session ID provided.')); 
    }

    public function cancel(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status != 'active')
        {
            $jobOffer->status = JobOffer::STATUS_UNPAID;
            $jobOffer->save();
            
            return redirect()->route('company.joboffers.index', $user)->with('error', __('The payment was not completed.'));
        }

        return redirect()->route('company.joboffers.index', $user);
    }
}
