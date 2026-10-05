<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Parent Dashboard') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="font-semibold text-lg">{{ $student->name }}</div>
                <div class="text-sm text-gray-500">
                    Class: {{ $student->schoolClass?->name ?? '—' }} &middot;
                    Section: {{ $student->section?->name ?? '—' }} &middot;
                    Roll: {{ $student->roll ?? '—' }}
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('parent.attendance') }}" class="bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md block">
                    <div class="text-sm text-gray-500">Attendance</div>
                    <div class="text-indigo-600 font-semibold">View Records &rarr;</div>
                </a>
                <a href="{{ route('parent.fees') }}" class="bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md block">
                    <div class="text-sm text-gray-500">Fees</div>
                    <div class="text-indigo-600 font-semibold">View &amp; Pay &rarr;</div>
                </a>
                <a href="{{ route('parent.results') }}" class="bg-white shadow-sm sm:rounded-lg p-6 hover:shadow-md block">
                    <div class="text-sm text-gray-500">Results</div>
                    <div class="text-indigo-600 font-semibold">View Results &rarr;</div>
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg">Latest Notices</h3>
                    <a href="{{ route('parent.notices') }}" class="text-sm text-indigo-600 hover:underline">View all</a>
                </div>
                <ul class="space-y-3">
                    @forelse ($notices as $notice)
                        <li class="border-b pb-2">
                            <div class="font-medium">{{ $notice->title }}</div>
                            <div class="text-sm text-gray-500">{{ $notice->published_at->format('Y-m-d') }}</div>
                        </li>
                    @empty
                        <li class="text-gray-400 text-center py-4">No notices yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
