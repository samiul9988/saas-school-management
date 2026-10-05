<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\SmsLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmsSettingController extends Controller
{
    public function edit(Request $request): View
    {
        $school = $request->user()->school;
        $logs = SmsLog::latest()->limit(20)->get();

        return view('school.sms-settings.edit', compact('school', 'logs'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sms_provider' => ['nullable', 'string', 'max:255'],
            'sms_api_key' => ['nullable', 'string', 'max:255'],
            'sms_sender_id' => ['nullable', 'string', 'max:255'],
        ]);

        $request->user()->school->update($data);

        return redirect()->route('school.sms-settings.edit')->with('status', 'SMS settings updated.');
    }
}
