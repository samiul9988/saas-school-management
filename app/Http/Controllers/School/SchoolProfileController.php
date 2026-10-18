<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $school = $request->user()->school;
        $academicYears = AcademicYear::orderByDesc('starts_on')->get();

        return view('school.profile.edit', compact('school', 'academicYears'));
    }

    public function update(Request $request): RedirectResponse
    {
        $school = $request->user()->school;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'admin_name' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }

            $data['logo'] = $request->file('logo')->store('school-logos', 'public');
        }

        $school->update($data);

        return redirect()->route('school.profile.edit')->with('status', 'Institute profile updated.');
    }
}
