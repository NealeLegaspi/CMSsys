@extends('layouts.registrar')

@section('title', 'Section Management')
@section('header')
    <i class="bi bi-building me-2"></i> Section Management
@endsection

@push('scripts')
<script>
(function () {
  const gradeSubjectsMap = @json(
    $subjects->groupBy('grade_level_id')->map(function ($group) {
        return $group->map(function ($subject) {
            return [
                'id' => $subject->id,
                'name' => $subject->name,
            ];
        })->values();
    })
  );


  const renderSubjectsList = (container, subjects, countEl) => {
    if (!container) return;
    if (!subjects || !subjects.length) {
      container.innerHTML = '<span class="text-muted">No subjects found for this grade level.</span>';
      if (countEl) countEl.textContent = '';
      return;
    }

    const listHtml = subjects
      .map(sub => `<span class="badge text-bg-secondary me-1 mb-1">${sub.name}</span>`)
      .join('');

    container.innerHTML = listHtml;
    if (countEl) {
      countEl.textContent = `${subjects.length} subject${subjects.length === 1 ? '' : 's'}`;
    }
  };

  const initAddSectionSubjects = () => {
    const modal = document.getElementById('addSectionModal');
    if (!modal || modal.dataset.subjectsBound === 'true') {
      return;
    }

    const gradeSelect = modal.querySelector('.add-section-grade-select');
    const subjectList = modal.querySelector('#add-section-subject-list');
    const subjectCount = modal.querySelector('#add-section-subject-count');

    if (!gradeSelect || !subjectList) {
      return;
    }

    const handleUpdate = () => {
      const gradeId = gradeSelect.value;
      if (!gradeId) {
        subjectList.innerHTML = 'Select a grade level to view its subjects.';
        if (subjectCount) subjectCount.textContent = '';
        return;
      }
      const subjects = gradeSubjectsMap[gradeId] || [];
      if (!subjects.length) {
        subjectList.innerHTML = '<span class="text-muted">No subjects found for this grade level.</span>';
        if (subjectCount) subjectCount.textContent = '';
        return;
      }
      renderSubjectsList(subjectList, subjects, subjectCount);
    };

    gradeSelect.addEventListener('change', handleUpdate);
    modal.addEventListener('shown.bs.modal', handleUpdate);
    modal.dataset.subjectsBound = 'true';
  };

  document.addEventListener('DOMContentLoaded', () => {
    initAddSectionSubjects();
  });

  document.addEventListener('page:loaded', () => {
    initAddSectionSubjects();
  });
})();
</script>
@endpush

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-body">
    @include('partials.alerts')

    {{-- Alert for Closed or Missing School Year --}}
    @if(!$currentSY || $currentSY->status !== 'active')
      <div class="alert alert-warning d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
          <strong>Note:</strong> No active school year detected.
          All management actions are disabled until a school year is activated.
        </div>
      </div>
    @endif

    @php
        $canReuseSections = $currentSY && $reusableSchoolYears->isNotEmpty();
    @endphp

    <form method="GET" action="{{ route('registrars.sections') }}" class="row g-2 mb-3">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control"
               placeholder="Search section or adviser..."
               value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('registrars.sections') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
      </div>
      <div class="col-md-6 d-flex justify-content-end gap-2 flex-wrap">
        <a href="{{ route('registrars.sections.archived') }}" class="btn btn-outline-dark me-2">
          <i class="bi bi-archive"></i> View Archived
        </a>

        <button type="button"
                class="btn btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#reuseSectionsModal"
                {{ $canReuseSections ? '' : 'disabled' }}>
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reuse Previous
        </button>

        {{-- Add Section Button (Disabled if no active SY) --}}
        <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#addSectionModal"
                {{ (!$currentSY || $currentSY->status !== 'active') ? 'disabled' : '' }}>
          <i class="bi bi-plus-circle me-1"></i> Add Section
        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Section Name</th>
            <th>Grade Level</th>
            <th>Adviser</th>
            <th>Capacity</th>
            <th>Enrolled</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sections as $index => $sec)
            <tr>
              <td>{{ $sections->firstItem() + $index }}</td>
              <td class="fw-bold">{{ $sec->name }}</td>
              <td>{{ $sec->gradeLevel->name ?? 'N/A' }}</td>
              <td>
                {{ $sec->adviser?->profile?->first_name ?? 'N/A' }}
                {{ $sec->adviser?->profile?->last_name ?? '' }}
              </td>
              <td>{{ $sec->capacity ?? '∞' }}</td>
              <td>{{ $sec->enrollments->count() }}</td>
              <td>
                <div class="d-flex justify-content-center gap-1">
                  {{-- Manage Subjects --}}
                  <a href="{{ route('registrars.sections.subjects', ['id' => $sec->id]) }}"
                     class="btn btn-sm btn-dark {{ (!$currentSY || $currentSY->status !== 'active') ? 'disabled' : '' }}"
                     title="Manage Subject Load">
                    <i class="bi bi-journal-bookmark-fill"></i>
                  </a>

                  {{-- View Class List (Still enabled even if closed) --}}
                  <a href="{{ route('registrars.classlist', $sec->id) }}"
                     class="btn btn-sm btn-info text-white"
                     title="View Class List">
                    <i class="bi bi-people"></i>
                  </a>

                  {{-- Edit Section (Toggle Expand) --}}
                  <button class="btn btn-sm btn-warning"
                          type="button"
                          data-bs-toggle="collapse"
                          data-bs-target="#sectionDetails{{ $sec->id }}"
                          aria-expanded="false"
                          aria-controls="sectionDetails{{ $sec->id }}"
                          {{ (!$currentSY || $currentSY->status !== 'active') ? 'disabled' : '' }}>
                    <i class="bi bi-pencil"></i>
                  </button>

                  {{-- Archive Section --}}
                  <button class="btn btn-sm btn-secondary"
                          data-bs-toggle="modal" data-bs-target="#archiveSectionModal{{ $sec->id }}"
                          {{ (!$currentSY || $currentSY->status !== 'active') ? 'disabled' : '' }}>
                    <i class="bi bi-archive"></i>
                  </button>
                </div>
              </td>
            </tr>

            {{-- Expanded Row for Subject Assignments --}}
            <tr class="collapse" id="sectionDetails{{ $sec->id }}">
              <td colspan="7" class="p-0">
                <div class="p-3 bg-light">
                  <form method="POST" action="{{ route('registrars.sections.update', $sec->id) }}" id="sectionForm{{ $sec->id }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="selected_section_id" value="{{ $sec->id }}">
                    
                    <div class="row mb-3">
                      <div class="col-md-3">
                        <label class="form-label fw-semibold">Section Name</label>
                        <input type="text" class="form-control" value="{{ $sec->name }}" readonly>
                      </div>
                      <div class="col-md-3">
                        <label class="form-label fw-semibold">Grade Level</label>
                        <input type="text" class="form-control" value="{{ $sec->gradeLevel->name ?? 'N/A' }}" readonly>
                      </div>
                      <div class="col-md-3">
                        <label class="form-label fw-semibold">Adviser</label>
                        <select name="adviser_id" class="form-select">
                          <option value="">-- None --</option>
                          @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ $sec->adviser_id == $t->id ? 'selected' : '' }}>
                              {{ $t->profile->first_name }} {{ $t->profile->last_name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label class="form-label fw-semibold">Capacity (Max: 30)</label>
                        <input type="number" name="capacity" class="form-control" min="1" max="30"
                               value="{{ $sec->capacity ?? 30 }}" required>
                      </div>
                    </div>

                    {{-- Subject Assignments Table --}}
                    @php
                      $sectionGradeLevelId = $sec->gradelevel_id;
                      $subjectsForGrade = $subjects->where('grade_level_id', $sectionGradeLevelId);
                      $currentAssignments = $sectionSubjectAssignments[$sec->id] ?? [];
                    @endphp
                    
                    @if($subjectsForGrade->count() > 0)
                      <div class="mb-3">
                        <label class="form-label fw-bold">Subject Assignments</label>
                        <div class="table-responsive">
                          <table class="table table-sm table-bordered bg-white">
                            <thead class="table-light">
                              <tr>
                                <th style="width: 50%;">Subject</th>
                                <th style="width: 50%;">Subject Teacher</th>
                              </tr>
                            </thead>
                              <tbody>
                              @foreach($subjectsForGrade as $subject)
                                @php
                                  // Only show teachers who are already assigned to this subject
                                  // across any section in the active school year.
                                  $teachersForSubject = isset($subjectTeachers[$subject->id]) 
                                    ? $teachers->whereIn('id', $subjectTeachers[$subject->id])
                                    : collect();

                                  // Also include the currently assigned teacher (for this section)
                                  // even if they are not in the global subject teacher list.
                                  $currentTeacherId = $currentAssignments[$subject->id] ?? null;
                                  if ($currentTeacherId && !$teachersForSubject->contains('id', $currentTeacherId)) {
                                    $currentTeacher = $teachers->firstWhere('id', $currentTeacherId);
                                    if ($currentTeacher) {
                                      $teachersForSubject = $teachersForSubject->push($currentTeacher);
                                    }
                                  }
                                @endphp
                                <tr>
                                  <td class="align-middle">{{ $subject->name }}</td>
                                  <td>
                                    <select name="subject_teachers[{{ $subject->id }}]" class="form-select form-select-sm">
                                      <option value="">-- None --</option>
                                      @if($teachersForSubject->count() > 0)
                                        @foreach($teachersForSubject as $t)
                                          <option value="{{ $t->id }}" 
                                            {{ isset($currentAssignments[$subject->id]) && $currentAssignments[$subject->id] == $t->id ? 'selected' : '' }}>
                                            {{ $t->profile->first_name }} {{ $t->profile->last_name }}
                                          </option>
                                        @endforeach
                                      @else
                                        <option value="" disabled>No teachers assigned to this subject</option>
                                      @endif
                                    </select>
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    @else
                      <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>No subjects found for {{ $sec->gradeLevel->name ?? 'this grade level' }}.
                      </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2">
                      <button type="button" class="btn btn-secondary" 
                              data-bs-toggle="collapse" 
                              data-bs-target="#sectionDetails{{ $sec->id }}">Cancel</button>
                      <button type="submit" class="btn btn-warning text-white">Update</button>
                    </div>
                  </form>
                </div>
              </td>
            </tr>

            {{-- Archive Confirmation Modal --}}
            <div class="modal fade" id="archiveSectionModal{{ $sec->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="bi bi-archive me-2"></i>Archive Section</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body text-center">
                    <p>Are you sure you want to archive the section <strong class="text-secondary">"{{ $sec->name }}"</strong>?</p>
                  </div>
                  <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('registrars.sections.archive', $sec->id) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-secondary">Archive</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

          @empty
            <tr>
              <td colspan="7" class="text-center text-muted">No sections available.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
      {{ $sections->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

{{-- Add Section Modal --}}
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.sections.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Section</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Section Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Grade Level</label>
            <select name="gradelevel_id" class="form-select add-section-grade-select" required>
              <option value="">-- Select --</option>
              @foreach($gradeLevels as $gl)
                <option value="{{ $gl->id }}">{{ $gl->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label d-flex justify-content-between align-items-center">
              <span>Subjects for Selected Grade</span>
              <span class="text-muted small" id="add-section-subject-count"></span>
            </label>
            <div id="add-section-subject-list" class="border rounded p-3 bg-light-subtle small text-muted">
              Select a grade level to view its subjects.
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">School Year</label>
            <input type="text" class="form-control" value="{{ $currentSY->name ?? 'N/A' }}" readonly>
            <input type="hidden" name="school_year_id" value="{{ $currentSY->id ?? '' }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Adviser</label>
            <select name="adviser_id" class="form-select">
              <option value="">-- None --</option>
              @foreach(($availableAdvisers ?? $teachers) as $t)
                <option value="{{ $t->id }}">
                  {{ $t->profile?->first_name ?? 'N/A' }} {{ $t->profile?->last_name ?? '' }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Capacity (Max: 30)</label>
            <input type="number" name="capacity" class="form-control" min="1" max="30" value="30" required>
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

{{-- Reuse Sections Modal --}}
<div class="modal fade" id="reuseSectionsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.sections.reuse') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title"><i class="bi bi-arrow-counterclockwise me-2"></i>Reuse Sections</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @if(!$canReuseSections)
            <div class="alert alert-warning mb-0">
              No previous school years available to reuse.
            </div>
          @else
            <div class="mb-3">
              <label class="form-label fw-semibold">Source School Year</label>
              <select name="source_school_year_id" class="form-select" required>
                <option value="">-- Select School Year --</option>
                @foreach($reusableSchoolYears as $sy)
                  <option value="{{ $sy->id }}">{{ $sy->name }} ({{ ucfirst($sy->status) }})</option>
                @endforeach
              </select>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" value="1" name="include_subject_assignments" id="include_subject_assignments">
              <label class="form-check-label" for="include_subject_assignments">
                Include subject-teacher assignments
              </label>
            </div>
            <small class="text-muted d-block">
              Existing sections with the same name and grade level will be updated with the selected year's details.
            </small>
          @endif
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-info text-white" {{ $canReuseSections ? '' : 'disabled' }}>Reuse</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
