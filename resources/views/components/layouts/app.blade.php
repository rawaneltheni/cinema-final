<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Use the title passed from the page, or use the default project title. --}}
    <title>{{ $title ?? 'Cinema Showtime Management System' }}</title>
    <style>
        /* Shared dashboard, form, login, table, and responsive styles for pages using this layout. */
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #10131a; color: #edf2f7; }
        a { color: inherit; text-decoration: none; }
        .shell { min-height: 100vh; }
        .topbar { background: #07090f; color: #fff; border-bottom: 1px solid #252b36; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .brand { font-size: 20px; font-weight: 700; }
        .user { display: flex; align-items: center; gap: 14px; font-size: 14px; }
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
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 13px 10px; border-bottom: 1px solid #2b3342; vertical-align: top; }
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
        @media (max-width: 700px) {
            .topbar { padding: 14px 16px; align-items: flex-start; flex-direction: column; }
            .row, .stats { grid-template-columns: 1fr; }
            table, thead, tbody, th, td, tr { display: block; }
            thead { display: none; }
            tr { border-bottom: 1px solid #2b3342; padding: 12px 0; }
            td { border: 0; padding: 6px 0; }
            td::before { content: attr(data-label); display: block; font-weight: 700; color: #a7b0c0; font-size: 13px; }
        }
    </style>
</head>
<body>
    {{-- This browser-tab check only runs on logged-in pages where a username is passed to the layout. --}}
    @isset($username)
        <script>
            @if (session('logged_in_tab'))
                // Mark the current browser tab as the tab that completed login.
                sessionStorage.setItem('cinemaLoggedInTab', '1');
            @else
                // If this tab was not marked after login, send it back to the login page.
                if (sessionStorage.getItem('cinemaLoggedInTab') !== '1') {
                    window.location.replace('{{ route('login') }}');
                }
            @endif
        </script>
    @endisset

    <div class="shell">
        {{-- The top navigation only appears after login because public pages do not pass username. --}}
        @isset($username)
            <header class="topbar">
                <a class="brand" href="{{ route('showtimes.index') }}">Cinema Showtime Management System</a>
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
        @endisset

        {{-- Page content from <x-layouts.app> is inserted here. --}}
        {{ $slot }}
    </div>
</body>
</html>
