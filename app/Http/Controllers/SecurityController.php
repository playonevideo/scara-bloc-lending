<?php

namespace App\Http\Controllers;

use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SecurityController extends Controller
{
    public function show(Request $request, TwoFactorService $twoFactor): View
    {
        $user = $request->user();

        $pendingSecret = $request->session()->get('auth.two_factor_pending_secret');

        return view('profile.security', [
            'user' => $user,
            'qrCodeUrl' => $pendingSecret ? $twoFactor->qrCodeUrl($user, $pendingSecret) : null,
            'pendingSecret' => $pendingSecret,
        ]);
    }

    public function setupTwoFactor(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $secret = $twoFactor->generateSecretKey();

        $request->session()->put('auth.two_factor_pending_secret', $secret);

        return back()->with('status', 'Scanează codul QR cu aplicația de autentificare, apoi introdu codul generat.');
    }

    public function confirmTwoFactor(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $user = $request->user();

        $secret = $request->session()->get('auth.two_factor_pending_secret');

        if (! $secret) {
            return back()->with('status', 'Configurează mai întâi autentificarea în doi pași.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'digits:6'],
        ]);

        if (! $twoFactor->verify($secret, $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'Codul introdus este invalid sau a expirat.',
            ]);
        }

        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_enabled' => true,
        ]);

        $request->session()->forget('auth.two_factor_pending_secret');

        return back()->with('status', 'Autentificarea în doi pași a fost activată.');
    }

    public function disableTwoFactor(Request $request): RedirectResponse
    {
        $request->user()->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ]);

        $request->session()->forget('auth.two_factor_pending_secret');

        return back()->with('status', 'Autentificarea în doi pași a fost dezactivată.');
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
