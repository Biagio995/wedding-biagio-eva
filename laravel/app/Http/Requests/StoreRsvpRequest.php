<?php

namespace App\Http\Requests;

use App\Http\Controllers\WeddingController;
use App\Models\Guest;
use Illuminate\Contracts\Validation\Validator;
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
        $merge = [];

        if ($this->has('notes')) {
            $notes = $this->input('notes');
            if (is_string($notes)) {
                $notes = trim($notes);
            }
            $merge['notes'] = ($notes === '' || $notes === null) ? null : $notes;
        }

        $name = $this->input('name');
        if (is_string($name)) {
            $name = trim($name);
        }

        $email = $this->input('email');
        if (is_string($email)) {
            $email = trim($email);
        }

        $merge['name'] = ($name === '' || $name === null) ? null : $name;
        $merge['email'] = ($email === '' || $email === null) ? null : $email;

        $companionNames = $this->normalizeCompanionNames($this->input('companion_names'));
        // Silently drop companion names when the guest is declining: they are meaningless.
        if ($this->input('rsvp_status') !== 'yes') {
            $companionNames = [];
        }
        $merge['companion_names'] = $companionNames;

        $this->merge($merge);
    }

    /**
     * Accepts either an array of strings or a single string with newline/comma separators.
     * Output: array of unique, trimmed, non-empty names; never null (empty array when none).
     *
     * @return array<int, string>
     */
    private function normalizeCompanionNames(mixed $raw): array
    {
        $items = [];

        if (is_array($raw)) {
            $items = $raw;
        } elseif (is_string($raw)) {
            $items = preg_split('/[\r\n,;]+/', $raw) ?: [];
        }

        $clean = [];
        foreach ($items as $item) {
            if (! is_string($item)) {
                continue;
            }
            $trimmed = trim($item);
            if ($trimmed === '') {
                continue;
            }
            if (! in_array($trimmed, $clean, true)) {
                $clean[] = $trimmed;
            }
        }

        return $clean;
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
            'companion_names' => ['array', 'max:49'],
            'companion_names.*' => ['string', 'max:120'],
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
            'companion_names.*.max' => __('Each companion name cannot exceed :max characters.'),
            'notes.max' => __('Notes cannot exceed :max characters.'),
            'name.required' => __('Please enter your name.'),
            'name.max' => __('Name cannot exceed :max characters.'),
            'email.email' => __('Please enter a valid email address.'),
        ];
    }

    /**
     * Cross-field rule: you can only list up to (guests_count - 1) companion names.
     * When attending alone or declining, companion_names must be empty.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $companions = $this->input('companion_names');
            if (! is_array($companions)) {
                return;
            }
            $count = count($companions);
            if ($count === 0) {
                return;
            }

            if ($this->input('rsvp_status') !== 'yes') {
                $v->errors()->add('companion_names', __('Companion names are only used when attending.'));

                return;
            }

            $guestsCount = (int) $this->input('guests_count');
            $maxCompanions = max(0, $guestsCount - 1);
            if ($count > $maxCompanions) {
                $v->errors()->add('companion_names', __(
                    'You can list at most :max companion name(s) for :count attendees.',
                    ['max' => $maxCompanions, 'count' => $guestsCount],
                ));
            }
        });
    }
}
