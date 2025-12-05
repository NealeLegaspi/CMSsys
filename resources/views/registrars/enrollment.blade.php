@extends('layouts.registrar')

@section('title','Student Registration')
@section('header')
    <i class="bi bi-person-plus me-2"></i> Student Registration
@endsection

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-body">
    @include('partials.alerts')

    @php
      $isActive = isset($activeSY) && $activeSY->status === 'active';
    @endphp

    {{-- Top: Active SY badge --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <small class="text-muted">Active School Year:
          @if($isActive)
            <span class="badge bg-success">{{ $activeSY->name }}</span>
          @else
            <span class="badge bg-warning text-dark">No active school year</span>
          @endif
        </small>
      </div>

      <div class="d-flex gap-2">
        <a href="{{ route('registrars.enrollment.export.csv') }}" class="btn btn-outline-success btn-sm">
          <i class="bi bi-file-earmark-excel me-1"></i> Excel
        </a>
        <a href="{{ route('registrars.enrollment.export.pdf') }}" class="btn btn-outline-danger btn-sm">
          <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
      </div>
    </div>

    {{-- Nav tabs --}}
    <ul class="nav nav-tabs" id="regTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-register" data-bs-toggle="tab" data-bs-target="#pane-register" type="button" role="tab">Student Registration</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-history" data-bs-toggle="tab" data-bs-target="#pane-history" type="button" role="tab">Enrollment History</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-archived" data-bs-toggle="tab" data-bs-target="#pane-archived" type="button" role="tab">Archived</button>
      </li>
    </ul>

    <div class="tab-content mt-3">
      {{-- TAB: Student Registration --}}
      <div class="tab-pane fade show active" id="pane-register" role="tabpanel">
        <div class="row g-3">

          {{-- MAIN TABLE: Enrolled Students --}}
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Enrolled Students — Active SY</strong>

                <form method="GET" class="d-flex" style="gap:8px;">
                  <input type="hidden" name="active_page" value="{{ request('active_page') }}">
                  <input type="text" name="search_active" value="{{ request('search_active') }}" class="form-control form-control-sm" placeholder="Search name...">
                  <button class="btn btn-outline-primary btn-sm">Search</button>
                </form>
              </div>

              <div class="table-responsive">
                <table class="table table-striped table-sm mb-0 align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>LRN</th>
                      <th>Student</th>
                      <th>Section</th>
                      <th>Adviser</th>
                      <th>Status</th>
                      <th class="text-center">Actions</th>
                    </tr>
                  </thead>

                  <tbody>
                    @forelse($activeEnrollments as $i => $en)
                      <tr>
                        <td>{{ $activeEnrollments->firstItem() + $i }}</td>
                        <td class="fw-bold text-primary">{{ $en->student->student_number ?? 'N/A' }}</td>
                        <td>{{ $en->student->user->profile->full_name ?? 'N/A' }}</td>
                        <td>{{ $en->section->name ?? 'N/A' }}</td>
                        <td>{{ $en->section->adviser->profile->full_name ?? 'N/A' }}</td>
                        <td>
                          <span class="badge {{ $en->status == 'Enrolled' ? 'bg-success' : ($en->status == 'For Verification' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ $en->status }}
                          </span>
                        </td>
                        <td class="text-center">
                          <a href="{{ route('registrars.student.record', $en->student->id) }}" class="btn btn-sm btn-info text-white" title="View Record">
                            <i class="bi bi-eye"></i>
                          </a>

                          @if($isActive)
                            {{-- Edit --}}
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEnrollmentModal-{{ $en->id }}" title="Edit Enrollment">
                              <i class="bi bi-pencil"></i>
                            </button>

                            {{-- Docs --}}
                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#docsModal-{{ $en->student->id }}" title="Upload Documents">
                              📎
                            </button>

                            {{-- Archive --}}
                            <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#archiveModal-{{ $en->id }}" title="Archive Enrollment">
                              <i class="bi bi-archive"></i>
                            </button>
                          @endif
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="7" class="text-center text-muted">No enrolled students for the active school year.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              <div class="card-footer d-flex justify-content-end">
                {{ $activeEnrollments->appends(request()->query())->links('pagination::bootstrap-5') }}
              </div>
            </div>
          </div>

          {{-- STUDENT TOOLS: moved below --}}
          <div class="col-12 mt-4">
            <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Student Tools</h5>
                <div class="d-flex align-items-center" style="gap:8px;">
                  <input type="text"
                         id="studentToolsSearch"
                         class="form-control form-control-sm"
                         placeholder="Search student...">
                  <button class="btn btn-primary btn-sm px-2 py-1" style="font-size: 0.75rem; white-space: nowrap;"
                          data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="bi bi-person-plus me-1"></i> Add / Register Student
                  </button>
                  <button class="btn btn-success btn-sm px-2 py-1" style="font-size: 0.75rem; white-space: nowrap;"
                          data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-upload me-1"></i> Import Excel
                  </button>
                </div>
              </div>

              <div class="card-body p-2">
                @if(isset($studentsNotEnrolled) && $studentsNotEnrolled->isEmpty())
                  <div class="text-muted small p-3">All students already enrolled in the active school year.</div>
                @elseif(isset($studentsNotEnrolled))
                  <ul class="list-group list-group-flush" id="studentToolsList">
                    @foreach($studentsNotEnrolled as $s)
                      @php
                        $info = $studentUpgradeInfo[$s->id] ?? null;
                        $allowedSectionIds = $info['allowed_section_ids'] ?? $sections->pluck('id')->all();
                        $fullName = $s->user->profile->full_name ?? 'N/A';
                        $studentNo = $s->student_number ?? '—';
                      @endphp
                      <li class="list-group-item d-flex justify-content-between align-items-center student-tools-item"
                          data-name="{{ Str::lower($fullName) }}"
                          data-lrn="{{ Str::lower($studentNo) }}">
                        <div>
                          <div class="fw-semibold">{{ $fullName }}</div>
                          <small class="text-muted d-block">{{ $studentNo }}</small>
                          @if($info && $info['last_sy_name'])
                            <small class="text-muted d-block">
                              Last enrolled: {{ $info['last_sy_name'] }} ({{ $info['last_grade_name'] ?? 'N/A' }})
                            </small>
                          @endif
                          @if($info && !empty($info['allowed_grade_names']))
                            <small class="text-muted d-block">
                              Allowed grades this SY: {{ implode(', ', $info['allowed_grade_names']) }}
                            </small>
                          @endif
                        </div>
                        <div>
                          @if($isActive)
                            <button class="btn btn-sm btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#enrollStudentModal-{{ $s->id }}">
                              Enroll
                            </button>
                          @else
                            <span class="small text-muted">Enrollment Closed</span>
                          @endif
                        </div>
                      </li>

                      {{-- Enrollment Modal for this student --}}
                      @php
                        $profile = $s->user->profile ?? null;
                        // Parse guardian_name into parts (assuming format: "First Middle Last" or "First Last")
                        $guardianParts = $profile && $profile->guardian_name 
                          ? explode(' ', trim($profile->guardian_name), 3) 
                          : ['', '', ''];
                        $guardianFirst = $guardianParts[0] ?? '';
                        $guardianMiddle = isset($guardianParts[1]) && count($guardianParts) > 2 ? $guardianParts[1] : '';
                        $guardianLast = count($guardianParts) > 2 ? ($guardianParts[2] ?? '') : ($guardianParts[1] ?? '');
                      @endphp
                      <div class="modal fade" id="enrollStudentModal-{{ $s->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                              <h5 class="modal-title"><i class="bi bi-person-plus me-1"></i> Enroll Student</h5>
                              <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" action="{{ route('registrars.enrollment.store') }}">
                              @csrf
                              <input type="hidden" name="student_id" value="{{ $s->id }}">
                              <div class="modal-body">
                                <div class="row g-2">
                                  <div class="col-md-4">
                                    <label class="form-label small">First Name</label>
                                    <input name="first_name" class="form-control" value="{{ $profile->first_name ?? '' }}" required>
                                  </div>
                                  <div class="col-md-4">
                                    <label class="form-label small">Middle Name</label>
                                    <input name="middle_name" class="form-control" value="{{ $profile->middle_name ?? '' }}">
                                  </div>
                                  <div class="col-md-4">
                                    <label class="form-label small">Last Name</label>
                                    <input name="last_name" class="form-control" value="{{ $profile->last_name ?? '' }}" required>
                                  </div>

                                  <div class="col-md-6">
                                    <label class="form-label small">Birthdate</label>
                                    <input type="date" name="birthdate_display" class="form-control" value="{{ $profile->birthdate ?? '' }}" disabled>
                                    <input type="hidden" name="birthdate" value="{{ $profile->birthdate ?? '' }}">
                                  </div>

                                  <div class="col-md-6">
                                    <label class="form-label small">Gender</label>
                                    <select name="sex_display" class="form-select" disabled>
                                      <option value="Male" {{ ($profile->sex ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                      <option value="Female" {{ ($profile->sex ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    <input type="hidden" name="sex" value="{{ $profile->sex ?? '' }}">
                                  </div>

                                  <div class="col-md-4">
                                    <label class="form-label small">Guardian First Name</label>
                                    <input name="guardian_first_name" class="form-control" value="{{ $guardianFirst }}" required>
                                  </div>
                                  <div class="col-md-4">
                                    <label class="form-label small">Guardian Middle Name</label>
                                    <input name="guardian_middle_name" class="form-control" value="{{ $guardianMiddle }}">
                                  </div>
                                  <div class="col-md-4">
                                    <label class="form-label small">Guardian Last Name</label>
                                    <input name="guardian_last_name" class="form-control" value="{{ $guardianLast }}" required>
                                  </div>

                                  <div class="col-12">
                                    <label class="form-label small">Contact Number</label>
                                    <input name="contact_number" class="form-control" value="{{ $profile->contact_number ?? '' }}" required>
                                  </div>

                                  <div class="col-12">
                                    <label class="form-label small">Address</label>
                                    <input name="address" class="form-control" value="{{ $profile->address ?? '' }}" required>
                                  </div>

                                  <div class="col-12">
                                    <label class="form-label small">LRN</label>
                                    <input class="form-control" value="{{ $s->student_number ?? '—' }}" disabled>
                                  </div>

                                  <div class="col-md-6">
                                    <label class="form-label small">Adviser</label>
                                    <select id="enrollAdviser-{{ $s->id }}" class="form-select" disabled style="background-color: #e9ecef; cursor: not-allowed;">
                                      <option value="">-- Select Section First --</option>
                                      @foreach($teachers as $teacher)
                                        @php
                                          // Find section assigned to this teacher (within allowed sections)
                                          $teacherSection = $sections->whereIn('id', $allowedSectionIds)->firstWhere('adviser_id', $teacher->id);
                                        @endphp
                                        <option value="{{ $teacher->id }}" data-section-id="{{ $teacherSection->id ?? '' }}">
                                          {{ $teacher->profile->full_name ?? $teacher->email }}
                                        </option>
                                      @endforeach
                                    </select>
                                    {{-- Hidden input to submit adviser_id even though select is disabled --}}
                                    <input type="hidden" name="adviser_id" id="enrollAdviserHidden-{{ $s->id }}">
                                  </div>

                                  <div class="col-md-6">
                                    <label class="form-label small">Section <span class="text-danger">*</span></label>
                                    <select name="section_id" id="enrollSection-{{ $s->id }}" class="form-select" required>
                                      <option value="" disabled selected>-- Select Section --</option>
                                      @php
                                        $sectionsByGrade = $sections->whereIn('id', $allowedSectionIds)->groupBy(fn($sec) => $sec->gradeLevel->name ?? 'No Grade');
                                      @endphp
                                      @foreach($sectionsByGrade as $gradeName => $gradeSections)
                                        <optgroup label="{{ $gradeName }}">
                                          @foreach($gradeSections as $section)
                                            <option value="{{ $section->id }}" data-adviser-id="{{ $section->adviser_id ?? '' }}">{{ $section->name }}</option>
                                          @endforeach
                                        </optgroup>
                                      @endforeach
                                    </select>
                                  </div>
                                  
                                  {{-- Subject Assignments Display (read-only, per student enrollment) --}}
                                  <div class="col-12 mt-3 enroll-subject-assignments" id="enrollSubjectAssignments-{{ $s->id }}" style="display: none;">
                                    <label class="form-label small fw-bold">Subject Assignments</label>
                                    <div class="table-responsive">
                                      <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                          <tr>
                                            <th>Subject</th>
                                            <th>Subject Teacher</th>
                                          </tr>
                                        </thead>
                                        <tbody id="enrollSubjectAssignmentsBody-{{ $s->id }}">
                                          {{-- Populated via JavaScript --}}
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <div class="modal-footer">
                                <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Enroll</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              </div>
                            </form>

                          </div>
                        </div>
                      </div>
                    @endforeach
                  </ul>
                @else
                  {{-- fallback if variable not available --}}
                  <div class="text-muted small p-3">No data available.</div>
                @endif
              </div>
            </div>
          </div>

        </div>
      </div>

      {{-- TAB: Enrollment History --}}
      <div class="tab-pane fade" id="pane-history" role="tabpanel">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Enrollment History</strong>
            <form method="GET" class="d-flex" style="gap:8px;">
              <input type="text" name="search_history" value="{{ request('search_history') }}" class="form-control form-control-sm" placeholder="Search...">
              <select name="history_school_year_id" class="form-select form-select-sm">
                <option value="">All SY</option>
                @foreach($schoolYears as $sy)
                  <option value="{{ $sy->id }}" {{ request('history_school_year_id') == $sy->id ? 'selected' : '' }}>{{ $sy->name }}</option>
                @endforeach
              </select>
              <button class="btn btn-outline-primary btn-sm">Filter</button>
            </form>
          </div>

          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>LRN</th>
                  <th>Student</th>
                  <th>Section</th>
                  <th>School Year</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($historyEnrollments as $i => $h)
                  <tr>
                    <td>{{ $historyEnrollments->firstItem() + $i }}</td>
                    <td>{{ $h->student->student_number ?? 'N/A' }}</td>
                    <td>{{ $h->student->user->profile->full_name ?? 'N/A' }}</td>
                    <td>{{ $h->section->name ?? 'N/A' }}</td>
                    <td>{{ $h->schoolYear->name ?? 'N/A' }}</td>
                    <td>
                      <span class="badge {{ $h->status == 'Enrolled' ? 'bg-success' : ($h->status == 'For Verification' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                        {{ $h->status }}
                      </span>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="6" class="text-center text-muted">No records.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="card-footer d-flex justify-content-end">
            {{ $historyEnrollments->appends(request()->query())->links('pagination::bootstrap-5') }}
          </div>
        </div>
      </div>

      {{-- TAB: Archived --}}
      <div class="tab-pane fade" id="pane-archived" role="tabpanel">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Archived Enrollments</strong>
            <form method="GET" class="d-flex" style="gap:8px;">
              <input type="text" name="search_archived" value="{{ request('search_archived') }}" class="form-control form-control-sm" placeholder="Search...">
              <button class="btn btn-outline-secondary btn-sm">Search</button>
            </form>
          </div>

          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>LRN</th>
                  <th>Student</th>
                  <th>Section</th>
                  <th>School Year</th>
                  <th>Archived At</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($archivedEnrollments as $i => $a)
                  <tr>
                    <td>{{ $archivedEnrollments->firstItem() + $i }}</td>
                    <td>{{ $a->student->student_number ?? 'N/A' }}</td>
                    <td>{{ $a->student->user->profile->full_name ?? 'N/A' }}</td>
                    <td>{{ $a->section->name ?? 'N/A' }}</td>
                    <td>{{ $a->schoolYear->name ?? 'N/A' }}</td>
                    <td>{{ optional($a->updated_at)->format('Y-m-d') }}</td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-success"
                              data-bs-toggle="modal"
                              data-bs-target="#restoreArchivedModal-{{ $a->id }}">
                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                      </button>
                    </td>
                  </tr>
                  <div class="modal fade" id="restoreArchivedModal-{{ $a->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                          <h5 class="modal-title">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Restore Enrollment
                          </h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                          <p class="mb-1">Restore enrollment for:</p>
                          <strong class="text-success">{{ $a->student->user->profile->full_name ?? 'Student' }}</strong>
                        </div>
                        <div class="modal-footer justify-content-center">
                          <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                          <form method="POST" action="{{ route('registrars.enrollment.restore', $a->id) }}">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-success">Restore</button>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                @empty
                  <tr><td colspan="7" class="text-center text-muted">No archived enrollments.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="card-footer d-flex justify-content-end">
            {{ $archivedEnrollments->appends(request()->query())->links('pagination::bootstrap-5') }}
          </div>
        </div>
      </div>

    </div> {{-- end tab-content --}}
  </div>
</div>

{{-- ----------------------------- MODALS ----------------------------- --}}

<div class="modal fade" id="importModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.enrollment.import') }}" enctype="multipart/form-data" class="modal-content">
      @csrf

      <div class="modal-header">
        <h5 class="modal-title">Import Students (Excel)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <a href="{{ route('registrars.enrollment.template') }}" 
        class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-file-earmark-arrow-down me-1"></i> Download Template
      </a>


      <div class="modal-body">
        <p class="small text-muted">Upload an Excel file (.xlsx/.xls) with the correct column format.</p>

        <input type="file" name="import_file" class="form-control" required>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary"><i class="bi bi-upload me-1"></i> Upload</button>
      </div>
    </form>
  </div>
</div>

{{-- Add Student Modal --}}
<div class="modal fade" id="addStudentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-person-plus me-1"></i> Add / Register Student</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="{{ route('registrars.enrollment.addStudent') }}">
        @csrf
        <div class="modal-body">
          <div class="row g-2">
            <div class="col-md-4">
              <label class="form-label small">First Name</label>
              <input name="first_name" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small">Middle Name</label>
              <input name="middle_name" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label small">Last Name</label>
              <input name="last_name" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Birthdate</label>
              <input type="date" name="birthdate" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Gender</label>
              <select name="sex" class="form-select" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label small">Guardian First Name</label>
              <input name="guardian_first_name" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label small">Guardian Middle Name</label>
              <input name="guardian_middle_name" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label small">Guardian Last Name</label>
              <input name="guardian_last_name" class="form-control" required>
            </div>

            <div class="col-12">
              <label class="form-label small">Contact Number</label>
              <input name="contact_number" class="form-control" required>
            </div>

            <div class="col-12">
              <label class="form-label small">Address</label>
              <input name="address" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Section</label>
              <select name="section_id" id="addStudentSection" class="form-select" required>
                <option value="" disabled selected>-- Select Section --</option>
                @php
                  $sectionsByGrade = $sections->groupBy(fn($s) => $s->gradeLevel->name ?? 'No Grade');
                @endphp
                @foreach($sectionsByGrade as $gradeName => $gradeSections)
                  <optgroup label="{{ $gradeName }}">
                    @foreach($gradeSections as $section)
                      <option value="{{ $section->id }}" data-adviser-id="{{ $section->adviser_id ?? '' }}">{{ $section->name }}</option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Adviser</label>
              <select id="addStudentAdviser" class="form-select" disabled style="background-color: #e9ecef; cursor: not-allowed;">
                <option value="">-- Select Section First --</option>
              </select>
              {{-- Hidden input to ensure adviser_id is submitted even when select is disabled --}}
              <input type="hidden" name="adviser_id" id="addStudentAdviserHidden">
            </div>

            {{-- Subject Assignments Display (read-only) --}}
            <div class="col-12 mt-3" id="addStudentSubjectAssignments" style="display: none;">
              <label class="form-label small fw-bold">Subject Assignments</label>
              <div class="table-responsive">
                <table class="table table-sm table-bordered">
                  <thead class="table-light">
                    <tr>
                      <th>Subject</th>
                      <th>Subject Teacher</th>
                    </tr>
                  </thead>
                  <tbody id="addStudentSubjectAssignmentsBody">
                    {{-- Will be populated via JavaScript --}}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Register & Enroll</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>

    </div>
  </div>
</div>

{{-- Render per-enrollment modals OUTSIDE table to avoid nesting issues --}}
@isset($activeEnrollments)
  @foreach($activeEnrollments as $en)
    {{-- Edit Enrollment Modal --}}
    <div class="modal fade" id="editEnrollmentModal-{{ $en->id }}" tabindex="-1">
      <div class="modal-dialog">
        <form method="POST" action="{{ route('registrars.enrollment.update', $en->id) }}">
          @csrf @method('PUT')
          <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
              <h5 class="modal-title">Edit Enrollment — {{ $en->student->user->profile->full_name ?? 'Student' }}</h5>
              <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-2">
                <label class="form-label small">Section</label>
                <select name="section_id" class="form-select" required>
                  @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ $en->section_id == $section->id ? 'selected' : '' }}>
                      {{ $section->gradeLevel->name ?? 'No Grade' }} - {{ $section->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="mb-2">
                <label class="form-label small">School Year</label>
                <select name="school_year_id" class="form-select" required>
                  @foreach($schoolYears as $sy)
                    <option value="{{ $sy->id }}" {{ $en->school_year_id == $sy->id ? 'selected' : '' }}>
                      {{ $sy->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-warning">Update</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    {{-- Docs Modal --}}
    <div class="modal fade" id="docsModal-{{ $en->student->id }}" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title">Documents — {{ $en->student->user->profile->full_name ?? 'Student' }}</h5>
            <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="{{ route('registrars.documents.store', $en->student->id) }}" enctype="multipart/form-data">
              @csrf
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label small">Document Type</label>
                  <select name="type" class="form-select" required>
                    <option value="Birth Certificate">Birth Certificate</option>
                    <option value="Form 137">Form 137</option>
                    <option value="Good Moral">Good Moral</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label small">File</label>
                  <input type="file" name="file" class="form-control" required>
                </div>
              </div>
              <div class="text-end mt-3">
                <button class="btn btn-primary">Upload</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    {{-- Archive Confirmation Modal --}}
    <div class="modal fade" id="archiveModal-{{ $en->id }}" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title">Archive Enrollment</h5>
            <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to archive enrollment for
            <strong>{{ $en->student->user->profile->full_name ?? 'Student' }}</strong>?
          </div>
          <div class="modal-footer">
            <form method="POST" action="{{ route('registrars.enrollment.archive', $en->id) }}">
              @csrf
              @method('PUT')
              <button class="btn btn-secondary">Archive</button>
            </form>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  @endforeach
@endisset

{{-- Scripts --}}
@push('scripts')
<script>
// Live search for Student Tools (works on initial load and AJAX page loads)
function initStudentToolsSearch() {
  console.log('initStudentToolsSearch called');
  const searchInput = document.getElementById('studentToolsSearch');
  const items = document.querySelectorAll('.student-tools-item');

  console.log('Student Tools items found:', items.length);

  if (!searchInput || !items.length) return;

  // Ensure we don't add multiple listeners
  if (searchInput.dataset.bound === 'true') return;
  searchInput.dataset.bound = 'true';

  searchInput.addEventListener('input', function () {
    console.log('Student Tools search input:', this.value);
    const term = this.value.toLowerCase().trim();

    items.forEach(item => {
      const name = item.dataset.name || '';
      const lrn  = item.dataset.lrn || '';
      const match = !term || name.includes(term) || lrn.includes(term);
      if (match) {
        item.classList.remove('d-none');
      } else {
        item.classList.add('d-none');
      }
    });
  });
}

// Run once on initial load (even if DOMContentLoaded already fired),
// and again every time the layout AJAX-loads a new page.
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initStudentToolsSearch);
} else {
  initStudentToolsSearch();
}
document.addEventListener('page:loaded', initStudentToolsSearch);

// Keep tab open on page reload if hash present or query param page exists
document.addEventListener('DOMContentLoaded', function () {
  // Show tab according to URL hash
  const hash = window.location.hash;
  if (hash) {
    const tabTriggerEl = document.querySelector(`#regTabs button[data-bs-target="${hash}"]`);
    if (tabTriggerEl) {
      new bootstrap.Tab(tabTriggerEl).show();
    }
  }

  // Maintain tab on pagination/search: add active pane hash to links
  const tabButtons = document.querySelectorAll('#regTabs button[data-bs-target]');
  tabButtons.forEach(btn => {
    btn.addEventListener('shown.bs.tab', function (e) {
      const target = e.target.getAttribute('data-bs-target');
      history.replaceState(null, null, target);
    });
  });

  // Section subject assignments data from server
  const sectionSubjectAssignments = @json($sectionSubjectAssignments ?? []);

  // Auto-fill adviser and show subject assignments when section is selected
  function initAddStudentAutoFill() {
    const addStudentSection = document.getElementById('addStudentSection');
    const addStudentAdviser = document.getElementById('addStudentAdviser');
    const subjectAssignmentsDiv = document.getElementById('addStudentSubjectAssignments');
    const subjectAssignmentsBody = document.getElementById('addStudentSubjectAssignmentsBody');

    if (addStudentSection && addStudentAdviser) {
      // Populate adviser dropdown with all teachers (for display purposes)
      const teachers = @json($teachers->map(function($t) {
        return ['id' => $t->id, 'name' => $t->profile->full_name ?? $t->email];
      })->values());
      addStudentAdviser.innerHTML = '<option value="">-- Select Section First --</option>';
      teachers.forEach(teacher => {
        const option = document.createElement('option');
        option.value = teacher.id;
        option.textContent = teacher.name;
        addStudentAdviser.appendChild(option);
      });

      // When section is selected, update adviser and show subject assignments
      addStudentSection.onchange = function() {
        const selectedOption = this.options[this.selectedIndex];
        const sectionId = this.value;
        const adviserId = selectedOption.getAttribute('data-adviser-id');
        
        // Update adviser dropdown (readonly, but show the value)
        const adviserHiddenInput = document.getElementById('addStudentAdviserHidden');
        if (adviserId && adviserId !== '' && adviserId !== '0') {
          addStudentAdviser.value = adviserId;
          if (adviserHiddenInput) {
            adviserHiddenInput.value = adviserId;
          }
        } else {
          addStudentAdviser.value = '';
          if (adviserHiddenInput) {
            adviserHiddenInput.value = '';
          }
        }

        // Show/hide and populate subject assignments
        if (sectionId && sectionSubjectAssignments[sectionId]) {
          const assignments = sectionSubjectAssignments[sectionId];
          subjectAssignmentsBody.innerHTML = '';
          
          if (assignments.length > 0) {
            assignments.forEach(assignment => {
              const row = document.createElement('tr');
              row.innerHTML = `
                <td>${assignment.subject_name || 'N/A'}</td>
                <td>${assignment.teacher_name || 'Not Assigned'}</td>
              `;
              subjectAssignmentsBody.appendChild(row);
            });
            subjectAssignmentsDiv.style.display = 'block';
          } else {
            subjectAssignmentsBody.innerHTML = '<tr><td colspan="2" class="text-muted text-center">No subjects available for this grade level</td></tr>';
            subjectAssignmentsDiv.style.display = 'block';
          }
        } else {
          subjectAssignmentsDiv.style.display = 'none';
          subjectAssignmentsBody.innerHTML = '';
        }
      };
    }
  }

  // Initialize on page load
  initAddStudentAutoFill();

  // Re-initialize when page is loaded via AJAX
  document.addEventListener('page:loaded', initAddStudentAutoFill);

  // Initialize when "Add / Register Student" modal is shown
  const addStudentModal = document.getElementById('addStudentModal');
  if (addStudentModal) {
    addStudentModal.addEventListener('shown.bs.modal', function() {
      initAddStudentAutoFill();
    });
    
    // Reset form and hide subject assignments when modal is closed
    addStudentModal.addEventListener('hidden.bs.modal', function() {
      const form = addStudentModal.querySelector('form');
      if (form) {
        form.reset();
      }
      const subjectAssignmentsDiv = document.getElementById('addStudentSubjectAssignments');
      const subjectAssignmentsBody = document.getElementById('addStudentSubjectAssignmentsBody');
      if (subjectAssignmentsDiv) {
        subjectAssignmentsDiv.style.display = 'none';
      }
      if (subjectAssignmentsBody) {
        subjectAssignmentsBody.innerHTML = '';
      }
      const addStudentAdviser = document.getElementById('addStudentAdviser');
      if (addStudentAdviser) {
        addStudentAdviser.innerHTML = '<option value="">-- Select Section First --</option>';
      }
    });
  }

  // Auto-fill between Section and Adviser for enrollment modals
  function initEnrollmentAutoFill() {
    document.querySelectorAll('[id^="enrollSection-"]').forEach(function(sectionSelect) {
      const studentId = sectionSelect.id.replace('enrollSection-', '');
      const adviserSelect = document.getElementById('enrollAdviser-' + studentId);
      const adviserHiddenInput = document.getElementById('enrollAdviserHidden-' + studentId);
      const subjectAssignmentsDiv = document.getElementById('enrollSubjectAssignments-' + studentId);
      const subjectAssignmentsBody = document.getElementById('enrollSubjectAssignmentsBody-' + studentId);
      
      if (adviserSelect) {
        // When section is selected, auto-fill adviser and show subject assignments
        sectionSelect.onchange = function() {
          const selectedOption = this.options[this.selectedIndex];
          const adviserId = selectedOption.getAttribute('data-adviser-id');
          const sectionId = this.value;
          
          if (adviserId && adviserId !== '' && adviserId !== '0') {
            const adviserOption = Array.from(adviserSelect.options).find(opt => opt.value === adviserId);
            if (adviserOption) {
              adviserSelect.value = adviserId;
              if (adviserHiddenInput) {
                adviserHiddenInput.value = adviserId;
              }
            }
          } else {
            adviserSelect.value = '';
            if (adviserHiddenInput) {
              adviserHiddenInput.value = '';
            }
          }

          // Show/hide and populate subject assignments for this enrollment
          if (sectionId && sectionSubjectAssignments[sectionId]) {
            const assignments = sectionSubjectAssignments[sectionId];
            if (subjectAssignmentsBody && subjectAssignmentsDiv) {
              subjectAssignmentsBody.innerHTML = '';
              if (assignments.length > 0) {
                assignments.forEach(assignment => {
                  const row = document.createElement('tr');
                  row.innerHTML = `
                    <td>${assignment.subject_name || 'N/A'}</td>
                    <td>${assignment.teacher_name || 'Not Assigned'}</td>
                  `;
                  subjectAssignmentsBody.appendChild(row);
                });
                subjectAssignmentsDiv.style.display = 'block';
              } else {
                subjectAssignmentsBody.innerHTML = '<tr><td colspan="2" class="text-muted text-center">No subjects available for this grade level</td></tr>';
                subjectAssignmentsDiv.style.display = 'block';
              }
            }
          } else if (subjectAssignmentsDiv && subjectAssignmentsBody) {
            subjectAssignmentsDiv.style.display = 'none';
            subjectAssignmentsBody.innerHTML = '';
          }
        };
      }
    });
  }

  // Initialize on page load
  initEnrollmentAutoFill();

  // Re-initialize when page is loaded via AJAX
  document.addEventListener('page:loaded', initEnrollmentAutoFill);

  // Initialize when enrollment modals are shown
  document.querySelectorAll('[id^="enrollStudentModal-"]').forEach(function(modal) {
    modal.addEventListener('shown.bs.modal', function() {
      initEnrollmentAutoFill();
    });
  });
});
</script>
@endpush

@endsection
