<x-guest-layout>
    <!-- LOGIN FORM -->
    <div id="login_form" class="fade-form show">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-envelope'></i> Email</label>
                <input type="email" name="email"
                       class="form-control bg-light @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-lock'></i> Password</label>
                <input type="password" name="password"
                       class="form-control bg-light @error('password') is-invalid @enderror"
                       required placeholder="Password">
                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-info w-100 text-white">Login</button>

            <p class="mt-3 text-center">
                Don't have an account?
                <a href="javascript:void(0)" onclick="showForm('register_form')">Register</a> |
                <a href="javascript:void(0)" onclick="showForm('forgot_form')">Forgot Password?</a>
            </p>
        </form>
    </div>

    <!-- REGISTER FORM -->
    <div id="register_form" class="fade-form">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger py-2 mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- First Name -->
            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-id-card'></i> First Name</label>
                <input type="text" name="first_name" class="form-control bg-light"
                    value="{{ old('first_name') }}" placeholder="Enter your first name" required>
            </div>

            <!-- Middle Name -->
            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-id-card'></i> Middle Name</label>
                <input type="text" name="middle_name" class="form-control bg-light"
                    value="{{ old('middle_name') }}" placeholder="Enter your middle name">
            </div>

            <!-- Last Name -->
            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-id-card'></i> Last Name</label>
                <input type="text" name="last_name" class="form-control bg-light"
                    value="{{ old('last_name') }}" placeholder="Enter your last name" required>
            </div>

            <!-- Email -->
            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-envelope-open'></i> Email</label>
                <input type="email" name="email" class="form-control bg-light"
                    value="{{ old('email') }}" placeholder="Enter your email address" required>
            </div>

            <!-- Password -->
            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-lock'></i> Password</label>
                <input type="password" name="password" class="form-control bg-light"
                    placeholder="Enter your password" required autocomplete="new-password">
            </div>

            <!-- Confirm Password -->
            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-lock-alt'></i> Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control bg-light"
                    placeholder="Re-enter your password" required>
            </div>

            <!-- Role -->
            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-user-circle'></i> Role</label>
                <select name="role_id" class="form-select bg-light" required>
                    <option value="">--Select Role--</option>
                    <option value="1" {{ old('role_id') == 1 ? 'selected' : '' }}>Administrator</option>
                    <option value="2" {{ old('role_id') == 2 ? 'selected' : '' }}>Registrar</option>
                    <option value="3" {{ old('role_id') == 3 ? 'selected' : '' }}>Teacher</option>
                    <option value="4" {{ old('role_id') == 4 ? 'selected' : '' }}>Student</option>
                </select>
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn btn-info w-100 text-white">Register</button>

            <!-- Login Link -->
            <p class="mt-3 text-center">Already have an account?
                <a href="javascript:void(0)" onclick="showForm('login_form')">Login</a>
            </p>
        </form>
    </div>


    <!-- FORGOT PASSWORD FORM -->
    <div id="forgot_form" class="fade-form">
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3 text-start">
                <label class="form-label"><i class='bx bx-envelope'></i> Email</label>
                <input type="email" name="email"
                       class="form-control bg-light @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required placeholder="Enter your email">
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-info w-100 text-white">
                Send Password Reset Link
            </button>

            <p class="mt-3 text-center">
                <a href="javascript:void(0)" onclick="showForm('login_form')">Back to Login</a>
            </p>
        </form>
    </div>
</x-guest-layout>
