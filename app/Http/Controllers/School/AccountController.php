<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Services\Accounting\ChartOfAccountSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        if (ChartOfAccount::count() === 0) {
            ChartOfAccountSeeder::seedFor($request->user()->school);
        }

        $accounts = ChartOfAccount::with('parent')->orderBy('code')->get()->groupBy('type');

        return view('school.accounts.index', compact('accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $schoolId = $request->user()->school_id;

        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('chart_of_accounts')->where('school_id', $schoolId)],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:'.implode(',', ChartOfAccount::TYPES)],
            'parent_id' => ['nullable', 'exists:chart_of_accounts,id'],
        ]);

        ChartOfAccount::create($data + ['school_id' => $request->user()->school_id]);

        return redirect()->route('school.accounts.index')->with('status', 'Account created.');
    }

    public function update(Request $request, ChartOfAccount $account): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $account->update($data);

        return redirect()->route('school.accounts.index')->with('status', 'Account updated.');
    }

    public function destroy(ChartOfAccount $account): RedirectResponse
    {
        if ($account->is_system) {
            return redirect()->route('school.accounts.index')->with('status', 'System accounts cannot be deleted.');
        }

        if ($account->lines()->exists() || $account->children()->exists()) {
            return redirect()->route('school.accounts.index')->with('status', 'Cannot delete an account with existing transactions or sub-accounts.');
        }

        $account->delete();

        return redirect()->route('school.accounts.index')->with('status', 'Account deleted.');
    }
}
