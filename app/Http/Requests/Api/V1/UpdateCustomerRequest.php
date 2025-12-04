<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customer = $this->route('customer');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'lastname' => ['nullable', 'string', 'max:255'],
            'phone' => [
                'sometimes',
                'string',
                'max:20',
                // Unique but ignore this specific customer ID
                Rule::unique('customers')->ignore($customer->id)
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers')->ignore($customer->id)
            ],
        ];
    }
}
