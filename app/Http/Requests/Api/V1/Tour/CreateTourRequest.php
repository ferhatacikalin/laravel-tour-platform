<?php

namespace App\Http\Requests\Api\V1\Tour;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateTourRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check(); // User must be logged in
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'TOUR_NAME_REQUIRED',
            'name.max' => 'TOUR_NAME_TOO_LONG',
            'description.required' => 'TOUR_DESCRIPTION_REQUIRED',
            'location.required' => 'TOUR_LOCATION_REQUIRED',
            'location.max' => 'TOUR_LOCATION_TOO_LONG',
            'start_date.required' => 'TOUR_START_DATE_REQUIRED',
            'start_date.date' => 'TOUR_START_DATE_INVALID',
            'start_date.after_or_equal' => 'TOUR_START_DATE_MUST_BE_FUTURE',
            'end_date.required' => 'TOUR_END_DATE_REQUIRED',
            'end_date.date' => 'TOUR_END_DATE_INVALID',
            'end_date.after' => 'TOUR_END_DATE_MUST_BE_AFTER_START',
            'price.required' => 'TOUR_PRICE_REQUIRED',
            'price.numeric' => 'TOUR_PRICE_MUST_BE_NUMBER',
            'price.min' => 'TOUR_PRICE_MUST_BE_POSITIVE',
            'price.max' => 'TOUR_PRICE_TOO_HIGH',
        ];
    }
}
