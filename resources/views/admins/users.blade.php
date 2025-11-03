@extends('layouts.admin')

@section('title','Users')
@section('header')
    <i class="bi bi-people-fill me-2"></i> User Management
@endsection

@section('content')
<div class="container-fluid my-4">
  <div class="card shadow-sm border-0">

    {{-- Body --}}
    <div class="card-body">

      {{-- 🔍 Filters --}}
      <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-3">
          <label class="form-label fw-semibold">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="{{ request('search') }}">
        </div>

        <div class="col-md-2">
          <label class="form-label fw-semibold">Role</label>
          <select name="role_id" class="form-select">
            <option value="">All</option>
            @foreach($roles as $r)
              <option value="{{ $r->id }}" {{ request('role_id')==$r->id?'selected':'' }}>
                {{ $r->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
          </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-search"></i> Search
          </button>
          <a href="{{ route('admins.users') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </a>
        </div>

        <div class="col-md-2 text-end">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-circle me-1"></i> Add User
          </button>
        </div>
      </form>

      {{-- Alerts --}}
      @include('partials.alerts')

      {{-- 📋 User Table --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th>Last Login</th>
              <th width="250">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users as $u)
              <tr>
                <td class="text-start">
                  @if($u->profile)
                    {{ $u->profile->last_name }}, {{ $u->profile->first_name }}
                    {{ $u->profile->middle_name ? substr($u->profile->middle_name,0,1).'.' : '' }}
                  @else
                    <em class="text-muted">N/A</em>
                  @endif
                </td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->role->name ?? '-' }}</td>
                <td>
                  <span class="badge bg-{{ $u->status=='active'?'success':'secondary' }}">
                    {{ ucfirst($u->status) }}
                  </span>
                </td>
                <td>{{ $u->last_login_at ? $u->last_login_at->format('M d, Y h:i A') : '-' }}</td>
                <td>
                  <div class="d-flex justify-content-center gap-2 flex-wrap">
                    {{-- Edit --}}
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                      <i class="bi bi-pencil"></i>
                    </button>

                    {{-- Activate/Deactivate --}}
                    <form action="{{ route('admins.users.toggle',$u->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button class="btn btn-sm btn-{{ $u->status=='active'?'secondary':'success' }}">
                        {{ $u->status=='active'?'Deactivate':'Activate' }}
                      </button>
                    </form>

                    {{-- Reset Password --}}
                    <form action="{{ route('admins.users.reset',$u->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button class="btn btn-sm btn-info">
                        <i class="bi bi-key"></i>
                      </button>
                    </form>

                    {{-- Delete --}}
                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $u->id }}">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>

              {{-- ✏️ Edit User Modal --}}
              <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <form method="POST" action="{{ route('admins.users.update',$u->id) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-content">
                      <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit User</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body row g-3">
                        <div class="col-md-6">
                          <input type="text" name="first_name" value="{{ $u->profile->first_name ?? '' }}" placeholder="First Name" class="form-control mb-2" required>
                          <input type="text" name="middle_name" value="{{ $u->profile->middle_name ?? '' }}" placeholder="Middle Name" class="form-control mb-2">
                          <input type="text" name="last_name" value="{{ $u->profile->last_name ?? '' }}" placeholder="Last Name" class="form-control mb-2" required>
                          <select name="sex" class="form-select mb-2">
                            <option value="">-- Select Sex --</option>
                            <option value="Male" {{ ($u->profile->sex ?? '')=='Male'?'selected':'' }}>Male</option>
                            <option value="Female" {{ ($u->profile->sex ?? '')=='Female'?'selected':'' }}>Female</option>
                          </select>
                          <input type="date" name="birthdate" value="{{ $u->profile->birthdate ?? '' }}" class="form-control mb-2">
                        </div>
                        <div class="col-md-6">
                          <input type="text" name="address" value="{{ $u->profile->address ?? '' }}" placeholder="Address" class="form-control mb-2">
                          <input type="text" name="contact_number" value="{{ $u->profile->contact_number ?? '' }}" placeholder="Contact Number" class="form-control mb-2">
                          <input type="email" name="email" value="{{ $u->email }}" class="form-control mb-2" required>
                          <select name="role_id" class="form-select mb-2" required>
                            @foreach($roles as $r)
                              <option value="{{ $r->id }}" {{ $u->role_id==$r->id?'selected':'' }}>
                                {{ $r->name }}
                              </option>
                            @endforeach
                          </select>

                          <div class="mb-2">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" name="profile_picture" class="form-control">
                            @if($u->profile && $u->profile->profile_picture)
                              <img src="{{ asset('storage/'.$u->profile->profile_picture) }}" alt="Profile" class="img-thumbnail mt-2" width="100">
                            @endif
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-warning">Update</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              {{-- 🗑️ Delete User Modal --}}
              <div class="modal fade" id="deleteUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                      <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirm Delete</h5>
                      <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p>Are you sure you want to delete user <strong>{{ $u->email }}</strong>? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <form method="POST" action="{{ route('admins.users.destroy',$u->id) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger">Delete</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <tr><td colspan="6" class="text-center text-muted py-4">
                <i class="bi bi-info-circle"></i> No users found.
              </td></tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div class="d-flex justify-content-end mt-3">
        {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>

{{-- ➕ Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form method="POST" action="{{ route('admins.users.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="bi bi-person-plus"></i> Add User</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-3">
          <div class="col-md-6">
            <input type="text" name="first_name" placeholder="First Name" class="form-control mb-2" required>
            <input type="text" name="middle_name" placeholder="Middle Name" class=" form-control mb-2">
            <input type="text" name="last_name" placeholder="Last Name" class="form-control mb-2" required>
            <select name="sex" class="form-select mb-2">
              <option value="">-- Select Sex --</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
            <input type="date" name="birthdate" class="form-control mb-2">
          </div>
          <div class="col-md-6">
            <input type="text" name="address" placeholder="Address" class="form-control mb-2">
            <input type="text" name="contact_number" placeholder="Contact Number" class="form-control mb-2">
            <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
            <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" class="form-control mb-2" required>
            <select name="role_id" class="form-select mb-2" required>
              <option value="">-- Select Role --</option>
              @foreach($roles as $r)
                <option value="{{ $r->id }}">{{ $r->name }}</option>
              @endforeach
            </select>
            <input type="file" name="profile_picture" class="form-control mb-2">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-success">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection