<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800|caveat:600" rel="stylesheet" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .font-script { font-family: 'Caveat', cursive; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col lg:flex-row bg-white">

            <!-- Left: form panel -->
            <div class="w-full lg:w-[44%] flex flex-col justify-center px-6 sm:px-12 lg:px-16 py-10">
                <a href="/" class="flex items-center gap-3 mb-10">
                    <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600 text-white text-2xl">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <span>
                        <span class="block font-extrabold text-lg leading-tight text-gray-900">
                            SaaS School <span class="text-indigo-600">Management</span>
                        </span>
                        <span class="block text-[11px] tracking-widest uppercase text-gray-400">
                            Smarter School &middot; Better Future
                        </span>
                    </span>
                </a>

                <div class="w-full max-w-sm">
                    {{ $slot }}
                </div>

                <div class="mt-12 text-xs text-gray-400">
                    Built with <span class="text-red-500">&hearts;</span> for Education
                </div>
            </div>

            <!-- Right: visual panel -->
            <div class="hidden lg:flex lg:w-[56%] relative overflow-hidden bg-gradient-to-br from-indigo-600 via-blue-600 to-sky-500">
                <div class="absolute inset-0 opacity-20"
                     style="background-image:radial-gradient(circle at 20% 20%, white 0, transparent 40%), radial-gradient(circle at 80% 70%, white 0, transparent 35%);"></div>

                <div class="absolute top-8 right-8 bg-white/95 rounded-xl shadow-lg px-4 py-3 flex items-center gap-3">
                    <span class="flex items-center justify-center w-9 h-9 rounded-full bg-green-100 text-green-600">
                        <i class="bi bi-shield-check"></i>
                    </span>
                    <span class="text-left leading-tight">
                        <span class="block text-sm font-semibold text-gray-800">Multi-Tenant SaaS</span>
                        <span class="block text-xs text-gray-500">100% Data Isolation</span>
                    </span>
                </div>

                <div class="relative z-10 flex flex-col justify-center px-14 w-full">
                    <h1 class="text-white text-4xl font-extrabold leading-tight">
                        Better Management<br>
                        <span class="font-script text-5xl font-semibold text-sky-100">Brighter Future</span>
                    </h1>

                    <div class="mt-14 flex items-center justify-center">
                        <div class="relative w-72 h-72 rounded-full bg-white/10 flex items-center justify-center">
                            <div class="absolute inset-6 rounded-full bg-white/10"></div>
                            <i class="bi bi-building text-white text-8xl relative z-10"></i>
                            <span class="absolute -top-2 -right-2 flex items-center justify-center w-14 h-14 rounded-full bg-white shadow-lg">
                                <i class="bi bi-mortarboard-fill text-indigo-600 text-2xl"></i>
                            </span>
                            <span class="absolute bottom-2 -left-4 flex items-center justify-center w-12 h-12 rounded-full bg-white shadow-lg">
                                <i class="bi bi-people-fill text-sky-600 text-xl"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mt-14 grid grid-cols-2 gap-4 max-w-md">
                        <div class="bg-white/95 rounded-xl p-4">
                            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 mb-2">
                                <i class="bi bi-buildings"></i>
                            </span>
                            <div class="text-sm font-semibold text-gray-800">Multiple Schools</div>
                            <div class="text-xs text-gray-500">Single Platform</div>
                        </div>
                        <div class="bg-white/95 rounded-xl p-4">
                            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-green-100 text-green-600 mb-2">
                                <i class="bi bi-person-badge"></i>
                            </span>
                            <div class="text-sm font-semibold text-gray-800">3 User Roles</div>
                            <div class="text-xs text-gray-500">Super Admin, Admin, Parent</div>
                        </div>
                        <div class="bg-white/95 rounded-xl p-4">
                            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-pink-100 text-pink-600 mb-2">
                                <i class="bi bi-wallet2"></i>
                            </span>
                            <div class="text-sm font-semibold text-gray-800">bKash / Nagad</div>
                            <div class="text-xs text-gray-500">Payment Support</div>
                        </div>
                        <div class="bg-white/95 rounded-xl p-4">
                            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-blue-100 text-blue-600 mb-2">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <div class="text-sm font-semibold text-gray-800">Secure &amp; Reliable</div>
                            <div class="text-xs text-gray-500">Your Data, Our Priority</div>
                        </div>
                    </div>

                    <div class="font-script text-sky-100 text-xl mt-10">
                        Manage Students &middot; Track Progress &middot; Build Tomorrow
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
