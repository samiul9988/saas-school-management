<?php

namespace App\Http\Controllers\School;

use App\Exceptions\Sms\SmsSendingException;
use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use App\Services\Sms\SmsSendingService;
use App\Services\Sms\SmsTemplateRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DynamicSmsController extends Controller
{
    public function index(): View
    {
        $defaultContent = SmsTemplate::defaultContentFor('dynamic');

        return view('school.sms-send.dynamic', compact('defaultContent'));
    }

    public function store(Request $request, SmsSendingService $sendingService, SmsTemplateRenderer $renderer): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', function ($attribute, $value, $fail) {
                if (! in_array(strtolower($value->getClientOriginalExtension()), ['csv', 'xls', 'xlsx'], true)) {
                    $fail('The file must be a csv, xls, or xlsx file.');
                }
            }],
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

        $header = array_map(fn ($value) => Str::slug(trim((string) $value), '_'), array_shift($rows) ?? []);

        $phoneIndex = array_search('phone', $header, true);
        if ($phoneIndex === false) {
            $phoneIndex = array_search('mobile', $header, true);
        }

        if ($phoneIndex === false) {
            return redirect()->route('school.sms-send.dynamic.index')->with('status', 'The file must have a "Phone" or "Mobile" column.');
        }

        $sent = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $values = [];
            foreach ($header as $index => $key) {
                if ($key !== '') {
                    $values[$key] = trim((string) ($row[$index] ?? ''));
                }
            }

            $phone = trim((string) ($row[$phoneIndex] ?? ''));

            if ($phone === '') {
                $skipped++;

                continue;
            }

            $message = $renderer->render($data['content'], $values);

            try {
                $sendingService->send($request->user()->school, [$phone], $message, (string) Str::uuid(), 'dynamic_sms', $request->user()->id);
                $sent++;
            } catch (SmsSendingException $e) {
                $failed++;
                break;
            }
        }

        return redirect()->route('school.sms-send.dynamic.index')->with('status', "Sent: {$sent}, Skipped (no phone): {$skipped}".($failed ? ", stopped early: {$failed}" : ''));
    }
}
