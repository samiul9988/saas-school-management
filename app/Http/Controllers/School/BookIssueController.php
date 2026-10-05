<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookIssueController extends Controller
{
    public function index(): View
    {
        $issues = BookIssue::with('book', 'student', 'teacher')->latest('issued_on')->paginate(20);

        return view('school.library.issues.index', compact('issues'));
    }

    public function create(): View
    {
        $books = Book::where('available_copies', '>', 0)->orderBy('title')->get();
        $students = Student::where('status', 'active')->orderBy('name')->get();
        $teachers = Teacher::where('status', 'active')->orderBy('name')->get();

        return view('school.library.issues.create', compact('books', 'students', 'teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'student_id' => ['nullable', 'exists:students,id'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'issued_on' => ['required', 'date'],
            'due_on' => ['required', 'date', 'after_or_equal:issued_on'],
        ]);

        $book = Book::findOrFail($data['book_id']);

        if ($book->available_copies < 1) {
            return back()->withErrors(['book_id' => 'No copies available.']);
        }

        BookIssue::create($data);
        $book->decrement('available_copies');

        return redirect()->route('school.book-issues.index')->with('status', 'Book issued.');
    }

    public function returnBook(BookIssue $bookIssue): RedirectResponse
    {
        if ($bookIssue->returned_on) {
            return back()->with('status', 'Already returned.');
        }

        $fine = $bookIssue->isOverdue() ? now()->diffInDays($bookIssue->due_on) * 5 : 0;

        $bookIssue->update(['returned_on' => now(), 'fine_amount' => $fine]);
        $bookIssue->book->increment('available_copies');

        return redirect()->route('school.book-issues.index')->with('status', 'Book returned.');
    }
}
