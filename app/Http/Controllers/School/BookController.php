<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(): View
    {
        $books = Book::with('category')->orderBy('title')->paginate(20);

        return view('school.library.books.index', compact('books'));
    }

    public function create(): View
    {
        $categories = BookCategory::orderBy('name')->get();

        return view('school.library.books.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['available_copies'] = $data['total_copies'];

        Book::create($data);

        return redirect()->route('school.books.index')->with('status', 'Book added.');
    }

    public function edit(Book $book): View
    {
        $categories = BookCategory::orderBy('name')->get();

        return view('school.library.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $this->validated($request);
        $diff = $data['total_copies'] - $book->total_copies;
        $data['available_copies'] = max(0, $book->available_copies + $diff);

        $book->update($data);

        return redirect()->route('school.books.index')->with('status', 'Book updated.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('school.books.index')->with('status', 'Book removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'book_category_id' => ['nullable', 'exists:book_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255'],
            'total_copies' => ['required', 'integer', 'min:1'],
        ]);
    }
}
