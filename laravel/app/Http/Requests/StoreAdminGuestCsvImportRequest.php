<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreAdminGuestCsvImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->get('wedding_admin') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxKb = max(64, (int) config('wedding.admin.csv_max_file_kilobytes', 1024));

        return [
            'file' => [
                'required',
                // US-26: require text/CSV family MIME from file contents, not extension alone.
                File::types(['text/csv', 'text/plain', 'application/csv', 'csv', 'txt'])
                    ->max($maxKb),
            ],
        ];
    }
}
