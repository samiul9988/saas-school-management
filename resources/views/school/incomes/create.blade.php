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
                        <x-input-label for="amount" value="Amount" />
                        <x-text-input id="amount" type="number" step="0.01" name="amount" class="mt-1 block w-full" value="{{ old('amount') }}" required />
                    </div>
                    <div>
                        <x-input-label for="date" value="Date" />
                        <x-text-input id="date" type="date" name="date" class="mt-1 block w-full" value="{{ old('date', now()->toDateString()) }}" required />
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
