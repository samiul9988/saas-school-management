<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookCategoryController extends Controller
{
    public function index(): View
    {
        $categories = BookCategory::withCount('books')->orderBy('name')->get();

        return view('school.library.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => ['required', 'string', 'max:255']]);

        BookCategory::create($request->only('name'));

        return redirect()->route('school.book-categories.index')->with('status', 'Category added.');
    }

    public function destroy(BookCategory $bookCategory): RedirectResponse
    {
        $bookCategory->delete();

        return redirect()->route('school.book-categories.index')->with('status', 'Category removed.');
    }
}
