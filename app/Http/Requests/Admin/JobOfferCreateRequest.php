<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class JobOfferCreateRequest extends FormRequest
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
        $request = request()->all();
        
        return [
            'title' => 'required|unique:job_offers',
            'description' => 'required',
            'address' => 'required',
            'region' => 'nullable',
            'province' => 'nullable',
            'city' => 'nullable',
            'country' => 'nullable',
            'postal_code' => 'nullable',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'programmingLang' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'programmingLang');
            }),
            'languages' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'languages') && Arr::get($request, 'languages') != 'no validation';
            }),
            'exp' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'exp') && Arr::get($request, 'exp') != 'no validation';
            }),
            'contracts' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'contracts') && Arr::get($request, 'contracts') != 'no validation';
            }),
            'specialization' =>  'required',
            'category' => 'required',
            'min_salary' => [
                'min:0',
                Rule::requiredIf(function() use($request) {
                    return Arr::has($request, 'max_salary') && Arr::get($request, 'max_salary' > 0);
                }),
            ],
            'max_salary' => [
                'min:0',
                Rule::requiredIf(function() use($request) {
                    return Arr::has($request, 'min_salary') && Arr::get($request, 'min_salary' > 0);
                }),
            ],
            'currency' => [
                'max:20',
                Rule::requiredIf(function() use($request) {
                    return Arr::has($request, 'min_salary') && Arr::get($request, 'min_salary' > 0);
                }),
            ],
            'tech_id' => 'required'
        ];
    }
}
