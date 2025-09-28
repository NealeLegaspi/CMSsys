@extends('layouts.registrar')

@section('title','Sections')
@section('header','Sections')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <!-- Header -->
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">📚 Sections</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
      <i class="bi bi-plus-circle me-1"></i> Add Section
    </button>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <!-- 🔍 Search & Filter -->
    <form method="GET" action="{{ route('registrars.sections') }}" class="row g-2 mb-3">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search section..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <select name="gradelevel_id" class="form-select">
          <option value="">All Grade Levels</option>
          @foreach($gradeLevels as $gl)
            <option value="{{ $gl->id }}" {{ request('gradelevel_id')==$gl->id ? 'selected' : '' }}>
              {{ $gl->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select name="school_year_id" class="form-select">
          <option value="">All School Years</option>
          @foreach($schoolYears as $sy)
            <option value="{{ $sy->id }}" {{ request('school_year_id')==$sy->id ? 'selected' : '' }}>
              {{ $sy->name ?? ($sy->start_date.' - '.$sy->end_date) }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
        <a href="{{ route('registrars.sections') }}" class="btn btn-secondary w-100"><i class="bi bi-arrow-repeat"></i></a>
      </div>
    </form>

    <!-- 📋 Sections Table -->
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-primary">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Grade Level</th>
          <th>School Year</th>
          <th>Adviser</th>
          <th>Capacity</th>
          <th width="200">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sections as $i => $sec)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $sec->name }}</td>
          <td>{{ optional($sec->gradeLevel)->name ?? '-' }}</td>
          <td>{{ optional($sec->schoolYear)->name ?? (optional($sec->schoolYear)->start_date.' - '.optional($sec->schoolYear)->end_date) }}</td>
          <td>{{ optional($sec->adviser->profile)->first_name }} {{ optional($sec->adviser->profile)->last_name }}</td>
          <td>{{ $sec->capacity ?? '∞' }}</td>
          <td class="d-flex gap-1">
            <!-- Assign Subjects -->
            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#assignSubjectModal{{ $sec->id }}">
              <i class="bi bi-book"></i>
            </button>

            <!-- Edit -->
            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSectionModal{{ $sec->id }}">
              <i class="bi bi-pencil-square"></i>
            </button>

            <!-- Delete -->
            <form action="{{ route('registrars.sections.destroy',$sec->id) }}" method="POST" onsubmit="return confirm('Delete section?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>

        <!-- Edit Section Modal -->
        <div class="modal fade" id="editSectionModal{{ $sec->id }}" tabindex="-1">
          <div class="modal-dialog">
            <form method="POST" action="{{ route('registrars.sections.update',$sec->id) }}">
              @csrf @method('PUT')
              <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                  <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Edit Section</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name',$sec->name) }}" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Grade Level</label>
                    <select name="gradelevel_id" class="form-select" required>
                      @foreach($gradeLevels as $gl)
                        <option value="{{ $gl->id }}" {{ $gl->id==$sec->gradelevel_id ? 'selected' : '' }}>
                          {{ $gl->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">School Year</label>
                    <select name="school_year_id" class="form-select" required>
                      @foreach($schoolYears as $sy)
                        <option value="{{ $sy->id }}" {{ $sy->id==$sec->school_year_id ? 'selected' : '' }}>
                          {{ $sy->name ?? ($sy->start_date . ' - ' . $sy->end_date) }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Adviser</label>
                    <select name="adviser_id" class="form-select">
                      <option value="">-- None --</option>
                      @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ $teacher->id==$sec->adviser_id ? 'selected' : '' }}>
                          {{ $teacher->profile->first_name }} {{ $teacher->profile->last_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control" min="1" value="{{ old('capacity',$sec->capacity) }}">
                  </div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button class="btn btn-warning text-white">Update</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Assign Subject Modal -->
        <div class="modal fade" id="assignSubjectModal{{ $sec->id }}" tabindex="-1">
          <div class="modal-dialog">
            <form method="POST" action="{{ route('registrars.sections.assign',$sec->id) }}">
              @csrf
              <div class="modal-content">
                <div class="modal-header bg-info text-white">
                  <h5 class="modal-title"><i class="bi bi-book me-2"></i> Assign Subject to Teacher</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Teacher</label>
                    <select name="teacher_id" class="form-select" required>
                      <option value="">-- Select Teacher --</option>
                      @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">
                          {{ $teacher->profile->first_name }} {{ $teacher->profile->last_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select" required>
                      <option value="">-- Select Subject --</option>
                      @foreach($subjects as $subj)
                        <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <!-- Already Assigned -->
                  <h6 class="mt-4">Already Assigned</h6>
                  <table class="table table-sm table-bordered">
                    <thead>
                      <tr>
                        <th>Subject</th>
                        <th>Teacher</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse($sec->teachers as $t)
                        @php
                          $subj = $subjects->firstWhere('id',$t->pivot->subject_id);
                        @endphp
                        <tr>
                          <td>{{ $subj->name ?? '-' }}</td>
                          <td>{{ $t->profile->first_name }} {{ $t->profile->last_name }}</td>
                        </tr>
                      @empty
                        <tr><td colspan="2" class="text-center text-muted">No subjects assigned yet.</td></tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button class="btn btn-info text-white">Assign</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        @empty
        <tr><td colspan="7" class="text-center text-muted">No sections yet.</td></tr>
        @endforelse
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-3">{{ $sections->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add Section -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.sections.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add Section</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Grade Level</label>
            <select name="gradelevel_id" class="form-select" required>
              <option value="">-- Choose --</option>
              @foreach($gradeLevels as $gl)
                <option value="{{ $gl->id }}">{{ $gl->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">School Year</label>
            <select name="school_year_id" class="form-select" required>
              <option value="">-- Choose --</option>
              @foreach($schoolYears as $sy)
                <option value="{{ $sy->id }}">{{ $sy->name ?? ($sy->start_date . ' - ' . $sy->end_date) }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Adviser</label>
            <select name="adviser_id" class="form-select">
              <option value="">-- None --</option>
              @foreach($teachers as $teacher)
                <option value="{{ $teacher->id }}">{{ $teacher->profile->first_name }} {{ $teacher->profile->last_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control" min="1" placeholder="e.g. 40">
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
