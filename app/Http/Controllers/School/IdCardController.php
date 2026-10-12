<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IdCardController extends Controller
{
    public const SELECTABLE_TEMPLATES = [1, 2];

    public function index(): View
    {
        $sections = Section::with('schoolClass')->orderBy('name')->get();
        $school = Auth::user()->school;

        return view('school.students.id-card.index', compact('sections', 'school'));
    }

    public function selectTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'template' => ['required', 'integer', 'in:'.implode(',', self::SELECTABLE_TEMPLATES)],
        ]);

        Auth::user()->school->update(['id_card_template' => $data['template']]);

        return redirect()->route('school.students.id-card.index')->with('status', 'ID card template selected.');
    }

    public function preview(Request $request, int $template): View
    {
        abort_unless(in_array($template, self::SELECTABLE_TEMPLATES), 404);

        $school = Auth::user()->school;
        $side = in_array($request->get('side'), ['front', 'back']) ? $request->get('side') : 'both';
        $student = (object) [
            'name' => 'Nazmus Sakib',
            'admission_number' => '123456',
            'roll' => '01',
            'photo' => null,
            'schoolClass' => (object) ['name' => 'Nine'],
            'section' => (object) ['name' => 'Lal'],
            'group_name' => 'Science',
            'guardian_phone' => '01861006000',
        ];

        return view('school.students.id-card.cards.preview', compact('school', 'student', 'template', 'side'));
    }

    private function studentsForDownload(Request $request)
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'from_roll' => ['nullable', 'numeric'],
            'to_roll' => ['nullable', 'numeric'],
        ]);

        $section = Section::with('schoolClass')->findOrFail($data['section_id']);

        $students = Student::with('schoolClass', 'section')
            ->where('section_id', $section->id)
            ->when($data['from_roll'] ?? null, fn ($q) => $q->where('roll', '>=', $data['from_roll']))
            ->when($data['to_roll'] ?? null, fn ($q) => $q->where('roll', '<=', $data['to_roll']))
            ->orderByRaw('CAST(roll AS UNSIGNED)')
            ->get();

        return [$section, $students];
    }

    public function downloadFront(Request $request)
    {
        [$section, $students] = $this->studentsForDownload($request);
        $school = Auth::user()->school;
        $template = in_array($school->id_card_template, self::SELECTABLE_TEMPLATES) ? $school->id_card_template : 1;

        $pdf = Pdf::loadView('school.students.id-card.cards.batch-front', compact('school', 'students', 'template'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('ID Card Front '.$section->name.'.pdf');
    }

    public function downloadBack(Request $request)
    {
        [$section, $students] = $this->studentsForDownload($request);
        $school = Auth::user()->school;
        $template = in_array($school->id_card_template, self::SELECTABLE_TEMPLATES) ? $school->id_card_template : 1;

        $pdf = Pdf::loadView('school.students.id-card.cards.batch-back', compact('school', 'students', 'template'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('ID Card Back '.$section->name.'.pdf');
    }
}
