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
            'name.required' => __('enum.TOUR_NAME_REQUIRED.Message'),
            'name.max' => __('enum.TOUR_NAME_TOO_LONG.Message'),
            'description.required' => __('enum.TOUR_DESCRIPTION_REQUIRED.Message'),
            'location.required' => __('enum.TOUR_LOCATION_REQUIRED.Message'),
            'location.max' => __('enum.TOUR_LOCATION_TOO_LONG.Message'),
            'start_date.required' => __('enum.TOUR_START_DATE_REQUIRED.Message'),
            'start_date.date' => __('enum.TOUR_START_DATE_INVALID.Message'),
            'start_date.after_or_equal' => __('enum.TOUR_START_DATE_MUST_BE_FUTURE.Message'),
            'end_date.required' => __('enum.TOUR_END_DATE_REQUIRED.Message'),
            'end_date.date' => __('enum.TOUR_END_DATE_INVALID.Message'),
            'end_date.after' => __('enum.TOUR_END_DATE_MUST_BE_AFTER_START.Message'),
            'price.required' => __('enum.TOUR_PRICE_REQUIRED.Message'),
            'price.numeric' => __('enum.TOUR_PRICE_MUST_BE_NUMBER.Message'),
            'price.min' => __('enum.TOUR_PRICE_MUST_BE_POSITIVE.Message'),
            'price.max' => __('enum.TOUR_PRICE_TOO_HIGH.Message'),
        ];
    }
}
