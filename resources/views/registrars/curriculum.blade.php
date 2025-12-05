@extends('layouts.registrar')

@php
use App\Models\Subject;
@endphp

@section('title', 'Curriculum')
@section('header')
    <i class="bi bi-journal-bookmark me-2"></i> Curriculum Management
@endsection

@section('content')
<div class="container-fluid my-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      @include('partials.alerts')

      {{-- School Year Filter --}}
      <form method="GET" action="{{ route('registrars.curriculum') }}" class="row g-3 align-items-end mb-4">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Select School Year</label>
          <select name="school_year_id" class="form-select" onchange="this.form.submit()">
            <option value="">-- Select School Year --</option>
            @foreach($schoolYears as $sy)
              <option value="{{ $sy->id }}" {{ $selectedSchoolYearId == $sy->id ? 'selected' : '' }}>
                {{ $sy->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-8 d-flex justify-content-end gap-2">
          <button type="button"
                  class="btn btn-outline-secondary"
                  data-bs-toggle="modal"
                  data-bs-target="#setCurriculumModal"
                  @if(!$selectedSchoolYearId) disabled title="Please select a school year first to set curriculum." @endif>
            <i class="bi bi-sliders me-1"></i> Set Curriculum
          </button>
          <button type="button"
                  class="btn btn-outline-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#reuseCurriculaModal"
                  @if(!$canReuseCurricula) disabled title="Cannot reuse curricula. No active school year or no previous school years available." @endif>
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reuse Previous
          </button>
          <button type="button"
                  class="btn btn-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#addCurriculumModal"
                  @if(!$selectedSchoolYearId) disabled title="Please select a school year first to add a curriculum definition." @endif>
            <i class="bi bi-plus-circle me-1"></i> Add Curriculum
          </button>
        </div>
      </form>

      @if($selectedSchoolYearId)
        {{-- Curriculum Table --}}
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-primary">
              <tr>
                <th>#</th>
                <th>Curriculum Name</th>
                <th>School Year</th>
                <th>Grade Levels</th>
                <th>Total Subjects</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($curricula as $index => $curriculum)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>
                    <strong>{{ $curriculum->name }}</strong>
                  </td>
                  <td>{{ $curriculum->schoolYear->name ?? 'N/A' }}</td>
                  <td>
                    @php
                      $gradeLevels = $curriculum->subjects->pluck('gradeLevel.name')->unique()->filter()->sort();
                    @endphp
                    @if($gradeLevels->isNotEmpty())
                      <span class="badge bg-info">{{ $gradeLevels->implode(', ') }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge bg-secondary">{{ $curriculum->subjects->count() }} subjects</span>
                  </td>
                  <td>
                    <div class="d-flex gap-2">
                      <a href="{{ route('registrars.curriculum.show', $curriculum->id) }}" 
                         class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i> View
                      </a>
                      <button type="button"
                              class="btn btn-sm btn-warning"
                              data-bs-toggle="modal"
                              data-bs-target="#editCurriculumModal{{ $curriculum->id }}">
                        <i class="bi bi-pencil"></i> Edit
                      </button>
                      <form action="{{ route('registrars.curriculum.destroy', $curriculum->id) }}" 
                            method="POST" 
                            style="display:inline;"
                            onsubmit="return confirm('Are you sure you want to delete this curriculum?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                          <i class="bi bi-trash"></i> Delete
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>

                {{-- Edit Curriculum Modal --}}
                <div class="modal fade" id="editCurriculumModal{{ $curriculum->id }}" tabindex="-1">
                  <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('registrars.curriculum.update', $curriculum->id) }}">
                      @csrf
                      @method('PUT')
                      <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                          <h5 class="modal-title"><i class="bi bi-pencil me-2"></i> Edit Curriculum</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label">Curriculum Name</label>
                            <input type="text" name="name" value="{{ $curriculum->name }}" class="form-control" required>
                          </div>
                          
                          <div class="mb-3">
                            <label class="form-label">Select Subjects (per Grade Level)</label>
                            <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                              @php
                                $curriculumSubjects = Subject::where('school_year_id', $curriculum->school_year_id)
                                  ->where('is_archived', false)
                                  ->with('gradeLevel')
                                  ->get()
                                  ->groupBy('grade_level_id');
                                $selectedSubjectIds = $curriculum->subjects->pluck('id')->toArray();
                              @endphp
                              
                              @foreach($curriculumSubjects as $gradeLevelId => $subjects)
                                @php
                                  $gradeLevel = $subjects->first()->gradeLevel;
                                @endphp
                                <div class="mb-3 grade-group-edit" data-grade-id="{{ $gradeLevelId }}" data-curriculum-id="{{ $curriculum->id }}">
                                  <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="text-primary mb-0">
                                      <i class="bi bi-bookmark"></i> {{ $gradeLevel->name ?? 'Unassigned' }}
                                    </h6>
                                    <div class="form-check">
                                      <input class="form-check-input select-all-grade-edit" 
                                             type="checkbox" 
                                             id="edit_select_all_{{ $curriculum->id }}_{{ $gradeLevelId }}">
                                      <label class="form-check-label small" for="edit_select_all_{{ $curriculum->id }}_{{ $gradeLevelId }}">
                                        Select All
                                      </label>
                                    </div>
                                  </div>
                                  <div class="row g-2">
                                    @foreach($subjects as $subject)
                                      <div class="col-md-6">
                                        <div class="form-check">
                                          <input class="form-check-input edit-grade-{{ $gradeLevelId }}-subject" 
                                                 type="checkbox" 
                                                 name="subjects[]" 
                                                 value="{{ $subject->id }}"
                                                 id="edit_subject_{{ $curriculum->id }}_{{ $subject->id }}"
                                                 {{ in_array($subject->id, $selectedSubjectIds) ? 'checked' : '' }}>
                                          <label class="form-check-label" for="edit_subject_{{ $curriculum->id }}_{{ $subject->id }}">
                                            {{ $subject->name }}
                                          </label>
                                        </div>
                                      </div>
                                    @endforeach
                                  </div>
                                </div>
                              @endforeach
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-warning">Update Curriculum</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-info-circle"></i> No curricula found for this school year. Click "Add Curriculum" to create one.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-info text-center">
          <i class="bi bi-info-circle me-2"></i>
          Please select a school year to view or manage curricula.
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Reuse Curricula Modal --}}
<div class="modal fade" id="reuseCurriculaModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.curriculum.reuse') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title"><i class="bi bi-arrow-counterclockwise me-2"></i> Reuse Curricula</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @if(!$canReuseCurricula)
            <div class="alert alert-warning mb-0">
              No previous school years available for reuse.
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
            <small class="text-muted d-block">
              Curricula from the selected school year will be copied to the active school year ({{ $currentSY->name ?? 'N/A' }}). 
              Subjects will be matched by name and grade level. Existing curricula with the same name will be updated.
            </small>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-info text-white" @if(!$canReuseCurricula) disabled @endif>Reuse Curricula</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Add Curriculum Modal --}}
