@extends('layouts.registrar')

@section('title','Enrollment')
@section('header','Enrollment')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">
      <i class="bi bi-clipboard-data-fill me-2"></i> Enrollment List
    </h6>
    <div>
      <a href="{{ route('registrars.enrollment.export.csv') }}" class="btn btn-sm btn-success me-2">
        <i class="bi bi-file-earmark-excel me-1"></i> Excel
      </a>
      <a href="{{ route('registrars.enrollment.export.pdf') }}" class="btn btn-sm btn-danger me-2">
        <i class="bi bi-file-earmark-pdf me-1"></i> PDF
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
        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search Name or Section...">
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
      <div class="col-md-4">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> Search
        </button>
        <a href="{{ route('registrars.teachers') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-clockwise"></i> Reset
        </a>
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
            <th>Status</th>
            <th width="230">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($enrollments as $index => $enrollment)
            <tr>
              <td>{{ $enrollments->firstItem() + $index }}</td>
              <td class="fw-bold text-primary">{{ $enrollment->student->student_number ?? 'N/A' }}</td>
              <td>{{ $enrollment->student->user->profile->full_name ?? '' }}</td>
              <td>{{ $enrollment->section->name ?? 'N/A' }}</td>
              <td>{{ $enrollment->schoolYear->name ?? 'N/A' }}</td>
              <td>
                <span class="badge 
                  @if($enrollment->status == 'Enrolled') bg-success
                  @elseif($enrollment->status == 'For Verification') bg-warning text-dark
                  @else bg-secondary @endif">
                  {{ $enrollment->status }}
                </span>
              </td>
              <td>
                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#docsModal{{ $enrollment->student->id }}">
                  📎 Docs
                </button>

                @if($enrollment->status !== 'Enrolled')
                <form method="POST" action="{{ route('registrars.enrollment.verify', $enrollment->id) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-success">
                    <i class="bi bi-check-circle"></i> Verify
                  </button>
                </form>
                @endif

                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEnrollmentModal{{ $enrollment->id }}">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEnrollmentModal{{ $enrollment->id }}">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>

            <!-- Docs Modal -->
            <div class="modal fade" id="docsModal{{ $enrollment->student->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Documents for {{ $enrollment->student->user->profile->full_name ?? 'Student' }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <form method="POST" action="{{ route('registrars.documents.store', $enrollment->student->id) }}" enctype="multipart/form-data">
                      @csrf
                      <div class="row g-2 mb-3">
                        <div class="col-md-6">
                          <select name="type" class="form-select" required>
                            <option value="Birth Certificate">Birth Certificate</option>
                            <option value="Form 137">Form 137</option>
                            <option value="Good Moral">Good Moral</option>
                          </select>
                        </div>
                        <div class="col-md-6">
                          <input type="file" name="file" class="form-control" required>
                        </div>
                      </div>
                      <div class="text-end">
                        <button class="btn btn-primary"><i class="bi bi-upload"></i> Upload</button>
                      </div>
                    </form>

                    <hr>
                    <table class="table table-sm table-bordered">
                      <thead class="table-light">
                        <tr><th>Type</th><th>Status</th><th>Action</th></tr>
                      </thead>
                      <tbody>
                        @foreach($enrollment->student->documents as $doc)
                          <tr>
                            <td>{{ $doc->type }}</td>
                            <td>{{ $doc->status }}</td>
                            <td>
                              <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                              @if($doc->status != 'Verified')
                              <form action="{{ route('registrars.documents.verify', $doc->id) }}" method="POST" class="d-inline">@csrf
                                <button class="btn btn-sm btn-success">Mark Verified</button>
                              </form>
                              @endif
                              <form action="{{ route('registrars.documents.destroy', $doc->id) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                              </form>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <tr><td colspan="7" class="text-center text-muted">No enrollments yet.</td></tr>
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
                <option value="{{ $s->id }}">{{ $s->student_number }} - {{ $s->user->profile->full_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Section</label>
            <select name="section_id" class="form-select" required>
              <option value="">-- Choose --</option>
              @foreach($sections as $sec)
                @php $enrolledCount = $sec->enrollments->count(); @endphp
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
