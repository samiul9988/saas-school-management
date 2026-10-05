<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Salary Structures') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('school.payrolls.index') }}" class="text-indigo-600 hover:underline text-sm self-center mr-auto">View Monthly Payroll &rarr;</a>
            </div>

            @if ($teachersWithout->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold mb-3">Set Up Salary Structure</h3>
                    <form method="POST" action="{{ route('school.salary-structures.store') }}" class="flex flex-wrap gap-2 items-end">
                        @csrf
                        <div>
                            <x-input-label for="teacher_id" value="Teacher" />
                            <select id="teacher_id" name="teacher_id" class="mt-1 border-gray-300 rounded-md shadow-sm" required>
                                <option value="">— Select —</option>
                                @foreach ($teachersWithout as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="basic_salary" value="Basic" />
                            <x-text-input id="basic_salary" type="number" step="0.01" name="basic_salary" class="mt-1 w-28" required />
                        </div>
                        <div>
                            <x-input-label for="allowances" value="Allowances" />
                            <x-text-input id="allowances" type="number" step="0.01" name="allowances" class="mt-1 w-28" value="0" required />
                        </div>
                        <div>
                            <x-input-label for="deductions" value="Deductions" />
                            <x-text-input id="deductions" type="number" step="0.01" name="deductions" class="mt-1 w-28" value="0" required />
                        </div>
                        <x-primary-button>Save</x-primary-button>
                    </form>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-3 px-4">Teacher</th>
                            <th class="py-3 px-4">Basic</th>
                            <th class="py-3 px-4">Allowances</th>
                            <th class="py-3 px-4">Deductions</th>
                            <th class="py-3 px-4">Net</th>
                            <th class="py-3 px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($structures as $structure)
                            <tr class="border-t">
                                <td class="py-3 px-4">{{ $structure->teacher->name }}</td>
                                <td class="py-3 px-4">{{ number_format($structure->basic_salary, 2) }}</td>
                                <td class="py-3 px-4">{{ number_format($structure->allowances, 2) }}</td>
                                <td class="py-3 px-4">{{ number_format($structure->deductions, 2) }}</td>
                                <td class="py-3 px-4 font-semibold">{{ number_format($structure->netSalary(), 2) }}</td>
                                <td class="py-3 px-4">
                                    <form action="{{ route('school.salary-structures.destroy', $structure) }}" method="POST" onsubmit="return confirm('Remove this structure?')">
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
        </div>
    </div>
</x-app-layout>
