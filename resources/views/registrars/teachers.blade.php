@extends('layouts.registrar')

@section('title','Teachers')
@section('header','Teachers')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">👩‍🏫 Teachers</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
      <i class="bi bi-plus-circle me-1"></i> Add Teacher
    </button>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <!-- Search -->
    <form method="GET" action="{{ route('registrars.teachers') }}" class="row g-2 mb-3">
      <div class="col-md-6">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search teacher...">
      </div>
      <div class="col-md-6 d-flex">
        <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i></button>
        <a href="{{ route('registrars.teachers') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i></a>
      </div>
    </form>

    <table class="table table-bordered table-striped align-middle">
      <thead class="table-primary">
        <tr>
          <th style="width:50px;">#</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Contact</th>
          <th style="width:140px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($teachers as $i => $teacher)
        <tr>
          <td>{{ $teachers->firstItem() + $i }}</td>
          <td>{{ $teacher->profile->first_name }} {{ $teacher->profile->middle_name }} {{ $teacher->profile->last_name }}</td>
          <td>{{ $teacher->email }}</td>
          <td>{{ $teacher->profile->contact_number ?? '-' }}</td>
          <td>
            <!-- View Button -->
            <button class="btn btn-sm btn-info me-1" data-bs-toggle="modal" data-bs-target="#viewTeacherModal{{ $teacher->id }}">
              <i class="bi bi-eye"></i>
            </button>
            <!-- Edit Button -->
            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editTeacherModal{{ $teacher->id }}">
              <i class="bi bi-pencil"></i>
            </button>
            <!-- Delete Button -->
            <form action="{{ route('registrars.teachers.destroy',$teacher->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Delete teacher?')"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>

        <!-- View Teacher Modal -->
        <div class="modal fade" id="viewTeacherModal{{ $teacher->id }}" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i> Teacher Details</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <p><strong>Full Name:</strong> {{ $teacher->profile->first_name }} {{ $teacher->profile->middle_name }} {{ $teacher->profile->last_name }}</p>
                <p><strong>Email:</strong> {{ $teacher->email }}</p>
                <p><strong>Contact:</strong> {{ $teacher->profile->contact_number ?? 'N/A' }}</p>
              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Teacher Modal -->
        <div class="modal fade" id="editTeacherModal{{ $teacher->id }}" tabindex="-1">
          <div class="modal-dialog">
            <form method="POST" action="{{ route('registrars.teachers.update',$teacher->id) }}">
              @csrf
              @method('PUT')
              <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                  <h5 class="modal-title"><i class="bi bi-pencil me-2"></i> Edit Teacher</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ $teacher->profile->first_name }}" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" value="{{ $teacher->profile->middle_name }}">
                  </div>
                  <div class="mb-3">
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

        @empty
        <tr>
          <td colspan="5" class="text-center text-muted">No teachers found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-3">{{ $teachers->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.teachers.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add Teacher</h5>
          <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middle_name" class="form-control">
          </div>
          <div class="mb-3">
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
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" checked disabled>
            <label class="form-check-label">
              Default password: <code>password123</code>
            </label>
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
