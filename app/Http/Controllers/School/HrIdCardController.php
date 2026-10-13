<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HrIdCardController extends Controller
{
    public const SELECTABLE_TEMPLATES = [1, 2];

    public function index(): View
    {
        $school = Auth::user()->school;

        return view('school.teachers.id-card.index', compact('school'));
    }

    public function single(): View
    {
        $teachers = Teacher::latest()->get();

        return view('school.teachers.id-card.single', compact('teachers'));
    }

    public function selectTemplate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'template' => ['required', 'integer', 'in:'.implode(',', self::SELECTABLE_TEMPLATES)],
        ]);

        Auth::user()->school->update(['hr_id_card_template' => $data['template']]);

        return redirect()->route('school.teachers.id-card.index')->with('status', 'ID card template selected.');
    }

    public function preview(Request $request, int $template): View
    {
        abort_unless(in_array($template, self::SELECTABLE_TEMPLATES), 404);

        $school = Auth::user()->school;
        $side = in_array($request->get('side'), ['front', 'back']) ? $request->get('side') : 'both';
        $teacher = (object) [
            'id' => 8002,
            'staff_serial' => '8002',
            'name' => 'Nazmus Sakib',
            'designation' => 'Teacher',
            'phone' => '01861006000',
            'email' => 'teacher@example.com',
            'blood_group' => 'O+',
            'photo' => null,
        ];

        return view('school.teachers.id-card.cards.preview', compact('school', 'teacher', 'template', 'side'));
    }

    public function download(Teacher $teacher)
    {
        $school = Auth::user()->school;
        $template = in_array($school->hr_id_card_template, self::SELECTABLE_TEMPLATES) ? $school->hr_id_card_template : 1;

        $pdf = Pdf::loadView('school.teachers.id-card.cards.single', compact('school', 'teacher', 'template'))
            ->setPaper([0, 0, 620, 260], 'portrait');

        return $pdf->download(($teacher->staff_serial ?: $teacher->id).'_'.str_replace(' ', '_', $teacher->name).'_ID_CARD.pdf');
    }
}
