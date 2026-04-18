<?php

namespace App\Http\Requests;

use App\Models\SeatingTable;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdminSeatingTableRequest extends FormRequest
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

        $capacity = $this->input('capacity');
        if ($capacity === '' || $capacity === null) {
            $this->merge(['capacity' => null]);
        }

        $sort = $this->input('sort_order');
        if ($sort === '' || $sort === null) {
            $nextSort = (int) (SeatingTable::query()->max('sort_order') ?? 0) + 10;
            $this->merge(['sort_order' => $nextSort]);
        }
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:120'],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
