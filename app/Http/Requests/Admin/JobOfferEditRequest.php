<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class JobOfferEditRequest extends FormRequest
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

        $rules = [
            'title' => 'required|unique:job_offers,title,'.$this->jobOffer->id,
            'description' => 'required',
            'address' => 'required',
            'region' => 'nullable',
            'province' => 'nullable',
            'city' => 'nullable',
            'country' => 'nullable',
            'postal_code' => 'nullable',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'sectors' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'sectors');
            }),
            'industries' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'industries');
            }),
            'languages' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'languages');
            }),
            'processes' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'processes');
            }),
            'machineTypes' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'machineTypes');
            }),
            'machines' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'machines');
            }),
            'techSkills' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'techSkills');
            }),
            'exp' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'exp');
            }),
            'contracts' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'contracts');
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
            ]
        ];

        if (auth()->user()->isAdmin())
        {
            $rules['validity_days'] = 'required|integer';
            $rules['published_at'] = 'required|date';
            $rules['status'] = 'required';
        }

        return $rules;
    }
}
