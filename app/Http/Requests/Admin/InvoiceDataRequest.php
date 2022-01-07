<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceDataRequest extends FormRequest
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
        dd(request()->all());
        return [
            'invoice_name' => 'required',
            'invoice_street' => 'required',
            'invoice_city' => 'required',
            'invoice_country' => 'required',
            'invoice_postal_code' => 'required',
            'invoice_phone' => 'required|max:20|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            'invoice_email' => 'required|email',
            'invoice_vat_number' => 'required',
        ];
    }
}
