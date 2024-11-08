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
            'xray_type' => 'required|string|max:255',
            'view_type' => 'required|string|max:255',
            'body_side' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ];
    }
    public function prepareForValidation()
    {
        $this->merge([

            'xray_type' => isset($this->xray_type) ? implode(',', $this->xray_type) : null,
            'view_type' => isset($this->view_type) ? implode(',', $this->view_type) : null,
            'body_side' => isset($this->body_side) ? implode(',', $this->body_side) : null,
        ]);
    }
    public function messages(): array
    {
        return [
            'patient_id.required' => 'L\'identifiant du patient est requis.',
            'patient_id.exists' => 'Le patient spécifié n\'existe pas.',
            'xray_type.required' => 'Le type de radiographie est requis.',
            'xray_type.string' => 'Le type de radiographie doit être une chaîne de caractères.',
            'view_type.required' => 'Le type de vue est requis.',
            'view_type.string' => 'Le type de vue doit être une chaîne de caractères.',
            'body_side.string' => 'Le côté du corps doit être une chaîne de caractères.',
            'note.string' => 'La note doit être une chaîne de caractères.',
        ];
    }
}
