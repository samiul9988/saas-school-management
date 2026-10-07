<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeatPlanController extends Controller
{
    public function index(Request $request): View
    {
        $classes = SchoolClass::orderBy('sort_order')->get();
        $exams = Exam::orderByDesc('starts_on')->get();

        $seats = collect();
        $exam = null;
        $class = null;

        if ($request->filled('exam_id') && $request->filled('school_class_id')) {
            $exam = Exam::findOrFail($request->exam_id);
            $class = SchoolClass::findOrFail($request->school_class_id);

            $perRoom = (int) $request->input('per_room', 20);
            $startingSeat = (int) $request->input('starting_seat', 1);

            $students = Student::where('school_class_id', $class->id)
                ->where('status', 'active')
                ->orderBy('roll')
                ->get();

            $seats = $students->values()->map(function ($student, $index) use ($startingSeat, $perRoom) {
                $seatNumber = $startingSeat + $index;
                $room = intdiv($index, $perRoom) + 1;
                $bench = ($seatNumber - 1) % $perRoom + 1;

                return [
                    'student' => $student,
                    'room' => $room,
                    'seat_no' => $bench,
                ];
            });
        }

        return view('school.exams.seat-plan', compact('classes', 'exams', 'seats', 'exam', 'class'));
    }
}
