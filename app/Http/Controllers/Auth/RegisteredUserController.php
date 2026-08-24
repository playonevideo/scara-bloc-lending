<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterResidentRequest;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function showCodeForm(): View
    {
        return view('auth.enter-code');
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'max:255']]);

        $code = $request->string('code')->toString();

        if (! $this->resolveCode($code)) {
            return back()->withErrors(['code' => 'Codul de invitație nu este valid sau a expirat.']);
        }

        return redirect()->route('register', ['code' => $code]);
    }

    public function create(string $code): View
    {
        $context = $this->resolveCode($code);

        if (! $context) {
            abort(404, 'Codul de invitație nu este valid sau a expirat.');
        }

        return view('auth.register', [
            'code' => $code,
            'apartment' => $context['invitation']?->apartment,
        ]);
    }

    public function store(RegisterResidentRequest $request): RedirectResponse
    {
        $context = $this->resolveCode($request->string('code')->toString());

        if (! $context) {
            return back()->withErrors(['code' => 'Codul de invitație nu este valid sau a expirat.']);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => Str::lower($request->input('email')),
            'phone' => $request->input('phone'),
            'password' => $request->input('password'),
            'role' => Role::Resident,
            'apartment_id' => $context['apartment_id'],
        ]);

        if ($context['invitation']) {
            $context['invitation']->update([
                'used_at' => now(),
                'used_by' => $user->id,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Resolve an invitation code to a registration context.
     *
     * @return array{invitation: ?Invitation, apartment_id: ?int}|null
     */
    private function resolveCode(string $code): ?array
    {
        $invitation = Invitation::query()->where('code', $code)->first();

        if ($invitation && $invitation->isUsable()) {
            return [
                'invitation' => $invitation,
                'apartment_id' => $invitation->apartment_id,
            ];
        }

        $communityCode = config('app.community_invitation_code');

        if ($communityCode && hash_equals($communityCode, $code)) {
            return [
                'invitation' => null,
                'apartment_id' => null,
            ];
        }

        return null;
    }
}
