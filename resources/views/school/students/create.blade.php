<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Admit Student') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('school.students.store') }}" class="space-y-4">
                    @csrf
                    @include('school.students._form')

                    <div class="border-t pt-4" x-data="{ sendSms: true }">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <input type="checkbox" name="send_welcome_sms" value="1" x-model="sendSms" checked>
                            Send Welcome SMS to Guardian
                        </label>
                        <div x-show="sendSms" class="mt-2">
                            <x-input-label for="welcome_sms_content" value="SMS Content" />
                            <textarea id="welcome_sms_content" name="welcome_sms_content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('welcome_sms_content', $defaultSmsContent) }}</textarea>
                            <p class="text-xs text-gray-400 mt-1">Tags: @{{school_name}}, @{{student_name}}, @{{class}}, @{{student_id}}, @{{date}}</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Admit Student</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
