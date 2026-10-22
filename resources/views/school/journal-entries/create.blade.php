<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('New Journal Entry') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('school.journal-entries.store') }}" class="space-y-4" x-data="{
                    lines: [{account_id: '', debit: '', credit: ''}, {account_id: '', debit: '', credit: ''}],
                    totalDebit() { return this.lines.reduce((s, l) => s + (parseFloat(l.debit) || 0), 0).toFixed(2); },
                    totalCredit() { return this.lines.reduce((s, l) => s + (parseFloat(l.credit) || 0), 0).toFixed(2); },
                }">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="date" value="Date" />
                            <x-text-input id="date" type="date" name="date" class="mt-1 block w-full" value="{{ old('date', now()->toDateString()) }}" required />
                        </div>
                        <div>
                            <x-input-label for="reference" value="Reference (optional)" />
                            <x-text-input id="reference" name="reference" class="mt-1 block w-full" value="{{ old('reference') }}" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="description" value="Description" />
                        <x-text-input id="description" name="description" class="mt-1 block w-full" value="{{ old('description') }}" />
                    </div>

                    <div>
                        <x-input-label value="Lines" />
                        <template x-for="(line, index) in lines" :key="index">
                            <div class="grid grid-cols-4 gap-2 mt-2">
                                <select :name="'lines['+index+'][account_id]'" x-model="line.account_id" class="border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">-- Account --</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" step="0.01" :name="'lines['+index+'][debit]'" x-model="line.debit" placeholder="Debit" class="border-gray-300 rounded-md shadow-sm text-sm">
                                <input type="number" step="0.01" :name="'lines['+index+'][credit]'" x-model="line.credit" placeholder="Credit" class="border-gray-300 rounded-md shadow-sm text-sm">
                                <button type="button" @click="lines.splice(index, 1)" class="text-red-600 text-xs">Remove</button>
                            </div>
                        </template>
                        <button type="button" @click="lines.push({account_id: '', debit: '', credit: ''})" class="mt-2 text-indigo-600 text-sm">+ Add Line</button>
                    </div>

                    <div class="text-sm text-gray-600 flex gap-6">
                        <span>Total Debit: <span x-text="totalDebit()"></span></span>
                        <span>Total Credit: <span x-text="totalCredit()"></span></span>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Post Entry</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
