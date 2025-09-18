<div id="usersTableContainer">
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
          <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $u->id }}">
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-secondary btn-toggle" data-id="{{ $u->id }}">
            {{ $u->status=='active'?'Deactivate':'Activate' }}
          </button>
          <button class="btn btn-sm btn-info btn-reset" data-id="{{ $u->id }}">
            Reset
          </button>
          <button class="btn btn-sm btn-danger btn-delete" data-id="{{ $u->id }}">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="text-center text-muted">No users found.</td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="mt-3">{{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
</div>
