<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminSeatingTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->get('wedding_admin') === true;
    }

    protected function prepareForValidation(): void
    {
        $label = $this->input('label');
        if (is_string($label)) {
            $this->merge(['label' => trim($label)]);
        }

        $notes = $this->input('notes');
        if (is_string($notes)) {
            $trimmed = trim($notes);
            $this->merge(['notes' => $trimmed === '' ? null : $trimmed]);
        }

        if ($this->input('capacity') === '') {
            $this->merge(['capacity' => null]);
        }

        if ($this->input('sort_order') === '' || $this->input('sort_order') === null) {
            $this->merge(['sort_order' => 0]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:120'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
