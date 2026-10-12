<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\TransferCertificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TransferCertificateController extends Controller
{
    public const CAUSES = ['Family Relocation', 'Financial Problem', 'Discipline', 'Completion of Course', 'Others'];

    public function create(Request $request): View
    {
        $student = null;
        $certificate = null;

        if ($request->filled('student_id')) {
            $student = Student::with('schoolClass')->find($request->student_id)
                ?? Student::with('schoolClass')->where('admission_number', $request->student_id)->first();

            if ($student) {
                $certificate = TransferCertificate::where('student_id', $student->id)->latest()->first();
            }
        }

        $causes = self::CAUSES;

        return view('school.students.tc.create', compact('student', 'certificate', 'causes'));
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
            'cause_of_leaving' => ['nullable', 'string', 'max:255'],
            'examination_name' => ['nullable', 'string', 'max:255'],
            'examination_date' => ['nullable', 'date'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'session' => ['nullable', 'string', 'max:50'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:son,daughter'],
            'promoted_class' => ['nullable', 'string', 'max:255'],
        ]);

        $student = Student::findOrFail($data['student_id']);

        TransferCertificate::updateOrCreate(
            ['student_id' => $student->id],
            array_merge($data, [
                'serial' => (string) Str::uuid(),
                'student_name' => $student->name,
                'issue_date' => now(),
            ])
        );

        return redirect()->route('school.students.tc.create', ['student_id' => $student->id])
            ->with('status', 'Transfer certificate generated.');
    }

    public function index(): View
    {
        $certificates = TransferCertificate::with('student')->orderByDesc('id')->get();

        return view('school.students.tc.index', compact('certificates'));
    }

    public function download(TransferCertificate $transferCertificate)
    {
        $school = Auth::user()->school;
        $certificate = $transferCertificate;

        $pdf = Pdf::loadView('school.students.tc.pdf', compact('school', 'certificate'))->setPaper('a4', 'landscape');

        return $pdf->download('Transfer Certificate '.$certificate->student_name.'.pdf');
    }
}
