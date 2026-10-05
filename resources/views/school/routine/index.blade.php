<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Class Routine') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 flex justify-between items-end">
                <form method="GET" class="flex gap-4 items-end">
                    <div>
                        <x-input-label for="school_class_id" value="Class" />
                        <select id="school_class_id" name="school_class_id" class="mt-1 border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                            <option value="">— Select class —</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(request('school_class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($sections->isNotEmpty())
                        <div>
                            <x-input-label for="section_id" value="Section" />
                            <select id="section_id" name="section_id" class="mt-1 border-gray-300 rounded-md shadow-sm" onchange="this.form.submit()">
                                <option value="">All sections</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}" @selected(request('section_id') == $section->id)>{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </form>
                <a href="{{ route('school.routine.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    + Add Period
                </a>
            </div>

            @if ($periods->isNotEmpty())
                @foreach (\App\Models\RoutinePeriod::DAYS as $dayNum => $dayName)
                    @if ($periods->has($dayNum))
                        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 font-semibold text-gray-700">{{ $dayName }}</div>
                            <table class="w-full text-sm text-left">
                                <tbody>
                                    @foreach ($periods[$dayNum] as $period)
                                        <tr class="border-t">
                                            <td class="py-2 px-4">{{ \Illuminate\Support\Carbon::parse($period->starts_at)->format('H:i') }} - {{ \Illuminate\Support\Carbon::parse($period->ends_at)->format('H:i') }}</td>
                                            <td class="py-2 px-4">{{ $period->subject?->name ?? '—' }}</td>
                                            <td class="py-2 px-4">{{ $period->teacher?->name ?? '—' }}</td>
                                            <td class="py-2 px-4">
                                                <form action="{{ route('school.routine.destroy', $period) }}" method="POST" onsubmit="return confirm('Remove this period?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Remove</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endforeach
            @elseif (request('school_class_id'))
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-400">No periods scheduled yet.</div>
            @endif
        </div>
    </div>
</x-app-layout>
