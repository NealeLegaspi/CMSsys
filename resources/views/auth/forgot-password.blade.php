<x-guest-layout>
    <!-- FORGOT PASSWORD FORM -->
    <div id="forgot_password_form" class="fade-form show fade-in">
        <div class="mb-4 text-sm text-muted text-start">
            Forgot your password? No problem. Just let us know your email address
            and we will email you a password reset link that will allow you to choose a new one.
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success py-2 mb-3">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-envelope'></i> Email</label>
                <input id="email" type="email" name="email"
                       class="form-control bg-light @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-info w-100 text-white">
                Email Password Reset Link
            </button>

            <!-- Back to Login -->
            <p class="mt-3 text-center">
                <a href="{{ route('login') }}">Back to Login</a>
            </p>
        </form>
    </div>
</x-guest-layout>
