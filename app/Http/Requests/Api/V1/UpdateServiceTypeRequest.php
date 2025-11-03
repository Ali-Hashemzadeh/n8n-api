<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * We will handle authorization in the Policy/Controller.
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
        // $this->route('service_type') gets the {service_type} model
        // from the route.
        $serviceType = $this->route('service_type');

        return [
            'name' => [
                'sometimes', // 'sometimes' means it only validates if present
                'required',  // 'required' ensures it's not empty if it is present
                'string',
                'max:255',
                // This unique rule is similar, but it *ignores* the current
                // service type's ID, allowing you to change other details
                // without falling foul of the unique name check.
                Rule::unique('service_types')
                    ->where('company_id', $serviceType->company_id)
                    ->ignore($serviceType->id)
            ],
        ];
    }
}
