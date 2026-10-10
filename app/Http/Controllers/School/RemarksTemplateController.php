<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\RemarkTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RemarksTemplateController extends Controller
{
    public function index(): View
    {
        $templates = RemarkTemplate::orderByDesc('id')->get();

        return view('school.subjects.remarks-template', compact('templates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        RemarkTemplate::create($data);

        return redirect()->route('school.exam-configuration.remarks-template')->with('status', 'Remarks template saved.');
    }

    public function update(Request $request, RemarkTemplate $remarkTemplate): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $remarkTemplate->update($data);

        return redirect()->route('school.exam-configuration.remarks-template')->with('status', 'Remarks template updated.');
    }

    public function destroy(RemarkTemplate $remarkTemplate): RedirectResponse
    {
        $remarkTemplate->delete();

        return redirect()->route('school.exam-configuration.remarks-template')->with('status', 'Remarks template removed.');
    }
}
