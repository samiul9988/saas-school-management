<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Controllers\School\ResultController;
use App\Models\Exam;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $student = $request->user()->portalStudent;
        $student->load('schoolClass', 'section', 'fees.feeType');

        $notices = Notice::visibleTo('parents')->latest('published_at')->limit(5)->get();

        return view('parent.dashboard', compact('student', 'notices'));
    }

    public function attendance(Request $request): View
    {
        $student = $request->user()->portalStudent;
        $attendances = $student->attendances()->orderByDesc('date')->paginate(30);

        return view('parent.attendance', compact('student', 'attendances'));
    }

    public function fees(Request $request): View
    {
        $student = $request->user()->portalStudent;
        $fees = $student->fees()->with('feeType', 'payments')->latest()->get();

        return view('parent.fees', compact('student', 'fees'));
    }

    public function results(Request $request): View
    {
        $student = $request->user()->portalStudent;
        $exams = \App\Models\Exam::whereHas('examSubjects', fn ($q) => $q->where('school_class_id', $student->school_class_id))
            ->orderByDesc('starts_on')
            ->get();

        return view('parent.results', compact('student', 'exams'));
    }

    public function notices(Request $request): View
    {
        $notices = Notice::visibleTo('parents')->latest('published_at')->paginate(15);

        return view('parent.notices', compact('notices'));
    }

    public function marksheet(Request $request, Exam $exam): View
    {
        $student = $request->user()->portalStudent;

        return app(ResultController::class)->marksheet($exam, $student);
    }
}
