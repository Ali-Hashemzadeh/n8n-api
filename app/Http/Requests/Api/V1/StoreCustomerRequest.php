<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['nullable', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:customers,phone' // Phone should generally be unique in the system
            ],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],

            // Allow Super-Admins to assign companies immediately
            'company_ids' => ['nullable', 'array'],
            'company_ids.*' => ['integer', 'exists:companies,id'],
        ];
    }
}
