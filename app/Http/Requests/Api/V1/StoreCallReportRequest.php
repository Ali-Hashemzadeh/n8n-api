<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreCallReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * The middleware handles this, so we return true.
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
        return [
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'profile' => ['required', 'array'],
            'profile.phone' => ['required', 'string', 'max:255'],
            'profile.name' => ['required', 'string', 'max:255'],
            'profile.lastname' => ['nullable', 'string', 'max:255'],
            'profile.email' => ['nullable', 'email', 'max:255'],
            'text' => ['required', 'string'],
            'json' => ['required', 'array'],
            'meta' => ['nullable', 'array'],
            'state' => ['required', 'string', 'in:confirmed,failed,unfinished'],
            'timestamp' => ['nullable', 'date'], // n8n can send its own timestamp
        ];
    }
}
