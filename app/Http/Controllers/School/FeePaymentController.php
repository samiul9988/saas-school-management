<?php

namespace App\Http\Controllers\School;

use App\Exceptions\Sms\SmsSendingException;
use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\StudentFee;
use App\Services\Sms\SmsSendingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FeePaymentController extends Controller
{
    public function __construct(private SmsSendingService $sms) {}

    public function create(StudentFee $studentFee): View
    {
        $studentFee->load('student', 'feeType');

        return view('school.student-fees.pay', compact('studentFee'));
    }

    public function store(Request $request, StudentFee $studentFee): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.$studentFee->dueAmount()],
            'method' => ['required', 'in:cash,bkash,nagad,bank,other'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'paid_on' => ['required', 'date'],
        ]);

        $payment = FeePayment::create([
            'student_fee_id' => $studentFee->id,
            'student_id' => $studentFee->student_id,
            'receipt_number' => FeePayment::generateReceiptNumber($studentFee->school_id),
            'amount' => $data['amount'],
            'method' => $data['method'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'paid_on' => $data['paid_on'],
            'received_by' => $request->user()->id,
        ]);

        $studentFee->paid_amount += $data['amount'];
        $studentFee->recalculateStatus();

        $smsStatus = null;

        if ($studentFee->student->guardian_phone) {
            try {
                $this->sms->send(
                    $request->user()->school,
                    [$studentFee->student->guardian_phone],
                    "Payment of {$data['amount']} received for {$studentFee->student->name}. Receipt: {$payment->receipt_number}.",
                    (string) Str::uuid(),
                    'fee_payment',
                    $request->user()->id,
                );
            } catch (SmsSendingException $e) {
                $smsStatus = $e->getMessage();
            }
        }

        return redirect()->route('school.fee-payments.receipt', $payment)
            ->with('status', 'Payment recorded.'.($smsStatus ? ' SMS not sent: '.$smsStatus : ''));
    }

    public function receipt(FeePayment $feePayment): View
    {
        $feePayment->load('student', 'studentFee.feeType', 'receivedBy');

        return view('school.student-fees.receipt', compact('feePayment'));
    }
}
