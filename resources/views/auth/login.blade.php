<x-layouts.app title="Login">
    <main class="auth">
        <section class="panel">
            <h1>Cinema Showtime Management System</h1>
            <p class="muted">Enter your username and password to manage cinema schedules.</p>

            <form action="{{ route('login.store') }}" method="POST">
                @csrf

                <div class="field">
                    <label for="username">Username</label>
                    <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" autofocus>
                    @error('username')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password">
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <button class="button primary" type="submit">Login</button>
            </form>
        </section>
    </main>
</x-layouts.app>
