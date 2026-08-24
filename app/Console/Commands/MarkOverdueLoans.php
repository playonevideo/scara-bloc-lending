<?php

namespace App\Console\Commands;

use App\Enums\LoanStatus;
use App\Models\Loan;
use App\Notifications\LoanNotification;
use Illuminate\Console\Command;

class MarkOverdueLoans extends Command
{
    protected $signature = 'loans:mark-overdue';

    protected $description = 'Marchează împrumuturile întârziate și notifică părțile.';

    public function handle(): int
    {
        $overdue = Loan::query()
            ->where('status', LoanStatus::Borrowed->value)
            ->where('ends_at', '<', now()->toDateString())
            ->get();

        foreach ($overdue as $loan) {
            $loan->update(['status' => LoanStatus::Overdue]);

            $loan->borrower->notify(new LoanNotification(
                $loan,
                "Împrumutul pentru „{$loan->object->title}” a depășit termenul de returnare."
            ));

            $loan->lender->notify(new LoanNotification(
                $loan,
                "Împrumutul pentru „{$loan->object->title}” este întârziat."
            ));
        }

        $this->info("{$overdue->count()} împrumuturi marcate ca întârziate.");

        return self::SUCCESS;
    }
}
