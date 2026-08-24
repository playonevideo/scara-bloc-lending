<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->load(['apartment.floor.staircase.building', 'reviewsReceived.reviewer']);

        return view('profile.show', [
            'user' => $user,
            'averageRating' => $user->reviewsReceived()->avg('rating'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'show_apartment' => ['sometimes', 'boolean'],
            'show_floor' => ['sometimes', 'boolean'],
            'show_phone' => ['sometimes', 'boolean'],
            'show_email' => ['sometimes', 'boolean'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'show_apartment' => $request->boolean('show_apartment'),
            'show_floor' => $request->boolean('show_floor'),
            'show_phone' => $request->boolean('show_phone'),
            'show_email' => $request->boolean('show_email'),
        ]);

        return back()->with('status', 'Profilul a fost actualizat.');
    }
}
