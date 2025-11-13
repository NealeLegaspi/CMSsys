@extends('layouts.registrar')

@section('title','Student Records')
@section('header')
    <i class="bi bi-clipboard-data-fill me-2"></i> Student Records
@endsection

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-body">
    @include('partials.alerts')

    @if(session('temp_password'))
      <div class="alert alert-info alert-dismissible fade show shadow-sm mt-2">
        <i class="bi bi-key me-2"></i>
        <strong>Temporary Password:</strong> {{ session('temp_password') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @php
      $isActive = isset($activeSY) && $activeSY->status === 'active';
    @endphp

    @if(!$isActive)
      <div class="alert alert-warning d-flex align-items-center">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Note: </strong> The current school year is closed. All actions are disabled.
      </div>
    @endif

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('registrars.enrollment') }}" class="row g-2 mb-3 align-items-center">
      <div class="col-md-3">
        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search Name or Section...">
      </div>
      <div class="col-md-2">
        <select name="school_year_id" class="form-select">
          <option value="">All School Years</option>
          @foreach($schoolYears as $sy)
            <option value="{{ $sy->id }}" {{ request('school_year_id') == $sy->id ? 'selected' : '' }}>
              {{ $sy->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-outline-primary flex-fill">
          <i class="bi bi-search"></i> Search
        </button>
        <a href="{{ route('registrars.enrollment') }}" class="btn btn-outline-secondary flex-fill">
          <i class="bi bi-arrow-clockwise"></i> Reset
        </a>
      </div>

      <div class="col-md-4">
        <div class="d-flex flex-wrap justify-content-end gap-2">
            <div class="btn-group">
              <button type="button" class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export
              </button>
              <ul class="dropdown-menu shadow-sm">
                <li>
                  <a class="dropdown-item" href="{{ route('registrars.enrollment.export.csv') }}">
                    <i class="bi bi-file-earmark-excel me-2 text-success"></i> Excel
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('registrars.enrollment.export.pdf') }}">
                    <i class="bi bi-file-earmark-pdf me-2 text-danger"></i> PDF
                  </a>
                </li>
              </ul>
            </div>
          <a href="{{ route('registrars.enrollment.archived') }}" class="btn btn-outline-dark btn-sm">
              <i class="bi bi-archive me-1"></i> Archived
          </a>
          @if($isActive)
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                <i class="bi bi-plus-circle me-1"></i> Add Student
            </button>
          @else
            <button type="button" class="btn btn-secondary btn-sm" disabled>
                <i class="bi bi-lock me-1"></i> Add Student (Closed)
            </button>
          @endif
        </div>
      </div>
    </form>


    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>LRN</th>
            <th>Student Name</th>
            <th>Gender</th>
            <th>Contact</th>
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
              <td>{{ $enrollment->student->user->profile->full_name ?? 'N/A' }}</td>
              <td>{{ $enrollment->student->user->profile->sex ?? 'N/A' }}</td>
              <td>{{ $enrollment->student->user->profile->contact_number ?? 'N/A' }}</td>
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

              <a href="{{ route('registrars.student.record', $enrollment->student->id) }}" class="btn btn-sm btn-info text-white"> <i class="bi bi-eye"></i> </a>
                @if($isActive)
                  <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#docsModal{{ $enrollment->student->id }}">
                    📎 Docs
                  </button>
                  <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEnrollmentModal{{ $enrollment->id }}">
                    <i class="bi bi-pencil"></i>
                  </button>

                  {{-- 🗄 Archive --}}
                  <form id="archiveForm{{ $enrollment->id }}" 
                        action="{{ route('registrars.enrollment.archive', $enrollment->id) }}" 
                        method="POST" style="display:inline;">
                    @csrf
                    @method('PUT')

                    <button type="button" class="btn btn-sm btn-secondary" 
                            data-bs-toggle="modal" 
                            data-bs-target="#archiveEnrollmentModal{{ $enrollment->id }}">
                      <i class="bi bi-archive"></i>
                    </button>

                    <div class="modal fade" id="archiveEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header bg-secondary text-white">
                            <h5 class="modal-title"><i class="bi bi-archive me-2"></i> Archive</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body text-center">
                            Are you sure you want to archive this enrollment record for 
                            <strong>{{ $enrollment->student->user->profile->full_name ?? 'N/A' }}</strong>?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-secondary">Archive</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>

                @else
                  <button class="btn btn-sm btn-secondary" disabled>
                    <i class="bi bi-lock"></i> Locked
                  </button>
                @endif
              </td>
            </tr>

            <!-- Edit Modal -->
            <div class="modal fade" id="editEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form method="POST" action="{{ route('registrars.enrollment.update', $enrollment->id) }}">
                  @csrf
                  @method('PUT')
                  <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                      <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Enrollment</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Section</label>
                        <select name="section_id" class="form-select" required>
                            <option value="" disabled>-- Select Section --</option>
                            @php
                                $sectionsByGrade = $sections->groupBy(function($section) {
                                    return $section->gradeLevel->name ?? 'No Grade';
                                });
                            @endphp

                            @foreach ($sectionsByGrade as $gradeName => $gradeSections)
                                <optgroup label="{{ $gradeName }}">
                                    @foreach ($gradeSections as $section)
                                        <option 
                                            value="{{ $section->id }}" 
                                            {{ $enrollment->section_id == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">School Year</label>
                        <select name="school_year_id" class="form-select" required>
                          @foreach ($schoolYears as $sy)
                            <option value="{{ $sy->id }}" {{ $enrollment->school_year_id == $sy->id ? 'selected' : '' }}>
                              {{ $sy->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-warning">Update</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

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
                  </div>
                </div>
              </div>
            </div>
          @empty
            <tr><td colspan="9" class="text-center text-muted">No enrollments yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $enrollments->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-semibold"><i class="bi bi-person-plus me-2"></i> Add New Student</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('registrars.enrollment.addStudent') }}">
        @csrf
        <div class="modal-body">

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" name="middle_name" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Guardian Name</label>
              <input type="text" name="guardian_name" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Contact Number</label>
              <input type="text" name="contact_number" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Birthdate</label> 
              <input type="date" name="birthdate" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Section</label>
                <select name="section_id" class="form-select" required>
                    <option value="" disabled selected>-- Select Section --</option>
                    @php
                        $sectionsByGrade = $sections->groupBy(function($section) {
                            return $section->gradeLevel->name ?? 'No Grade';
                        });
                    @endphp

                    @foreach ($sectionsByGrade as $gradeName => $sections)
                        <optgroup label="{{ $gradeName }}">
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Gender</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="sex" id="male" value="Male" required>
                <label class="form-check-label" for="male">Male</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="sex" id="female" value="Female" required>
                <label class="form-check-label" for="female">Female</label>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button type="submit" class="btn btn-success px-4">
            <i class="bi bi-check-circle me-2"></i> Add Student
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
