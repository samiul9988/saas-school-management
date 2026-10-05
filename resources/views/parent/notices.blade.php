<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Notices') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                @forelse ($notices as $notice)
                    <div class="border-b pb-4">
                        <div class="font-semibold">{{ $notice->title }}</div>
                        <div class="text-sm text-gray-500 mb-2">{{ $notice->published_at->format('Y-m-d H:i') }}</div>
                        <p class="text-sm text-gray-700">{{ $notice->body }}</p>
                    </div>
                @empty
                    <div class="text-center text-gray-400 py-4">No notices yet.</div>
                @endforelse
            </div>
            {{ $notices->links() }}
        </div>
    </div>
</x-app-layout>
