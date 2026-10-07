<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Admit & Seat Plan') }}</h2>
    </x-slot>

    <div class="space-y-4">
        <div class="bg-white shadow-sm rounded-lg p-6 print:hidden">
            <form method="GET" class="grid grid-cols-2 sm:grid-cols-5 gap-4 items-end">
                <div>
                    <x-input-label for="exam_id" value="Exam" />
                    <select id="exam_id" name="exam_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($exams as $e)
                            <option value="{{ $e->id }}" @selected(request('exam_id') == $e->id)>{{ $e->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="school_class_id" value="Class" />
                    <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" @selected(request('school_class_id') == $c->id)>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="per_room" value="Seats per Room" />
                    <x-text-input id="per_room" type="number" name="per_room" class="mt-1 block w-full" value="{{ request('per_room', 20) }}" />
                </div>
                <div>
                    <x-input-label for="starting_seat" value="Starting Seat No." />
                    <x-text-input id="starting_seat" type="number" name="starting_seat" class="mt-1 block w-full" value="{{ request('starting_seat', 1) }}" />
                </div>
                <div>
                    <x-primary-button>Generate</x-primary-button>
                </div>
            </form>
        </div>

        @if ($seats->isNotEmpty())
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-center mb-4 print:hidden">
                    <h3 class="font-semibold">{{ $exam->name }} &mdash; {{ $class->name }} Seat Plan</h3>
                    <button onclick="window.print()" class="text-indigo-600 hover:underline text-sm">Print</button>
                </div>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-2 px-3">Roll</th>
                            <th class="py-2 px-3">Name</th>
                            <th class="py-2 px-3">Room</th>
                            <th class="py-2 px-3">Seat No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($seats as $row)
                            <tr class="border-t">
                                <td class="py-2 px-3">{{ $row['student']->roll }}</td>
                                <td class="py-2 px-3">{{ $row['student']->name }}</td>
                                <td class="py-2 px-3">{{ $row['room'] }}</td>
                                <td class="py-2 px-3">{{ $row['seat_no'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
