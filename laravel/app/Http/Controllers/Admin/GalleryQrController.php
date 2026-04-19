<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $galleryUrl = route('gallery.show', [], absolute: true);
        $result = $generator->make($galleryUrl);

        $dataUri = 'data:'.$result->getMimeType().';base64,'.base64_encode($result->getString());

        return view('admin.gallery.qr', [
            'galleryUrl' => $galleryUrl,
            'qrDataUri' => $dataUri,
            'eventTitle' => (string) config('wedding.event.title', config('app.name', 'Wedding')),
        ]);
    }
}
