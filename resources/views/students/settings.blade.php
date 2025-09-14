@extends('layouts.student')

@section('title','Settings')
@section('header','Settings')

@section('content')
<div class="container my-4">

  {{-- Flash Messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Tabs -->
  <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
        <i class="bx bx-user me-1"></i> Profile Settings
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
        <i class="bx bx-lock-alt me-1"></i> Change Password
      </button>
    </li>
  </ul>

  <div class="tab-content mt-3" id="settingsTabsContent">

    <!-- Profile Tab -->
    <div class="tab-pane fade show active" id="profile" role="tabpanel">
      <div class="card p-4 shadow-sm border-0">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">Profile Information</h5>
          <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
            <i class='bx bx-edit-alt me-1'></i> Update Profile
          </button>
        </div>

        <!-- Profile Summary -->
        <div class="text-center mb-3">
          <img src="{{ auth()->user()->profile && auth()->user()->profile->profile_picture 
              ? asset('storage/'.auth()->user()->profile->profile_picture) 
              : asset('images/default-student.png') }}" 
               alt="Profile Picture" class="rounded-circle mb-2 shadow-sm"
               style="width: 120px; height: 120px; object-fit: cover;">
          <h6 class="mt-2 fw-semibold">
            {{ optional(auth()->user()->profile)->first_name }} {{ optional(auth()->user()->profile)->last_name }}
          </h6>
        </div>

        <table class="table table-bordered table-striped align-middle">
          <tr><th>Full Name</th><td>{{ optional(auth()->user()->profile)->first_name }} {{ optional(auth()->user()->profile)->middle_name }} {{ optional(auth()->user()->profile)->last_name }}</td></tr>
          <tr><th>Email</th><td>{{ auth()->user()->email }}</td></tr>
          <tr><th>Contact Number</th><td>{{ optional(auth()->user()->profile)->contact_number ?? 'N/A' }}</td></tr>
          <tr><th>Gender</th><td>{{ optional(auth()->user()->profile)->sex ?? 'N/A' }}</td></tr>
          <tr><th>Birthdate</th><td>{{ optional(auth()->user()->profile)->birthdate ?? 'N/A' }}</td></tr>
          <tr><th>Address</th><td>{{ optional(auth()->user()->profile)->address ?? 'N/A' }}</td></tr>
        </table>

        <!-- Logout -->
        <div class="text-end">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger">
              <i class='bx bx-log-out me-1'></i> Logout
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Change Password Tab -->
    <div class="tab-pane fade" id="password" role="tabpanel">
      <div class="card p-4 shadow-sm border-0">
        <h5 class="fw-bold mb-3">
          <i class="bx bx-lock-alt me-1"></i> Change Password
        </h5>
        <form id="changePasswordForm" method="POST" action="{{ route('students.changePassword') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required>
            @error('current_password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" class="form-control @error('new_password') is-invalid @enderror" name="new_password" required>
            @error('new_password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" name="new_password_confirmation" required>
            @error('new_password_confirmation')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <button type="submit" class="btn btn-warning">
            <i class="bx bx-refresh me-1"></i> Change Password
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Update Profile Modal -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class='bx bx-user me-2'></i>Update Profile</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="updateProfileForm" method="POST" action="{{ route('students.updateSettings') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4 text-center mb-3">
              <img src="{{ auth()->user()->profile && auth()->user()->profile->profile_picture 
                  ? asset('storage/'.auth()->user()->profile->profile_picture) 
                  : asset('images/default-student.png') }}" 
                  alt="Profile Picture" class="rounded-circle mb-2 shadow-sm"
                  style="width: 120px; height: 120px; object-fit: cover;">
              <input type="file" class="form-control mt-2 @error('profile_picture') is-invalid @enderror" name="profile_picture" accept="image/*">
              @error('profile_picture')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-8">
              <div class="row mb-3">
                <div class="col">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                         name="first_name" value="{{ old('first_name', optional(auth()->user()->profile)->first_name) }}" required>
                  @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col">
                  <label class="form-label">Middle Name</label>
                  <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                         name="middle_name" value="{{ old('middle_name', optional(auth()->user()->profile)->middle_name) }}">
                  @error('middle_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="col">
                  <label class="form-label">Last Name</label>
                  <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                         name="last_name" value="{{ old('last_name', optional(auth()->user()->profile)->last_name) }}" required>
                  @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email', auth()->user()->email) }}">
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control @error('contact_number') is-invalid @enderror" 
                       name="contact_number" value="{{ old('contact_number', optional(auth()->user()->profile)->contact_number) }}">
                @error('contact_number')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-3">
                <label class="form-label">Gender</label>
                <select class="form-select @error('sex') is-invalid @enderror" name="sex">
                  <option value="Male" {{ optional(auth()->user()->profile)->sex == 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ optional(auth()->user()->profile)->sex == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('sex')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-3">
                <label class="form-label">Birthdate</label>
                <input type="date" class="form-control @error('birthdate') is-invalid @enderror" 
                       name="birthdate" value="{{ old('birthdate', optional(auth()->user()->profile)->birthdate) }}">
                @error('birthdate')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control @error('address') is-invalid @enderror" rows="2" name="address">{{ old('address', optional(auth()->user()->profile)->address) }}</textarea>
                @error('address')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            <i class='bx bx-save me-1'></i> Save Changes
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection