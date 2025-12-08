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

      {{-- Active School Year Display --}}
      @if($currentSY)
        <div class="alert alert-info mb-4">
          <i class="bi bi-info-circle me-2"></i>
          <strong>Active School Year:</strong> {{ $currentSY->name }}
        </div>
      @else
        <div class="alert alert-warning mb-4">
          <i class="bi bi-exclamation-triangle me-2"></i>
          <strong>No Active School Year:</strong> Please activate a school year first to manage curricula.
        </div>
      @endif

      {{-- Action Buttons --}}
      <div class="d-flex justify-content-end gap-2 mb-4">
        <button type="button"
                class="btn btn-outline-secondary"
                data-bs-toggle="modal"
                data-bs-target="#setCurriculumModal"
                @if(!$currentSY) disabled title="Please activate a school year first to set curriculum." @endif>
          <i class="bi bi-sliders me-1"></i> Set Curriculum
        </button>
        <button type="button"
                class="btn btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#addCurriculumNameModal"
                @if(!$currentSY) disabled title="Please activate a school year first to add a curriculum name." @endif>
          <i class="bi bi-tag me-1"></i> Add Curriculum Name
        </button>
        <button type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#addCurriculumModal"
                @if(!$currentSY) disabled title="Please activate a school year first to add a curriculum definition." @endif>
          <i class="bi bi-plus-circle me-1"></i> Add Curriculum
        </button>
      </div>

      @if($currentSY)
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

{{-- Add Curriculum Name Modal --}}
<div class="modal fade" id="addCurriculumNameModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.curriculum.storeName') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-tag me-2"></i> Add Curriculum Name</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          @if(!$currentSY)
            <div class="alert alert-warning mb-0">
              Please activate a school year first to add a curriculum name.
            </div>
          @else
            <div class="mb-3">
              <label class="form-label fw-semibold">Curriculum Name</label>
              <input type="text" 
                     name="name" 
                     class="form-control" 
                     required 
                     placeholder="e.g., Matatag Curriculum"
                     value="{{ old('name') }}">
              <small class="text-muted d-block mt-2">
                The curriculum name will be stored as a fixed template in the database and can be reused across different school years. 
                A curriculum instance will be created for the active school year ({{ $currentSY->name }}). 
                You can add subjects to it later by editing the curriculum.
              </small>
            </div>
            <input type="hidden" name="school_year_id" value="{{ $currentSY->id }}">
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" @if(!$currentSY) disabled @endif>Add Curriculum</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Add Curriculum Modal --}}
<div class="modal fade" id="addCurriculumModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('registrars.curriculum.store') }}" id="addCurriculumForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add Curriculum</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Select Curriculum Template</label>
            <select name="curriculum_template_id" id="curriculumTemplateSelect" class="form-select" required>
              <option value="">-- Select Curriculum Template --</option>
              @foreach($curriculumTemplates as $template)
                <option value="{{ $template->id }}" data-name="{{ $template->name }}" 
                  @if(isset($currentCurriculumTemplateId) && $currentCurriculumTemplateId == $template->id) selected @endif>
                  {{ $template->name }}
                </option>
              @endforeach
            </select>
            <small class="text-muted d-block mt-1">Select a curriculum template to use for this school year.</small>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Select Grade Levels</label>
            <div class="border rounded p-3">
              @php
                $allGradeLevels = \App\Models\GradeLevel::orderBy('id')->get();
              @endphp
              @if($allGradeLevels->count() > 0)
                <div class="row g-2">
                  @foreach($allGradeLevels as $gradeLevel)
                    <div class="col-md-4 col-sm-6">
                      <div class="form-check">
                        <input class="form-check-input grade-level-checkbox" 
                               type="checkbox" 
                               name="grade_levels[]" 
                               value="{{ $gradeLevel->id }}" 
                               id="grade_level_{{ $gradeLevel->id }}"
                               data-grade-name="{{ $gradeLevel->name }}">
                        <label class="form-check-label" for="grade_level_{{ $gradeLevel->id }}">
                          {{ $gradeLevel->name }}
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <p class="text-muted small text-center py-2">No grade levels available. Please add grade levels first.</p>
              @endif
            </div>
            <small class="text-muted d-block mt-1">Select grade levels to show their subjects below.</small>
          </div>
          
          {{-- Hidden fields --}}
          <input type="hidden" name="name" id="curriculumNameInput" value="">
          <input type="hidden" name="school_year_id" id="addCurriculumSchoolYear" value="{{ $currentSY->id ?? '' }}">

          <div class="mb-3">
            <label class="form-label">Select Subjects (per Grade Level)</label>
            <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;" id="subjectsContainer">
              <p class="text-muted small text-center py-3">Select grade levels above to load subjects.</p>
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
          <input type="hidden" name="school_year_id" value="{{ $currentSY->id ?? '' }}">

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
          <button type="submit" class="btn btn-secondary" @if(!$currentSY || $allCurricula->isEmpty()) disabled @endif>Apply Curriculum</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Add Subject Modal --}}
