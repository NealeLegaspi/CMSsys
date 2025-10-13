@extends('layouts.registrar')

@section('title','Teachers')
@section('header','Teachers')

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">
      <i class="bi bi-person-workspace me-2"></i> Teachers
    </h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
      <i class="bi bi-plus-circle me-1"></i> Add Teacher
    </button>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <!-- 🔍 Search -->
    <form method="GET" action="{{ route('registrars.teachers') }}" class="row g-2 mb-3">
      <div class="col-md-6">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or email...">
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> Search
        </button>
        <a href="{{ route('registrars.teachers') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-clockwise"></i> Reset
        </a>
      </div>
    </form>

    <!-- 🧾 Teachers Table -->
    <div class="table-responsive">
      <table class="table table-hover table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:50px;">#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th class="text-center" style="width:160px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($teachers as $i => $teacher)
          <tr>
            <td>{{ $teachers->firstItem() + $i }}</td>
            <td>{{ $teacher->profile->last_name }}, {{ $teacher->profile->first_name }} {{ $teacher->profile->middle_name }}</td>
            <td>{{ $teacher->email }}</td>
            <td>{{ $teacher->profile->contact_number ?? '—' }}</td>
            <td class="text-center">
              <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewTeacher{{ $teacher->id }}" title="View">
                  <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editTeacher{{ $teacher->id }}" title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTeacher{{ $teacher->id }}" title="Delete">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>

          <!-- 👁️ View Modal -->
          <div class="modal fade" id="viewTeacher{{ $teacher->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header bg-info text-white">
                  <h5 class="modal-title"><i class="bi bi-eye me-2"></i> Teacher Details</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p><strong>Full Name:</strong> {{ $teacher->profile->first_name }} {{ $teacher->profile->middle_name }} {{ $teacher->profile->last_name }}</p>
                  <p><strong>Email:</strong> {{ $teacher->email }}</p>
                  <p><strong>Contact:</strong> {{ $teacher->profile->contact_number ?? 'N/A' }}</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>

          <!-- ✏️ Edit Modal -->
          <div class="modal fade" id="editTeacher{{ $teacher->id }}" tabindex="-1">
            <div class="modal-dialog">
              <form method="POST" action="{{ route('registrars.teachers.update',$teacher->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-content">
                  <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark"><i class="bi bi-pencil-square me-2"></i> Edit Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row g-2">
                      <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ $teacher->profile->first_name }}" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="{{ $teacher->profile->middle_name }}">
                      </div>
                    </div>
                    <div class="mb-3 mt-2">
                      <label class="form-label">Last Name</label>
                      <input type="text" name="last_name" class="form-control" value="{{ $teacher->profile->last_name }}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" value="{{ $teacher->email }}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Contact Number</label>
                      <input type="text" name="contact_number" class="form-control" value="{{ $teacher->profile->contact_number }}">
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="reset_password" id="resetPassword{{ $teacher->id }}">
                      <label class="form-check-label" for="resetPassword{{ $teacher->id }}">
                        Reset password to <code>password123</code>
                      </label>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- 🗑️ Delete Modal -->
          <div class="modal fade" id="deleteTeacher{{ $teacher->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                  <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirm Delete</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  Are you sure you want to delete <strong>{{ $teacher->profile->first_name }} {{ $teacher->profile->last_name }}</strong>?
                </div>
                <div class="modal-footer">
                  <form method="POST" action="{{ route('registrars.teachers.destroy', $teacher->id) }}">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Delete</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-4">
              <i class="bi bi-exclamation-circle"></i> No teachers found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
      {{ $teachers->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

<!-- ➕ Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.teachers.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add Teacher</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Middle Name</label>
              <input type="text" name="middle_name" class="form-control">
            </div>
          </div>
          <div class="mb-3 mt-2">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact_number" class="form-control">
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" checked disabled>
            <label class="form-check-label">Default password: <code>password123</code></label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Add</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
