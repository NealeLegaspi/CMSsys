@extends('layouts.student')

@section('title','Settings')
@section('header','Settings')

@section('content')
<div class="container my-4">

  <!-- Alerts -->
  {{-- <div class="alert alert-success">Profile updated successfully!</div> --}}
  {{-- <div class="alert alert-danger">Failed to update profile.</div> --}}

  <!-- Tabs -->
  <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
        Profile Settings
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
        Change Password
      </button>
    </li>
  </ul>

  <div class="tab-content mt-3" id="settingsTabsContent">

    <!-- Profile Tab -->
    <div class="tab-pane fade show active" id="profile" role="tabpanel">
      <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Profile Information</h5>
          <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
            <i class='bx bx-edit-alt me-1'></i> Update Profile
          </button>
        </div>

        <!-- Profile Summary -->
        <div class="text-center mb-3">
          <img src="{{ asset('images/default.png') }}" 
               alt="Profile Picture" class="rounded-circle mb-2"
               style="width: 120px; height: 120px; object-fit: cover;">
          <h6 class="mt-2">{{ $student->name ?? 'Student Name' }}</h6>
        </div>

        <table class="table table-striped table-bordered">
          <tr><th>Full Name</th><td>{{ $student->name ?? 'Juan Dela Cruz' }}</td></tr>
          <tr><th>Email</th><td>{{ $student->email ?? 'student@example.com' }}</td></tr>
          <tr><th>Contact Number</th><td>{{ $student->contact_number ?? '09123456789' }}</td></tr>
          <tr><th>Gender</th><td>{{ $student->sex ?? 'Male' }}</td></tr>
          <tr><th>Birthdate</th><td>{{ $student->birthdate ?? '2005-01-01' }}</td></tr>
          <tr><th>Address</th><td>{{ $student->address ?? 'Quezon City' }}</td></tr>
        </table>
      </div>
    </div>

    <!-- Change Password Tab -->
    <div class="tab-pane fade" id="password" role="tabpanel">
      <div class="card p-4">
        <h5 class="mb-3">Change Password</h5>
        <form id="changePasswordForm">
          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" required>
          </div>
          <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#confirmPasswordModal">
            Change Password
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
      <form id="updateProfileForm">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4 text-center mb-3">
              <img src="{{ asset('images/default.png') }}" 
                   alt="Profile Picture" class="rounded-circle mb-2"
                   style="width: 120px; height: 120px; object-fit: cover;">
              <input type="file" class="form-control mt-2" accept="image/*">
            </div>
            <div class="col-md-8">
              <div class="row mb-3">
                <div class="col">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" value="{{ $student->first_name ?? 'Juan' }}" required>
                </div>
                <div class="col">
                  <label class="form-label">Middle Name</label>
                  <input type="text" class="form-control" value="{{ $student->middle_name ?? 'Santos' }}">
                </div>
                <div class="col">
                  <label class="form-label">Last Name</label>
                  <input type="text" class="form-control" value="{{ $student->last_name ?? 'Dela Cruz' }}" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" value="{{ $student->email ?? 'student@example.com' }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control" value="{{ $student->contact_number ?? '09123456789' }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Gender</label>
                <select class="form-select">
                  <option value="Male" {{ ($student->sex ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ ($student->sex ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Birthdate</label>
                <input type="date" class="form-control" value="{{ $student->birthdate ?? '2005-01-01' }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" rows="2">{{ $student->address ?? 'Quezon City' }}</textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmUpdateProfileModal">
            <i class='bx bx-save me-1'></i> Save Changes
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Confirm Update Profile Modal -->
<div class="modal fade" id="confirmUpdateProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class='bx bx-user-check me-2'></i>Confirm Profile Update</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to update your profile information?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" form="updateProfileForm">Yes, Update</button>
      </div>
    </div>
  </div>
</div>

<!-- Confirm Password Modal -->
<div class="modal fade" id="confirmPasswordModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title"><i class='bx bx-lock-alt me-2'></i>Confirm Password Change</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to change your password?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-warning" form="changePasswordForm">Yes, Change It</button>
      </div>
    </div>
  </div>
</div>
@endsection
