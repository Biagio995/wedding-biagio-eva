<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (! $this->passwordConfigured()) {
            abort(503, __('Admin access is not configured.'));
        }

        if ($request->session()->get('wedding_admin') === true) {
            return redirect()->route('admin.guests.create');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        if (! $this->passwordConfigured()) {
            abort(503, __('Admin access is not configured.'));
        }

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $hash = config('wedding.admin.password_hash');
        if (! is_string($hash) || ! Hash::check($request->input('password'), $hash)) {
            throw ValidationException::withMessages([
                'password' => [__('Invalid password.')],
            ]);
        }

        $request->session()->put('wedding_admin', true);

        return redirect()->intended(route('admin.guests.create'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('wedding_admin');

        return redirect()->route('admin.login');
    }

    private function passwordConfigured(): bool
    {
        $hash = config('wedding.admin.password_hash');

        return is_string($hash) && $hash !== '';
    }
}
