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
                --brand-1: #22a559;
                --brand-2: #15803d;
                --top-active: #2196f3;
                --sidebar-bg: #ffffff;
                --sidebar-text: #334155;
                --content-bg: #eaf6fc;
                --panel-border: #bae6fd;
            }

            body {
                font-family: 'Figtree', sans-serif;
                background: var(--content-bg);
            }

            /* Sidebar */
            .app-sidebar {
                background: var(--sidebar-bg) !important;
                border-right: 1px solid var(--panel-border);
            }
            .app-sidebar, .app-sidebar .nav-link, .app-sidebar .brand-text {
                color: var(--sidebar-text) !important;
            }
            .app-sidebar .nav-icon { color: var(--brand-2); }
            .app-sidebar .sidebar-brand {
                overflow: hidden;
                border-bottom: 1px solid var(--panel-border);
            }
            .app-sidebar .brand-link {
                background: transparent;
                overflow: hidden;
                min-width: 0;
            }
            .app-sidebar .brand-link .brand-text {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                display: block;
                font-weight: 700;
            }
            .app-sidebar .sidebar-menu > .nav-item > .nav-link,
            .app-sidebar .nav-treeview .nav-link {
                border-radius: 0;
                margin: 0;
            }
            .app-sidebar .sidebar-menu > .nav-item > .nav-link.active {
                background: var(--top-active) !important;
                color: #fff !important;
            }
            .app-sidebar .nav-treeview .nav-link.active {
                background: var(--brand-1) !important;
                color: #fff !important;
            }
            .app-sidebar .sidebar-menu > .nav-item > .nav-link.active .nav-icon,
            .app-sidebar .nav-treeview .nav-link.active .nav-icon {
                color: #fff;
            }
            .app-sidebar .nav-link:hover {
                background: #ecfdf5;
            }

            /* Temporarily locked modules: looks normal, just not clickable */
            .nav-locked,
            .nav-locked * {
                pointer-events: none !important;
                cursor: not-allowed !important;
            }

            /* Topbar */
            .app-header {
                background: #ffffff !important;
                border-bottom: 1px solid var(--panel-border);
            }
            .app-header, .app-header .nav-link { color: var(--sidebar-text) !important; }

            .content-header h1 { font-size: 1.25rem; }

            /* Recolor primary Tailwind buttons/links app-wide */
            .bg-indigo-600 {
                background: linear-gradient(90deg, var(--brand-1), var(--brand-2)) !important;
                border: none !important;
            }
            .hover\:bg-indigo-700:hover { filter: brightness(.93); }
            .text-indigo-600 { color: var(--brand-2) !important; }
            .text-indigo-600:hover, .hover\:text-indigo-800:hover { color: var(--brand-1) !important; }
            .text-red-600 { color: #dc2626 !important; }

            /* Light blue table headers */
            .bg-gray-50 { background: #f0f9ff !important; }

            /* Light blue bordered panels, Dcampus-style */
            .bg-white.shadow-sm {
                border: 1px solid var(--panel-border) !important;
                box-shadow: none !important;
            }

            /* Let forms/listings use the full content width instead of a narrow centered column */
            .app-content .max-w-xl,
            .app-content .max-w-2xl,
            .app-content .max-w-3xl,
            .app-content .max-w-4xl,
            .app-content .max-w-5xl,
            .app-content .max-w-6xl,
            .app-content .max-w-7xl {
                max-width: 100% !important;
            }

            .app-footer { background: #fff; border-top: 1px solid var(--panel-border); }
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