<div class="modal fade" id="addCurriculumModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('registrars.curriculum.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add Curriculum</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Curriculum Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="e.g., K-12 Basic Education Curriculum">
          </div>
          
          {{-- Hidden school year, bound to selected filter but not chosen here (curriculum is conceptually global) --}}
          <input type="hidden" name="school_year_id" id="addCurriculumSchoolYear" value="{{ $selectedSchoolYearId }}">

          <div class="mb-3" id="existingCurriculaContainer" style="display: none;">
            <label class="form-label">Existing Curricula <small class="text-muted">(Optional - Select to edit or leave empty to create new)</small></label>
            <select name="existing_curriculum_id" class="form-select" id="existingCurriculaSelect">
              <option value="">-- Create New Curriculum --</option>
            </select>
            <small class="text-muted">Select an existing curriculum to edit, or leave as "Create New" to add a new one.</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Select Subjects (per Grade Level)</label>
            <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;" id="subjectsContainer">
              <p class="text-muted small">Please select a school year first to load subjects.</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Curriculum</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Set Curriculum (Apply Existing) Modal --}}
<div class="modal fade" id="setCurriculumModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.curriculum.templates.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-secondary text-white">
          <h5 class="modal-title"><i class="bi bi-sliders me-2"></i> Set Curriculum</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="school_year_id" value="{{ $selectedSchoolYearId }}">

          <div class="mb-3">
            <label class="form-label">Select Curriculum</label>
            <select name="curriculum_id" class="form-select" id="setCurriculumSelect" required>
              <option value="">-- Select Curriculum --</option>
              @foreach($allCurricula as $curriculum)
                <option value="{{ $curriculum->id }}">
                  {{ $curriculum->name }} ({{ $curriculum->subjects->count() }} subjects)
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">Subjects in selected curriculum</label>
            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
              @if($allCurricula->isEmpty())
                <p class="text-muted small mb-0">
                  No curricula available. Please add a curriculum first.
                </p>
              @else
                @foreach($allCurricula as $curriculum)
                  @php
                    $subjectsByGrade = $curriculum->subjects
                      ->load('gradeLevel')
                      ->groupBy(fn($s) => $s->gradeLevel->name ?? 'Unassigned');
                  @endphp
                  <div class="curriculum-subjects d-none" id="curriculumSubjects{{ $curriculum->id }}">
                    @forelse($subjectsByGrade as $gradeName => $subjects)
                      <div class="mb-2">
                        <h6 class="text-primary mb-1">
                          <i class="bi bi-bookmark"></i> {{ $gradeName }}
                        </h6>
                        <ul class="small mb-1">
                          @foreach($subjects as $subject)
                            <li>{{ $subject->name }}</li>
                          @endforeach
                        </ul>
                      </div>
                    @empty
                      <p class="text-muted small mb-0">No subjects defined for this curriculum.</p>
                    @endforelse
                  </div>
                @endforeach
              @endif
            </div>
            <p class="text-muted small mt-1 mb-0">
              The selected curriculum's subjects will be inherited for this school year.
            </p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-secondary" @if(!$selectedSchoolYearId || $allCurricula->isEmpty()) disabled @endif>Apply Curriculum</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const schoolYearSelect = document.querySelector('#addCurriculumSchoolYear');
  const subjectsContainer = document.getElementById('subjectsContainer');
  const existingCurriculaContainer = document.getElementById('existingCurriculaContainer');
  const existingCurriculaSelect = document.getElementById('existingCurriculaSelect');
  
  if (schoolYearSelect) {
    schoolYearSelect.addEventListener('change', function() {
      const schoolYearId = this.value;
      
      // Load existing curricula for the selected school year
      if (schoolYearId) {
        fetch(`{{ url('/registrar/curriculum/get-curricula') }}?school_year_id=${schoolYearId}`)
          .then(response => response.json())
          .then(data => {
            if (data.curricula && data.curricula.length > 0) {
              existingCurriculaSelect.innerHTML = '<option value="">-- Create New Curriculum --</option>';
              data.curricula.forEach(curriculum => {
                const option = document.createElement('option');
                option.value = curriculum.id;
                option.textContent = curriculum.name + ' (' + curriculum.subjects_count + ' subjects)';
                existingCurriculaSelect.appendChild(option);
              });
              existingCurriculaContainer.style.display = 'block';
            } else {
              existingCurriculaContainer.style.display = 'none';
              existingCurriculaSelect.innerHTML = '<option value="">-- Create New Curriculum --</option>';
            }
          })
          .catch(error => {
            console.error('Error fetching curricula:', error);
            existingCurriculaContainer.style.display = 'none';
          });
      } else {
        existingCurriculaContainer.style.display = 'none';
        existingCurriculaSelect.innerHTML = '<option value="">-- Create New Curriculum --</option>';
      }
      
      // Load subjects
      if (schoolYearId) {
        // Fetch subjects for the selected school year
        fetch(`{{ url('/registrar/curriculum/get-subjects') }}?school_year_id=${schoolYearId}`)
          .then(response => response.json())
          .then(data => {
            if (data.subjects && data.subjects.length > 0) {
              let html = '';
              const subjectsByGrade = {};
              
              // Group subjects by grade level
              data.subjects.forEach(subject => {
                const gradeLevelId = subject.grade_level_id || 'unassigned';
                const gradeLevelName = subject.grade_level_name || 'Unassigned';
                
                if (!subjectsByGrade[gradeLevelId]) {
                  subjectsByGrade[gradeLevelId] = {
                    name: gradeLevelName,
                    subjects: []
                  };
                }
                subjectsByGrade[gradeLevelId].subjects.push(subject);
              });
              
              // Render grouped subjects
              Object.keys(subjectsByGrade).forEach(gradeLevelId => {
                const group = subjectsByGrade[gradeLevelId];
                html += `<div class="mb-3 grade-group" data-grade-id="${gradeLevelId}">`;
                html += `<div class="d-flex justify-content-between align-items-center mb-2">`;
                html += `<h6 class="text-primary mb-0"><i class="bi bi-bookmark"></i> ${group.name}</h6>`;
                html += `<div class="form-check">`;
                html += `<input class="form-check-input select-all-grade" type="checkbox" id="select_all_${gradeLevelId}">`;
                html += `<label class="form-check-label small" for="select_all_${gradeLevelId}">Select All</label>`;
                html += `</div>`;
                html += `</div>`;
                html += `<div class="row g-2">`;
                group.subjects.forEach(subject => {
                  html += `<div class="col-md-6">`;
                  html += `<div class="form-check">`;
                  html += `<input class="form-check-input grade-${gradeLevelId}-subject" type="checkbox" name="subjects[]" value="${subject.id}" id="subject_${subject.id}">`;
                  html += `<label class="form-check-label" for="subject_${subject.id}">${subject.name}</label>`;
                  html += `</div></div>`;
                });
                html += `</div></div>`;
              });
              
              subjectsContainer.innerHTML = html;
              
              // If existing curriculum is selected, load its subjects
              existingCurriculaSelect.addEventListener('change', function() {
                const curriculumId = this.value;
                if (curriculumId) {
                  // Redirect to edit page or load curriculum data
                  window.location.href = `{{ url('/registrar/curriculum') }}/${curriculumId}`;
                }
              });
              
              // Add select all functionality
              document.querySelectorAll('.select-all-grade').forEach(selectAllCheckbox => {
                selectAllCheckbox.addEventListener('change', function() {
                  const gradeId = this.id.replace('select_all_', '');
                  const gradeSubjects = document.querySelectorAll(`.grade-${gradeId}-subject`);
                  gradeSubjects.forEach(subjectCheckbox => {
                    subjectCheckbox.checked = this.checked;
                  });
                });
              });
              
              // Update select all when individual checkboxes change
              document.querySelectorAll('[class*="grade-"][class*="-subject"]').forEach(subjectCheckbox => {
                subjectCheckbox.addEventListener('change', function() {
                  const classList = Array.from(this.classList);
                  const gradeClass = classList.find(cls => cls.startsWith('grade-') && cls.includes('-subject'));
                  if (gradeClass) {
                    const gradeId = gradeClass.match(/grade-(.+)-subject/)[1];
                    const gradeSubjects = document.querySelectorAll(`.grade-${gradeId}-subject`);
                    const selectAllCheckbox = document.getElementById(`select_all_${gradeId}`);
                    if (selectAllCheckbox) {
                      const allChecked = Array.from(gradeSubjects).every(cb => cb.checked);
                      const someChecked = Array.from(gradeSubjects).some(cb => cb.checked);
                      selectAllCheckbox.checked = allChecked;
                      selectAllCheckbox.indeterminate = someChecked && !allChecked;
                    }
                  }
                });
              });
            } else {
              subjectsContainer.innerHTML = '<p class="text-muted small">No subjects found for this school year.</p>';
            }
          })
          .catch(error => {
            console.error('Error fetching subjects:', error);
            subjectsContainer.innerHTML = '<p class="text-danger small">Error loading subjects. Please try again.</p>';
          });
      } else {
        subjectsContainer.innerHTML = '<p class="text-muted small">Please select a school year first to load subjects.</p>';
      }
    });
    
    // Trigger change if school year is already selected
    if (schoolYearSelect.value) {
      schoolYearSelect.dispatchEvent(new Event('change'));
    }
  }
});

