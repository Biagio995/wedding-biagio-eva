<?php

namespace App\Http\Requests;

use App\Http\Controllers\WeddingController;
use App\Models\Guest;
use Illuminate\Foundation\Http\FormRequest;

class StoreSongRecommendationRequest extends FormRequest
{
    /**
     * Keep validation errors in a dedicated bag so they don't spill into the
     * RSVP form rendered on the same page.
     */
    protected $errorBag = 'songs';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        foreach (['title', 'artist', 'notes', 'submitted_by'] as $key) {
            $value = $this->input($key);
            if (is_string($value)) {
                $value = trim($value);
            }
            $merge[$key] = ($value === '' || $value === null) ? null : $value;
        }
        $this->merge($merge);
    }

    /**
     * Name is required only when no recognised guest is in session.
     */
    protected function requiresAuthorName(): bool
    {
        $id = $this->session()->get(WeddingController::SESSION_WEDDING_GUEST_ID);
        if ($id === null) {
            return true;
        }

        return ! Guest::query()->whereKey($id)->exists();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:200'],
            'artist' => ['nullable', 'string', 'max:200'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];

        $rules['submitted_by'] = $this->requiresAuthorName()
            ? ['required', 'string', 'max:120']
            : ['nullable', 'string', 'max:120'];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => __('Please enter a song title.'),
            'title.max' => __('Song title cannot exceed :max characters.'),
            'artist.max' => __('Artist name cannot exceed :max characters.'),
            'notes.max' => __('Note cannot exceed :max characters.'),
            'submitted_by.required' => __('Please enter your name.'),
            'submitted_by.max' => __('Name cannot exceed :max characters.'),
        ];
    }
}
