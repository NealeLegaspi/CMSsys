<x-guest-layout>
    <!-- RESET PASSWORD FORM -->
    <div class="fade-in">
        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="mb-3 text-start">
                <label for="email" class="form-label">
                    <i class='bx bx-envelope'></i> Email
                </label>
                <input id="email" type="email" name="email"
                       class="form-control bg-light @error('email') is-invalid @enderror"
                       value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                       placeholder="Enter your email">

                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3 text-start">
                <label for="password" class="form-label">
                    <i class='bx bx-lock'></i> New Password
                </label>
                <input id="password" type="password" name="password"
                       class="form-control bg-light @error('password') is-invalid @enderror"
                       required autocomplete="new-password"
                       placeholder="Enter new password">

                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-3 text-start">
                <label for="password_confirmation" class="form-label">
                    <i class='bx bx-lock-alt'></i> Confirm Password
                </label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                       class="form-control bg-light @error('password_confirmation') is-invalid @enderror"
                       required autocomplete="new-password"
                       placeholder="Re-enter new password">

                @error('password_confirmation')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Reset Button -->
            <button type="submit" class="btn btn-info w-100 text-white">
                Reset Password
            </button>

            <!-- Back to Login -->
            <p class="mt-3 text-center">
                <a href="{{ route('login') }}">Back to Login</a>
            </p>
        </form>
    </div>
</x-guest-layout>
