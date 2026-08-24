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

    public function changePhone(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'new_phone' => ['required', 'string', 'regex:/^[0-9+]{9,20}$/'],
            'current_password' => ['required', 'current_password'],
        ]);

        if ($validated['new_phone'] === $user->phone) {
            throw ValidationException::withMessages([
                'new_phone' => 'Noul număr este identic cu cel actual.',
            ]);
        }

        try {
            $twoFactor->sendCode($user, $validated['new_phone']);
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages(['new_phone' => $e->getMessage()]);
        }

        $request->session()->put('auth.pending_phone', $validated['new_phone']);

        return back()->with('status', 'Am trimis un cod de verificare pe noul număr. Introdu codul mai jos.');
    }

    public function verifyPhoneChange(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $user = $request->user();

        $pendingPhone = $request->session()->get('auth.pending_phone');

        if (! $pendingPhone) {
            return back()->with('status', 'Introdu mai întâi noul număr de telefon.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'digits:'.config('sms.code.length', 6)],
        ]);

        if (! $twoFactor->verify($user, $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'Codul introdus este invalid sau a expirat.',
            ]);
        }

        $user->update([
            'phone' => $pendingPhone,
            'phone_verified_at' => now(),
        ]);

        $request->session()->forget('auth.pending_phone');

        return back()->with('status', 'Numărul de telefon a fost actualizat cu succes.');
    }

    public function removePasskey(Request $request, string $credential): RedirectResponse
    {
        $request->user()->webAuthnCredentials()->whereKey($credential)->first()?->delete();

        return back()->with('status', 'Cheia de acces a fost eliminată.');
    }
}
