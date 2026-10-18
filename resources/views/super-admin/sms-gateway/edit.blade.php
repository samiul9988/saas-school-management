<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('SMS Gateway Settings') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Central Balance (internal ledger)</p>
                    <p class="text-2xl font-bold">{{ number_format($settings->central_balance) }} credits</p>
                </div>
                <form method="POST" action="{{ route('super-admin.sms-gateway.sync-balance') }}">
                    @csrf
                    <button type="submit" class="bg-gray-100 text-gray-700 px-3 py-2 rounded hover:bg-gray-200 text-sm">Check Provider Balance</button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-4">
                    Configure the central SMS gateway used to send all SMS on behalf of every school. This API key is never exposed to school users.
                </p>
                <form method="POST" action="{{ route('super-admin.sms-gateway.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="provider" value="Provider" />
                        <x-text-input id="provider" name="provider" class="mt-1 block w-full" value="{{ old('provider', $settings->provider) }}" required />
                    </div>
                    <div>
                        <x-input-label for="api_key" value="API Key" />
                        <x-text-input id="api_key" name="api_key" type="password" class="mt-1 block w-full" placeholder="{{ $settings->api_key ? 'Leave blank to keep current key' : '' }}" />
                    </div>
                    <div>
                        <x-input-label for="sender_id" value="Sender ID" />
                        <x-text-input id="sender_id" name="sender_id" class="mt-1 block w-full" value="{{ old('sender_id', $settings->sender_id) }}" />
                    </div>
                    <div>
                        <x-input-label for="api_base_url" value="API Base URL" />
                        <x-text-input id="api_base_url" name="api_base_url" class="mt-1 block w-full" value="{{ old('api_base_url', $settings->api_base_url) }}" placeholder="http://bulksmsbd.net" />
                    </div>
                    <div>
                        <x-input-label for="price_per_sms" value="Selling Price per SMS (to schools)" />
                        <x-text-input id="price_per_sms" name="price_per_sms" type="number" step="0.0001" min="0" class="mt-1 block w-full" value="{{ old('price_per_sms', $settings->price_per_sms) }}" required />
                    </div>
                    <div>
                        <x-input-label for="low_balance_threshold" value="Low Balance Alert Threshold (optional)" />
                        <x-text-input id="low_balance_threshold" name="low_balance_threshold" type="number" min="0" class="mt-1 block w-full" value="{{ old('low_balance_threshold', $settings->low_balance_threshold) }}" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
