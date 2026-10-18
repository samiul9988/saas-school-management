<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('SMS Report - Date Wise Send') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" class="bg-white shadow-sm sm:rounded-lg p-6 flex items-end gap-4">
                <div>
                    <x-input-label for="from" value="From" />
                    <x-text-input id="from" name="from" type="date" class="mt-1 block w-full" value="{{ $from->format('Y-m-d') }}" />
                </div>
                <div>
                    <x-input-label for="to" value="To" />
                    <x-text-input id="to" name="to" type="date" class="mt-1 block w-full" value="{{ $to->format('Y-m-d') }}" />
                </div>
                <x-primary-button>Filter</x-primary-button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500">
                            <th class="py-2 px-4">Context</th>
                            <th class="py-2 px-4">Recipients</th>
                            <th class="py-2 px-4">Segments</th>
                            <th class="py-2 px-4">Credits</th>
                            <th class="py-2 px-4">Status</th>
                            <th class="py-2 px-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sendRequests as $req)
                            <tr class="border-t">
                                <td class="py-2 px-4">{{ $req->context }}</td>
                                <td class="py-2 px-4">{{ count($req->recipients) }}</td>
                                <td class="py-2 px-4">{{ $req->segments }}</td>
                                <td class="py-2 px-4">{{ $req->credits_required }}</td>
                                <td class="py-2 px-4 capitalize">{{ $req->status }}</td>
                                <td class="py-2 px-4">{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-4 px-4 text-center text-gray-400">No sends in this range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $sendRequests->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
