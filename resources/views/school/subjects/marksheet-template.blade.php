<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Marksheet Template Configuration') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            @foreach ($templates as $number => $label)
                <div class="bg-white shadow-sm rounded-lg p-4 flex flex-col items-center">
                    <div class="font-semibold text-sm mb-2">Marksheet Template-{{ $number }}</div>

                    <div class="marksheet-preview-box w-full border border-gray-300 rounded overflow-hidden bg-white"
                         data-src="{{ route('school.exam-configuration.marksheet-template.preview', $number) }}"></div>

                    <div class="text-xs text-gray-500 mt-2 mb-3">{{ $label }}</div>

                    <button type="button" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-1.5 rounded"
                            data-bs-toggle="modal" data-bs-target="#template-modal-{{ $number }}">
                        Select
                    </button>
                </div>
            @endforeach
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-blue-100">
                    <tr class="text-gray-700">
                        <th class="py-3 px-4">Class</th>
                        <th class="py-3 px-4">Marksheet Type</th>
                        <th class="py-3 px-4">Template Name</th>
                        <th class="py-3 px-4">Marksheet Header</th>
                        <th class="py-3 px-4">Merit Position</th>
                        <th class="py-3 px-4">Student Img Status</th>
                        <th class="py-3 px-4">Watermark Status</th>
                        <th class="py-3 px-4">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($configs as $config)
                        <tr class="border-t">
                            <td class="py-2 px-4">{{ $config->schoolClass?->name }}</td>
                            <td class="py-2 px-4">General</td>
                            <td class="py-2 px-4">General Marksheet Format-{{ $config->template_number }}</td>
                            <td class="py-2 px-4">{{ $config->marksheet_header }}</td>
                            <td class="py-2 px-4">{{ $config->mark_sheet_size }}</td>
                            <td class="py-2 px-4">{{ $config->student_img_status ? 'Yes' : 'No' }}</td>
                            <td class="py-2 px-4">{{ $config->watermark_status ? 'Yes' : 'No' }}</td>
                            <td class="py-2 px-4">
                                <form action="{{ route('school.exam-configuration.marksheet-template.destroy', $config) }}" method="POST" onsubmit="return confirm('Remove this marksheet template assignment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-6 text-center text-gray-400">No classes configured yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($templates as $number => $label)
        <div class="modal fade" id="template-modal-{{ $number }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('school.exam-configuration.marksheet-template.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="template_number" value="{{ $number }}">
                        <div class="modal-header">
                            <h5 class="modal-title">Apply Marksheet Template-{{ $number }} ({{ $label }})</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body space-y-4">
                            <div>
                                <x-input-label :for="'school_class_id_'.$number" value="Class" />
                                <select id="school_class_id_{{ $number }}" name="school_class_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">Select</option>
                                    @foreach ($classes as $classOption)
                                        <option value="{{ $classOption->id }}">{{ $classOption->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label :for="'mark_sheet_size_'.$number" value="Merit Position" />
                                    <select id="mark_sheet_size_{{ $number }}" name="mark_sheet_size" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                        @foreach ($meritPositions as $position)
                                            <option value="{{ $position }}">{{ $position }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label :for="'marksheet_header_'.$number" value="Marksheet Header" />
                                    <select id="marksheet_header_{{ $number }}" name="marksheet_header" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                        @foreach ($headers as $header)
                                            <option value="{{ $header }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label :for="'student_img_status_'.$number" value="Student Image" />
                                    <select id="student_img_status_{{ $number }}" name="student_img_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label :for="'watermark_status_'.$number" value="Water Mark" />
                                    <select id="watermark_status_{{ $number }}" name="watermark_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Assign</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        (function () {
            // Fallback guess used only until the iframe loads and reports its real .sheet size.
            var FALLBACK_HEIGHT = 300;

            document.querySelectorAll('.marksheet-preview-box').forEach(function (box) {
                box.style.height = FALLBACK_HEIGHT + 'px';

                var iframe = document.createElement('iframe');
                iframe.scrolling = 'no';
                iframe.tabIndex = -1;
                iframe.style.cssText = 'width:' + box.clientWidth + 'px; height:' + FALLBACK_HEIGHT + 'px; border:0; pointer-events:none;';

                iframe.addEventListener('load', function () {
                    try {
                        var sheet = iframe.contentDocument.querySelector('.sheet');
                        if (!sheet) {
                            return;
                        }
                        var naturalWidth = sheet.getBoundingClientRect().width;
                        if (naturalWidth <= 0) {
                            return;
                        }
                        var zoom = box.clientWidth / naturalWidth;
                        sheet.style.zoom = zoom;

                        var zoomedHeight = Math.ceil(sheet.getBoundingClientRect().height);
                        iframe.style.width = box.clientWidth + 'px';
                        iframe.style.height = zoomedHeight + 'px';
                        box.style.height = zoomedHeight + 'px';
                    } catch (e) {
                        // cross-origin fallback: keep the guessed height
                    }
                });

                iframe.src = box.dataset.src;
                box.appendChild(iframe);
            });
        })();
    </script>
</x-app-layout>
