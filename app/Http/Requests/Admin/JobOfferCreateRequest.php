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
            'title' => 'required',
            'description' => 'required',
            'address' => 'required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'sectors' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'sectors') && Arr::get($request, 'sectors') != 'no validation';
            }),
            'industries' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'industries') && Arr::get($request, 'industries') != 'no validation';
            }),
            'languages' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'languages') && Arr::get($request, 'languages') != 'no validation';
            }),
            'processes' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'processes') && Arr::get($request, 'processes') != 'no validation';
            }),
            'machineTypes '=> Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'machineTypes') && Arr::get($request, 'machineTypes') != 'no validation';
            }),
            'machines' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'machines') && Arr::get($request, 'machines') != 'no validation';
            }),
            'techSkills' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'techSkills') && Arr::get($request, 'techSkills') != 'no validation';
            }),
            'exp' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'exp') && Arr::get($request, 'exp') != 'no validation';
            }),
            'contracts' => Rule::requiredIf(function() use($request) {
                return Arr::has($request, 'contracts') && Arr::get($request, 'contracts') != 'no validation';
            }),
            'specialization' =>  'required',
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
    }
}
