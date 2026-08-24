<?php

namespace App\Http\Controllers;

use App\Models\CommunityRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityRequestController extends Controller
{
    public function index(Request $request): View
    {
        $requests = CommunityRequest::with('user', 'category')
            ->latest()
            ->paginate(15);

        return view('community-requests.index', ['requests' => $requests]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:3000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        CommunityRequest::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'status' => 'open',
        ]);

        return back()->with('status', 'Cererea ta a fost publicată.');
    }

    public function close(Request $request, CommunityRequest $communityRequest): RedirectResponse
    {
        if ($communityRequest->user_id !== $request->user()->id && ! $request->user()->role->isAdmin()) {
            abort(403);
        }

        $communityRequest->update(['status' => 'closed']);

        return back()->with('status', 'Cererea a fost închisă.');
    }
}
