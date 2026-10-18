<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Buy SMS Credits') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-4">
                    Current price: <span class="font-semibold">{{ number_format($pricePerSms, 4) }}</span> per SMS credit.
                    This is a purchase request &mdash; your wallet will be credited once the super admin approves it.
                </p>
                <form method="POST" action="{{ route('school.sms-credit-orders.store') }}" class="space-y-4" x-data="{ credits: 0, price: {{ $pricePerSms }} }">
                    @csrf
                    <div>
                        <x-input-label for="credits" value="Quantity" />
                        <x-text-input id="credits" name="credits" type="number" min="1" class="mt-1 block w-full" x-model.number="credits" value="{{ old('credits') }}" required />
                    </div>
                    <div class="text-sm text-gray-600">
                        Total payable: <span class="font-semibold" x-text="(credits * price).toFixed(2)"></span>
                    </div>
                    <div>
                        <x-input-label for="method" value="Payment Method" />
                        <select id="method" name="method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="manual">Manual / Cash</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="mobile_banking">Mobile Banking</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="transaction_id" value="Transaction / Reference ID (optional)" />
                        <x-text-input id="transaction_id" name="transaction_id" class="mt-1 block w-full" value="{{ old('transaction_id') }}" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Submit Request</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
