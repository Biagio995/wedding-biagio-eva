<?php

namespace App\Http\Requests;

use App\Http\Controllers\WeddingController;
use App\Models\Guest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRsvpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $notes = $this->input('notes');
        if (is_string($notes)) {
            $notes = trim($notes);
        }

        $name = $this->input('name');
        if (is_string($name)) {
            $name = trim($name);
        }

        $email = $this->input('email');
        if (is_string($email)) {
            $email = trim($email);
        }

        $this->merge([
            'notes' => ($notes === '' || $notes === null) ? null : $notes,
            'name' => ($name === '' || $name === null) ? null : $name,
            'email' => ($email === '' || $email === null) ? null : $email,
        ]);
    }

    /**
     * No session guest (or stale id): RSVP requires name so we can create a guest row.
     */
    protected function requiresOpenRsvpIdentity(): bool
    {
        $id = $this->session()->get(WeddingController::SESSION_WEDDING_GUEST_ID);

        if ($id === null) {
            return true;
        }

        return ! Guest::query()->whereKey($id)->exists();
    }

    /**
     * US-05: Yes/No, number of attendees when attending, validated fields.
     * US-06: optional free-text notes (allergies, requests), max length, stored on `guests.notes`.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'rsvp_status' => ['required', Rule::in(['yes', 'no'])],
            'guests_count' => [
                Rule::requiredIf(fn () => $this->input('rsvp_status') === 'yes'),
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];

        if ($this->requiresOpenRsvpIdentity()) {
            $rules['name'] = ['required', 'string', 'max:120'];
            $rules['email'] = ['nullable', 'email', 'max:255'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rsvp_status.required' => __('Please choose whether you will attend.'),
            'rsvp_status.in' => __('Please choose Yes or No.'),
            'guests_count.required' => __('Enter how many people will attend (including you).'),
            'guests_count.integer' => __('The number of guests must be a whole number.'),
            'guests_count.min' => __('There must be at least one guest.'),
            'guests_count.max' => __('The number of guests cannot exceed :max.'),
            'notes.max' => __('Notes cannot exceed :max characters.'),
            'name.required' => __('Please enter your name.'),
            'name.max' => __('Name cannot exceed :max characters.'),
            'email.email' => __('Please enter a valid email address.'),
        ];
    }
}
