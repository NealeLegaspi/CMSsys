@extends('layouts.registrar')

@section('title','Enrollment')
@section('header','Enrollment')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">📋 Enrollment List</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">
      <i class="bi bi-plus-circle me-1"></i> Enroll Student
    </button>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>LRN</th>
            <th>Student Name</th>
            <th>Section</th>
            <th>School Year</th>
            <th width="140">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($enrollments as $index => $enrollment)
            <tr>
              <td>{{ $enrollments->firstItem() + $index }}</td>
              <td class="fw-bold text-primary">{{ $enrollment->student->student_number ?? 'N/A' }}</td>
              <td>{{ $enrollment->student->user->profile->first_name ?? '' }} {{ $enrollment->student->user->profile->last_name ?? '' }}</td>
              <td>{{ $enrollment->section->name ?? 'N/A' }}</td>
              <td>{{ $enrollment->schoolYear->name ?? 'N/A' }}</td>
              <td>
                <!-- Edit -->
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEnrollmentModal{{ $enrollment->id }}">
                  <i class="bi bi-pencil"></i>
                </button>

                <!-- Delete -->
                <form action="{{ route('registrars.enrollment.destroy', $enrollment->id) }}" method="POST" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this enrollment?')">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
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
                          @foreach($sections->groupBy('gradelevel_id') as $gradeId => $gradeSections)
                            <optgroup label="{{ $gradeSections->first()->gradeLevel->name }}">
                              @foreach($gradeSections as $sec)
                                <option value="{{ $sec->id }}" {{ $enrollment->section_id == $sec->id ? 'selected' : '' }}>
                                  {{ $sec->name }}
                                </option>
                              @endforeach
                            </optgroup>
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
          @empty
            <tr><td colspan="6" class="text-center text-muted">No enrollments yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $enrollments->links('pagination::bootstrap-5') }}</div>
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
              @foreach($sections->groupBy('gradelevel_id') as $gradeId => $gradeSections)
                <optgroup label="{{ $gradeSections->first()->gradeLevel->name }}">
                  @foreach($gradeSections as $sec)
                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                  @endforeach
                </optgroup>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">School Year</label>
            <select name="school_year_id" class="form-select" required>
              <option value="">-- Choose --</option>
              @foreach($schoolYears as $sy)
                <option value="{{ $sy->id }}">{{ $sy->name }}</option>
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
