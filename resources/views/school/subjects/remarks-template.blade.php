<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Exam Remarks Template') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form id="remarks-template-form" method="POST" action="{{ route('school.exam-configuration.remarks-template.store') }}" class="max-w-xl space-y-4">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div>
                    <x-input-label for="name" value="Remarks Template Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                </div>

                <div>
                    <x-input-label for="remarks" value="Remarks" />
                    <textarea id="remarks" name="remarks" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" id="submit-btn" class="px-8 py-2.5 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700">
                        Update
                    </button>
                    <button type="button" id="cancel-btn" class="px-8 py-2.5 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-red-700">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">Remarks Template Name</th>
                        <th class="py-3 px-4">Remarks</th>
                        <th class="py-3 px-4">Edit</th>
                        <th class="py-3 px-4">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $template->name }}</td>
                            <td class="py-2 px-4">{{ $template->remarks }}</td>
                            <td class="py-2 px-4">
                                <button type="button" class="text-blue-600 hover:text-blue-800 edit-template-btn"
                                        data-id="{{ $template->id }}"
                                        data-name="{{ $template->name }}"
                                        data-remarks="{{ $template->remarks }}"
                                        data-url="{{ route('school.exam-configuration.remarks-template.update', $template) }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </td>
                            <td class="py-2 px-4">
                                <form action="{{ route('school.exam-configuration.remarks-template.destroy', $template) }}" method="POST" onsubmit="return confirm('Remove this remarks template?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-400">No remarks templates yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        (function () {
            var form = document.getElementById('remarks-template-form');
            var methodField = document.getElementById('form-method');
            var nameField = document.getElementById('name');
            var remarksField = document.getElementById('remarks');
            var submitBtn = document.getElementById('submit-btn');
            var cancelBtn = document.getElementById('cancel-btn');
            var defaultAction = form.getAttribute('action');

            function resetForm() {
                form.setAttribute('action', defaultAction);
                methodField.value = 'POST';
                nameField.value = '';
                remarksField.value = '';
                submitBtn.textContent = 'Update';
            }

            document.querySelectorAll('.edit-template-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    form.setAttribute('action', btn.dataset.url);
                    methodField.value = 'PUT';
                    nameField.value = btn.dataset.name;
                    remarksField.value = btn.dataset.remarks;
                    submitBtn.textContent = 'Update';
                    nameField.focus();
                });
            });

            cancelBtn.addEventListener('click', resetForm);
        })();
    </script>
</x-app-layout>
