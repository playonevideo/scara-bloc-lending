<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('auth.two_factor_user_id');

        if (! $userId || ! User::find($userId)) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $userId = $request->session()->get('auth.two_factor_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'digits:'.config('sms.code.length', 6)],
        ]);

        if (! $twoFactor->verify($user, $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'Codul introdus este invalid sau a expirat.',
            ]);
        }

        $request->session()->forget('auth.two_factor_user_id');

        Auth::login($user, true);
        $request->session()->regenerate();

        return $user->isAdmin()
            ? redirect('/admin')
            : redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request, TwoFactorService $twoFactor): RedirectResponse
    {
        $userId = $request->session()->get('auth.two_factor_user_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            return redirect()->route('login');
        }

        try {
            $twoFactor->sendCode($user);

            return back()->with('status', 'Un nou cod a fost trimis pe telefonul tău.');
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages(['code' => $e->getMessage()]);
        }
    }
}
