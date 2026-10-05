<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::orderBy('sort_order')->get();

        return view('super-admin.plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('super-admin.plans.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);
        $data['features'] = $this->parseFeatures($request->input('features', ''));
        $data['is_active'] = $request->boolean('is_active');

        Plan::create($data);

        return redirect()->route('super-admin.plans.index')->with('status', 'Plan created successfully.');
    }

    public function edit(Plan $plan): View
    {
        return view('super-admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $this->validated($request);
        $data['features'] = $this->parseFeatures($request->input('features', ''));
        $data['is_active'] = $request->boolean('is_active');

        $plan->update($data);

        return redirect()->route('super-admin.plans.index')->with('status', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route('super-admin.plans.index')->with('status', 'Plan deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'yearly_price' => ['required', 'numeric', 'min:0'],
            'max_students' => ['nullable', 'integer', 'min:0'],
            'max_teachers' => ['nullable', 'integer', 'min:0'],
            'max_branches' => ['required', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);
    }

    private function parseFeatures(string $raw): array
    {
        return collect(explode(',', $raw))
            ->map(fn ($f) => trim($f))
            ->filter()
            ->values()
            ->all();
    }
}