<div class="modal fade" id="addSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="addSubjectForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add Subject</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="grade_level_id" id="addSubjectGradeLevelId">
          <input type="hidden" name="school_year_id" id="addSubjectSchoolYearId" value="{{ $currentSY->id ?? '' }}">
          <div class="mb-3">
            <label class="form-label">Grade Level</label>
            <input type="text" class="form-control" id="addSubjectGradeLevelName" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Subject Name</label>
            <input type="text" name="name" id="addSubjectName" class="form-control" required placeholder="Enter subject name">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Subject</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const schoolYearSelect = document.querySelector('#addCurriculumSchoolYear');
  const subjectsContainer = document.getElementById('subjectsContainer');
  const curriculumTemplateSelect = document.getElementById('curriculumTemplateSelect');
  const curriculumNameInput = document.getElementById('curriculumNameInput');
  const addCurriculumModal = document.getElementById('addCurriculumModal');
  const gradeLevelCheckboxes = document.querySelectorAll('.grade-level-checkbox');
  
  // Store loaded subjects data
  let allSubjectsData = {};
  // Store temporarily added subjects (not yet saved to database)
  let temporarySubjects = {};
  
  // Handle curriculum template selection
  if (curriculumTemplateSelect && curriculumNameInput) {
    curriculumTemplateSelect.addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      if (selectedOption.value) {
        const curriculumName = selectedOption.getAttribute('data-name');
        curriculumNameInput.value = curriculumName || '';
      } else {
        curriculumNameInput.value = '';
      }
    });
  }
  
  // Load all subjects for the active school year
  function loadAllSubjects() {
    const schoolYearId = schoolYearSelect ? schoolYearSelect.value : null;
    
    if (!schoolYearId) {
      return;
    }
    
    fetch(`{{ url('/registrar/curriculum/get-subjects') }}?school_year_id=${schoolYearId}`)
      .then(response => response.json())
      .then(data => {
        if (data.subjects && data.subjects.length > 0) {
          // Group subjects by grade level
          data.subjects.forEach(subject => {
            const gradeLevelId = subject.grade_level_id || 'unassigned';
            if (!allSubjectsData[gradeLevelId]) {
              allSubjectsData[gradeLevelId] = {
                name: subject.grade_level_name || 'Unassigned',
                subjects: []
              };
            }
            allSubjectsData[gradeLevelId].subjects.push(subject);
          });
          
          // Update display based on selected grade levels
          updateSubjectsDisplay();
        } else {
          // Even if no subjects, update display to show selected grade levels
          updateSubjectsDisplay();
        }
      })
      .catch(error => {
        console.error('Error fetching subjects:', error);
        // Even on error, update display to show selected grade levels
        updateSubjectsDisplay();
      });
  }
  
  // Update subjects display based on selected grade levels
  function updateSubjectsDisplay() {
    // Get current checkboxes - try modal first, then document
    const modal = document.getElementById('addCurriculumModal');
    let currentCheckboxes = modal ? modal.querySelectorAll('.grade-level-checkbox') : [];
    
    if (currentCheckboxes.length === 0) {
      currentCheckboxes = document.querySelectorAll('.grade-level-checkbox');
    }
    
    const selectedGradeLevels = Array.from(currentCheckboxes)
      .filter(cb => cb.checked)
      .map(cb => ({
        id: cb.value,
        name: cb.getAttribute('data-grade-name') || 'Unassigned'
      }));
    
    if (selectedGradeLevels.length === 0) {
      subjectsContainer.innerHTML = '<p class="text-muted small text-center py-3">Select grade levels above to load subjects.</p>';
      return;
    }
    
    let html = '';
    
    selectedGradeLevels.forEach(gradeLevel => {
      const gradeLevelId = gradeLevel.id;
      const gradeLevelName = gradeLevel.name;
      
      html += `<div class="mb-3 grade-group" data-grade-id="${gradeLevelId}">`;
      html += `<div class="d-flex justify-content-between align-items-center mb-2">`;
      html += `<h6 class="text-primary mb-0"><i class="bi bi-bookmark"></i> ${gradeLevelName}</h6>`;
      html += `<div class="d-flex gap-2 align-items-center">`;
      
      // Check if this grade level has subjects
      if (allSubjectsData[gradeLevelId] && allSubjectsData[gradeLevelId].subjects.length > 0) {
        const group = allSubjectsData[gradeLevelId];
        html += `<div class="form-check me-2">`;
        html += `<input class="form-check-input select-all-grade" type="checkbox" id="select_all_${gradeLevelId}">`;
        html += `<label class="form-check-label small" for="select_all_${gradeLevelId}">Select All</label>`;
        html += `</div>`;
      }
      html += `<button type="button" class="btn btn-sm btn-outline-primary add-subject-btn" data-grade-id="${gradeLevelId}" data-grade-name="${gradeLevelName}">`;
      html += `<i class="bi bi-plus-circle me-1"></i> Add Subject`;
      html += `</button>`;
      html += `</div>`;
      html += `</div>`;
      
      // Subjects list
      const existingSubjects = allSubjectsData[gradeLevelId] && allSubjectsData[gradeLevelId].subjects.length > 0 
        ? allSubjectsData[gradeLevelId].subjects 
        : [];
      const tempSubjects = temporarySubjects[gradeLevelId] || [];
      
      if (existingSubjects.length > 0 || tempSubjects.length > 0) {
        html += `<div class="row g-2">`;
        
        // Display existing subjects
        existingSubjects.forEach(subject => {
          html += `<div class="col-md-6">`;
          html += `<div class="form-check">`;
          html += `<input class="form-check-input grade-${gradeLevelId}-subject" type="checkbox" name="subjects[]" value="${subject.id}" id="subject_${subject.id}">`;
          html += `<label class="form-check-label" for="subject_${subject.id}">${subject.name}</label>`;
          html += `</div></div>`;
        });
        
        // Display temporary subjects (not yet saved)
        tempSubjects.forEach((subject, index) => {
          html += `<div class="col-md-6">`;
          html += `<div class="form-check">`;
          html += `<input class="form-check-input grade-${gradeLevelId}-subject" type="checkbox" name="temp_subjects[${gradeLevelId}][]" value="${subject.name}" id="temp_subject_${gradeLevelId}_${index}" checked>`;
          html += `<label class="form-check-label" for="temp_subject_${gradeLevelId}_${index}">${subject.name} <span class="badge bg-warning text-dark ms-1">New</span></label>`;
          html += `</div></div>`;
        });
        
        html += `</div>`;
      } else {
        // No subjects for this grade level
        html += `<p class="text-muted small text-center py-2">No subjects available for this grade level in the current school year.</p>`;
      }
      
      html += `</div>`;
    });
    
    subjectsContainer.innerHTML = html;
    
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
    
    // Add event listeners for "Add Subject" buttons (using event delegation)
    // This ensures buttons work even after dynamic HTML updates
    subjectsContainer.querySelectorAll('.add-subject-btn').forEach(btn => {
      // Remove any existing listeners
      const newBtn = btn.cloneNode(true);
      btn.parentNode.replaceChild(newBtn, btn);
      
      newBtn.addEventListener('click', function() {
        const gradeLevelId = this.getAttribute('data-grade-id');
        const gradeLevelName = this.getAttribute('data-grade-name');
        
        // Set modal values
        document.getElementById('addSubjectGradeLevelId').value = gradeLevelId;
        document.getElementById('addSubjectGradeLevelName').value = gradeLevelName;
        document.getElementById('addSubjectName').value = '';
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('addSubjectModal'));
        modal.show();
      });
    });
  }
  
  // Use event delegation for "Add Subject" buttons at the modal level
  if (addCurriculumModal) {
    addCurriculumModal.addEventListener('click', function(e) {
      if (e.target && e.target.closest('.add-subject-btn')) {
        const btn = e.target.closest('.add-subject-btn');
        const gradeLevelId = btn.getAttribute('data-grade-id');
        const gradeLevelName = btn.getAttribute('data-grade-name');
        
        // Set modal values
        document.getElementById('addSubjectGradeLevelId').value = gradeLevelId;
        document.getElementById('addSubjectGradeLevelName').value = gradeLevelName;
        document.getElementById('addSubjectName').value = '';
        
        // Show modal
        const addSubjectModal = new bootstrap.Modal(document.getElementById('addSubjectModal'));
        addSubjectModal.show();
      }
    });
  }
  
  // Handle Add Subject form submission (temporary, not saved to database yet)
  const addSubjectForm = document.getElementById('addSubjectForm');
  if (addSubjectForm) {
    addSubjectForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const gradeLevelId = document.getElementById('addSubjectGradeLevelId').value;
      const gradeLevelName = document.getElementById('addSubjectGradeLevelName').value;
      const subjectName = document.getElementById('addSubjectName').value.trim();
      
      if (!subjectName) {
        alert('Please enter a subject name');
        return;
      }
      
      // Check if subject already exists in temporary subjects for this grade level
      if (temporarySubjects[gradeLevelId]) {
        const exists = temporarySubjects[gradeLevelId].some(s => s.name.toLowerCase() === subjectName.toLowerCase());
        if (exists) {
          alert('This subject has already been added for this grade level.');
          return;
        }
      }
      
      // Check if subject already exists in loaded subjects
      if (allSubjectsData[gradeLevelId]) {
        const exists = allSubjectsData[gradeLevelId].subjects.some(s => s.name.toLowerCase() === subjectName.toLowerCase());
        if (exists) {
          alert('This subject already exists for this grade level.');
          return;
        }
      }
      
      // Add to temporary subjects
      if (!temporarySubjects[gradeLevelId]) {
        temporarySubjects[gradeLevelId] = [];
      }
      
      temporarySubjects[gradeLevelId].push({
        name: subjectName,
        grade_level_id: gradeLevelId,
        grade_level_name: gradeLevelName
      });
      
      // Update display
      updateSubjectsDisplay();
      
      // Close modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('addSubjectModal'));
      modal.hide();
      
      // Clear form
      document.getElementById('addSubjectName').value = '';
    });
  }
  
  // Use event delegation for grade level checkboxes (works even if modal is opened dynamically)
  if (addCurriculumModal) {
    // Listen for changes on grade level checkboxes using event delegation
    addCurriculumModal.addEventListener('change', function(e) {
      if (e.target && e.target.classList.contains('grade-level-checkbox')) {
        // Always update display immediately when checkbox changes
        // Use setTimeout to ensure the checkbox state is updated
        setTimeout(function() {
          if (Object.keys(allSubjectsData).length === 0) {
            // Load subjects if not loaded yet, but also update display immediately
            updateSubjectsDisplay(); // Show selected grade levels even without subjects
            loadAllSubjects();
          } else {
            // Update display immediately
            updateSubjectsDisplay();
          }
        }, 10);
      }
    });
    
    // Load subjects when modal opens if school year is available
    addCurriculumModal.addEventListener('shown.bs.modal', function() {
      // Auto-select current curriculum template if available
      @if(isset($currentCurriculumTemplateId) && $currentCurriculumTemplateId)
        if (curriculumTemplateSelect) {
          curriculumTemplateSelect.value = '{{ $currentCurriculumTemplateId }}';
          // Trigger change event to populate curriculum name
          curriculumTemplateSelect.dispatchEvent(new Event('change'));
        }
      @endif
      
      // First, update display to show any already checked grade levels
      updateSubjectsDisplay();
      
      // Then load subjects if school year is available and data not loaded
      if (schoolYearSelect && schoolYearSelect.value && Object.keys(allSubjectsData).length === 0) {
        loadAllSubjects();
      } else if (Object.keys(allSubjectsData).length > 0) {
        // If subjects are already loaded, just update the display
        updateSubjectsDisplay();
      }
    });
  }
  
  // Reset form when modal is hidden
  if (addCurriculumModal) {
    addCurriculumModal.addEventListener('hidden.bs.modal', function() {
      if (curriculumTemplateSelect) {
        curriculumTemplateSelect.value = '';
      }
      if (curriculumNameInput) {
        curriculumNameInput.value = '';
      }
      // Uncheck all grade level checkboxes
      const currentCheckboxes = document.querySelectorAll('.grade-level-checkbox');
      currentCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
      });
      // Reset subjects container to empty state
      if (subjectsContainer) {
        subjectsContainer.innerHTML = '<p class="text-muted small text-center py-3">Select grade levels above to load subjects.</p>';
      }
      // Clear subjects data
      allSubjectsData = {};
      temporarySubjects = {};
    });
  }
  
  // Handle curriculum form submission - validate that at least one subject is selected
  const addCurriculumForm = document.getElementById('addCurriculumForm');
  if (addCurriculumForm) {
    addCurriculumForm.addEventListener('submit', function(e) {
      // Check if at least one subject is selected (existing or temporary)
      const existingSubjectCheckboxes = this.querySelectorAll('input[name="subjects[]"]:checked');
      const tempSubjectCheckboxes = this.querySelectorAll('input[name^="temp_subjects"]:checked');
      
      if (existingSubjectCheckboxes.length === 0 && tempSubjectCheckboxes.length === 0) {
        e.preventDefault();
        alert('Please select at least one subject or add a new subject.');
        return false;
      }
    });
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

