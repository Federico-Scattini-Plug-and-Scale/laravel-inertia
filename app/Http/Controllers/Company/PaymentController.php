<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InvoiceDataRequest;
use App\Models\InvoiceDetail;
use App\Models\JobOffer;
use App\Models\JobOfferApiError;
use App\Models\Order;
use App\Models\User;
use App\Notifications\JobOffers\JobOfferUnderApproval;
use App\Notifications\JobOffers\OrderPaid;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('currentUser');
    }

    public function preview(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status == JobOffer::STATUS_UNDER_APPROVAL)
        {
            return redirect()->back()->with('info', 'The selected job offer is under approval.');
        }

        $jobOffer->load('jobOfferType');
        $user->load('invoiceDetails');

        return Inertia::render('Company/PaymentPreview', [
            'jobOffer' => $jobOffer,
            'company' => $user,
            'hasInvoiceDetails' => $user->getHasInvoiceDetails()
        ]);
    }

    public function invoiceData(User $user, JobOffer $jobOffer, InvoiceDataRequest $request)
    {
        InvoiceDetail::updateOrCreate([
            'user_id' => $user->id
        ], array_merge($request->validated(), ['is_completed' => true]));

        return redirect()->route('company.payment', [$user, $jobOffer]);
    }

    public function payment(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status != JobOffer::STATUS_UNDER_APPROVAL)
        {
            setStripeKey();
            try {
                $price = retrieveStripePrice($jobOffer->jobOfferType->stripe_price_id);
            } catch (\Stripe\Exception\RateLimitException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            } catch (\Stripe\Exception\AuthenticationException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            } catch (Exception $e) {
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            }
            
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
                try {
                    $stripeCustomer = \Stripe\Customer::retrieve($user->stripe_customer_id);
                } catch (\Stripe\Exception\RateLimitException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                } catch (Exception $e) {
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                }
                $paymentData['customer'] = $stripeCustomer->id;
            }
            else
            {
                $paymentData['customer_email'] = $user->email;
            }
            try {
                $checkoutSession = \Stripe\Checkout\Session::create($paymentData);
            } catch (\Stripe\Exception\RateLimitException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            } catch (\Stripe\Exception\AuthenticationException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            } catch (Exception $e) {
                return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
            }
            return Inertia::location($checkoutSession->url);
        }

        return redirect()->back()->with('info', 'The selected job offer is under approval.');
    }

    public function success(User $user, JobOffer $jobOffer, Request $request)
    {
        if ($request->has('session_id'))
        {
            $order = Order::getByStripeId($request->get('session_id'));

            if (empty($order))
            {
                setStripeKey();
                try {
                    $data = \Stripe\Checkout\Session::retrieve($request->get('session_id'));
                } catch (\Stripe\Exception\RateLimitException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                } catch (Exception $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('info', __('There was an error with the service. Please, contact the service.'));
                }

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

                $user->notify(new OrderPaid());

                if ($jobOffer->status != JobOffer::STATUS_CART)
                {
                    $jobOffer->status = JobOffer::STATUS_ACTIVE;
                    $jobOffer->published_at = Carbon::now('Europe/Rome');
                    $jobOffer->validity_days += JobOffer::VALIDITY;
                }
                else
                {
                    $jobOffer->status = JobOffer::STATUS_UNDER_APPROVAL;
                    $user->notify(new JobOfferUnderApproval());
                }

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
        if ($jobOffer->status != JobOffer::STATUS_ACTIVE && $jobOffer->status != JobOffer::STATUS_UNDER_APPROVAL && $jobOffer->status != JobOffer::STATUS_CART)
        {
            $jobOffer->status = JobOffer::STATUS_UNPAID;
            $jobOffer->save();
        }

        return redirect()->route('company.payment.preview', [$user, $jobOffer])->with('error', __('The payment was not completed.'));
    }

    private function createApiError($e, $jobOfferId)
    {
        JobOfferApiError::create([
            'error' => json_encode([
                'Status' => $e->getHttpStatus(),
                'Type' => $e->getError()->type,
                'Code' => $e->getError()->code,
                'Param' => $e->getError()->param,
                'Message' => $e->getError()->message,
            ]),
            'job_offer_id' => $jobOfferId
        ]);
    }
}
