# SaaS School Management — Project Overview

A multi-tenant SaaS school management system built in Laravel, so **one codebase + one database** runs many schools, each with isolated data. Styled to match the Bangladeshi school-ERP product **Dcampus** (dcampusbd.com) — same sidebar structure, theme colors, and several PDF document layouts were built by directly browsing their live demo.

Read this file first in any new session before making changes — it saves re-discovering the architecture from scratch.

## Stack

- **Laravel** (see `composer.json` for exact version), PHP 8.3, served locally via **Herd** at `http://saasschoolmanagement.test`
- **MySQL** (`saas_school_management` db), connects via `127.0.0.1:3306` (Herd's MySQL, not socket)
- **Blade + Tailwind** (Laravel Breeze scaffold) for forms/content, **AdminLTE 4 (Bootstrap 5)** loaded via CDN for the dashboard shell (sidebar/topbar) — see `resources/views/layouts/app.blade.php`. Both CSS frameworks coexist; don't be surprised to see both Tailwind utility classes and Bootstrap classes in the same Blade file.
- **No npm required in production** — Tailwind is pre-built via `npm run build` locally only (shared-hosting-friendly, per the original architecture brief).
- PDF generation: `barryvdh/laravel-dompdf`. QR codes: `simplesoftwareio/simple-qrcode`. Excel import/export: `phpoffice/phpspreadsheet`.

## Multi-tenancy

Every tenant-scoped model uses the `App\Models\Concerns\BelongsToSchool` trait: a global Eloquent scope auto-filters by `Auth::user()->school_id`, and auto-fills `school_id` on create. This is the core tenant-isolation mechanism — **always use this trait on new tenant data models**, never hand-roll `where('school_id', ...)`.

## Roles (one `users` table, `role` column)

| Role | Guard route group | Middleware | Notes |
|---|---|---|---|
| `super_admin` | `/super-admin/*` | `super_admin` | Manages Schools + Plans (SaaS-level) |
| `school_admin` | `/school/*` | `school_context` (checks `role === school_admin` + school active) | Runs one school's day-to-day operations |
| `parent` | `/parent/*` | `parent_portal` (checks `role === parent` + has a linked student) | Read-only view of their one child, created via `Student::portal_user_id` |

Generic `/dashboard` route redirects to the right one based on role (`routes/web.php`).

## Test accounts (local dev)

- **Super Admin**: `samiulkabir9988@gmail.com` / `password`
- **School Admin** (Green Valley School): `admin@greenvalley.test` / `password123`
- Another school exists: Rajuk Uttara Model College (`admin@gmail.com`) — password was reset ad hoc during testing; check with user before relying on it.

## Directory conventions

- Controllers: `app/Http/Controllers/SuperAdmin/*`, `app/Http/Controllers/School/*`, `app/Http/Controllers/Parent/*`
- Views mirror this: `resources/views/super-admin/*`, `resources/views/school/*`, `resources/views/parent/*`
- PDF-specific Blade views live under `resources/views/school/exams/pdf/*` (separate from the on-screen HTML views) — dompdf renders these, so keep their CSS dompdf-safe (see gotcha below).
- Shared layout: `resources/views/layouts/app.blade.php` (authenticated shell + all theme CSS) and `resources/views/layouts/navigation.blade.php` (sidebar/topbar markup, built per role).

## Theme

Dcampus-style: white sidebar, light-blue content background, green gradient brand color, blue "active" color for top-level sidebar items vs. green for submenu items. All theme CSS lives inline in `layouts/app.blade.php` inside one `<style>` block — look there first for any color/spacing tweak. Submenu icons are forced to a bullet (`•`) via CSS override (`.nav-treeview .nav-icon::before`); a third nesting level would auto-show `-` instead (not yet used).

There's a **temporary sidebar-lock mechanism**: `$lockedMenu` boolean at the top of `navigation.blade.php`. When `true`, specific modules render visible but non-clickable (`nav-locked` class + `pointer-events: none` CSS). Currently set to `false` (unlocked) — the user toggled it off themselves after a prior request to lock modules temporarily.

## Feature map (by phase, roughly build order)

1. **SaaS core** — Super Admin: Schools CRUD (creates a School + its admin User together), Plans CRUD with **checkbox-based feature list** (`Plan::FEATURES` constant), subscription status.
2. **School core** — Academic Years, Classes, Sections, Subjects, Teachers, Students (with bulk Excel import — see below), Attendance.
3. **Exams & Results** — rebuilt twice to match the real Dcampus demo (browsed live via the `browser-automation` skill):
   - **Exams** (setup), **Grading Scale**
   - **Mark Input / Mark Update** — same search-and-list page (`ExamSubjectSearchController`), filters by Class/Exam/Subject
   - **Admit & Seat Plan** — two tabs: download admit cards (4 selectable PDF templates, `School::admit_card_template`) and per-student "Seat of Exam" cards as PDF
   - **Top Sheet** — blank mark-collection sheet PDF, doesn't require exam-subjects to exist first
   - **Tabulation Sheet** — full subject-by-subject grid PDF, per exam (spans all classes)
   - **Settings** — tabbed page (Exam/Subject/Exam Short Code/Exam Grade); only "Exam Short Code" is a genuinely new CRUD here, the others link out to their full pages
   - **Admit Instruction** — per Class+Exam, up to 4 instruction lines printed on admit cards
4. **Finance** — Fee Types, bulk fee assignment to a class, payment collection (**bKash/Nagad/Cash**), receipts, Income/Expense, finance dashboard.
5. **Communication** — Notices (audience-targeted), SMS settings + log (stub gateway — logs only until real API key configured), Parent Portal (attendance/fees/results/notices, tenant-safe — always resolves to the logged-in parent's own child, never trusts a student ID from the URL).
6. **Optional modules** — Library (books/issue/return), Transport (vehicles/routes/stops/assignments), Inventory (stock in/out), Payroll (salary structures + monthly generation).

**Not built**: Hostel, Multi-Branch (would need `branch_id` retrofitted across most tables), public self-service school signup (the login page's "Create New School" button currently points at Breeze's generic `/register`, which does *not* create a School — flagged to the user, not wired up).

## Known dompdf gotchas (useful if PDFs ever look broken again)

- **CSS floats don't contain their height reliably in dompdf.** If a bordered box collapses or content overlaps, switch that layout from `float`/`clear` to a `<table>` — this bit us on the Seat Plan cards and was the actual fix (see git log "Rebuild Seat Plan PDF").
- Local images: reference via `public_path('storage/'.$school->logo)`, not `Storage::url()` (dompdf needs a filesystem path, not a URL) — unless `allow_remote` is configured.
- Visual QA loop that works well: `Pdf::...->save()` the file, then render it to PNG with Python (`pip3 install pymupdf`, `fitz.open(path).load_page(0).get_pixmap()`), then `Read` the PNG — far faster than guessing at CSS blind.

## Verifying changes

No automated test suite is set up for this app yet. The working pattern throughout this project has been: make the change, `php artisan optimize:clear`, then either `curl` through the real HTTP flow (login → action → assert on response HTML) or use the `browser-automation` skill for anything visual (screenshot + read it back). Clean up any test data created this way (`php artisan tinker`) before finishing.
