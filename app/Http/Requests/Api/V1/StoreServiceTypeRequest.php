<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * We will handle authorization in the Policy/Controller,
     * so we can return true here for now.
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
        // $this->route('company') automatically gets the {company} model
        // from the route.
        $companyId = $this->route('company')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // This rule ensures the 'name' is unique in the 'service_types' table
                // specifically where the 'company_id' matches the company
                // from our route.
                Rule::unique('service_types')->where('company_id', $companyId)
            ],
        ];
    }
}
