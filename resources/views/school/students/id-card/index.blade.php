<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('ID Card') }}</h2>
    </x-slot>

    <div class="space-y-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded">{{ $errors->first() }}</div>
        @endif

        <div class="bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b font-semibold">STEP 1: Select Design</div>
        </div>

        <div class="bg-[#1a8bc4] rounded-lg">
            <div class="px-6 py-3 text-white font-semibold">STEP 2: Download ID Card</div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach ([1, 2] as $templateNumber)
                    <div class="border rounded-lg p-4 flex flex-col items-center gap-3 {{ $school->id_card_template == $templateNumber ? 'border-green-500 ring-1 ring-green-500' : 'border-gray-200' }}">
                        <div class="flex gap-2 w-full">
                            <button type="button" class="flex-1 px-3 py-1.5 text-sm bg-gray-100 rounded hover:bg-gray-200" data-bs-toggle="modal" data-bs-target="#preview-modal-{{ $templateNumber }}">
                                <i class="bi bi-search"></i> View
                            </button>
                            <form method="POST" action="{{ route('school.students.id-card.select-template') }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="template" value="{{ $templateNumber }}">
                                <button type="submit" class="w-full px-3 py-1.5 text-sm {{ $school->id_card_template == $templateNumber ? 'bg-green-600' : 'bg-gray-700' }} text-white rounded hover:opacity-90">
                                    <i class="bi bi-check-lg"></i> Select
                                </button>
                            </form>
                        </div>
                        <div class="card-preview-box w-full border border-gray-300 rounded overflow-hidden bg-white"
                             data-src="{{ route('school.students.id-card.preview', $templateNumber) }}?side=front"></div>
                    </div>
                @endforeach

                <div class="border rounded-lg p-4 flex flex-col items-center gap-3 border-gray-200">
                    <div class="flex gap-2 w-full opacity-50">
                        <button type="button" disabled class="flex-1 px-3 py-1.5 text-sm bg-gray-100 rounded"><i class="bi bi-search"></i> View</button>
                    </div>
                    <div class="w-full h-[150px] rounded bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white font-bold text-lg">
                        Coming Soon....
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg p-6">
            <form method="GET" action="{{ route('school.students.id-card.front') }}" id="id-card-form" class="grid grid-cols-1 sm:grid-cols-5 gap-4 items-end">
                <div>
                    <x-input-label value="Section" />
                    <select name="section_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select</option>
                        @foreach ($sections as $sectionOption)
                            <option value="{{ $sectionOption->id }}">{{ $sectionOption->schoolClass?->name }} - {{ $sectionOption->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label value="From Roll" />
                    <x-text-input type="number" name="from_roll" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label value="To Roll" />
                    <x-text-input type="number" name="to_roll" class="mt-1 block w-full" />
                </div>
                <div>
                    <button type="submit" class="w-full px-4 py-2.5 bg-green-600 text-white rounded-md font-semibold text-sm hover:bg-green-700">
                        <i class="bi bi-download"></i> Download Front
                    </button>
                </div>
                <div>
                    <button type="submit" formaction="{{ route('school.students.id-card.back') }}" class="w-full px-4 py-2.5 bg-green-600 text-white rounded-md font-semibold text-sm hover:bg-green-700">
                        <i class="bi bi-download"></i> Download Back
                    </button>
                </div>
            </form>
        </div>
    </div>

    @foreach ([1, 2] as $templateNumber)
        <div class="modal fade" id="preview-modal-{{ $templateNumber }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Template {{ $templateNumber }} Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body flex justify-center">
                        <iframe src="{{ route('school.students.id-card.preview', $templateNumber) }}?side=both" style="width: 100%; height: 230px; border: 0;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        (function () {
            var CONTENT_WIDTH = 340;
            document.querySelectorAll('.card-preview-box').forEach(function (box) {
                var scale = box.clientWidth / CONTENT_WIDTH;
                box.style.height = Math.round(210 * scale) + 'px';

                var iframe = document.createElement('iframe');
                iframe.scrolling = 'no';
                iframe.tabIndex = -1;
                iframe.style.cssText = 'width:' + CONTENT_WIDTH + 'px; height:210px; border:0; pointer-events:none; transform: scale(' + scale + '); transform-origin: top left;';
                iframe.src = box.dataset.src;
                box.appendChild(iframe);
            });
        })();
    </script>
</x-app-layout>
