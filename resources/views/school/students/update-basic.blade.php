<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Student Basic Update') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="w-64">
                    <select name="section_id" class="block w-full border-gray-300 rounded-md shadow-sm" required onchange="this.form.submit()">
                        <option value="">Select</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}" @selected($section && $section->id === $sectionOption->id)>
                                {{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-[#2196f3] border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-600">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div>

        @if ($section)
            <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-700">
                            <th class="py-3 px-4"></th>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Roll</th>
                            <th class="py-3 px-4">Group</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="border-t">
                                <td class="py-2 px-4">
                                    <input type="radio" name="pick" class="student-pick"
                                           data-id="{{ $student->id }}"
                                           data-name="{{ $student->name }}"
                                           data-father="{{ $student->father_name }}"
                                           data-mother="{{ $student->mother_name }}"
                                           data-dob="{{ $student->date_of_birth?->format('Y-m-d') }}"
                                           data-gender="{{ $student->gender }}"
                                           data-religion="{{ $student->religion }}"
                                           data-guardian="{{ $student->guardian_name }}"
                                           data-phone="{{ $student->guardian_phone }}"
                                           data-blood="{{ $student->blood_group }}"
                                           data-village="{{ $student->village }}"
                                           data-post-office="{{ $student->post_office }}"
                                           data-upazila="{{ $student->upazila_thana }}"
                                           data-district="{{ $student->district }}">
                                </td>
                                <td class="py-2 px-4">{{ $student->admission_number }}</td>
                                <td class="py-2 px-4">{{ $student->name }}</td>
                                <td class="py-2 px-4">{{ $student->roll }}</td>
                                <td class="py-2 px-4">{{ $student->group_name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-gray-400">No students found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="edit-panel" class="bg-white shadow-sm rounded-lg p-6 hidden">
                <h3 class="font-semibold mb-4">Update Basic Info</h3>
                <form method="POST" action="{{ route('school.students.update-basic.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="section_id" value="{{ $section->id }}">
                    <input type="hidden" name="student_id" id="f_student_id">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Name" />
                            <x-text-input name="name" id="f_name" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label value="Mobile" />
                            <x-text-input name="guardian_phone" id="f_phone" class="mt-1 block w-full" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Father's Name" />
                            <x-text-input name="father_name" id="f_father" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Mother's Name" />
                            <x-text-input name="mother_name" id="f_mother" class="mt-1 block w-full" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Date of Birth" />
                            <x-text-input type="date" name="date_of_birth" id="f_dob" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Gender" />
                            <select name="gender" id="f_gender" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Religion" />
                            <x-text-input name="religion" id="f_religion" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Guardian Name" />
                            <x-text-input name="guardian_name" id="f_guardian" class="mt-1 block w-full" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Blood Group" />
                            <select name="blood_group" id="f_blood" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select</option>
                                @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bloodGroup)
                                    <option value="{{ $bloodGroup }}">{{ $bloodGroup }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Village" />
                            <x-text-input name="village" id="f_village" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="Post Office" />
                            <x-text-input name="post_office" id="f_post_office" class="mt-1 block w-full" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Upazila / Thana" />
                            <x-text-input name="upazila_thana" id="f_upazila" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <x-input-label value="District" />
                            <x-text-input name="district" id="f_district" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-8 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>

            <script>
                document.querySelectorAll('.student-pick').forEach(function (radio) {
                    radio.addEventListener('change', function () {
                        var panel = document.getElementById('edit-panel');
                        panel.classList.remove('hidden');
                        document.getElementById('f_student_id').value = radio.dataset.id;
                        document.getElementById('f_name').value = radio.dataset.name || '';
                        document.getElementById('f_father').value = radio.dataset.father || '';
                        document.getElementById('f_mother').value = radio.dataset.mother || '';
                        document.getElementById('f_dob').value = radio.dataset.dob || '';
                        document.getElementById('f_gender').value = radio.dataset.gender || '';
                        document.getElementById('f_religion').value = radio.dataset.religion || '';
                        document.getElementById('f_guardian').value = radio.dataset.guardian || '';
                        document.getElementById('f_phone').value = radio.dataset.phone || '';
                        document.getElementById('f_blood').value = radio.dataset.blood || '';
                        document.getElementById('f_village').value = radio.dataset.village || '';
                        document.getElementById('f_post_office').value = radio.dataset.postOffice || '';
                        document.getElementById('f_upazila').value = radio.dataset.upazila || '';
                        document.getElementById('f_district').value = radio.dataset.district || '';
                        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    });
                });
            </script>
        @endif
    </div>
</x-app-layout>
