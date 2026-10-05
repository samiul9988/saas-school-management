<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Assign Fee to Class') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-500 mb-4">This assigns the fee to every active student in the selected class.</p>
                <form method="POST" action="{{ route('school.student-fees.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label for="fee_type_id" value="Fee Type" />
                        <select id="fee_type_id" name="fee_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">— Select fee type —</option>
                            @foreach ($feeTypes as $feeType)
                                <option value="{{ $feeType->id }}" data-amount="{{ $feeType->default_amount }}" @selected(old('fee_type_id') == $feeType->id)>{{ $feeType->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('fee_type_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="school_class_id" value="Class" />
                        <select id="school_class_id" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">— Select class —</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('school_class_id') == $class->id)>{{ $class->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('school_class_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="academic_year_id" value="Academic Year" />
                        <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">—</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id)>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="amount" value="Amount" />
                        <x-text-input id="amount" type="number" step="0.01" name="amount" class="mt-1 block w-full" value="{{ old('amount', 0) }}" required />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="due_date" value="Due Date" />
                        <x-text-input id="due_date" type="date" name="due_date" class="mt-1 block w-full" value="{{ old('due_date') }}" />
                    </div>
                    <div class="flex justify-end">
                        <x-primary-button>Assign Fee</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('fee_type_id')?.addEventListener('change', function (e) {
            const amount = e.target.selectedOptions[0]?.dataset.amount;
            if (amount) document.getElementById('amount').value = amount;
        });
    </script>
</x-app-layout>
