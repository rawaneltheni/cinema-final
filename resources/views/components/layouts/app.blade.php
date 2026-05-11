<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cinema Movies' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: #f6f7fb; color: #1f2937; }
        a { color: inherit; text-decoration: none; }
        .shell { min-height: 100vh; }
        .topbar { background: #151923; color: #fff; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .brand { font-size: 20px; font-weight: 700; }
        .user { display: flex; align-items: center; gap: 14px; font-size: 14px; }
        .container { width: min(1100px, calc(100% - 32px)); margin: 32px auto; }
        .panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; box-shadow: 0 12px 30px rgba(17, 24, 39, 0.06); }
        .auth { min-height: 100vh; display: grid; place-items: center; padding: 24px; background: linear-gradient(135deg, #151923 0%, #2f3a4f 55%, #8b2635 100%); }
        .auth .panel { width: min(420px, 100%); }
        h1 { margin: 0 0 8px; font-size: 28px; }
        .muted { color: #6b7280; margin: 0 0 22px; }
        .row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        label { display: block; font-weight: 700; margin-bottom: 7px; }
        input, textarea { width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 11px 12px; font: inherit; background: #fff; }
        textarea { min-height: 110px; resize: vertical; }
        .field { margin-bottom: 16px; }
        .error { color: #b91c1c; font-size: 14px; margin-top: 6px; }
        .actions { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .button { border: 0; border-radius: 6px; padding: 10px 14px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; min-height: 40px; }
        .primary { background: #b4233c; color: #fff; }
        .secondary { background: #e5e7eb; color: #111827; }
        .danger { background: #991b1b; color: #fff; }
        .ghost { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,.35); }
        .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
        .search { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 18px; }
        .search input { min-width: 260px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 13px 10px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        th { color: #374151; font-size: 14px; background: #f9fafb; }
        .table-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .status { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 10px 12px; border-radius: 6px; margin-bottom: 16px; }
        .empty { text-align: center; color: #6b7280; padding: 36px 12px; }
        .pagination { margin-top: 18px; }
        @media (max-width: 700px) {
            .topbar { padding: 14px 16px; align-items: flex-start; flex-direction: column; }
            .row { grid-template-columns: 1fr; }
            table, thead, tbody, th, td, tr { display: block; }
            thead { display: none; }
            tr { border-bottom: 1px solid #e5e7eb; padding: 12px 0; }
            td { border: 0; padding: 6px 0; }
            td::before { content: attr(data-label); display: block; font-weight: 700; color: #6b7280; font-size: 13px; }
        }
    </style>
</head>
<body>
    <div class="shell">
        @isset($username)
            <header class="topbar">
                <a class="brand" href="{{ route('movies.index') }}">Cinema Movies</a>
                <div class="user">
                    <span>Signed in as <strong>{{ $username }}</strong></span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="button ghost" type="submit">Logout</button>
                    </form>
                </div>
            </header>
        @endisset

        {{ $slot }}
    </div>
</body>
</html>
