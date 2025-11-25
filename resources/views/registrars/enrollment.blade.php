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
                      <tr><td colspan="6" class="text-center text-muted">No enrolled students for the active school year.</td></tr>
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
                <div>
                  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="bi bi-person-plus me-1"></i> Add / Register Student
                  </button>
                </div>
              </div>

              <div class="card-body p-2">
                @if(isset($studentsNotEnrolled) && $studentsNotEnrolled->isEmpty())
                  <div class="text-muted small p-3">All students already enrolled in the active school year.</div>
                @elseif(isset($studentsNotEnrolled))
                  <ul class="list-group list-group-flush">
                    @foreach($studentsNotEnrolled as $s)
                      <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                          <div class="fw-semibold">{{ $s->user->profile->full_name ?? 'N/A' }}</div>
                          <small class="text-muted">{{ $s->student_number ?? '—' }}</small>
                        </div>
                        <div>
                          @if($isActive)
                            <form method="POST" action="{{ route('registrars.enrollment.store') }}" class="d-inline">
                              @csrf
                              <input type="hidden" name="student_id" value="{{ $s->id }}">
                              <select name="section_id" class="form-select form-select-sm d-inline-block me-2" style="width:220px;" required>
                                <option value="" disabled selected>-- Select Section --</option>
                                @foreach($sections as $section)
                                  <option value="{{ $section->id }}">{{ $section->gradeLevel->name ?? 'No Grade' }} - {{ $section->name }}</option>
                                @endforeach
                              </select>
                              <button class="btn btn-sm btn-primary">Enroll</button>
                            </form>
                          @else
                            <span class="small text-muted">Enrollment Closed</span>
                          @endif
                        </div>
                      </li>
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

            <div class="col-md-6">
              <label class="form-label small">Contact Number</label>
              <input name="contact_number" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Guardian Name</label>
              <input name="guardian_name" class="form-control" required>
            </div>

            <div class="col-12">
              <label class="form-label small">Address</label>
              <input name="address" class="form-control" required>
            </div>

            <div class="col-12">
              <label class="form-label small">Section</label>
              <select name="section_id" class="form-select" required>
                <option value="" disabled selected>-- Select Section --</option>
                @php
                  $sectionsByGrade = $sections->groupBy(fn($s) => $s->gradeLevel->name ?? 'No Grade');
                @endphp
                @foreach($sectionsByGrade as $gradeName => $gradeSections)
                  <optgroup label="{{ $gradeName }}">
                    @foreach($gradeSections as $section)
                      <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
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

{{-- Keep tab open on page reload if hash present or query param page exists --}}
@push('scripts')
<script>
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
});
</script>
@endpush

@endsection
