<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- AdminLTE (Bootstrap 5 based admin dashboard) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css">

        <!-- App styles (Tailwind utility classes used inside page content) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --brand-1: #ff7849;
                --brand-2: #ef4169;
                --navy-1: #1c2140;
                --navy-2: #1c2140;
                --peach-1: #fdf0e6;
                --peach-2: #fbe4ee;
            }

            body {
                font-family: 'Figtree', sans-serif;
                background: linear-gradient(135deg, var(--peach-1), var(--peach-2)) fixed;
            }

            /* Sidebar */
            .app-sidebar {
                background: linear-gradient(180deg, var(--navy-1), var(--navy-2)) !important;
            }
            .app-sidebar .sidebar-brand {
                overflow: hidden;
            }
            .app-sidebar .brand-link {
                background: transparent;
                border-bottom: 1px solid rgba(255,255,255,.08);
                overflow: hidden;
                min-width: 0;
            }
            .app-sidebar .brand-link .brand-text {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                display: block;
            }
            .app-sidebar .sidebar-menu > .nav-item > .nav-link,
            .app-sidebar .nav-treeview .nav-link {
                border-radius: 0;
                margin: 0;
            }
            .app-sidebar .sidebar-menu > .nav-item > .nav-link.active,
            .app-sidebar .nav-treeview .nav-link.active {
                background: linear-gradient(90deg, var(--brand-1), var(--brand-2)) !important;
                color: #fff !important;
            }
            .app-sidebar .nav-link:hover {
                background: rgba(255,255,255,.07);
            }

            /* Topbar */
            .app-header {
                background: transparent !important;
                border-bottom: 1px solid rgba(0,0,0,.06);
            }

            .content-header h1 { font-size: 1.25rem; }

            /* Recolor primary Tailwind buttons/links app-wide */
            .bg-indigo-600 {
                background: linear-gradient(90deg, var(--brand-1), var(--brand-2)) !important;
                border: none !important;
            }
            .hover\:bg-indigo-700:hover { filter: brightness(.93); }
            .text-indigo-600 { color: var(--brand-2) !important; }
            .text-indigo-600:hover, .hover\:text-indigo-800:hover { color: var(--brand-1) !important; }
            .text-red-600 { color: #e11d48 !important; }

            /* Peach table headers */
            .bg-gray-50 { background: var(--peach-1) !important; }

            /* Gradient top accent on card/panel containers */
            .bg-white.shadow-sm {
                position: relative;
                border: 1px solid rgba(239,65,105,.08);
            }
            .bg-white.shadow-sm::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 3px;
                background: linear-gradient(90deg, var(--brand-1), var(--brand-2));
            }
            .bg-white.shadow-sm.rounded-lg::before { border-radius: 8px 8px 0 0; }
        </style>
    </head>
    <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
        <div class="app-wrapper">
            @include('layouts.navigation')

            <main class="app-main">
                @isset($header)
                    <div class="app-content-header">
                        <div class="container-fluid">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

                <div class="app-content">
                    <div class="container-fluid">
                        {{ $slot }}
                    </div>
                </div>
            </main>

            <footer class="app-footer d-flex justify-content-between">
                <div>&copy; {{ now()->year }} {{ config('app.name') }}</div>
                <div class="text-body-secondary">SaaS School Management</div>
            </footer>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js"></script>
    </body>
</html>
