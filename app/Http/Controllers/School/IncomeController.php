<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(): View
    {
        $incomes = Income::latest('date')->paginate(20);

        return view('school.incomes.index', compact('incomes'));
    }

    public function create(): View
    {
        return view('school.incomes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Income::create($this->validated($request));

        return redirect()->route('school.incomes.index')->with('status', 'Income recorded.');
    }

    public function destroy(Income $income): RedirectResponse
    {
        $income->delete();

        return redirect()->route('school.incomes.index')->with('status', 'Income removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
