@extends('layouts.registrar')

@section('title','Teachers')
@section('header','Teachers')

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-header bg-light">
    <div class="d-flex justify-content-between align-items-center">
      <h6 class="fw-bold mb-0">
        <i class="bi bi-person-workspace me-2"></i> Teachers
      </h6>
    </div>
    <ul class="nav nav-tabs mt-3">
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('registrars.teachers') ? 'active' : '' }}" href="{{ route('registrars.teachers') }}">
          Active
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('registrars.teachers.archived') ? 'active' : '' }}" href="{{ route('registrars.teachers.archived') }}">
          Archived
        </a>
      </li>
    </ul>
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
          @php
            $advisories = $advisoriesByTeacher[$teacher->id] ?? collect();
            $loads      = $teachingLoads[$teacher->id] ?? collect();
          @endphp
          <tr>
            <td>{{ $teachers->firstItem() + $i }}</td>
            <td>
              {{ $teacher->profile->last_name }}, {{ $teacher->profile->first_name }} {{ $teacher->profile->middle_name }}
              @if($advisories->isNotEmpty())
                <div class="small text-muted mt-1">
                  <div>
                    <strong>Advisory:</strong>
                    @foreach($advisories as $sec)
                      {{ $sec->gradeLevel->name ?? 'Grade' }} - {{ $sec->name }}@if(!$loop->last),@endif
                    @endforeach
                  </div>
                </div>
              @endif
            </td>
            <td>{{ $teacher->email }}</td>
            <td>{{ $teacher->profile->contact_number ?? '—' }}</td>
            <td class="text-center">
              <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewTeacher{{ $teacher->id }}" title="View">
                  <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#assignTeacher{{ $teacher->id }}" title="Assign Advisory & Subjects">
                  <i class="bi bi-diagram-3"></i>
                </button>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editTeacher{{ $teacher->id }}" title="Edit">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#deleteTeacher{{ $teacher->id }}" title="Archive">
                  <i class="bi bi-archive"></i>
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

          <!-- 📚 Assign Advisory & Subjects Modal -->
          <div class="modal fade" id="assignTeacher{{ $teacher->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <form method="POST" action="{{ route('registrars.teachers.assignLoad', $teacher->id) }}">
                @csrf
                <div class="modal-content">
                  <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">
                      <i class="bi bi-diagram-3 me-2"></i>
                      Assign Advisory & Teaching Load
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    @if(isset($currentSY))
                      <p class="small text-muted mb-3">
                        Active School Year: <strong>{{ $currentSY->name ?? ($currentSY->start_date.' - '.$currentSY->end_date) }}</strong>
                      </p>
                    @endif

                    <div class="mb-3">
                      <label class="form-label fw-semibold">Advisory Section</label>
                      <select name="advisory_section_id"
                              class="form-select advisory-section-select"
                              data-teacher-id="{{ $teacher->id }}">
                        <option value="">-- None / No Change --</option>
                        @foreach($sections as $section)
                          <option value="{{ $section->id }}"
                            data-gradelevel-id="{{ $section->gradelevel_id }}"
                            @if($advisories->contains('id', $section->id)) selected @endif>
                            {{ $section->gradeLevel->name ?? 'Grade' }} - {{ $section->name }}
                          </option>
                        @endforeach
                      </select>
                      <small class="text-muted">This sets which section (grade & section) the teacher will advise for the active school year.</small>
                    </div>

                    <hr>

                    <div class="mb-2">
                      <label class="form-label fw-semibold">Subjects (for this teacher in their advisory)</label>
                      <div class="border rounded p-2" style="max-height:220px; overflow:auto;">
                        @forelse($subjects as $subject)
                          <div class="form-check">
                            <input class="form-check-input subject-checkbox" type="checkbox"
                                   name="teaching_subject_ids[]"
                                   value="{{ $subject->id }}"
                                   id="teachSub{{ $teacher->id }}_{{ $subject->id }}"
                                   data-gradelevel-id="{{ $subject->grade_level_id }}"
                                   data-subject-name="{{ strtolower(trim($subject->name)) }}"
                                   @if($loads->contains('subject_id', $subject->id)) checked @endif>
                            <label class="form-check-label" for="teachSub{{ $teacher->id }}_{{ $subject->id }}">
                              {{ $subject->gradeLevel->name ?? 'Grade' }} - {{ $subject->name }}
                            </label>
                          </div>
                        @empty
                          <div class="text-muted small">No subjects available for the active school year.</div>
                        @endforelse
                      </div>
                      <small class="text-muted d-block mt-1">
                        Checked subjects will be assigned to this teacher in their advisory section.
                      </small>
                    </div>

                    @if($loads->isNotEmpty())
                      <hr>
                      <div class="small">
                        <strong>Current Load:</strong>
                        <ul class="mb-0">
                          @foreach($loads as $load)
                            <li>
                              {{ $gradeLevels[$load->gradelevel_id] ?? 'Grade' }} - {{ $load->section_name }}:
                              {{ $load->subject_name }}
                            </li>
                          @endforeach
                        </ul>
                      </div>
                    @endif
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Save Assignments</button>
                  </div>
                </div>
              </form>
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

          <!-- 🗃️ Archive Modal -->
          <div class="modal fade" id="deleteTeacher{{ $teacher->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                  <h5 class="modal-title"><i class="bi bi-archive"></i> Archive Teacher</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  Are you sure you want to archive <strong>{{ $teacher->profile->first_name }} {{ $teacher->profile->last_name }}</strong>?
                </div>
                <div class="modal-footer">
                  <form method="POST" action="{{ route('registrars.teachers.destroy', $teacher->id) }}">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-archive"></i> Archive</button>
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

