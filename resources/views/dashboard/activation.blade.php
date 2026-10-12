<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Site Maintenance — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background: #f3f4f6; }
        .card { width: min(640px, 100%); background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px 32px; box-shadow: 0 4px 16px rgba(0,0,0,.06); }
        .card h2 { margin: 0 0 20px; color: #111827; font-size: 1.2rem; font-weight: 800; display: flex; align-items: center; gap: .5rem; }
        .status-row { display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; }
        .status-badge { display: inline-flex; align-items: center; gap: .4rem; padding: .35rem .75rem; border-radius: 999px; font-size: .8rem; font-weight: 700; }
        .status-badge.is-off { background: #dcfce7; color: #15803d; }
        .status-badge.is-on { background: #fee2e2; color: #b91c1c; }
        .status-desc { margin: .6rem 0 0; max-width: 380px; color: #6b7280; font-size: .85rem; line-height: 1.6; }
        .toggle-switch { position: relative; flex: 0 0 auto; width: 62px; height: 34px; border: 0; border-radius: 999px; background: #d1d5db; cursor: pointer; transition: background .2s ease; }
        .toggle-switch.is-on { background: #16a34a; }
        .toggle-knob { position: absolute; top: 3px; left: 3px; width: 28px; height: 28px; border-radius: 50%; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.25); transition: transform .2s ease; }
        .toggle-switch.is-on .toggle-knob { transform: translateX(28px); }
        .preview-link { margin-top: 1.2rem; }
        .btn-light { display: inline-flex; align-items: center; gap: .4rem; padding: .4rem .8rem; border-radius: 6px; background: #f3f4f6; color: #374151; font-size: .8rem; text-decoration: none; border: 1px solid #e5e7eb; }
        .btn-light:hover { background: #e5e7eb; }
        .bookmark-note { margin-top: 1.2rem; padding: .8rem 1rem; border-radius: 6px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e; font-size: .82rem; line-height: 1.6; }
        .bookmark-note code { background: rgba(0,0,0,.06); padding: .05rem .3rem; border-radius: 3px; word-break: break-all; }
        .alert-success { margin-bottom: 16px; padding: 10px 14px; border-radius: 6px; background: #dcfce7; color: #15803d; font-size: .85rem; }
    </style>
</head>
<body>
    <div class="card">
        <h2><i class="bi bi-toggle-on"></i> Site Maintenance Mode</h2>

        @if (session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        <div class="status-row">
            <div>
                <span class="status-badge {{ $active ? 'is-on' : 'is-off' }}">
                    <i class="bi {{ $active ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' }}"></i>
                    {{ $active ? 'Maintenance mode is ON' : 'Site is live' }}
                </span>
                <p class="status-desc">
                    @if ($active)
                        Visitors are currently seeing the maintenance page instead of the site. This page works without logging in, so you can always turn it back off.
                    @else
                        The site is running normally for everyone right now.
                    @endif
                </p>
            </div>

            <form method="POST" action="{{ route('maintenance.toggle', $key ? ['key' => $key] : []) }}">
                @csrf
                @if ($key)
                    <input type="hidden" name="key" value="{{ $key }}">
                @endif
                <button type="submit" class="toggle-switch {{ $active ? 'is-on' : '' }}" aria-pressed="{{ $active ? 'true' : 'false' }}">
                    <span class="toggle-knob"></span>
                </button>
            </form>
        </div>

        <div class="preview-link">
            <a href="{{ route('maintenance.preview') }}" target="_blank" class="btn-light">
                <i class="bi bi-eye"></i> Preview maintenance page
            </a>
        </div>

        @unless (auth()->check())
            <div class="bookmark-note">
                <i class="bi bi-bookmark-fill"></i>
                Bookmark this page's full URL (including <code>?key=...</code>) — it's the only way back here without logging in.
            </div>
        @endunless
    </div>
</body>
</html>
