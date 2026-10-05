<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @if (Auth::user()->isSuperAdmin())
                        <x-nav-link :href="route('super-admin.schools.index')" :active="request()->routeIs('super-admin.schools.*')">
                            {{ __('Schools') }}
                        </x-nav-link>
                        <x-nav-link :href="route('super-admin.plans.index')" :active="request()->routeIs('super-admin.plans.*')">
                            {{ __('Plans') }}
                        </x-nav-link>
                    @elseif (Auth::user()->school_id)
                        <x-nav-link :href="route('school.students.index')" :active="request()->routeIs('school.students.*')">
                            {{ __('Students') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.teachers.index')" :active="request()->routeIs('school.teachers.*')">
                            {{ __('Teachers') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.classes.index')" :active="request()->routeIs('school.classes.*')">
                            {{ __('Classes') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.sections.index')" :active="request()->routeIs('school.sections.*')">
                            {{ __('Sections') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.subjects.index')" :active="request()->routeIs('school.subjects.*')">
                            {{ __('Subjects') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.academic-years.index')" :active="request()->routeIs('school.academic-years.*')">
                            {{ __('Academic Years') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.attendance.index')" :active="request()->routeIs('school.attendance.*')">
                            {{ __('Attendance') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.exams.index')" :active="request()->routeIs('school.exams.*')">
                            {{ __('Exams') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.results.index')" :active="request()->routeIs('school.results.*')">
                            {{ __('Results') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.routine.index')" :active="request()->routeIs('school.routine.*')">
                            {{ __('Routine') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.grades.index')" :active="request()->routeIs('school.grades.*')">
                            {{ __('Grades') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.finance.index')" :active="request()->routeIs('school.finance.*', 'school.fee-types.*', 'school.student-fees.*', 'school.incomes.*', 'school.expenses.*')">
                            {{ __('Finance') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.notices.index')" :active="request()->routeIs('school.notices.*')">
                            {{ __('Notices') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.sms-settings.edit')" :active="request()->routeIs('school.sms-settings.*')">
                            {{ __('SMS Settings') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.books.index')" :active="request()->routeIs('school.book*')">
                            {{ __('Library') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.vehicles.index')" :active="request()->routeIs('school.vehicles.*', 'school.transport-routes.*', 'school.student-transports.*')">
                            {{ __('Transport') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.inventory-items.index')" :active="request()->routeIs('school.inventory-*')">
                            {{ __('Inventory') }}
                        </x-nav-link>
                        <x-nav-link :href="route('school.salary-structures.index')" :active="request()->routeIs('school.salary-structures.*', 'school.payrolls.*')">
                            {{ __('Payroll') }}
                        </x-nav-link>
                    @elseif (Auth::user()->isParent())
                        <x-nav-link :href="route('parent.attendance')" :active="request()->routeIs('parent.attendance')">
                            {{ __('Attendance') }}
                        </x-nav-link>
                        <x-nav-link :href="route('parent.fees')" :active="request()->routeIs('parent.fees')">
                            {{ __('Fees') }}
                        </x-nav-link>
                        <x-nav-link :href="route('parent.results')" :active="request()->routeIs('parent.results')">
                            {{ __('Results') }}
                        </x-nav-link>
                        <x-nav-link :href="route('parent.notices')" :active="request()->routeIs('parent.notices')">
                            {{ __('Notices') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
