<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function show(Request $request): View
    {
        return view('profile.security', [
            'user' => $request->user(),
        ]);
    }

    public function toggleTwoFactor(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            $user->update(['two_factor_enabled' => false]);

            return back()->with('status', 'Autentificarea în doi pași a fost dezactivată.');
        }

        if (! $user->phone) {
            throw ValidationException::withMessages([
                'phone' => 'Adaugă mai întâi un număr de telefon în profil pentru a activa autentificarea în doi pași.',
            ]);
        }

        $user->update(['two_factor_enabled' => true]);

        return back()->with('status', 'Autentificarea în doi pași a fost activată.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('status', 'Parola a fost schimbată.');
    }

    public function removePasskey(Request $request, string $credential): RedirectResponse
    {
        $request->user()->webAuthnCredentials()->whereKey($credential)->first()?->delete();

        return back()->with('status', 'Cheia de acces a fost eliminată.');
    }
}
