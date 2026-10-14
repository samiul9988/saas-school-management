@php
    $isActiveGroup = fn (...$patterns) => request()->routeIs(...$patterns) ? 'active' : '';
    // Temporary: set to false to re-enable these sidebar modules.
    $lockedMenu = true;
    $locked = fn () => $lockedMenu ? 'nav-locked' : '';
@endphp

<!-- Topbar -->
<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="{{ route('dashboard') }}" class="nav-link fw-semibold">
                    {{ config('app.name') }}
                </a>
            </li>
        </ul>

        @if (Auth::user()->role === \App\Models\User::ROLE_SCHOOL_ADMIN)
            <ul class="navbar-nav d-none d-lg-flex flex-row gap-2 mx-auto">
                <li class="nav-item"><a href="{{ route('school.students.create') }}" class="btn btn-sm btn-outline-secondary">{{ __('Add Student') }}</a></li>
                <li class="nav-item {{ $locked() }}"><a href="{{ route('school.attendance.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Attendance') }}</a></li>
                <li class="nav-item"><a href="{{ route('school.mark-input.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Mark Input') }}</a></li>
                <li class="nav-item {{ $locked() }}"><a href="{{ route('school.student-fees.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Fee Collect') }}</a></li>
                <li class="nav-item {{ $locked() }}"><a href="{{ route('school.sms-settings.edit') }}" class="btn btn-sm btn-outline-secondary">{{ __('Send SMS') }}</a></li>
            </ul>
        @endif

        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">
                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="bi bi-gear me-2"></i> {{ __('Profile') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="dropdown-item"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i> {{ __('Log Out') }}
                        </a>
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- Sidebar -->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    @php($brandSchool = Auth::user()->school)
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center gap-2">
            <span class="d-flex align-items-center justify-content-center rounded overflow-hidden flex-shrink-0" style="width:32px;height:32px;background:linear-gradient(135deg,var(--brand-1),var(--brand-2));">
                @if ($brandSchool?->logo)
                    <img src="{{ Storage::url($brandSchool->logo) }}" alt="{{ $brandSchool->name }}" class="w-100 h-100" style="object-fit:cover;">
                @else
                    <i class="bi bi-mortarboard-fill text-white"></i>
                @endif
            </span>
            <span class="brand-text fw-semibold" title="{{ $brandSchool?->name ?? config('app.name') }}">
                {{ $brandSchool?->name ?? config('app.name') }}
            </span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="true">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ $isActiveGroup('dashboard', 'school.dashboard', 'super-admin.dashboard', 'parent.dashboard') }}">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>{{ __('Dashboard') }}</p>
                    </a>
                </li>

                @if (Auth::user()->isSuperAdmin())
                    <li class="nav-item">
                        <a href="{{ route('super-admin.schools.index') }}" class="nav-link {{ $isActiveGroup('super-admin.schools.*') }}">
                            <i class="nav-icon bi bi-building"></i>
                            <p>{{ __('Schools') }}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('super-admin.plans.index') }}" class="nav-link {{ $isActiveGroup('super-admin.plans.*') }}">
                            <i class="nav-icon bi bi-box-seam"></i>
                            <p>{{ __('Plans') }}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('maintenance.show') }}" class="nav-link {{ $isActiveGroup('maintenance.*') }}">
                            <i class="nav-icon bi bi-toggle-on"></i>
                            <p>{{ __('Site Maintenance') }}</p>
                        </a>
                    </li>
                @elseif (Auth::user()->role === \App\Models\User::ROLE_SCHOOL_ADMIN)
                    <li class="nav-item has-treeview {{ request()->routeIs('school.students.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isActiveGroup('school.students.*') }}">
                            <i class="nav-icon bi bi-mortarboard"></i>
                            <p>{{ __('Students') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.students.index') }}" class="nav-link {{ $isActiveGroup('school.students.index','school.students.show','school.students.edit') }}"><i class="nav-icon bi bi-list-ul"></i><p>{{ __('All Students') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.students.create') }}" class="nav-link {{ $isActiveGroup('school.students.create') }}"><i class="nav-icon bi bi-person-plus"></i><p>{{ __('Add Single Student') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.students.import.create') }}" class="nav-link {{ $isActiveGroup('school.students.import.*') }}"><i class="nav-icon bi bi-people"></i><p>{{ __('Add Multiple Student') }}</p></a></li>

                            <li class="nav-item has-treeview {{ request()->routeIs('school.students.update-*','school.students.migration-*','school.students.enable-disable') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-pencil-square"></i>
                                    <p>{{ __('Update') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.students.update-basic') }}" class="nav-link {{ $isActiveGroup('school.students.update-basic*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Basic') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.update-photo') }}" class="nav-link {{ $isActiveGroup('school.students.update-photo*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Photo') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.update-academic') }}" class="nav-link {{ $isActiveGroup('school.students.update-academic*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Academic Info') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.migration-without-merit') }}" class="nav-link {{ $isActiveGroup('school.students.migration-without-merit*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Migration (Without Merit)') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.migration-with-merit') }}" class="nav-link {{ $isActiveGroup('school.students.migration-with-merit*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Migration (With Merit)') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.enable-disable') }}" class="nav-link {{ $isActiveGroup('school.students.enable-disable*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Enable / Disable') }}</p></a></li>
                                </ul>
                            </li>

                            <li class="nav-item has-treeview {{ request()->routeIs('school.students.report.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-clipboard-data"></i>
                                    <p>{{ __('Report') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.students.report.all') }}" class="nav-link {{ $isActiveGroup('school.students.report.all') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('All Student') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.report.section-summary') }}" class="nav-link {{ $isActiveGroup('school.students.report.section-summary') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Section Wise Summary') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.report.section-wise') }}" class="nav-link {{ $isActiveGroup('school.students.report.section-wise*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Section Wise') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.report.class-wise') }}" class="nav-link {{ $isActiveGroup('school.students.report.class-wise*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Class Wise') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.report.group-wise') }}" class="nav-link {{ $isActiveGroup('school.students.report.group-wise*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Group Wise') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.report.gender-wise') }}" class="nav-link {{ $isActiveGroup('school.students.report.gender-wise*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Gender Wise') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.report.religion-wise') }}" class="nav-link {{ $isActiveGroup('school.students.report.religion-wise*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Religion Wise') }}</p></a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a href="{{ route('school.students.id-card.index') }}" class="nav-link {{ $isActiveGroup('school.students.id-card.*') }}"><i class="nav-icon bi bi-person-badge"></i><p>{{ __('ID Card') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.students.tc.create') }}" class="nav-link {{ $isActiveGroup('school.students.tc.*') }}"><i class="nav-icon bi bi-file-earmark-text"></i><p>{{ __('TC') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.students.testimonial.create') }}" class="nav-link {{ $isActiveGroup('school.students.testimonial.*') }}"><i class="nav-icon bi bi-file-earmark-ruled"></i><p>{{ __('Testimonial') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.students.initial-setup', 'academic-year') }}" class="nav-link {{ $isActiveGroup('school.students.initial-setup*') }}"><i class="nav-icon bi bi-gear"></i><p>{{ __('Settings') }}</p></a></li>

                            <li class="nav-item has-treeview {{ request()->routeIs('school.students.configuration.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-sliders"></i>
                                    <p>{{ __('Configuration') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.students.configuration.class') }}" class="nav-link {{ $isActiveGroup('school.students.configuration.class*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Class') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.students.configuration.group') }}" class="nav-link {{ $isActiveGroup('school.students.configuration.group*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Group') }}</p></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item has-treeview {{ request()->routeIs('school.teachers.*', 'school.designations.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isActiveGroup('school.teachers.*', 'school.designations.*') }}">
                            <i class="nav-icon bi bi-person-badge"></i>
                            <p>{{ __('HR') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.teachers.hr-list') }}" class="nav-link {{ $isActiveGroup('school.teachers.hr-list') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('HR List') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.teachers.create') }}" class="nav-link {{ $isActiveGroup('school.teachers.create') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Add HR') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.teachers.id-card.index') }}" class="nav-link {{ $isActiveGroup('school.teachers.id-card.*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('ID Card') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.teachers.hr-update') }}" class="nav-link {{ $isActiveGroup('school.teachers.hr-update') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Update') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.teachers.enable-disable') }}" class="nav-link {{ $isActiveGroup('school.teachers.enable-disable') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Enable / Disable') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.designations.index') }}" class="nav-link {{ $isActiveGroup('school.designations.*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Create Designation') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ request()->routeIs('school.hr-attendance.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isActiveGroup('school.hr-attendance.*') }}">
                            <i class="nav-icon bi bi-person-check"></i>
                            <p>{{ __('HR Attendance') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.hr-attendance.take-attendance') }}" class="nav-link {{ $isActiveGroup('school.hr-attendance.take-attendance') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Take Attendance') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.hr-attendance.update-attendance') }}" class="nav-link {{ $isActiveGroup('school.hr-attendance.update-attendance') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Update Attendance') }}</p></a></li>
                            <li class="nav-item has-treeview {{ request()->routeIs('school.hr-attendance.report.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-clipboard-data"></i>
                                    <p>{{ __('Report') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.hr-attendance.report.staff-in-out-time') }}" class="nav-link {{ $isActiveGroup('school.hr-attendance.report.staff-in-out-time') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Staff In/out Time') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.hr-attendance.report.date-wise') }}" class="nav-link {{ $isActiveGroup('school.hr-attendance.report.date-wise') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Day Details') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.hr-attendance.report.month-wise') }}" class="nav-link {{ $isActiveGroup('school.hr-attendance.report.month-wise') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Month Details') }}</p></a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a href="{{ route('school.hr-attendance.leave-input') }}" class="nav-link {{ $isActiveGroup('school.hr-attendance.leave-input') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Leave Input') }}</p></a></li>
                            <li class="nav-item has-treeview {{ request()->routeIs('school.hr-attendance.setting.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-gear"></i>
                                    <p>{{ __('Setting') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.hr-attendance.setting.time-configuration') }}" class="nav-link {{ $isActiveGroup('school.hr-attendance.setting.time-configuration') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Time Configuration') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.hr-attendance.setting.machine-id-mapping') }}" class="nav-link {{ $isActiveGroup('school.hr-attendance.setting.machine-id-mapping') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Machine ID Mapping') }}</p></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ request()->routeIs('school.student-attendance.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ $isActiveGroup('school.student-attendance.*') }}">
                            <i class="nav-icon bi bi-clipboard-check"></i>
                            <p style="max-width:135px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block;">{{ __('Student Attendance') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.student-attendance.input') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.input') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Attendance Input') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.student-attendance.update') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.update') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Attendance Update') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.student-attendance.leave') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.leave') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Leave') }}</p></a></li>
                            <li class="nav-item has-treeview {{ request()->routeIs('school.student-attendance.report.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-clipboard-data"></i>
                                    <p>{{ __('Report') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.student-attendance.report.all-section-summary') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.report.all-section-summary') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('All Section Summary (Daily)') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.student-attendance.report.section-details-day') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.report.section-details-day') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Section Details (Day)') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.student-attendance.report.section-details-month') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.report.section-details-month') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Section Details (Month)') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.student-attendance.report.monthly-summary') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.report.monthly-summary') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Monthly Section Summary') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.student-attendance.report.device-data') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.report.device-data') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Device Data') }}</p></a></li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview {{ request()->routeIs('school.student-attendance.settings.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-gear"></i>
                                    <p>{{ __('Settings') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.student-attendance.settings.period') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.settings.period') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Set Period') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.student-attendance.settings.time-config') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.settings.time-config') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Set Time Configuration') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.student-attendance.settings.manual-process') }}" class="nav-link {{ $isActiveGroup('school.student-attendance.settings.manual-process') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Manual Attendance Process') }}</p></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ $locked() }} {{ request()->routeIs('school.classes.*','school.sections.*','school.subjects.*','school.academic-years.*','school.attendance.*','school.routine.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-journal-bookmark"></i>
                            <p>{{ __('Academics') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.academic-years.index') }}" class="nav-link {{ $isActiveGroup('school.academic-years.*') }}"><i class="nav-icon bi bi-calendar3"></i><p>{{ __('Academic Years') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.classes.index') }}" class="nav-link {{ $isActiveGroup('school.classes.*') }}"><i class="nav-icon bi bi-grid-3x3"></i><p>{{ __('Classes') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.sections.index') }}" class="nav-link {{ $isActiveGroup('school.sections.*') }}"><i class="nav-icon bi bi-columns"></i><p>{{ __('Sections') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.subjects.index') }}" class="nav-link {{ $isActiveGroup('school.subjects.*') }}"><i class="nav-icon bi bi-book"></i><p>{{ __('Subjects') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.attendance.index') }}" class="nav-link {{ $isActiveGroup('school.attendance.*') }}"><i class="nav-icon bi bi-clipboard-check"></i><p>{{ __('Attendance') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.routine.index') }}" class="nav-link {{ $isActiveGroup('school.routine.*') }}"><i class="nav-icon bi bi-clock"></i><p>{{ __('Routine') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ request()->routeIs('school.exams.*','school.results.*','school.results-process.*','school.mark-sheets.*','school.reports.*','school.exam-configuration.*','school.grades.*','school.marks.*','school.mark-input.*','school.exam-subjects.*','school.subject-components.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-pencil-square"></i>
                            <p>{{ __('Exams & Results') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.exams.index') }}" class="nav-link {{ $isActiveGroup('school.exams.*') }}"><i class="nav-icon bi bi-file-text"></i><p>{{ __('Exams') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.exams.seat-plan') }}" class="nav-link {{ $isActiveGroup('school.exams.seat-plan') }}"><i class="nav-icon bi bi-person-vcard"></i><p>{{ __('Admit & Seat Plan') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.exams.top-sheet') }}" class="nav-link {{ $isActiveGroup('school.exams.top-sheet') }}"><i class="nav-icon bi bi-journal-text"></i><p>{{ __('Top Sheet') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.mark-input.index') }}" class="nav-link {{ $isActiveGroup('school.mark-input.*') }}"><i class="nav-icon bi bi-pencil"></i><p>{{ __('Mark Input') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.exam-subjects.search') }}" class="nav-link {{ $isActiveGroup('school.exam-subjects.search') }}"><i class="nav-icon bi bi-pencil-square"></i><p>{{ __('Mark Update') }}</p></a></li>
                            <li class="nav-item has-treeview {{ request()->routeIs('school.results.*', 'school.results-process.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ $isActiveGroup('school.results.index') }}">
                                    <i class="nav-icon bi bi-bar-chart"></i>
                                    <p>{{ __('Result Process') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.results-process.general-exam') }}" class="nav-link {{ $isActiveGroup('school.results-process.general-exam') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('General Exam') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.results-process.grand-final') }}" class="nav-link {{ $isActiveGroup('school.results-process.grand-final*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Grand Final') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.results-process.merit-position') }}" class="nav-link {{ $isActiveGroup('school.results-process.merit-position') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Merit Position') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.results-process.give-remarks') }}" class="nav-link {{ $isActiveGroup('school.results-process.give-remarks') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Give Remarks') }}</p></a></li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview {{ request()->routeIs('school.mark-sheets.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-file-earmark-text"></i>
                                    <p>{{ __('Mark Sheet') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.mark-sheets.general-exam') }}" class="nav-link {{ $isActiveGroup('school.mark-sheets.general-exam') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('General Exam') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.mark-sheets.grand-final') }}" class="nav-link {{ $isActiveGroup('school.mark-sheets.grand-final') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Grand Final') }}</p></a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a href="{{ route('school.exams.tabulation-sheet') }}" class="nav-link {{ $isActiveGroup('school.exams.tabulation-sheet') }}"><i class="nav-icon bi bi-table"></i><p>{{ __('Tabulation Sheet') }}</p></a></li>
                            <li class="nav-item has-treeview {{ request()->routeIs('school.reports.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-clipboard-data"></i>
                                    <p>{{ __('Report') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.reports.unassigned-marks') }}" class="nav-link {{ $isActiveGroup('school.reports.unassigned-marks') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Un Assign Mark') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.reports.section-result') }}" class="nav-link {{ $isActiveGroup('school.reports.section-result') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Section Result') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.reports.class-merit') }}" class="nav-link {{ $isActiveGroup('school.reports.class-merit') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Class Merit') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.reports.section-merit') }}" class="nav-link {{ $isActiveGroup('school.reports.section-merit') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Section Merit') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.reports.class-failed') }}" class="nav-link {{ $isActiveGroup('school.reports.class-failed') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Class Failed') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.reports.section-failed') }}" class="nav-link {{ $isActiveGroup('school.reports.section-failed') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Section Failed') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.reports.grading-summary') }}" class="nav-link {{ $isActiveGroup('school.reports.grading-summary') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Section Wise Grading Summary') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.reports.pass-fail-summary') }}" class="nav-link {{ $isActiveGroup('school.reports.pass-fail-summary') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Section Wise Pass/Fail Summary') }}</p></a></li>
                                </ul>
                            </li>
                            <li class="nav-item has-treeview {{ request()->routeIs('school.exam-configuration.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-sliders"></i>
                                    <p>{{ __('Configuration') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item"><a href="{{ route('school.exam-configuration.index') }}" class="nav-link {{ $isActiveGroup('school.exam-configuration.*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Exam') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.exam-configuration.grand-final') }}" class="nav-link {{ $isActiveGroup('school.exam-configuration.grand-final') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Grand Final') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.exam-configuration.subject') }}" class="nav-link {{ $isActiveGroup('school.exam-configuration.subject*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Subject') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.exam-configuration.mark') }}" class="nav-link {{ $isActiveGroup('school.exam-configuration.mark*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Mark') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.exam-configuration.marksheet-template') }}" class="nav-link {{ $isActiveGroup('school.exam-configuration.marksheet-template*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('MarkSheet Template') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.exam-configuration.fourth-subject') }}" class="nav-link {{ $isActiveGroup('school.exam-configuration.fourth-subject*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('4th Subject') }}</p></a></li>
                                    <li class="nav-item"><a href="{{ route('school.exam-configuration.remarks-template') }}" class="nav-link {{ $isActiveGroup('school.exam-configuration.remarks-template*') }}"><i class="nav-icon bi bi-dash"></i><p>{{ __('Remarks Template') }}</p></a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a href="{{ route('school.exams.settings') }}" class="nav-link {{ $isActiveGroup('school.exams.settings') }}"><i class="nav-icon bi bi-gear"></i><p>{{ __('Settings') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.exams.admit-instruction') }}" class="nav-link {{ $isActiveGroup('school.exams.admit-instruction') }}"><i class="nav-icon bi bi-card-text"></i><p>{{ __('Admit Instruction') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ $locked() }} {{ request()->routeIs('school.finance.*','school.fee-types.*','school.student-fees.*','school.incomes.*','school.expenses.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-cash-coin"></i>
                            <p>{{ __('Finance') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.finance.index') }}" class="nav-link {{ $isActiveGroup('school.finance.index') }}"><i class="nav-icon bi bi-speedometer"></i><p>{{ __('Overview') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.fee-types.index') }}" class="nav-link {{ $isActiveGroup('school.fee-types.*') }}"><i class="nav-icon bi bi-tags"></i><p>{{ __('Fee Types') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.student-fees.index') }}" class="nav-link {{ $isActiveGroup('school.student-fees.*') }}"><i class="nav-icon bi bi-receipt"></i><p>{{ __('Student Fees') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.incomes.index') }}" class="nav-link {{ $isActiveGroup('school.incomes.*') }}"><i class="nav-icon bi bi-graph-up-arrow"></i><p>{{ __('Income') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.expenses.index') }}" class="nav-link {{ $isActiveGroup('school.expenses.*') }}"><i class="nav-icon bi bi-graph-down"></i><p>{{ __('Expenses') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ $locked() }} {{ request()->routeIs('school.book*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-journal-richtext"></i>
                            <p>{{ __('Library') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.books.index') }}" class="nav-link {{ $isActiveGroup('school.books.*') }}"><i class="nav-icon bi bi-book-half"></i><p>{{ __('Books') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.book-categories.index') }}" class="nav-link {{ $isActiveGroup('school.book-categories.*') }}"><i class="nav-icon bi bi-tag"></i><p>{{ __('Categories') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.book-issues.index') }}" class="nav-link {{ $isActiveGroup('school.book-issues.*') }}"><i class="nav-icon bi bi-arrow-left-right"></i><p>{{ __('Issue / Return') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ $locked() }} {{ request()->routeIs('school.vehicles.*','school.transport-routes.*','school.student-transports.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-bus-front"></i>
                            <p>{{ __('Transport') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.vehicles.index') }}" class="nav-link {{ $isActiveGroup('school.vehicles.*') }}"><i class="nav-icon bi bi-truck"></i><p>{{ __('Vehicles') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.transport-routes.index') }}" class="nav-link {{ $isActiveGroup('school.transport-routes.*') }}"><i class="nav-icon bi bi-signpost-split"></i><p>{{ __('Routes') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.student-transports.index') }}" class="nav-link {{ $isActiveGroup('school.student-transports.*') }}"><i class="nav-icon bi bi-person-check"></i><p>{{ __('Assignments') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ $locked() }} {{ request()->routeIs('school.inventory-*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-box2"></i>
                            <p>{{ __('Inventory') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.inventory-items.index') }}" class="nav-link {{ $isActiveGroup('school.inventory-items.*') }}"><i class="nav-icon bi bi-boxes"></i><p>{{ __('Items') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.inventory-categories.index') }}" class="nav-link {{ $isActiveGroup('school.inventory-categories.*') }}"><i class="nav-icon bi bi-tag"></i><p>{{ __('Categories') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ $locked() }} {{ request()->routeIs('school.salary-structures.*','school.payrolls.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-wallet2"></i>
                            <p>{{ __('Payroll') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.salary-structures.index') }}" class="nav-link {{ $isActiveGroup('school.salary-structures.*') }}"><i class="nav-icon bi bi-sliders"></i><p>{{ __('Salary Structures') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.payrolls.index') }}" class="nav-link {{ $isActiveGroup('school.payrolls.*') }}"><i class="nav-icon bi bi-cash-stack"></i><p>{{ __('Monthly Payroll') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ $locked() }} {{ request()->routeIs('school.notices.*','school.sms-settings.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-megaphone"></i>
                            <p>{{ __('Communication') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.notices.index') }}" class="nav-link {{ $isActiveGroup('school.notices.*') }}"><i class="nav-icon bi bi-bell"></i><p>{{ __('Notices') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.sms-settings.edit') }}" class="nav-link {{ $isActiveGroup('school.sms-settings.*') }}"><i class="nav-icon bi bi-chat-dots"></i><p>{{ __('SMS Settings') }}</p></a></li>
                        </ul>
                    </li>
                @elseif (Auth::user()->isParent())
                    <li class="nav-item">
                        <a href="{{ route('parent.attendance') }}" class="nav-link {{ $isActiveGroup('parent.attendance') }}">
                            <i class="nav-icon bi bi-clipboard-check"></i>
                            <p>{{ __('Attendance') }}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('parent.fees') }}" class="nav-link {{ $isActiveGroup('parent.fees') }}">
                            <i class="nav-icon bi bi-cash-coin"></i>
                            <p>{{ __('Fees') }}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('parent.results') }}" class="nav-link {{ $isActiveGroup('parent.results','parent.results.*') }}">
                            <i class="nav-icon bi bi-bar-chart"></i>
                            <p>{{ __('Results') }}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('parent.notices') }}" class="nav-link {{ $isActiveGroup('parent.notices') }}">
                            <i class="nav-icon bi bi-bell"></i>
                            <p>{{ __('Notices') }}</p>
                        </a>
                    </li>
                @endif

                <li class="nav-item mt-3">
                    <a href="{{ route('profile.edit') }}" class="nav-link {{ $isActiveGroup('profile.*') }}">
                        <i class="nav-icon bi bi-person-gear"></i>
                        <p>{{ __('Profile') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" class="nav-link"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="nav-icon bi bi-box-arrow-right"></i>
                            <p>{{ __('Log Out') }}</p>
                        </a>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
