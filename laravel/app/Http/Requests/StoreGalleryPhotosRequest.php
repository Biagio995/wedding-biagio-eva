<?php

namespace App\Http\Requests;

use App\Rules\GalleryUploadFile;
use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryPhotosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * US-10: multiple images; US-26: secure validation (content MIME, size, blocked extensions).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxFiles = max(1, (int) config('gallery.upload.max_files_per_request', 20));

        return [
            'photos' => ['required', 'array', 'min:1', 'max:'.$maxFiles],
            'photos.*' => [
                'file',
                new GalleryUploadFile,
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photos.required' => __('Please choose at least one photo.'),
            'photos.max' => __('You can upload at most :max photos at once.'),
        ];
    }
}
