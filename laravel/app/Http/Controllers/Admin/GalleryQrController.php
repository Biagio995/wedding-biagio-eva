<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GalleryExternalGallery;
use App\Services\WeddingInviteQrGenerator;
use Illuminate\View\View;

/**
 * Renders a printable table card with a QR code pointing at the public gallery.
 *
 * Intended to be printed and placed on reception tables so guests can scan and
 * upload their photos during the event without having to type the URL or know
 * the couple's website exists.
 */
class GalleryQrController extends Controller
{
    public function card(WeddingInviteQrGenerator $generator): View
    {
        $galleryUrl = GalleryExternalGallery::usesGooglePhotos()
            ? GalleryExternalGallery::publicUrl()
            : route('gallery.album', [], absolute: true);

        return view('admin.gallery.qr', [
            'galleryUrl' => $galleryUrl,
            'qrDataUri' => $generator->toDataUri($galleryUrl),
            'eventTitle' => (string) config('wedding.event.title', config('app.name', 'Wedding')),
        ]);
    }
}
