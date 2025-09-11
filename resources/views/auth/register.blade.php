<x-guest-layout>
    <!-- REGISTER FORM -->
    <div id="register_form" class="fade-form show fade-in">
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
                <a href="{{ route('login') }}">Login</a>
            </p>
        </form>
    </div>
</x-guest-layout>