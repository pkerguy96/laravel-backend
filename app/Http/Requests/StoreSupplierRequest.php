<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class StoreSupplierRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => [
                'nullable',
                'email',
                'max:255',

            ],
            'contact_person' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'supply_type' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'note' => 'nullable|string',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du fournisseur est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.email' => 'L\'adresse e-mail doit être valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être "active" ou "inactive".',
        ];
    }
}
