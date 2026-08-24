<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterResidentRequest;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(string $code): View
    {
        $invitation = Invitation::query()->where('code', $code)->first();

        if (! $invitation || ! $invitation->isUsable()) {
            abort(404, 'Invitația nu este validă sau a expirat.');
        }

        return view('auth.register', ['invitation' => $invitation]);
    }

    public function store(RegisterResidentRequest $request): RedirectResponse
    {
        $invitation = $request->invitation();

        if (! $invitation->isUsable()) {
            return back()->withErrors(['code' => 'Invitația nu mai este validă sau a expirat.']);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => Str::lower($request->input('email')),
            'phone' => $request->input('phone'),
            'password' => $request->input('password'),
            'role' => Role::Resident,
            'apartment_id' => $invitation->apartment_id,
        ]);

        $invitation->update([
            'used_at' => now(),
            'used_by' => $user->id,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
