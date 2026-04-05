<?php

namespace App\Services;

/** US-18: outcome of a CSV guest import (partial success allowed). */
final class GuestCsvImportResult
{
    /**
     * @param  list<array{line: int, message: string}>  $errors
     */
    public function __construct(
        public readonly int $created,
        public readonly array $errors,
    ) {}
}
