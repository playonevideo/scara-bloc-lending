<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Review;
use App\Notifications\ReviewReceived;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $tab = $request->string('tab', 'active');

        $received = Loan::query()
            ->where('lender_id', $user->id)
            ->with(['object', 'borrower'])
            ->when($tab === 'requests', fn ($q) => $q->where('status', LoanStatus::Requested->value))
            ->latest();

        $sent = Loan::query()
            ->where('borrower_id', $user->id)
            ->with(['object', 'lender'])
            ->latest();

        $active = Loan::query()
            ->where(fn ($q) => $q->where('borrower_id', $user->id)->orWhere('lender_id', $user->id))
            ->active()
            ->with(['object', 'borrower', 'lender'])
            ->latest();

        return view('loans.index', [
            'tab' => $tab,
            'received' => $received->get(),
            'sent' => $sent->get(),
            'active' => $active->get(),
        ]);
    }

    public function store(StoreLoanRequest $request, Item $object, LoanService $service): RedirectResponse
    {
        $service->request(
            $object,
            $request->user(),
            $request->date('starts_at'),
            $request->date('ends_at'),
            $request->input('message'),
        );

        return redirect()->route('loans.index')
            ->with('status', 'Solicitarea de împrumut a fost trimisă.');
    }

    public function accept(Loan $loan, LoanService $service): RedirectResponse
    {
        $this->authorize('accept', $loan);

        $service->accept($loan);

        return back()->with('status', 'Solicitarea a fost acceptată.');
    }

    public function refuse(Request $request, Loan $loan, LoanService $service): RedirectResponse
    {
        $this->authorize('refuse', $loan);

        $service->refuse($loan, $request->input('refused_reason'));

        return back()->with('status', 'Solicitarea a fost refuzată.');
    }

    public function cancel(Loan $loan, LoanService $service): RedirectResponse
    {
        $this->authorize('cancel', $loan);

        $service->cancel($loan);

        return back()->with('status', 'Împrumutul a fost anulat.');
    }

    public function markBorrowed(Loan $loan, LoanService $service): RedirectResponse
    {
        $this->authorize('markBorrowed', $loan);

        $service->markBorrowed($loan);

        return back()->with('status', 'Ai confirmat predarea obiectului.');
    }

    public function markReturned(Loan $loan, LoanService $service): RedirectResponse
    {
        $this->authorize('markReturned', $loan);

        $service->markReturned($loan);

        return back()->with('status', 'Ai confirmat returnarea obiectului.');
    }

    public function complete(Loan $loan, LoanService $service): RedirectResponse
    {
        $this->authorize('complete', $loan);

        $service->complete($loan);

        return back()->with('status', 'Împrumutul a fost finalizat.');
    }

    public function review(Request $request, Loan $loan): RedirectResponse
    {
        $this->authorize('review', $loan);

        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = Review::updateOrCreate(
            ['loan_id' => $loan->id, 'reviewer_id' => $request->user()->id],
            [
                'reviewee_id' => $loan->otherParty($request->user())->id,
                'rating' => $request->integer('rating'),
                'comment' => $request->input('comment'),
            ]
        );

        $review->reviewee->notify(new ReviewReceived($review));

        return back()->with('status', 'Recenzia a fost salvată. Mulțumim!');
    }
}
