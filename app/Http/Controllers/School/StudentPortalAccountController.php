<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentPortalAccountController extends Controller
{
    public function store(Request $request, Student $student): RedirectResponse
    {
        if ($student->portal_user_id) {
            return redirect()->route('school.students.show', $student)->with('status', 'Portal account already exists.');
        }

        $data = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $student->guardian_name ?: $student->name.' Guardian',
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_PARENT,
            'school_id' => $student->school_id,
        ]);

        $student->update(['portal_user_id' => $user->id]);

        return redirect()->route('school.students.show', $student)->with('status', 'Parent portal account created.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $userId = $student->portal_user_id;
        $student->update(['portal_user_id' => null]);

        if ($userId) {
            User::destroy($userId);
        }

        return redirect()->route('school.students.show', $student)->with('status', 'Parent portal account removed.');
    }
}
