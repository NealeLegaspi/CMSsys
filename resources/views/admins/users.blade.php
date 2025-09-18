@extends('layouts.admin')

@section('title','Users')
@section('header','User Management')

@section('content')
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h6 class="fw-bold mb-0">👥 Users</h6>
    <div class="d-flex">
      <!-- Filter Form -->
      <form method="GET" class="d-flex me-2">
        <input type="text" name="search" class="form-control form-control-sm me-2" 
               placeholder="Search name/email..." value="{{ request('search') }}">
        <select name="role_id" class="form-select form-select-sm me-2">
          <option value="">All Roles</option>
          @foreach($roles as $r)
            <option value="{{ $r->id }}" {{ request('role_id')==$r->id?'selected':'' }}>{{ $r->name }}</option>
          @endforeach
        </select>
        <select name="status" class="form-select form-select-sm me-2">
          <option value="">All Status</option>
          <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
          <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
        </select>
        <button class="btn btn-sm btn-outline-primary me-2">Filter</button>
        <a href="{{ route('admins.users') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
      </form>

      <!-- Add User Button -->
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-plus-lg"></i> Add User
      </button>
    </div>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <table class="table table-bordered table-striped align-middle">
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
          <td>{{ $u->profile->first_name ?? '' }} {{ $u->profile->last_name ?? '' }}</td>
          <td>{{ $u->email }}</td>
          <td>{{ $u->role->name }}</td>
          <td>
            <span class="badge bg-{{ $u->status=='active'?'success':'secondary' }}">
              {{ ucfirst($u->status) }}
            </span>
          </td>
          <td>{{ $u->last_login_at ? $u->last_login_at->format('M d, Y H:i') : '-' }}</td>
          <td>
            <!-- Edit -->
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
              <i class="bi bi-pencil"></i>
            </button>
            <!-- Toggle -->
            <form action="{{ route('admins.users.toggle',$u->id) }}" method="POST" class="d-inline">@csrf
              <button class="btn btn-sm btn-secondary">
                {{ $u->status=='active'?'Deactivate':'Activate' }}
              </button>
            </form>
            <!-- Reset -->
            <form action="{{ route('admins.users.reset',$u->id) }}" method="POST" class="d-inline">@csrf
              <button class="btn btn-sm btn-info">Reset</button>
            </form>
            <!-- Delete -->
            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $u->id }}">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>

        <!-- Edit Modal -->
        <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1">
          <div class="modal-dialog">
            <form method="POST" action="{{ route('admins.users.update',$u->id) }}">
              @csrf @method('PUT')
              <div class="modal-content">
                <div class="modal-header bg-warning">
                  <h5 class="modal-title">Edit User</h5>
                  <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <input type="text" name="first_name" value="{{ $u->profile->first_name ?? '' }}" class="form-control mb-2" required>
                  <input type="text" name="last_name" value="{{ $u->profile->last_name ?? '' }}" class="form-control mb-2" required>
                  <input type="email" name="email" value="{{ $u->email }}" class="form-control mb-2" required>
                  <select name="role_id" class="form-select mb-2" required>
                    @foreach($roles as $r)
                      <option value="{{ $r->id }}" {{ $u->role_id==$r->id?'selected':'' }}>{{ $r->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button class="btn btn-warning">Update</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteUserModal{{ $u->id }}" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Delete User</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to delete <strong>{{ $u->email }}</strong>? This action cannot be undone.</p>
              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admins.users.destroy',$u->id) }}" method="POST">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger">Delete</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        @empty
        <tr><td colspan="6" class="text-center text-muted">No users found.</td></tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-3">{{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admins.users.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Add User</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" name="first_name" placeholder="First Name" class="form-control mb-2" required>
          <input type="text" name="last_name" placeholder="Last Name" class="form-control mb-2" required>
          <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
          <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
          <input type="password" name="password_confirmation" placeholder="Confirm Password" class="form-control mb-2" required>
          <select name="role_id" class="form-select mb-2" required>
            <option value="">-- Select Role --</option>
            @foreach($roles as $r)
              <option value="{{ $r->id }}">{{ $r->name }}</option>
            @endforeach
          </select>
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
