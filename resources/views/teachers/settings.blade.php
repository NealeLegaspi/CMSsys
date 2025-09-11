@extends('layouts.teacher')

@section('title','Settings')
@section('header','Settings')

@section('content')
<div class="container my-4">

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
          <img src="{{ $teacher->profile && $teacher->profile->profile_picture ? asset('storage/'.$teacher->profile->profile_picture) : asset('images/default.png') }}" 
               alt="Profile Picture" class="rounded-circle mb-2"
               style="width: 120px; height: 120px; object-fit: cover;">
          <h6 class="mt-2">{{ $teacher->profile->first_name }} {{ $teacher->profile->last_name }}</h6>
        </div>

        <table class="table table-striped table-bordered">
          <tr><th>Full Name</th><td>{{ $teacher->profile->first_name }} {{ $teacher->profile->middle_name }} {{ $teacher->profile->last_name }}</td></tr>
          <tr><th>Email</th><td>{{ $teacher->email }}</td></tr>
          <tr><th>Contact Number</th><td>{{ $teacher->profile->contact_number ?? 'N/A' }}</td></tr>
          <tr><th>Gender</th><td>{{ $teacher->profile->sex ?? 'N/A' }}</td></tr>
          <tr><th>Birthdate</th><td>{{ $teacher->profile->birthdate ?? 'N/A' }}</td></tr>
          <tr><th>Address</th><td>{{ $teacher->profile->address ?? 'N/A' }}</td></tr>
        </table>

        <!-- Logout -->
        <div class="text-end">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger"><i class='bx bx-log-out me-1'></i> Logout</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Change Password Tab -->
    <div class="tab-pane fade" id="password" role="tabpanel">
      <div class="card p-4">
        <h5 class="mb-3">Change Password</h5>
        <form id="changePasswordForm" method="POST" action="{{ route('teachers.changePassword') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" class="form-control" name="current_password" required>
          </div>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" class="form-control" name="new_password" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" name="new_password_confirmation" required>
          </div>
          <button type="submit" class="btn btn-warning">
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
      <form id="updateProfileForm" method="POST" action="{{ route('teachers.updateSettings') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4 text-center mb-3">
              <img src="{{ $teacher->profile && $teacher->profile->profile_picture ? asset('storage/'.$teacher->profile->profile_picture) : asset('images/default.png') }}" 
                  alt="Profile Picture" class="rounded-circle mb-2"
                  style="width: 120px; height: 120px; object-fit: cover;">
              <input type="file" class="form-control mt-2" name="profile_picture" accept="image/*">
            </div>
            <div class="col-md-8">
              <div class="row mb-3">
                <div class="col">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $teacher->profile->first_name) }}" required>
                </div>
                <div class="col">
                  <label class="form-label">Middle Name</label>
                  <input type="text" class="form-control" name="middle_name" value="{{ old('middle_name', $teacher->profile->middle_name) }}">
                </div>
                <div class="col">
                  <label class="form-label">Last Name</label>
                  <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $teacher->profile->last_name) }}" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ old('email', $teacher->email) }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control" name="contact_number" value="{{ old('contact_number', $teacher->profile->contact_number) }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Sex</label>
                <select class="form-select" name="sex">
                  <option value="Male" {{ $teacher->profile->sex == 'Male' ? 'selected' : '' }}>Male</option>
                  <option value="Female" {{ $teacher->profile->sex == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Birthdate</label>
                <input type="date" class="form-control" name="birthdate" value="{{ old('birthdate', $teacher->profile->birthdate) }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea class="form-control" rows="2" name="address">{{ old('address', $teacher->profile->address) }}</textarea>
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
