<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'flight_number'    => ['required', 'string', 'max:10'],
            'branch_id'        => ['required', 'integer', 'exists:branches,id'],
            'airline_route_id' => ['required', 'integer', 'exists:airline_routes,id'],
            'equipment_id'     => ['required', 'integer', 'exists:equipments,id'],

            'sched_dep'        => ['required'],
            'sched_arr'        => ['required', 'after_or_equal:sched_dep'],
            'actual_dep'       => ['required'],
            'actual_arr'       => ['required', 'after_or_equal:actual_dep'],

            'notes'            => ['nullable', 'string', 'max:255'],
            'pax'              => ['nullable', 'integer', 'between:1,999'],
            'ground_time'      => ['nullable', 'integer', 'between:1,9999'],
            'pic'              => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'pax.required' => 'Please enter the number of passengers (PAX).',
            'pax.integer'  => 'PAX must be a number.',
            'ground_time.required' => 'Ground time (in minutes) is required.',
            'pic.required' => 'Please enter the PIC name.',
            'sched_arr.after_or_equal' => 'Scheduled arrival cannot be before departure.',
            'actual_arr.after_or_equal' => 'Actual arrival cannot be before departure.',
        ];
    }
}
