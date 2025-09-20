@extends('layouts.registrar')

@section('title','Enrollment')
@section('header','Enrollment')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">📋 Enrollment List</h6>
    <div>
      <a href="{{ route('registrars.enrollment.export.csv') }}" class="btn btn-sm btn-success me-2">
      <i class="bi bi-file-earmark-excel"></i> Excel
      </a>
      <a href="{{ route('registrars.enrollment.export.pdf') }}" class="btn btn-sm btn-danger me-2">
      <i class="bi bi-file-earmark-pdf"></i> PDF
      </a>
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">
        <i class="bi bi-plus-circle me-1"></i> Enroll Student
      </button>
    </div>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('registrars.enrollment') }}" class="row g-2 mb-3">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search LRN, Name or Section...">
      </div>
      <div class="col-md-3">
        <select name="school_year_id" class="form-select">
          <option value="">All School Years</option>
          @foreach($schoolYears as $sy)
            <option value="{{ $sy->id }}" {{ request('school_year_id') == $sy->id ? 'selected' : '' }}>
              {{ $sy->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3 d-flex">
        <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i></button>
        <a href="{{ route('registrars.enrollment') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i></a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>LRN</th>
            <th>Student Name</th>
            <th>Section</th>
            <th>School Year</th>
            <th width="160">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($enrollments as $index => $enrollment)
            <tr class="{{ $enrollment->schoolYear->status === 'active' ? 'table-success' : '' }}">
              <td>{{ $enrollments->firstItem() + $index }}</td>
              <td class="fw-bold text-primary">{{ $enrollment->student->student_number ?? 'N/A' }}</td>
              <td>{{ $enrollment->student->user->profile->first_name ?? '' }} {{ $enrollment->student->user->profile->last_name ?? '' }}</td>
              <td>
                {{ $enrollment->section->name ?? 'N/A' }}
                @if($enrollment->section && $enrollment->section->capacity)
                  <span class="text-muted small">
                    ({{ $enrollment->section->enrollments->count() }}/{{ $enrollment->section->capacity }})
                  </span>
                @endif
              </td>
              <td>{{ $enrollment->schoolYear->name ?? 'N/A' }}</td>
              <td>
                <!-- Edit -->
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEnrollmentModal{{ $enrollment->id }}">
                  <i class="bi bi-pencil"></i>
                </button>

                <!-- Delete -->
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEnrollmentModal{{ $enrollment->id }}">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form method="POST" action="{{ route('registrars.enrollment.update', $enrollment->id) }}">
                  @csrf @method('PUT')
                  <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                      <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Edit Enrollment</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <p><strong>Student:</strong> {{ $enrollment->student->user->profile->first_name }} {{ $enrollment->student->user->profile->last_name }}</p>
                      <div class="mb-3">
                        <label class="form-label">Section</label>
                        <select name="section_id" class="form-select" required>
                          @foreach($sections as $sec)
                            @php
                              $enrolledCount = $sec->enrollments->count();
                            @endphp
                            <option value="{{ $sec->id }}" 
                              {{ $enrollment->section_id == $sec->id ? 'selected' : '' }}
                              {{ $enrolledCount >= $sec->capacity && $enrollment->section_id != $sec->id ? 'disabled' : '' }}>
                              {{ $sec->name }} ({{ $enrolledCount }}/{{ $sec->capacity ?? '∞' }})
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">School Year</label>
                        <select name="school_year_id" class="form-select" required>
                          @foreach($schoolYears as $sy)
                            <option value="{{ $sy->id }}" {{ $enrollment->school_year_id == $sy->id ? 'selected' : '' }}>
                              {{ $sy->name }}
                            </option>
                          @endforeach
                        </select>
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

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteEnrollmentModal{{ $enrollment->id }}" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to remove <strong>{{ $enrollment->student->user->profile->first_name }} {{ $enrollment->student->user->profile->last_name }}</strong> from <strong>{{ $enrollment->section->name }}</strong>?
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('registrars.enrollment.destroy', $enrollment->id) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

          @empty
            <tr><td colspan="6" class="text-center text-muted">No enrollments yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $enrollments->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addEnrollmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.enrollment.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i> Enroll Student</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Student</label>
            <select name="student_id" class="form-select" required>
              <option value="">-- Choose --</option>
              @foreach($students as $s)
                <option value="{{ $s->id }}">{{ $s->student_number }} - {{ $s->user->profile->first_name }} {{ $s->user->profile->last_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Section</label>
            <select name="section_id" class="form-select" required>
              <option value="">-- Choose --</option>
              @foreach($sections as $sec)
                @php
                  $enrolledCount = $sec->enrollments->count();
                @endphp
                <option value="{{ $sec->id }}" {{ $enrolledCount >= $sec->capacity ? 'disabled' : '' }}>
                  {{ $sec->name }} ({{ $enrolledCount }}/{{ $sec->capacity ?? '∞' }})
                </option>
              @endforeach
            </select>
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
