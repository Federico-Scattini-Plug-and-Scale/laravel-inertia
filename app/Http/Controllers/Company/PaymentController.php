<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InvoiceDataRequest;
use App\Models\InvoiceDetail;
use App\Models\JobOffer;
use App\Models\JobOfferApiError;
use App\Models\JobOfferType;
use App\Models\Order;
use App\Models\User;
use App\Notifications\JobOffers\JobOfferExtended;
use App\Notifications\JobOffers\JobOfferUnderApproval;
use App\Notifications\JobOffers\OrderPaid;
use App\Notifications\JobOffers\JobOfferUpgrade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('currentUser');
    }

    public function packages(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status == JobOffer::STATUS_UNDER_APPROVAL)
        {
            return redirect()->back()->with('message', [
                'type' => 'info',
                'content' => __('The selected job offer is under approval.')
            ]);
        }

        if ($jobOffer->status == JobOffer::STATUS_ACTIVE)
        {
            return redirect()->back()->with('message', [
                'type' => 'error',
                'content' => __('The selected action is not available on active job offers.')
            ]);
        }

        $withoutFreeTrials = !empty($jobOffer->jobOfferType) && $jobOffer->jobOfferType->is_free ?
                                    true 
                                    : (empty($user->detail) ? 
                                    true 
                                    : $user->detail->nr_of_free_trials < 1);
    
        return Inertia::render('Company/PackageCart', [
            'jobOffer' => $jobOffer,
            'company' => $user,
            'jobOfferTypes' => JobOfferType::getOptions(getCountry(), $withoutFreeTrials)
        ]);
    }

    public function storePackage(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status == JobOffer::STATUS_UNDER_APPROVAL)
        {
            return redirect()->back()->with('message', [
                'type' => 'info',
                'content' => __('The selected job offer is under approval.')
            ]);
        }

        if ($jobOffer->status == JobOffer::STATUS_ACTIVE)
        {
            return redirect()->back()->with('message', [
                'type' => 'error',
                'content' => __('The selected action is not available on active job offers.')
            ]);
        }

        $this->validate(request(), [
            'job_offer_type_id' => 'required'
        ]);

        $jobOffer->job_offer_type_id = request()->get('job_offer_type_id');
        $jobOffer->save();

        return redirect()->route('company.payment.preview', [$user, $jobOffer]);
    }

    public function upgrade(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status == JobOffer::STATUS_UNDER_APPROVAL)
        {
            return redirect()->back()->with('message', [
                'type' => 'info',
                'content' => __('The selected job offer is under approval.')
            ]);
        }

        if ($jobOffer->status != JobOffer::STATUS_ACTIVE)
        {
            return redirect()->back()->with('message', [
                'type' => 'error',
                'content' => __('The selected action is available only for active job offers.')
            ]);
        }

        $jobOfferTypes = JobOfferType::getMoreExpensivePackages($jobOffer->jobOfferType->price, getCountry());
        
        $jobOfferTypes = $jobOfferTypes->map(function($item) use ($jobOffer)
        {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->calculateUpgradePrice($jobOffer),
                'currency' => $item->currency
            ];
        });
       
        return Inertia::render('Company/PackageUpgrade', [
            'jobOffer' => $jobOffer,
            'company' => $user,
            'jobOfferTypes' => $jobOfferTypes
        ]);
    }

    public function storeUpgrade(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status == JobOffer::STATUS_UNDER_APPROVAL)
        {
            return redirect()->back()->with('message', [
                'type' => 'info',
                'content' => __('The selected job offer is under approval.')
            ]);
        }

        if ($jobOffer->status != JobOffer::STATUS_ACTIVE)
        {
            return redirect()->back()->with('message', [
                'type' => 'error',
                'content' => __('The selected action is available only for active job offers.')
            ]);
        }

        $jobOffer::setUser($user);
        $jobOffer->saveAsDraft();
        $jobOffer->draft->setData('data', request()->get('job_offer_type'));

        return redirect()->route('company.payment.preview', [$user, $jobOffer, 'upgrade' => 'true']);
    }

    public function preview(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status == JobOffer::STATUS_UNDER_APPROVAL)
        {
            return redirect()->back()->with('message', [
                'type' => 'info',
                'content' => __('The selected job offer is under approval.')
            ]);
        }

        $user->load('invoiceDetails');
        $jobOffer->load('jobOfferType');

        $jobOfferType = $jobOffer->jobOfferType;

        $isUpgrade = false;

        if (request()->has('upgrade') && request()->get('upgrade') == 'true')
        {
            $isUpgrade = true;
            $jobOfferType->price = Arr::get($jobOffer->drafts->last()->data, 'data.price');
            $jobOfferType->name = Arr::get($jobOffer->drafts->last()->data, 'data.name');

            if (empty($jobOfferType->price))
            {
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'content' => __('Something went wrong. Please contact the support or try again.')
                ]);
            }
        }
        else if ($jobOfferType->is_free && $jobOffer->status == JobOffer::STATUS_ACTIVE)
        {
            return redirect()->route('company.joboffers.index', $user)->with('message', [
                'type' => 'info',
                'content' => __('The selected job offer is already active for free.')
            ]);
        }

        return Inertia::render('Company/PaymentPreview', [
            'jobOffer' => $jobOffer,
            'company' => $user,
            'hasInvoiceDetails' => $user->getHasInvoiceDetails(),
            'isUpgrade' => $isUpgrade
        ]);
    }

    public function invoiceData(User $user, JobOffer $jobOffer, InvoiceDataRequest $request)
    {
        InvoiceDetail::updateOrCreate([
            'user_id' => $user->id
        ], array_merge($request->validated(), ['is_completed' => true]));

        return redirect()->route('company.payment.index', [$user, $jobOffer, 'upgrade' => request()->has('upgrade') && request()->get('upgrade') == 'true' ? 'true' : 'false']);
    }

    public function payment(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status != JobOffer::STATUS_UNDER_APPROVAL)
        {
            if ($jobOffer->jobOfferType->is_free)
            {
                if ($user->detail == null || $user->detail->nr_of_free_trials < 1)
                {
                    return redirect()->route('company.joboffers.index', $user)->with('message', [
                        'type' => 'error',
                        'content' => __('You do not have any free trial available.')
                    ]);
                }

                $jobOffer->status = JobOffer::STATUS_UNDER_APPROVAL;
                $jobOffer->save();

                $user->notify(new JobOfferUnderApproval());

                $user->detail->nr_of_free_trials -= 1;
                $user->save();

                return redirect()->route('company.joboffers.index', $user)->with('message', [
                    'type' => 'success',
                    'content' => __('The order has been successfully paid.')
                ]);
            }

            setStripeKey();

            $isUpgrade = request()->has('upgrade') && request()->get('upgrade') == 'true';

            if ($isUpgrade)
            {
                $jobOfferType = JobOfferType::getById(Arr::get($jobOffer->drafts->last()->data, 'data.id'));

                if (empty($jobOfferType))
                {
                    return redirect()
                        ->route('company.payment.preview', [$user, $jobOffer, 'upgrade' => request()->has('upgrade') && request()->get('upgrade') == 'true' ? 'true' : 'false'])
                        ->with('message', [
                            'type' => 'error',
                            'content' => __('There was an error with the service. Please, contact the support.')
                        ]);
                }

                try {
                    $price = createStripePrice(
                        $jobOfferType->stripe_product_id, 
                        number_format((Arr::get($jobOffer->drafts->last()->data, 'data.price')*100), 0, '', ''), 
                        Arr::get($jobOffer->drafts->last()->data, 'data.currency')
                    );
                } catch (\Stripe\Exception\RateLimitException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (Exception $e) {
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                }
            }
            else
            {
                try {
                    $price = retrieveStripePrice($jobOffer->jobOfferType->stripe_price_id);
                } catch (\Stripe\Exception\RateLimitException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (Exception $e) {
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                }
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
                'success_url' => $isUpgrade ? 
                    route('company.payment.success', [$user, $jobOffer]) . "?session_id={CHECKOUT_SESSION_ID}&upgrade=true" 
                    : route('company.payment.success', [$user, $jobOffer]) . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('company.payment.cancel', [$user, $jobOffer, 'upgrade' => $isUpgrade ? 'true' : 'false']),
            ];

            if ($user->stripe_customer_id)
            {
                try {
                    $stripeCustomer = \Stripe\Customer::retrieve($user->stripe_customer_id);
                } catch (\Stripe\Exception\RateLimitException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
                } catch (Exception $e) {
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the support.')
                    ]);
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
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'content' => __('There was an error with the service. Please, contact the support.')
                ]);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'content' => __('There was an error with the service. Please, contact the support.')
                ]);
            } catch (\Stripe\Exception\AuthenticationException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'content' => __('There was an error with the service. Please, contact the support.')
                ]);
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'content' => __('There was an error with the service. Please, contact the support.')
                ]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $this->createApiError($e, $jobOffer->id);
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'content' => __('There was an error with the service. Please, contact the support.')
                ]);
            } catch (Exception $e) {
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'content' => __('There was an error with the service. Please, contact the support.')
                ]);
            }

            return Inertia::location($checkoutSession->url);
        }

        return redirect()->back()->with('message', [
            'type' => 'info',
            'content' => __('The selected job offer is under approval.')
        ]);
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
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the service.')
                    ]);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the service.')
                    ]);
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the service.')
                    ]);
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the service.')
                    ]);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the service.')
                    ]);
                } catch (Exception $e) {
                    $this->createApiError($e, $jobOffer->id);
                    return redirect()->back()->with('message', [
                        'type' => 'error',
                        'content' => __('There was an error with the service. Please, contact the service.')
                    ]);
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

                $isUpgrade = request()->has('upgrade') && request()->get('upgrade') == 'true';

                if ($jobOffer->status == JobOffer::STATUS_ACTIVE)
                {
                    if ($isUpgrade)
                    {
                        $jobOffer->job_offer_type_id = Arr::get($jobOffer->drafts->last()->data, 'data.id');

                        if ($jobOffer->jobOfferType->is_free)
                        {
                            $jobOffer->validity_days = JobOffer::VALIDITY;
                        }

                        $user->notify(new JobOfferUpgrade());
                    }
                    else
                    {
                        $jobOffer->validity_days += JobOffer::VALIDITY;

                        $user->notify(new JobOfferExtended());
                    }
                }
                else
                {
                    $jobOffer->status = JobOffer::STATUS_UNDER_APPROVAL;

                    $user->notify(new JobOfferUnderApproval());
                }

                $jobOffer->save();

                $user->stripe_customer_id = $data->customer;
                $user->save();

                return redirect()->route('company.joboffers.index', $user)->with('message', [
                    'type' => 'success',
                    'content' => __('The order has been successfully paid.')
                ]);
            }

            return redirect()->route('company.joboffers.index', $user)->with('message', [
                'type' => 'error',
                'content' => __('The order has been already paid.')
            ]);
        }
        
        return redirect()->route('company.joboffers.index', $user)->with('message', [
            'type' => 'error',
            'content' => __('No stripe session ID provided.')
        ]);
    }

    public function cancel(User $user, JobOffer $jobOffer)
    {
        if ($jobOffer->status != JobOffer::STATUS_ACTIVE && $jobOffer->status != JobOffer::STATUS_UNDER_APPROVAL && $jobOffer->status != JobOffer::STATUS_CART)
        {
            $jobOffer->status = JobOffer::STATUS_UNPAID;
            $jobOffer->save();
        }

        return redirect()
            ->route('company.payment.preview', [$user, $jobOffer, 'upgrade' => request()->has('upgrade') && request()->get('upgrade') == 'true' ? 'true' : 'false'])
            ->with('message', [
                'type' => 'warning',
                'content' => __('The payment was not completed.')
            ]);
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
