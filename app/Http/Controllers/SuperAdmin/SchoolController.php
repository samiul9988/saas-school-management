<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(): View
    {
        $schools = School::with('plan')->latest()->paginate(15);

        return view('super-admin.schools.index', compact('schools'));
    }

    public function create(): View
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return view('super-admin.schools.create', compact('plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'status' => ['required', 'in:pending,trial,active,suspended,expired'],
            'admin_password' => ['required', 'string', 'min:8'],
        ]);

        $school = School::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']).'-'.Str::random(5),
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'plan_id' => $data['plan_id'] ?? null,
            'status' => $data['status'],
            'trial_ends_at' => $data['status'] === 'trial' ? now()->addDays(14) : null,
        ]);

        User::create([
            'name' => $data['name'].' Admin',
            'email' => $data['email'],
            'password' => Hash::make($data['admin_password']),
            'role' => User::ROLE_SCHOOL_ADMIN,
            'school_id' => $school->id,
        ]);

        return redirect()->route('super-admin.schools.index')->with('status', 'School created successfully.');
    }

    public function show(School $school): View
    {
        $school->load('plan', 'users', 'subscriptions.payments');

        return view('super-admin.schools.show', compact('school'));
    }

    public function edit(School $school): View
    {
        $plans = Plan::orderBy('sort_order')->get();

        return view('super-admin.schools.edit', compact('school', 'plans'));
    }

    public function update(Request $request, School $school): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'status' => ['required', 'in:pending,trial,active,suspended,expired'],
        ]);

        $school->update($data);

        return redirect()->route('super-admin.schools.index')->with('status', 'School updated successfully.');
    }

    public function destroy(School $school): RedirectResponse
    {
        $school->delete();

        return redirect()->route('super-admin.schools.index')->with('status', 'School deleted successfully.');
    }
}
