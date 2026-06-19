<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Use the title passed from the page, or use the default project title. --}}
    <title>{{ $title ?? 'Cinema Showtime Management System' }}</title>
    <script>
        try {
            if (localStorage.getItem('cinema-sidebar-collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch (error) {
            // The sidebar still works when browser storage is unavailable.
        }
    </script>
    <style>
        /* Shared dashboard, form, login, table, and responsive styles for pages using this layout. */
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #10131a; color: #edf2f7; }
        a { color: inherit; text-decoration: none; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
        .shell { min-height: 100vh; }
        .topbar { position: sticky; top: 0; z-index: 50; background: #07090f; color: #fff; border-bottom: 1px solid #252b36; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .brand { font-size: 20px; font-weight: 700; }
        .topbar-main { display: flex; align-items: center; gap: 14px; }
        .user { display: flex; align-items: center; gap: 14px; font-size: 14px; }
        .sidebar-toggle { gap: 8px; padding: 8px 11px; border: 1px solid #343d4e; background: #171d28; color: #fff; }
        .sidebar-toggle-icon { font-size: 18px; line-height: 1; }
        .sidebar-toggle-label { font-size: 12px; }
        .admin-frame { min-height: calc(100vh - 73px); display: grid; grid-template-columns: 238px minmax(0, 1fr); transition: grid-template-columns .25s ease; }
        .admin-content { min-width: 0; }
        .sidebar { position: sticky; top: 73px; min-width: 0; height: calc(100vh - 73px); display: flex; flex-direction: column; padding: 24px 16px; overflow: hidden; border-right: 1px solid #252b36; background: #0b0f17; white-space: nowrap; transition: opacity .2s ease, transform .25s ease, padding .25s ease; }
        html.sidebar-collapsed .admin-frame { grid-template-columns: 0 minmax(0, 1fr); }
        html.sidebar-collapsed .sidebar { padding-right: 0; padding-left: 0; opacity: 0; border-right: 0; transform: translateX(-100%); pointer-events: none; }
        .sidebar-title { padding: 0 10px 14px; color: #737f92; font-size: 11px; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
        .sidebar-nav { display: grid; gap: 7px; }
        .sidebar-link { display: flex; align-items: center; gap: 11px; padding: 12px 13px; border: 1px solid transparent; border-radius: 7px; color: #aeb8c7; font-size: 14px; font-weight: 700; transition: background .18s ease, border-color .18s ease, color .18s ease, transform .18s ease; }
        .sidebar-link:hover { transform: translateX(3px); border-color: #343d4e; background: #161c27; color: #fff; }
        .sidebar-link.active { border-color: rgba(217, 39, 77, .55); background: rgba(217, 39, 77, .15); color: #fff; }
        .sidebar-icon { width: 29px; height: 29px; display: grid; place-items: center; flex: 0 0 auto; border-radius: 6px; background: #222a38; color: #fda4af; font-size: 13px; font-weight: 900; }
        .sidebar-link.active .sidebar-icon { background: #d9274d; color: #fff; }
        .sidebar-note { flex: 0 0 auto; margin-top: auto; padding: 14px; overflow-wrap: anywhere; border: 1px solid #252e3d; border-radius: 7px; background: #111722; color: #7f8a9b; font-size: 12px; line-height: 1.5; white-space: normal; }
        .sidebar-note strong, .sidebar-note span { display: block; }
        .sidebar-note strong { margin-bottom: 4px; color: #d8dee8; font-size: 13px; }
        .container { width: min(1100px, calc(100% - 32px)); margin: 32px auto; }
        .panel { background: #181d27; border: 1px solid #2b3342; border-radius: 8px; padding: 24px; box-shadow: 0 18px 45px rgba(0, 0, 0, 0.28); }
        .auth { min-height: 100vh; display: grid; place-items: center; padding: 24px; background: linear-gradient(135deg, #07090f 0%, #1a2230 55%, #8b1e34 100%); }
        .auth .panel { width: min(420px, 100%); }
        h1 { margin: 0 0 8px; font-size: 28px; }
        .muted { color: #a7b0c0; margin: 0 0 22px; }
        .stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 22px; }
        .stat { background: #111722; border: 1px solid #2b3342; border-radius: 8px; padding: 16px; transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease; }
        .stat:hover { transform: translateY(-4px); border-color: #d9274d; box-shadow: 0 14px 30px rgba(0, 0, 0, .25); }
        .stat span { display: block; color: #a7b0c0; font-size: 13px; margin-bottom: 6px; }
        .stat strong { font-size: 24px; }
        .row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        label { display: block; font-weight: 700; margin-bottom: 7px; }
        input, textarea, select { width: 100%; border: 1px solid #384254; border-radius: 6px; padding: 11px 12px; font: inherit; background: #0f141d; color: #f8fafc; }
        textarea { min-height: 110px; resize: vertical; }
        .field { margin-bottom: 16px; }
        .error { color: #fda4af; font-size: 14px; margin-top: 6px; }
        .actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .button { border: 0; border-radius: 6px; padding: 10px 14px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; min-height: 40px; transition: transform .18s ease, filter .18s ease, box-shadow .18s ease; }
        .button:hover { transform: translateY(-2px); filter: brightness(1.08); box-shadow: 0 10px 22px rgba(0, 0, 0, .24); }
        .primary { background: #d9274d; color: #fff; }
        .secondary { background: #2b3342; color: #fff; }
        .danger { background: #991b1b; color: #fff; }
        .ghost { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,.35); }
        .auth-divider { display: flex; align-items: center; gap: 12px; color: #a7b0c0; font-size: 13px; margin: 20px 0; }
        .auth-divider::before, .auth-divider::after { content: ""; flex: 1; height: 1px; background: #2b3342; }
        .google-login { width: 100%; gap: 10px; background: #fff; color: #1f2937; border: 1px solid rgba(255, 255, 255, .18); box-shadow: 0 12px 28px rgba(0, 0, 0, .2); }
        .google-login:hover { filter: none; background: #f8fafc; box-shadow: 0 14px 30px rgba(0, 0, 0, .28); }
        .google-mark { width: 22px; height: 22px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-style: normal; font-weight: 800; color: #fff; background: conic-gradient(from -45deg, #4285f4 0 25%, #34a853 0 50%, #fbbc05 0 75%, #ea4335 0); font-family: Arial, Helvetica, sans-serif; font-size: 14px; }
        .google-mark::before { content: "G"; }
        .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .search { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
        .search input { min-width: 260px; }
        .lookup-row { display: flex; gap: 8px; }
        .movie-lookup { background: #111722; border: 1px solid #2b3342; border-radius: 8px; padding: 16px; margin-bottom: 20px; }
        .lookup-message { min-height: 20px; margin: 8px 0 0; }
        .movie-results { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; }
        .movie-result { border: 1px solid #2b3342; border-radius: 6px; background: #0f141d; color: #f8fafc; padding: 8px; display: flex; align-items: center; gap: 10px; text-align: left; cursor: pointer; transition: transform .18s ease, border-color .18s ease, background .18s ease; }
        .movie-result:hover { transform: translateY(-2px); border-color: #d9274d; background: #181d27; }
        .movie-result img, .poster-empty { width: 42px; height: 58px; border-radius: 4px; flex: 0 0 auto; object-fit: cover; background: #2b3342; }
        .poster-empty { display: inline-flex; align-items: center; justify-content: center; color: #a7b0c0; font-size: 11px; text-align: center; padding: 4px; }
        .movie-result strong, .movie-result small { display: block; }
        .movie-result small { color: #a7b0c0; margin-top: 3px; }
        .image-preview-wrap { margin-top: 12px; }
        .image-preview { display: block; width: 120px; height: 176px; object-fit: cover; border: 1px solid #384254; border-radius: 6px; background: #2b3342; }
        .image-help { margin: 8px 0 0; font-size: 13px; }
        .movie-cell { display: flex; align-items: center; gap: 10px; min-width: 170px; }
        .movie-poster { width: 48px; height: 68px; object-fit: cover; flex: 0 0 auto; border-radius: 5px; background: #2b3342; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 13px 10px; border-bottom: 1px solid #2b3342; vertical-align: middle; }
        th { color: #cbd5e1; font-size: 14px; background: #111722; }
        tbody tr { transition: background .18s ease, transform .18s ease; }
        tbody tr:hover { background: #202838; }
        .table-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .status { background: #052e25; color: #a7f3d0; border: 1px solid #047857; padding: 10px 12px; border-radius: 6px; margin-bottom: 16px; }
        .badge { border-radius: 999px; display: inline-flex; font-weight: 700; font-size: 12px; padding: 5px 9px; }
        .badge-showing { background: #064e3b; color: #bbf7d0; }
        .badge-coming { background: #7c2d12; color: #fed7aa; }
        .empty { text-align: center; color: #a7b0c0; padding: 36px 12px; }
        .pagination { margin-top: 18px; }
        .calendar-container { width: min(1320px, calc(100% - 32px)); }
        .calendar-toolbar { align-items: flex-end; }
        .calendar-controls { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .month-picker { width: 165px; min-height: 40px; padding: 8px 10px; }
        .calendar-scroll { overflow-x: auto; border: 1px solid #2b3342; border-radius: 8px; }
        .calendar { min-width: 900px; background: #10151e; }
        .calendar-weekdays, .calendar-grid { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); }
        .calendar-weekday { padding: 11px 8px; border-right: 1px solid #2b3342; border-bottom: 1px solid #2b3342; background: #0c1119; color: #9aa6b8; font-size: 12px; font-weight: 800; letter-spacing: .08em; text-align: center; text-transform: uppercase; }
        .calendar-weekday:last-child { border-right: 0; }
        .calendar-day { min-height: 150px; padding: 9px; border-right: 1px solid #2b3342; border-bottom: 1px solid #2b3342; background: #151b25; }
        .calendar-day:nth-child(7n) { border-right: 0; }
        .calendar-day.outside-month { background: #0f141c; color: #576174; }
        .calendar-day.is-today { box-shadow: inset 0 0 0 2px #d9274d; }
        .calendar-date { width: 29px; height: 29px; display: grid; place-items: center; margin-bottom: 7px; border-radius: 50%; color: #cbd5e1; font-size: 13px; font-weight: 800; }
        .is-today .calendar-date { background: #d9274d; color: #fff; }
        .calendar-events { display: grid; gap: 6px; }
        .calendar-event { display: grid; grid-template-columns: 34px minmax(0, 1fr); gap: 7px; padding: 6px; overflow: hidden; border: 1px solid #343e50; border-radius: 6px; background: #202838; transition: border-color .18s ease, transform .18s ease; }
        .calendar-event:hover { transform: translateY(-2px); border-color: #d9274d; }
        .calendar-event img, .calendar-poster-empty { width: 34px; height: 47px; border-radius: 4px; object-fit: cover; background: #2b3342; }
        .calendar-poster-empty { display: grid; place-items: center; color: #8590a1; font-size: 8px; text-align: center; }
        .calendar-event-info { min-width: 0; }
        .calendar-event strong, .calendar-event small { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .calendar-event strong { margin-bottom: 2px; color: #fff; font-size: 12px; }
        .calendar-event small { color: #9eabba; font-size: 10px; }
        .calendar-empty { padding: 20px; color: #8490a2; font-size: 12px; text-align: center; }
        @media (max-width: 900px) {
            .admin-frame { display: block; }
            .sidebar { position: static; width: 100%; height: auto; padding: 12px 16px; border-right: 0; border-bottom: 1px solid #252b36; }
            .sidebar-title, .sidebar-note { display: none; }
            .sidebar-nav { display: flex; gap: 8px; overflow-x: auto; }
            .sidebar-link { flex: 0 0 auto; }
            .sidebar-link:hover { transform: translateY(-2px); }
            html.sidebar-collapsed .sidebar { display: none; }
        }
        @media (max-width: 700px) {
            .topbar { padding: 14px 16px; align-items: flex-start; flex-direction: column; }
            .row, .stats { grid-template-columns: 1fr; }
            .lookup-row { flex-direction: column; }
            table, thead, tbody, th, td, tr { display: block; }
            thead { display: none; }
            tr { border-bottom: 1px solid #2b3342; padding: 12px 0; }
            td { border: 0; padding: 6px 0; }
            td::before { content: attr(data-label); display: block; font-weight: 700; color: #a7b0c0; font-size: 13px; }
        }
    </style>
</head>
<body>
    <div class="shell">
        {{-- The top navigation only appears after login because public pages do not pass username. --}}
        @isset($username)
            <header class="topbar">
                <div class="topbar-main">
                    <button class="button sidebar-toggle" id="sidebar_toggle" type="button" aria-controls="admin_sidebar" aria-expanded="true">
                        <span class="sidebar-toggle-icon" aria-hidden="true">☰</span>
                        <span class="sidebar-toggle-label" id="sidebar_toggle_label">Hide sidebar</span>
                    </button>
                    <a class="brand" href="{{ route('showtimes.index') }}">Cinema Showtime Management System</a>
                </div>
                <div class="user">
                    {{-- Show the username saved in the Laravel session. --}}
                    <span>Signed in as <strong>{{ $username }}</strong></span>
                    {{-- Logout uses POST and CSRF protection for security. --}}
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="button ghost" type="submit">Logout</button>
                    </form>
                </div>
            </header>
            <div class="admin-frame">
                <aside class="sidebar" id="admin_sidebar" aria-label="Admin navigation">
                    <div class="sidebar-title">Cinema Admin</div>
                    <nav class="sidebar-nav">
                        <a class="sidebar-link {{ request()->routeIs('showtimes.*') ? 'active' : '' }}" href="{{ route('showtimes.index') }}" @if(request()->routeIs('showtimes.*')) aria-current="page" @endif>
                            <span class="sidebar-icon">DB</span>
                            <span>Dashboard</span>
                        </a>
                        <a class="sidebar-link {{ request()->routeIs('calendar') ? 'active' : '' }}" href="{{ route('calendar') }}" @if(request()->routeIs('calendar')) aria-current="page" @endif>
                            <span class="sidebar-icon">CA</span>
                            <span>Movie Calendar</span>
                        </a>
                    </nav>
                    <footer class="sidebar-note">
                        <strong>Cinema Manager</strong>
                        <span>Manage schedules, posters, halls, seats, and calendar dates from one place.</span>
                    </footer>
                </aside>

                <div class="admin-content">
                    {{-- Page content from <x-admin::layouts.app> is inserted here. --}}
                    {{ $slot }}
                </div>
            </div>
        @else
            {{ $slot }}
        @endisset
    </div>
    <script>
        const sidebarToggle = document.getElementById('sidebar_toggle');

        if (sidebarToggle) {
            const sidebarToggleLabel = document.getElementById('sidebar_toggle_label');

            function updateSidebarToggle() {
                const isCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
                sidebarToggle.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
                sidebarToggleLabel.textContent = isCollapsed ? 'Show sidebar' : 'Hide sidebar';
            }

            sidebarToggle.addEventListener('click', () => {
                const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');

                try {
                    localStorage.setItem('cinema-sidebar-collapsed', String(isCollapsed));
                } catch (error) {
                    // Keep the toggle usable even when browser storage is unavailable.
                }

                updateSidebarToggle();
            });

            updateSidebarToggle();
        }
    </script>
</body>
</html>
