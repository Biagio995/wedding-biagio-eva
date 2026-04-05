<?php

namespace App\Services;

use App\Models\Guest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use RuntimeException;

/** US-18: parse admin CSV and create {@see Guest} rows (header: name, optional email & token). */
final class GuestCsvImporter
{
    public function import(string $absolutePath): GuestCsvImportResult
    {
        $maxRows = max(1, (int) config('wedding.admin.csv_max_rows', 500));

        $handle = fopen($absolutePath, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Cannot read CSV file.');
        }

        try {
            $firstLine = fgets($handle);
            if ($firstLine === false || trim($firstLine) === '') {
                return new GuestCsvImportResult(0, [
                    ['line' => 1, 'message' => __('The file is empty.')],
                ]);
            }

            $delimiter = $this->detectDelimiter($firstLine);
            $headerRow = str_getcsv($firstLine, $delimiter);
            $headerRow = $this->normalizeHeaderRow($headerRow);
            $indexes = $this->mapHeaderIndexes($headerRow);

            if ($indexes['name'] === null) {
                return new GuestCsvImportResult(0, [
                    ['line' => 1, 'message' => __('The CSV must include a "name" column in the header row.')],
                ]);
            }

            $errors = [];
            $created = 0;
            $tokensSeenInFile = [];
            $lineNum = 1;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $lineNum++;

                if ($lineNum - 1 > $maxRows) {
                    $errors[] = [
                        'line' => $lineNum,
                        'message' => __('Too many rows (maximum :max).', ['max' => $maxRows]),
                    ];
                    break;
                }

                if ($this->rowIsEmpty($row)) {
                    continue;
                }

                $name = $this->cell($row, $indexes['name']);
                $email = $indexes['email'] !== null ? $this->cell($row, $indexes['email']) : null;
                $token = $indexes['token'] !== null ? $this->cell($row, $indexes['token']) : null;

                if ($token === '') {
                    $token = null;
                }
                if ($email === '') {
                    $email = null;
                }

                if ($name === '' || $name === null) {
                    $errors[] = [
                        'line' => $lineNum,
                        'message' => __('Name is required.'),
                    ];

                    continue;
                }

                if ($token !== null && isset($tokensSeenInFile[$token])) {
                    $errors[] = [
                        'line' => $lineNum,
                        'message' => __('This token is already used in the file (line :line).', ['line' => $tokensSeenInFile[$token]]),
                    ];

                    continue;
                }

                $validator = Validator::make(
                    [
                        'name' => $name,
                        'email' => $email,
                        'token' => $token,
                    ],
                    [
                        'name' => ['required', 'string', 'max:255'],
                        'email' => ['nullable', 'email', 'max:255'],
                        'token' => [
                            'nullable',
                            'string',
                            'max:64',
                            'regex:/^[A-Za-z0-9_-]+$/',
                            Rule::unique('guests', 'token'),
                        ],
                    ],
                );

                if ($validator->fails()) {
                    $errors[] = [
                        'line' => $lineNum,
                        'message' => $validator->errors()->first(),
                    ];

                    continue;
                }

                Guest::query()->create($validator->validated());
                $created++;
                if ($token !== null) {
                    $tokensSeenInFile[$token] = $lineNum;
                }
            }

            if ($created === 0 && $errors === []) {
                return new GuestCsvImportResult(0, [
                    ['line' => 2, 'message' => __('No data rows after the header.')],
                ]);
            }

            return new GuestCsvImportResult($created, $errors);
        } finally {
            fclose($handle);
        }
    }

    private function detectDelimiter(string $firstLine): string
    {
        $semis = substr_count($firstLine, ';');
        $commas = substr_count($firstLine, ',');

        return $semis > $commas ? ';' : ',';
    }

    /**
     * @param  list<string|null>  $headerRow
     * @return list<string>
     */
    private function normalizeHeaderRow(array $headerRow): array
    {
        if (isset($headerRow[0]) && is_string($headerRow[0])) {
            $headerRow[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headerRow[0]) ?? $headerRow[0];
        }

        return array_map(fn ($h) => strtolower(trim((string) $h)), $headerRow);
    }

    /**
     * @param  list<string>  $headerRow
     * @return array{name: ?int, email: ?int, token: ?int}
     */
    private function mapHeaderIndexes(array $headerRow): array
    {
        $indexes = ['name' => null, 'email' => null, 'token' => null];

        foreach ($headerRow as $i => $h) {
            match ($h) {
                'name' => $indexes['name'] = $i,
                'email' => $indexes['email'] = $i,
                'token' => $indexes['token'] = $i,
                default => null,
            };
        }

        return $indexes;
    }

    /**
     * @param  list<string|null>|false  $row
     */
    private function rowIsEmpty(mixed $row): bool
    {
        if (! is_array($row)) {
            return true;
        }

        foreach ($row as $cell) {
            if (is_string($cell) && trim($cell) !== '') {
                return false;
            }
            if ($cell !== null && $cell !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<string|null>  $row
     */
    private function cell(array $row, int $index): ?string
    {
        if (! array_key_exists($index, $row)) {
            return null;
        }

        $v = $row[$index];

        return is_string($v) ? trim($v) : null;
    }
}