@push('scripts')
<script>
(function () {
  const initAutoCheckSubjects = () => {
    document.querySelectorAll('.advisory-section-select').forEach(select => {
      if (select.dataset.bound === 'true') return;
      select.dataset.bound = 'true';

      const teacherId = select.dataset.teacherId;
      const modal = select.closest('.modal');
      if (!modal) return;

      const subjectCheckboxes = modal.querySelectorAll('.form-check-input[data-gradelevel-id]');

      const syncSubjects = () => {
        const selectedOption = select.options[select.selectedIndex];
        const gradeLevelId = selectedOption ? selectedOption.getAttribute('data-gradelevel-id') : null;

        if (!gradeLevelId) {
          // If no advisory selected, do not change existing checks
          return;
        }

        subjectCheckboxes.forEach(cb => {
          const cbGradeId = cb.getAttribute('data-gradelevel-id');
          if (cbGradeId === gradeLevelId) {
            cb.checked = true;
          }
        });
      };

      // Auto-check when advisory section changes
      select.addEventListener('change', syncSubjects);

      // Also run once when modal is first opened (in case advisory already selected)
      modal.addEventListener('shown.bs.modal', syncSubjects, { once: true });
    });
  };

  // Auto-check all subjects with the same name when one is checked
  const initSubjectNameGrouping = () => {
    document.querySelectorAll('.subject-checkbox').forEach(checkbox => {
      if (checkbox.dataset.groupingBound === 'true') return;
      checkbox.dataset.groupingBound = 'true';

      checkbox.addEventListener('change', function() {
        const subjectName = this.getAttribute('data-subject-name');
        const isChecked = this.checked;
        const modal = this.closest('.modal');
        
        if (!modal || !subjectName) return;

        // Find all checkboxes with the same subject name in this modal
        const sameNameCheckboxes = modal.querySelectorAll(
          `.subject-checkbox[data-subject-name="${subjectName}"]`
        );

        // Check/uncheck all subjects with the same name
        sameNameCheckboxes.forEach(cb => {
          if (cb !== this) {
            cb.checked = isChecked;
          }
        });
      });
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    initAutoCheckSubjects();
    initSubjectNameGrouping();
  });
  
  document.addEventListener('page:loaded', () => {
    initAutoCheckSubjects();
    initSubjectNameGrouping();
  });
})();
</script>
@endpush
