<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Services\Accounting\AccountingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalEntryController extends Controller
{
    public function index(): View
    {
        $entries = JournalEntry::with('lines.account')->latest('date')->latest('id')->paginate(20);

        return view('school.journal-entries.index', compact('entries'));
    }

    public function create(): View
    {
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();

        return view('school.journal-entries.create', compact('accounts'));
    }

    public function store(Request $request, AccountingService $accounting): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:100'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:chart_of_accounts,id'],
            'lines.*.debit' => ['nullable', 'numeric', 'min:0'],
            'lines.*.credit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $lines = array_map(fn ($line) => [
            'account_id' => $line['account_id'],
            'debit' => (float) ($line['debit'] ?? 0),
            'credit' => (float) ($line['credit'] ?? 0),
        ], $data['lines']);

        try {
            $accounting->post(
                $request->user()->school,
                $data['date'],
                $lines,
                $data['reference'] ?? null,
                $data['description'] ?? null,
                'manual',
                null,
                $request->user(),
            );
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('status', $e->getMessage());
        }

        return redirect()->route('school.journal-entries.index')->with('status', 'Journal entry posted.');
    }

    public function show(JournalEntry $journalEntry): View
    {
        $journalEntry->load('lines.account', 'createdBy');

        return view('school.journal-entries.show', compact('journalEntry'));
    }

    public function reverse(JournalEntry $journalEntry, AccountingService $accounting, Request $request): RedirectResponse
    {
        try {
            $accounting->reverse($journalEntry, $request->user());
        } catch (\RuntimeException $e) {
            return back()->with('status', $e->getMessage());
        }

        return redirect()->route('school.journal-entries.index')->with('status', 'Journal entry reversed.');
    }
}
