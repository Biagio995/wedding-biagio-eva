<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminGuestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->get('wedding_admin') === true;
    }

    protected function prepareForValidation(): void
    {
        $token = $this->input('token');
        if ($token === '') {
            $this->merge(['token' => null]);
        }
    }

    /**
     * US-16: create guest; token optional (auto-generated when omitted).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'token' => [
                'nullable',
                'string',
                'max:64',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('guests', 'token'),
            ],
        ];
    }
}
