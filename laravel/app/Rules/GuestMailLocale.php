<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class GuestMailLocale implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! is_string($value) || ! in_array($value, array_keys(config('wedding.mail_locales', [])), true)) {
            $fail(__('Choose a valid email language.'));
        }
    }
}
