<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * We'll handle this in the Policy later.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Get the company ID from the route
        $companyId = $this->route('company')->id;

        return [
            'name' => [
                'sometimes', // Only validate if present
                'required',  // If present, must not be empty
                'string',
                'max:255',
                // Must be unique, but ignore the current company's ID
                Rule::unique('companies')->ignore($companyId),
            ],
        ];
    }
}
