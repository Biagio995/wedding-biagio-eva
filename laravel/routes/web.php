<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\GuestController as AdminGuestController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\RsvpDashboardController as AdminRsvpDashboardController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\WeddingController;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', function (string $locale) {
    $allowed = array_keys(config('wedding.locales', []));
    if (! in_array($locale, $allowed, true)) {
        abort(404);
    }
    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

// US-29: routes below — guest access via token + session only; no Laravel user registration.
Route::get('/', [WeddingController::class, 'show'])->name('home');
Route::get('/w', [WeddingController::class, 'show'])->name('wedding.show');
Route::get('/w/{token}', [WeddingController::class, 'enterByToken'])
    ->where('token', '[A-Za-z0-9_-]+')
    ->middleware('throttle:60,1')
    ->name('wedding.enter');
Route::post('/w/rsvp', [WeddingController::class, 'storeRsvp'])
    ->middleware('throttle:30,1')
    ->name('wedding.rsvp.store');

Route::get('/gallery', [GalleryController::class, 'show'])->name('gallery.show');
Route::get('/gallery/album', [GalleryController::class, 'album'])->name('gallery.album');
Route::get('/gallery/feed', [GalleryController::class, 'feed'])
    ->middleware('throttle:120,1')
    ->name('gallery.feed');
Route::get('/gallery/photos/{photo}/download', [GalleryController::class, 'download'])
    ->middleware('throttle:120,1')
    ->whereNumber('photo')
    ->name('gallery.photo.download');
Route::get('/gallery/upload', [GalleryController::class, 'uploadAlias']);
Route::post('/gallery', [GalleryController::class, 'store'])
    ->middleware('throttle:gallery-uploads')
    ->name('gallery.store');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:10,1');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('wedding.admin')->group(function (): void {
        Route::get('rsvp', [AdminRsvpDashboardController::class, 'index'])->name('rsvp.dashboard');
        Route::get('photos', [AdminPhotoController::class, 'index'])->name('photos.index');
        Route::get('photos/archive.zip', [AdminPhotoController::class, 'downloadArchive'])
            ->middleware('throttle:6,1')
            ->name('photos.archive');
        Route::post('photos/{photo}/approve', [AdminPhotoController::class, 'approve'])
            ->middleware('throttle:60,1')
            ->whereNumber('photo')
            ->name('photos.approve');
        Route::delete('photos/{photo}', [AdminPhotoController::class, 'destroy'])
            ->middleware('throttle:60,1')
            ->whereNumber('photo')
            ->name('photos.destroy');
        Route::get('guests', [AdminGuestController::class, 'index'])->name('guests.index');
        Route::get('guests/create', [AdminGuestController::class, 'create'])->name('guests.create');
        Route::get('guests/import', [AdminGuestController::class, 'importForm'])->name('guests.import');
        Route::post('guests/import', [AdminGuestController::class, 'importStore'])
            ->middleware('throttle:10,1')
            ->name('guests.import.store');
        Route::post('guests', [AdminGuestController::class, 'store'])
            ->middleware('throttle:30,1')
            ->name('guests.store');
        Route::get('guests/{guest}/qr', [AdminGuestController::class, 'qr'])
            ->middleware('throttle:120,1')
            ->whereNumber('guest')
            ->name('guests.qr');
    });
});
