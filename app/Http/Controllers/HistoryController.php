<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $completed = Loan::query()
            ->where(fn ($q) => $q->where('borrower_id', $user->id)->orWhere('lender_id', $user->id))
            ->whereIn('status', ['completed', 'returned', 'refused', 'cancelled'])
            ->with(['object', 'borrower', 'lender', 'reviews'])
            ->latest()
            ->paginate(15);

        return view('history.index', [
            'loans' => $completed,
            'reviewsReceived' => $user->reviewsReceived()->with('reviewer')->latest()->get(),
        ]);
    }
}
