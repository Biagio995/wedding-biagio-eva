<?php

namespace App\Http\Requests;

use App\Models\Guest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminGuestRequest extends FormRequest
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

        $rsvp = $this->input('rsvp_status');
        if ($rsvp === '' || $rsvp === null) {
            $this->merge(['rsvp_status' => null]);
        }

        if ($this->input('rsvp_status') !== 'yes') {
            $this->merge(['guests_count' => null, 'companion_names' => null]);

            return;
        }

        $raw = $this->input('companion_names');
        $names = [];
        if (is_string($raw)) {
            foreach (preg_split('/[\r\n,;]+/', $raw) ?: [] as $item) {
                $trimmed = is_string($item) ? trim($item) : '';
                if ($trimmed !== '' && ! in_array($trimmed, $names, true)) {
                    $names[] = $trimmed;
                }
            }
        } elseif (is_array($raw)) {
            foreach ($raw as $item) {
                $trimmed = is_string($item) ? trim($item) : '';
                if ($trimmed !== '' && ! in_array($trimmed, $names, true)) {
                    $names[] = $trimmed;
                }
            }
        }

        $this->merge(['companion_names' => $names === [] ? null : $names]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $guest = $this->route('guest');
        $guestId = $guest instanceof Guest ? $guest->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'token' => [
                'required',
                'string',
                'max:64',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('guests', 'token')->ignore($guestId),
            ],
            'rsvp_status' => ['nullable', Rule::in(['yes', 'no'])],
            'guests_count' => [
                'nullable',
                'integer',
                'min:1',
                'max:500',
                Rule::requiredIf(fn (): bool => $this->input('rsvp_status') === 'yes'),
            ],
            'companion_names' => ['nullable', 'array', 'max:499'],
            'companion_names.*' => ['string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
