<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="bg-white shadow-sm rounded-lg p-4 d-flex align-items-center gap-4 mb-3">
                <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle overflow-hidden bg-light" style="width:90px;height:90px;">
                    @if ($school->logo)
                        <img src="{{ Storage::url($school->logo) }}" alt="{{ $school->name }}" class="w-100 h-100" style="object-fit:cover;">
                    @else
                        <i class="bi bi-mortarboard-fill text-secondary" style="font-size:2.5rem;"></i>
                    @endif
                </div>
                <div>
                    <div class="fw-bold fs-4">{{ $school->name }}</div>
                    <div class="text-muted small">
                        <div><strong>Academic Year:</strong> {{ $currentYear?->name ?? '—' }}</div>
                        <div><strong>Institute ID:</strong> {{ str_pad($school->id, 5, '0', STR_PAD_LEFT) }}</div>
                        <div><strong>Address:</strong> {{ $school->address ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-4">
                <h3 class="text-center fw-semibold mb-3" style="color: var(--brand-2);">Class Wise Student Summary</h3>
                <div class="table-responsive">
                    <table class="w-100 text-sm text-start">
                        <thead>
                            <tr class="text-muted border-bottom">
                                <th class="py-2">Class</th>
                                <th class="py-2 text-center">Male</th>
                                <th class="py-2 text-center">Female</th>
                                <th class="py-2 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($classSummary as $class)
                                <tr class="border-bottom">
                                    <td class="py-2 fw-semibold">{{ $class->name }}</td>
                                    <td class="py-2 text-center">{{ $class->male_count }}</td>
                                    <td class="py-2 text-center">{{ $class->female_count }}</td>
                                    <td class="py-2 text-center fw-semibold">{{ $class->students_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-center text-muted">No classes yet.</td></tr>
                            @endforelse
                        </tbody>
                        @if ($classSummary->isNotEmpty())
                            <tfoot>
                                <tr class="border-top fw-bold">
                                    <td class="py-2">Total</td>
                                    <td class="py-2 text-center">{{ $classSummary->sum('male_count') }}</td>
                                    <td class="py-2 text-center">{{ $classSummary->sum('female_count') }}</td>
                                    <td class="py-2 text-center">{{ $classSummary->sum('students_count') }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="d-flex flex-column gap-3">
                <div class="bg-white shadow-sm rounded-lg p-3 d-flex align-items-center gap-3">
                    <span class="d-flex align-items-center justify-content-center rounded-circle" style="width:48px;height:48px;background:#e0f2fe;color:#0284c7;">
                        <i class="bi bi-mortarboard fs-5"></i>
                    </span>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total_students'] }}</div>
                        <div class="text-muted small">Total Student</div>
                    </div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-3 d-flex align-items-center gap-3">
                    <span class="d-flex align-items-center justify-content-center rounded-circle" style="width:48px;height:48px;background:#e0f2fe;color:#0284c7;">
                        <i class="bi bi-person-badge fs-5"></i>
                    </span>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total_teachers'] }}</div>
                        <div class="text-muted small">Total Teachers</div>
                    </div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-3 d-flex align-items-center gap-3">
                    <span class="d-flex align-items-center justify-content-center rounded-circle" style="width:48px;height:48px;background:#e0f2fe;color:#0284c7;">
                        <i class="bi bi-chat-dots fs-5"></i>
                    </span>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['sms_sent'] }}</div>
                        <div class="text-muted small">SMS Sent</div>
                    </div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-3 d-flex align-items-center gap-3">
                    <span class="d-flex align-items-center justify-content-center rounded-circle" style="width:48px;height:48px;background:#e0f2fe;color:#0284c7;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </span>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['sms_wallet_balance'] }}</div>
                        <div class="text-muted small">SMS Credits &middot; <a href="{{ route('school.sms-wallet.index') }}" class="text-decoration-none">Wallet</a></div>
                    </div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-3 d-flex align-items-center gap-3">
                    <span class="d-flex align-items-center justify-content-center rounded-circle" style="width:48px;height:48px;background:#e0f2fe;color:#0284c7;">
                        <i class="bi bi-person-circle fs-5"></i>
                    </span>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total_users'] }}</div>
                        <div class="text-muted small">Total User</div>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-3 py-2 fw-semibold text-white" style="background: var(--top-active);">Support</div>
                    <div class="p-3 small text-muted">
                        Reach out to your platform administrator for billing, SMS top-up, or technical help.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="bg-white shadow-sm rounded-lg p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="fw-semibold mb-0">Recently Admitted Students</h3>
                    <a href="{{ route('school.students.index') }}" class="small text-decoration-none">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="w-100 text-sm text-start">
                        <thead>
                            <tr class="text-muted border-bottom">
                                <th class="py-2">Name</th>
                                <th class="py-2">Class</th>
                                <th class="py-2">Section</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentStudents as $student)
                                <tr class="border-bottom">
                                    <td class="py-2">
                                        <a href="{{ route('school.students.show', $student) }}" class="text-decoration-none">{{ $student->name }}</a>
                                    </td>
                                    <td class="py-2">{{ $student->schoolClass?->name ?? '—' }}</td>
                                    <td class="py-2">{{ $student->section?->name ?? '—' }}</td>
                                    <td class="py-2 text-capitalize">{{ $student->status }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-center text-muted">No students yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
