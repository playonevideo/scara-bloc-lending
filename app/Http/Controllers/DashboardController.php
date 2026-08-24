<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Models\Conversation;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $availableObjects = Item::query()
            ->published()
            ->available()
            ->with(['category', 'coverImage'])
            ->latest()
            ->limit(6)
            ->get();

        $activeLoans = Loan::query()
            ->where(function ($q) use ($user) {
                $q->where('borrower_id', $user->id)->orWhere('lender_id', $user->id);
            })
            ->active()
            ->with(['object', 'borrower', 'lender'])
            ->latest()
            ->limit(5)
            ->get();

        $recentConversations = $user->conversations()
            ->with(['participants', 'messages'])
            ->get()
            ->filter(fn (Conversation $c) => $c->messages->isNotEmpty())
            ->sortByDesc(fn (Conversation $c) => $c->lastMessage()?->created_at)
            ->take(4);

        $pendingRequests = Loan::query()
            ->where('lender_id', $user->id)
            ->where('status', LoanStatus::Requested->value)
            ->with(['object', 'borrower'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'availableObjects' => $availableObjects,
            'activeLoans' => $activeLoans,
            'recentConversations' => $recentConversations,
            'pendingRequests' => $pendingRequests,
            'stats' => [
                'available' => Item::query()->published()->available()->count(),
                'mine' => $user->objects()->count(),
                'activeLoans' => $user->loansAsBorrower()->active()->count() + $user->loansAsLender()->active()->count(),
                'unreadMessages' => $user->conversations()
                    ->with('messages')
                    ->get()
                    ->sum(fn (Conversation $c) => $c->messages->where('sender_id', '!=', $user->id)->whereNull('read_at')->count()),
            ],
        ]);
    }
}
