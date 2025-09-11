<x-guest-layout>
    <!-- CONFIRM PASSWORD FORM -->
    <div id="confirm_password_form" class="fade-form show fade-in">
        <div class="mb-4 text-sm text-muted text-start">
            This is a secure area of the application. Please confirm your password before continuing.
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-lock'></i> Password</label>
                <input id="password" type="password" name="password"
                       class="form-control bg-light @error('password') is-invalid @enderror"
                       required autocomplete="current-password"
                       placeholder="Enter your password">

                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Button -->
            <button type="submit" class="btn btn-info w-100 text-white">
                Confirm
            </button>

            <!-- Back to Login -->
            <p class="mt-3 text-center">
                <a href="{{ route('login') }}">Back to Login</a>
            </p>
        </form>
    </div>
</x-guest-layout>
