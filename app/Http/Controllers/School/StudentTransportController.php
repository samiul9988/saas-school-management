<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentTransport;
use App\Models\TransportRoute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentTransportController extends Controller
{
    public function index(): View
    {
        $assignments = StudentTransport::with('student', 'route', 'stop')->latest()->paginate(20);

        return view('school.transport.assignments.index', compact('assignments'));
    }

    public function create(): View
    {
        $students = Student::where('status', 'active')->orderBy('name')->get();
        $routes = TransportRoute::with('stops')->orderBy('name')->get();

        return view('school.transport.assignments.create', compact('students', 'routes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'transport_route_id' => ['required', 'exists:transport_routes,id'],
            'route_stop_id' => ['nullable', 'exists:route_stops,id'],
            'monthly_fee' => ['required', 'numeric', 'min:0'],
        ]);

        StudentTransport::updateOrCreate(['student_id' => $data['student_id']], $data);

        return redirect()->route('school.student-transports.index')->with('status', 'Transport assigned.');
    }

    public function destroy(StudentTransport $studentTransport): RedirectResponse
    {
        $studentTransport->delete();

        return redirect()->route('school.student-transports.index')->with('status', 'Assignment removed.');
    }
}
