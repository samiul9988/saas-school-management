<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoticeController extends Controller
{
    public function index(): View
    {
        $notices = Notice::with('creator')->latest('created_at')->paginate(15);

        return view('school.notices.index', compact('notices'));
    }

    public function create(): View
    {
        return view('school.notices.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        Notice::create($data);

        return redirect()->route('school.notices.index')->with('status', 'Notice published.');
    }

    public function edit(Notice $notice): View
    {
        return view('school.notices.edit', compact('notice'));
    }

    public function update(Request $request, Notice $notice): RedirectResponse
    {
        $notice->update($this->validated($request));

        return redirect()->route('school.notices.index')->with('status', 'Notice updated.');
    }

    public function destroy(Notice $notice): RedirectResponse
    {
        $notice->delete();

        return redirect()->route('school.notices.index')->with('status', 'Notice deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'audience' => ['required', 'in:all,students,teachers,parents'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['published_at'] = $data['published_at'] ?? now();

        return $data;
    }
}
