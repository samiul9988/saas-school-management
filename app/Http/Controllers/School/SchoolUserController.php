<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SchoolUserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::where('school_id', $request->user()->school_id)
            ->whereIn('role', User::SCHOOL_STAFF_ROLES)
            ->latest()
            ->get();

        return view('school.users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:'.implode(',', User::SCHOOL_STAFF_ROLES)],
        ]);

        User::create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'school_id' => $request->user()->school_id,
        ]);

        return redirect()->route('school.users.index')->with('status', 'User created.');
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        $this->authorizeSameSchool($request, $user);

        $user->update(['is_active' => ! $user->is_active]);

        return redirect()->route('school.users.index')->with('status', 'User status updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorizeSameSchool($request, $user);

        if ($user->id === $request->user()->id) {
            return redirect()->route('school.users.index')->with('status', 'You cannot delete your own account.');
        }

        $remainingAdmins = User::where('school_id', $request->user()->school_id)
            ->where('role', User::ROLE_SCHOOL_ADMIN)
            ->where('id', '!=', $user->id)
            ->count();

        if ($user->role === User::ROLE_SCHOOL_ADMIN && $remainingAdmins === 0) {
            return redirect()->route('school.users.index')->with('status', 'Cannot delete the only admin for this school.');
        }

        $user->delete();

        return redirect()->route('school.users.index')->with('status', 'User deleted.');
    }

    private function authorizeSameSchool(Request $request, User $user): void
    {
        abort_unless($user->school_id === $request->user()->school_id, 403);
    }
}
