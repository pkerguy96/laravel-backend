<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreXrayRequest extends FormRequest
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
            'patient_id' => 'required|exists:patients,id',
            'xrays' => 'required|array|min:1', // Validate 'xrays' as an array with at least one entry

            'xrays.*.xray_type' => 'required|array|min:1', // Validate xray_type as a required array
            'xrays.*.view_type' => 'nullable|array', // view_type is optional and can be null
            'xrays.*.body_side' => 'nullable|array', // body_side is optional and can be null
            'type' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'xrays.required' => 'Au moins une radiographie est requise.',
            'xrays.array' => 'Les radiographies doivent être un tableau.',
            'xrays.*.xray_type.required' => 'Le type de radiographie est requis pour chaque entrée.',
        ];
    }
}
