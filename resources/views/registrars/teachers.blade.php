@extends('layouts.registrar')

@section('title','Teachers')
@section('header','Teachers')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">👩‍🏫 Teacher List</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
      <i class="bi bi-plus-circle me-1"></i> Add Teacher
    </button>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th width="100">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($teachers as $index => $teacher)
          <tr>
            <td>{{ $teachers->firstItem() + $index }}</td>
            <td>{{ $teacher->profile->first_name ?? '' }} {{ $teacher->profile->last_name ?? '' }}</td>
            <td>{{ $teacher->email }}</td>
            <td>{{ $teacher->profile->contact_number ?? 'N/A' }}</td>
            <td>
              <form action="{{ route('registrars.teachers.destroy',$teacher->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete teacher?')">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted">No teachers found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $teachers->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.teachers.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i> Add Teacher</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
