<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Add Income') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('school.incomes.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="category" value="Category" />
                        <x-text-input id="category" name="category" class="mt-1 block w-full" value="{{ old('category') }}" placeholder="e.g. Donation, Rent, Other" required />
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="account_id" value="Income Account" />
                        <select id="account_id" name="account_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Select --</option>
                            @foreach ($incomeAccounts as $account)
                                <option value="{{ $account->id }}" @selected(old('account_id') == $account->id)>{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="amount" value="Amount" />
                        <x-text-input id="amount" type="number" step="0.01" name="amount" class="mt-1 block w-full" value="{{ old('amount') }}" required />
                    </div>
                    <div>
                        <x-input-label for="date" value="Date" />
                        <x-text-input id="date" type="date" name="date" class="mt-1 block w-full" value="{{ old('date', now()->toDateString()) }}" required />
                    </div>
                    <div>
                        <x-input-label for="method" value="Received Via" />
                        <select id="method" name="method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="cash">Cash</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="bank">Bank</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="payment_account_id" value="Deposited To" />
                        <select id="payment_account_id" name="payment_account_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Select --</option>
                            @foreach ($paymentAccounts as $account)
                                <option value="{{ $account->id }}" @selected(old('payment_account_id') == $account->id)>{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="reference" value="Reference (optional)" />
                        <x-text-input id="reference" name="reference" class="mt-1 block w-full" value="{{ old('reference') }}" />
                    </div>
                    <div>
                        <x-input-label for="description" value="Description" />
                        <x-text-input id="description" name="description" class="mt-1 block w-full" value="{{ old('description') }}" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
