<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobOfferTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'stripe_product_name' => 'required',
            'rank' => 'numeric|required|min:0|max:10',
            'currency' => 'required|max:10',
            'price' => 'numeric|min:0|required',
            'is_active' => 'required'
        ];
    }
}