// Show subjects for selected curriculum in Set Curriculum modal
document.addEventListener('DOMContentLoaded', function() {
  const setCurriculumSelect = document.getElementById('setCurriculumSelect');
  const subjectBlocks = document.querySelectorAll('.curriculum-subjects');

  if (setCurriculumSelect && subjectBlocks.length) {
    const updateSubjectsView = () => {
      subjectBlocks.forEach(b => b.classList.add('d-none'));
      const id = setCurriculumSelect.value;
      if (id) {
        const block = document.getElementById('curriculumSubjects' + id);
        if (block) {
          block.classList.remove('d-none');
        }
      }
    };

    setCurriculumSelect.addEventListener('change', updateSubjectsView);
    // Initialize view when modal opens (in case a value is pre-selected)
    updateSubjectsView();
  }
});

// Select All functionality for Edit Modal
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.select-all-grade-edit').forEach(selectAllCheckbox => {
    selectAllCheckbox.addEventListener('change', function() {
      const idParts = this.id.split('_');
      const gradeId = idParts[idParts.length - 1];
      const curriculumId = idParts[idParts.length - 2];
      const gradeSubjects = document.querySelectorAll(`.edit-grade-${gradeId}-subject`);
      gradeSubjects.forEach(subjectCheckbox => {
        subjectCheckbox.checked = this.checked;
      });
    });
  });
  
  // Update select all when individual checkboxes change in edit modal
  document.querySelectorAll('[class*="edit-grade-"][class*="-subject"]').forEach(subjectCheckbox => {
    subjectCheckbox.addEventListener('change', function() {
      const classList = Array.from(this.classList);
      const gradeClass = classList.find(cls => cls.startsWith('edit-grade-') && cls.includes('-subject'));
      if (gradeClass) {
        const gradeId = gradeClass.match(/edit-grade-(.+)-subject/)[1];
        const gradeGroup = this.closest('.grade-group-edit');
        const curriculumId = gradeGroup.dataset.curriculumId;
        const gradeSubjects = document.querySelectorAll(`.edit-grade-${gradeId}-subject`);
        const selectAllCheckbox = document.getElementById(`edit_select_all_${curriculumId}_${gradeId}`);
        if (selectAllCheckbox) {
          const allChecked = Array.from(gradeSubjects).every(cb => cb.checked);
          const someChecked = Array.from(gradeSubjects).some(cb => cb.checked);
          selectAllCheckbox.checked = allChecked;
          selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }
      }
    });
  });
  
  // Initialize select all checkboxes state in edit modal
  document.querySelectorAll('.grade-group-edit').forEach(gradeGroup => {
    const gradeId = gradeGroup.dataset.gradeId;
    const curriculumId = gradeGroup.dataset.curriculumId;
    const gradeSubjects = document.querySelectorAll(`.edit-grade-${gradeId}-subject`);
    const selectAllCheckbox = document.getElementById(`edit_select_all_${curriculumId}_${gradeId}`);
    if (selectAllCheckbox && gradeSubjects.length > 0) {
      const allChecked = Array.from(gradeSubjects).every(cb => cb.checked);
      const someChecked = Array.from(gradeSubjects).some(cb => cb.checked);
      selectAllCheckbox.checked = allChecked;
      selectAllCheckbox.indeterminate = someChecked && !allChecked;
    }
  });
});
</script>
@endsection

