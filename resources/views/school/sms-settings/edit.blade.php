<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('SMS Settings') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-4">Configure your SMS gateway provider credentials. Until configured, messages are logged only (not actually sent).</p>
                <form method="POST" action="{{ route('school.sms-settings.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label for="sms_provider" value="Provider Name" />
                        <x-text-input id="sms_provider" name="sms_provider" class="mt-1 block w-full" value="{{ old('sms_provider', $school->sms_provider) }}" placeholder="e.g. BulkSMSBD" />
                    </div>
                    <div>
                        <x-input-label for="sms_api_key" value="API Key" />
                        <x-text-input id="sms_api_key" name="sms_api_key" class="mt-1 block w-full" value="{{ old('sms_api_key', $school->sms_api_key) }}" />
                    </div>
                    <div>
                        <x-input-label for="sms_sender_id" value="Sender ID" />
                        <x-text-input id="sms_sender_id" name="sms_sender_id" class="mt-1 block w-full" value="{{ old('sms_sender_id', $school->sms_sender_id) }}" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold mb-3">Recent SMS Log</h3>
                <table class="w-full text-sm text-left">
                    <thead><tr class="text-gray-500 border-b"><th class="py-2">To</th><th class="py-2">Message</th><th class="py-2">Status</th><th class="py-2">Sent</th></tr></thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="border-b">
                                <td class="py-2">{{ $log->to }}</td>
                                <td class="py-2 truncate max-w-xs">{{ $log->message }}</td>
                                <td class="py-2 capitalize">{{ $log->status }}</td>
                                <td class="py-2">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-4 text-center text-gray-400">No SMS sent yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
