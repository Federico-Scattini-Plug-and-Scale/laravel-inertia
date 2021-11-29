<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobOfferTypeRequest extends FormRequest
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
            'stripe_product_name' => 'required_if:is_free,=,false',
            'ranking' => 'numeric|required|min:0|max:10',
            'currency' => 'required_if:is_free,=,false|max:10',
            'price' => 'numeric|min:1|required_if:is_free,=,false',
            'is_active' => 'required'
        ];
    }
}
