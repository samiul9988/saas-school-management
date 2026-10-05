<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Collect Payment') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mb-4 text-sm text-gray-600">
                    <div><span class="text-gray-500">Student:</span> {{ $studentFee->student->name }}</div>
                    <div><span class="text-gray-500">Fee:</span> {{ $studentFee->feeType->name }}</div>
                    <div><span class="text-gray-500">Total:</span> {{ number_format($studentFee->amount, 2) }}</div>
                    <div><span class="text-gray-500">Paid so far:</span> {{ number_format($studentFee->paid_amount, 2) }}</div>
                    <div class="font-semibold"><span class="text-gray-500">Due:</span> {{ number_format($studentFee->dueAmount(), 2) }}</div>
                </div>

                <form method="POST" action="{{ route('school.fee-payments.store', $studentFee) }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="amount" value="Amount" />
                        <x-text-input id="amount" type="number" step="0.01" name="amount" class="mt-1 block w-full" value="{{ old('amount', $studentFee->dueAmount()) }}" required />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="method" value="Payment Method" />
                        <select id="method" name="method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="cash">Cash</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="bank">Bank</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="transaction_id" value="Transaction ID (for bKash/Nagad/Bank)" />
                        <x-text-input id="transaction_id" name="transaction_id" class="mt-1 block w-full" value="{{ old('transaction_id') }}" />
                    </div>
                    <div>
                        <x-input-label for="paid_on" value="Paid On" />
                        <x-text-input id="paid_on" type="date" name="paid_on" class="mt-1 block w-full" value="{{ old('paid_on', now()->toDateString()) }}" required />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Record Payment</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
