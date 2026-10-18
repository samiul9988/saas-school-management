<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Record SMS Purchase') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('super-admin.sms-credit-purchases.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="credits" value="Credits Purchased" />
                        <x-text-input id="credits" name="credits" type="number" min="1" class="mt-1 block w-full" value="{{ old('credits') }}" required />
                    </div>
                    <div>
                        <x-input-label for="cost" value="Total Cost" />
                        <x-text-input id="cost" name="cost" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('cost') }}" required />
                    </div>
                    <div>
                        <x-input-label for="provider_reference" value="Provider Reference (optional)" />
                        <x-text-input id="provider_reference" name="provider_reference" class="mt-1 block w-full" value="{{ old('provider_reference') }}" />
                    </div>
                    <div>
                        <x-input-label for="purchased_at" value="Purchase Date" />
                        <x-text-input id="purchased_at" name="purchased_at" type="date" class="mt-1 block w-full" value="{{ old('purchased_at', now()->format('Y-m-d')) }}" required />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Save Purchase</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
