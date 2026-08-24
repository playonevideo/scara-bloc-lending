<?php

namespace App\Http\Controllers;

use App\Enums\ReportReason;
use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Models\Report;
use App\Models\User;
use App\Notifications\ObjectReported;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reportable_type' => ['required', 'string', Rule::in(['object', 'message', 'user'])],
            'reportable_id' => ['required', 'integer'],
            'reason' => ['required', Rule::enum(ReportReason::class)],
            'details' => ['nullable', 'string', 'max:3000'],
        ]);

        $modelClass = Relation::getMorphedModel($validated['reportable_type'])
            ?? 'App\\Models\\'.ucfirst($validated['reportable_type']);

        if (! $modelClass || ! class_exists($modelClass)) {
            abort(404);
        }

        $reportable = $modelClass::findOrFail($validated['reportable_id']);

        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'reportable_type' => $validated['reportable_type'],
            'reportable_id' => $validated['reportable_id'],
            'reason' => $validated['reason'],
            'details' => $validated['details'] ?? null,
            'status' => ReportStatus::New,
        ]);

        User::query()
            ->where('role', Role::Admin->value)
            ->get()
            ->each(fn (User $admin) => $admin->notify(new ObjectReported($report)));

        return back()->with('status', 'Mulțumim! Raportarea a fost trimisă administratorilor.');
    }
}
