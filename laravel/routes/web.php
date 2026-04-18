<?php

use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\GuestController as AdminGuestController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\RegistryItemController as AdminRegistryItemController;
use App\Http\Controllers\Admin\RsvpDashboardController as AdminRsvpDashboardController;
use App\Http\Controllers\Admin\SeatingTableController as AdminSeatingTableController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\RegistryController;
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

Route::get('/w/calendar.ics', [CalendarController::class, 'ics'])
    ->middleware('throttle:60,1')
    ->name('wedding.calendar.ics');

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

Route::get('/registry', [RegistryController::class, 'show'])->name('registry.show');
Route::post('/registry/items/{registryItem}/claim', [RegistryController::class, 'claim'])
    ->middleware('throttle:60,1')
    ->name('registry.claim');

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
        Route::get('guests/export.csv', [AdminGuestController::class, 'export'])
            ->middleware('throttle:6,1')
            ->name('guests.export');
        Route::get('guests/import', [AdminGuestController::class, 'importForm'])->name('guests.import');
        Route::post('guests/import', [AdminGuestController::class, 'importStore'])
            ->middleware('throttle:10,1')
            ->name('guests.import.store');
        Route::post('guests', [AdminGuestController::class, 'store'])
            ->middleware('throttle:30,1')
            ->name('guests.store');
        Route::get('guests/{guest}/edit', [AdminGuestController::class, 'edit'])
            ->whereNumber('guest')
            ->name('guests.edit');
        Route::put('guests/{guest}', [AdminGuestController::class, 'update'])
            ->middleware('throttle:60,1')
            ->whereNumber('guest')
            ->name('guests.update');
        Route::delete('guests/{guest}', [AdminGuestController::class, 'destroy'])
            ->middleware('throttle:60,1')
            ->whereNumber('guest')
            ->name('guests.destroy');
        Route::get('guests/{guest}/qr', [AdminGuestController::class, 'qr'])
            ->middleware('throttle:120,1')
            ->whereNumber('guest')
            ->name('guests.qr');

        Route::get('seating', [AdminSeatingTableController::class, 'index'])->name('seating.index');
        Route::post('seating', [AdminSeatingTableController::class, 'store'])
            ->middleware('throttle:60,1')
            ->name('seating.store');
        Route::get('seating/{seatingTable}/edit', [AdminSeatingTableController::class, 'edit'])
            ->whereNumber('seatingTable')
            ->name('seating.edit');
        Route::put('seating/{seatingTable}', [AdminSeatingTableController::class, 'update'])
            ->middleware('throttle:60,1')
            ->whereNumber('seatingTable')
            ->name('seating.update');
        Route::delete('seating/{seatingTable}', [AdminSeatingTableController::class, 'destroy'])
            ->middleware('throttle:60,1')
            ->whereNumber('seatingTable')
            ->name('seating.destroy');
        Route::post('seating/{seatingTable}/assign', [AdminSeatingTableController::class, 'assign'])
            ->middleware('throttle:120,1')
            ->whereNumber('seatingTable')
            ->name('seating.assign');
        Route::post('seating/guests/{guest}/unassign', [AdminSeatingTableController::class, 'unassign'])
            ->middleware('throttle:120,1')
            ->whereNumber('guest')
            ->name('seating.unassign');

        Route::get('registry', [AdminRegistryItemController::class, 'index'])->name('registry.index');
        Route::post('registry', [AdminRegistryItemController::class, 'store'])
            ->middleware('throttle:60,1')
            ->name('registry.store');
        Route::get('registry/{registryItem}/edit', [AdminRegistryItemController::class, 'edit'])->name('registry.edit');
        Route::put('registry/{registryItem}', [AdminRegistryItemController::class, 'update'])
            ->middleware('throttle:60,1')
            ->name('registry.update');
        Route::delete('registry/{registryItem}', [AdminRegistryItemController::class, 'destroy'])
            ->middleware('throttle:60,1')
            ->name('registry.destroy');

        Route::get('audit', [AdminAuditLogController::class, 'index'])->name('audit.index');
    });
});
