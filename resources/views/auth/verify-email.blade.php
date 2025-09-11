<x-guest-layout>
    <!-- EMAIL VERIFICATION NOTICE -->
    <div class="fade-in">
        <div class="mb-4 text-muted text-start">
            <i class='bx bx-mail-send'></i>
            Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.  
            If you didn’t receive the email, you can request another below.
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success small" role="alert">
                <i class='bx bx-check-circle'></i>
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mt-4">
            <!-- Resend Verification -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-info text-white">
                    <i class='bx bx-refresh'></i> Resend Verification Email
                </button>
            </form>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class='bx bx-log-out'></i> Log Out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
