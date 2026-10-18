<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsTemplateController extends Controller
{
    public function index(): View
    {
        $templates = SmsTemplate::orderBy('category')->orderByDesc('is_default')->get()->groupBy('category');

        return view('school.sms-templates.index', compact('templates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'in:'.implode(',', SmsTemplate::CATEGORIES)],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1000'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            SmsTemplate::where('category', $data['category'])->update(['is_default' => false]);
        }

        SmsTemplate::create($data + ['school_id' => $request->user()->school_id]);

        return redirect()->route('school.sms-templates.index')->with('status', 'Template saved.');
    }

    public function update(Request $request, SmsTemplate $smsTemplate): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:1000'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            SmsTemplate::where('category', $smsTemplate->category)->where('id', '!=', $smsTemplate->id)->update(['is_default' => false]);
        }

        $smsTemplate->update($data);

        return redirect()->route('school.sms-templates.index')->with('status', 'Template updated.');
    }

    public function destroy(SmsTemplate $smsTemplate): RedirectResponse
    {
        $smsTemplate->delete();

        return redirect()->route('school.sms-templates.index')->with('status', 'Template deleted.');
    }
}
