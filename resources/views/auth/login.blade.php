{{-- The login page uses the shared layout and passes a page title. --}}
<x-layouts.app title="Login">
    <main class="auth">
        <section class="panel">
            <h1>Cinema Showtime Management System</h1>
            <p class="muted">Enter your username and password to manage cinema schedules.</p>

            {{-- This form sends username and password to AuthController@login. --}}
            <form action="{{ route('login.store') }}" method="POST">
                {{-- CSRF protects the login form from fake external requests. --}}
                @csrf

                <div class="field">
                    <label for="username">Username</label>
                    {{-- old('username') keeps the username after a failed login attempt. --}}
                    <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" autofocus>
                    {{-- Show validation or invalid-login errors for the username field. --}}
                    @error('username')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    {{-- Password input is hidden while typing for privacy. --}}
                    <input id="password" name="password" type="password" autocomplete="current-password">
                    {{-- Show password validation errors if the password field is empty. --}}
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <button class="button primary" type="submit">Login</button>
            </form>

            <div class="auth-divider"><span>or</span></div>

            <a href="{{ route('login.google') }}" class="button google-login btn btn-danger">
                <i class="fab fa-google google-mark" aria-hidden="true"></i>
                تسجيل الدخول بـ Google
            </a>
        </section>
    </main>
</x-layouts.app>
