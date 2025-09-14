@extends('layouts.admin')

@section('title','User Management')
@section('header','Users')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">👥 Users</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
      <i class="bi bi-plus-circle me-1"></i> Add User
    </button>
  </div>
  <div class="card-body">
    @include('partials.alerts')

    <table class="table table-bordered table-striped align-middle">
      <thead class="table-primary">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Last Login</th>
          <th width="200">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $i => $u)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $u->first_name }} {{ $u->last_name }}</td>
          <td>{{ $u->email }}</td>
          <td>{{ $u->role->name ?? '-' }}</td>
          <td>
            <span class="badge bg-{{ $u->status === 'active' ? 'success' : 'secondary' }}">
              {{ ucfirst($u->status) }}
            </span>
          </td>
          <td>{{ $u->last_login_at ?? '-' }}</td>
          <td>
            <!-- Edit -->
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
              <i class="bi bi-pencil"></i>
            </button>

            <!-- Deactivate -->
            <form action="{{ route('admins.users.deactivate',$u->id) }}" method="POST" class="d-inline">
              @csrf
              <button class="btn btn-sm btn-secondary">
                {{ $u->status === 'active' ? 'Deactivate' : 'Activate' }}
              </button>
            </form>

            <!-- Reset Password -->
            <form action="{{ route('admins.users.reset',$u->id) }}" method="POST" class="d-inline">
              @csrf
              <button class="btn btn-sm btn-info">Reset</button>
            </form>

            <!-- Delete -->
            <form action="{{ route('admins.users.destroy',$u->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Delete user?')"><i class="bi bi-trash"></i></button>
            </form>
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
                  <input type="text" class="form-control mb-2" name="first_name" value="{{ $u->first_name }}" required>
                  <input type="text" class="form-control mb-2" name="last_name" value="{{ $u->last_name }}" required>
                  <input type="email" class="form-control mb-2" name="email" value="{{ $u->email }}" required>
                  <select name="role_id" class="form-select mb-2">
                    @foreach($roles as $r)
                      <option value="{{ $r->id }}" {{ $u->role_id == $r->id ? 'selected':'' }}>
                        {{ $r->name }}
                      </option>
                    @endforeach
                  </select>
                  <select name="status" class="form-select">
                    <option value="active" {{ $u->status=='active'?'selected':'' }}>Active</option>
                    <option value="inactive" {{ $u->status=='inactive'?'selected':'' }}>Inactive</option>
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
        @empty
        <tr><td colspan="7" class="text-center text-muted">No users yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admins.users.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Add User</h5>
          <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control mb-2" name="first_name" placeholder="First Name" required>
          <input type="text" class="form-control mb-2" name="last_name" placeholder="Last Name" required>
          <input type="email" class="form-control mb-2" name="email" placeholder="Email" required>
          <select name="role_id" class="form-select mb-2" required>
            <option value="">-- Select Role --</option>
            @foreach($roles as $r)
              <option value="{{ $r->id }}">{{ $r->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- User Details Modal -->
<div class="modal fade" id="userDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">User Details</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="user-details">
          <p><strong>Name:</strong> <span id="user-name"></span></p>
          <p><strong>Email:</strong> <span id="user-email"></span></p>
          <p><strong>Role:</strong> <span id="user-role"></span></p>
          <p><strong>Status:</strong> <span id="user-status"></span></p>
          <p><strong>Last Login:</strong> <span id="user-login"></span></p>
        </div>
        <hr>
        <h6>Recent Activity Logs</h6>
        <ul id="user-logs" class="list-group"></ul>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
function viewUser(id) {
  fetch(`/admin/users/${id}`)
    .then(res => res.json())
    .then(data => {
      document.getElementById('user-name').textContent = data.user.first_name + ' ' + data.user.last_name;
      document.getElementById('user-email').textContent = data.user.email;
      document.getElementById('user-role').textContent = data.user.role.name;
      document.getElementById('user-status').textContent = data.user.status;
      document.getElementById('user-login').textContent = data.user.last_login_at ?? '-';

      let logs = '';
      if (data.logs.length > 0) {
        data.logs.forEach(log => {
          logs += `<li class="list-group-item">${log.created_at} - ${log.action} (${log.description})</li>`;
        });
      } else {
        logs = `<li class="list-group-item text-muted">No recent activity.</li>`;
      }
      document.getElementById('user-logs').innerHTML = logs;

      new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
    })
    .catch(err => console.error(err));
}
</script>
@endsection
