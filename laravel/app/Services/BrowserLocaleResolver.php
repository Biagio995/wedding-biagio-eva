<?php

namespace App\Services;

use Illuminate\Http\Request;

/**
 * Picks the best UI locale from the browser's Accept-Language header.
 */
final class BrowserLocaleResolver
{
    /**
     * @param  list<string>  $allowed
     */
    public function resolve(Request $request, array $allowed): ?string
    {
        if ($allowed === []) {
            return null;
        }

        $allowedSet = array_fill_keys($allowed, true);

        foreach ($request->getLanguages() as $language) {
            if (isset($allowedSet[$language])) {
                return $language;
            }

            $primary = explode('-', $language, 2)[0];
            if (isset($allowedSet[$primary])) {
                return $primary;
            }
        }

        return null;
    }
}
