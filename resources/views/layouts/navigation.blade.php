@php
    $isActiveGroup = fn (...$patterns) => request()->routeIs(...$patterns) ? 'active' : '';
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
            <span class="brand-text fw-semibold text-truncate">
                {{ $brandSchool?->name ?? config('app.name') }}
            </span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="true">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ $isActiveGroup('dashboard') }}">
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
                @elseif (Auth::user()->role === \App\Models\User::ROLE_SCHOOL_ADMIN)
                    <li class="nav-item">
                        <a href="{{ route('school.students.index') }}" class="nav-link {{ $isActiveGroup('school.students.*') }}">
                            <i class="nav-icon bi bi-mortarboard"></i>
                            <p>{{ __('Students') }}</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('school.teachers.index') }}" class="nav-link {{ $isActiveGroup('school.teachers.*') }}">
                            <i class="nav-icon bi bi-person-badge"></i>
                            <p>{{ __('Teachers') }}</p>
                        </a>
                    </li>

                    <li class="nav-item has-treeview {{ request()->routeIs('school.classes.*','school.sections.*','school.subjects.*','school.academic-years.*','school.attendance.*','school.routine.*') ? 'menu-open' : '' }}">
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

                    <li class="nav-item has-treeview {{ request()->routeIs('school.exams.*','school.results.*','school.grades.*','school.marks.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-pencil-square"></i>
                            <p>{{ __('Exams & Results') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.exams.index') }}" class="nav-link {{ $isActiveGroup('school.exams.*') }}"><i class="nav-icon bi bi-file-text"></i><p>{{ __('Exams') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.results.index') }}" class="nav-link {{ $isActiveGroup('school.results.*') }}"><i class="nav-icon bi bi-bar-chart"></i><p>{{ __('Results') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.grades.index') }}" class="nav-link {{ $isActiveGroup('school.grades.*') }}"><i class="nav-icon bi bi-award"></i><p>{{ __('Grading Scale') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ request()->routeIs('school.finance.*','school.fee-types.*','school.student-fees.*','school.incomes.*','school.expenses.*') ? 'menu-open' : '' }}">
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

                    <li class="nav-item has-treeview {{ request()->routeIs('school.book*') ? 'menu-open' : '' }}">
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

                    <li class="nav-item has-treeview {{ request()->routeIs('school.vehicles.*','school.transport-routes.*','school.student-transports.*') ? 'menu-open' : '' }}">
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

                    <li class="nav-item has-treeview {{ request()->routeIs('school.inventory-*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-box2"></i>
                            <p>{{ __('Inventory') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.inventory-items.index') }}" class="nav-link {{ $isActiveGroup('school.inventory-items.*') }}"><i class="nav-icon bi bi-boxes"></i><p>{{ __('Items') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.inventory-categories.index') }}" class="nav-link {{ $isActiveGroup('school.inventory-categories.*') }}"><i class="nav-icon bi bi-tag"></i><p>{{ __('Categories') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ request()->routeIs('school.salary-structures.*','school.payrolls.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-wallet2"></i>
                            <p>{{ __('Payroll') }}<i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="{{ route('school.salary-structures.index') }}" class="nav-link {{ $isActiveGroup('school.salary-structures.*') }}"><i class="nav-icon bi bi-sliders"></i><p>{{ __('Salary Structures') }}</p></a></li>
                            <li class="nav-item"><a href="{{ route('school.payrolls.index') }}" class="nav-link {{ $isActiveGroup('school.payrolls.*') }}"><i class="nav-icon bi bi-cash-stack"></i><p>{{ __('Monthly Payroll') }}</p></a></li>
                        </ul>
                    </li>

                    <li class="nav-item has-treeview {{ request()->routeIs('school.notices.*','school.sms-settings.*') ? 'menu-open' : '' }}">
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
