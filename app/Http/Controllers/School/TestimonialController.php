<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Testimonial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function create(Request $request): View
    {
        $student = null;
        $testimonial = null;

        if ($request->filled('student_id')) {
            $student = Student::with('schoolClass')->find($request->student_id)
                ?? Student::with('schoolClass')->where('admission_number', $request->student_id)->first();

            if ($student) {
                $testimonial = Testimonial::where('student_id', $student->id)->latest()->first();
            }
        }

        return view('school.students.testimonial.create', compact('student', 'testimonial'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'class_name' => ['nullable', 'string', 'max:255'],
            'roll' => ['nullable', 'string', 'max:50'],
            'gpa' => ['nullable', 'string', 'max:20'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'session' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
        ]);

        $student = Student::findOrFail($data['student_id']);

        Testimonial::updateOrCreate(
            ['student_id' => $student->id],
            array_merge($data, [
                'serial' => (string) Str::uuid(),
                'student_name' => $student->name,
                'issue_date' => now(),
            ])
        );

        return redirect()->route('school.students.testimonial.create', ['student_id' => $student->id])
            ->with('status', 'Testimonial generated.');
    }

    public function index(): View
    {
        $testimonials = Testimonial::with('student')->orderByDesc('id')->get();

        return view('school.students.testimonial.index', compact('testimonials'));
    }

    public function download(Testimonial $testimonial)
    {
        $school = Auth::user()->school;
        $student = $testimonial->student;

        $pdf = Pdf::loadView('school.students.testimonial.pdf', compact('school', 'testimonial', 'student'))->setPaper('a4', 'landscape');

        return $pdf->download('Testimonial '.$testimonial->student_name.'.pdf');
    }
}
