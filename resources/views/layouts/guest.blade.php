<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            <div class="hidden lg:block lg:w-[56%] relative overflow-hidden bg-sky-50">
                <img src="{{ asset('images/school-login.png') }}" alt="SaaS School Management"
                     class="absolute inset-0 w-full h-full object-cover">
            </div>
        </div>
    </body>
</html>
