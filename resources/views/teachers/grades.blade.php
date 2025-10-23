@extends('layouts.teacher')

@section('title', 'Grades')
@section('header')
  <i class="bi bi-pencil-square me-2"></i> Grades
@endsection

@section('content')
<div class="container-fluid my-4">

  {{-- Success & Error Alerts --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
      <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @error('assignment_id')
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i> {{ $message }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @enderror

  {{-- Subject Selection --}}
  <div class="card shadow-sm border-0 mb-4 rounded-3">
    <div class="card-body">
      <form method="GET" action="{{ route('teachers.grades') }}">
        <div class="row g-2 align-items-center">
          <div class="col-md-9">
            <select name="assignment_id" class="form-select form-select-lg" required>
              <option value="">-- Choose Subject and Section --</option>
              @foreach($assignments as $assignment)
                <option value="{{ $assignment->assignment_id }}"
                  {{ $selectedAssignment && $selectedAssignment->assignment_id == $assignment->assignment_id ? 'selected' : '' }}>
                  {{ $assignment->subject_name }} — 
                  {{ $assignment->gradelevel_name ?? 'N/A' }} 
                  ({{ $assignment->section_name ?? 'No Section' }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-outline-primary w-100 btn-lg">
              <i class="bi bi-arrow-repeat me-1"></i> Load Students
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Grades Table --}}
  @if(isset($selectedAssignment))
    <div class="card shadow-sm border-0 rounded-3">
      <div class="card-body">
        @if($selectedAssignment->grade_status === 'approved')
        <div class="alert alert-success d-flex align-items-center">
            <i class="bi bi-lock-fill me-2"></i>
            Grades for this subject have been approved and are now locked.
        </div>
        @endif
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
          <h5 class="fw-bold mb-2">
            <i class="bi bi-journal-text text-primary me-2"></i>
            Encoding Grades for 
            <span class="text-primary">{{ $subject->name ?? 'Subject' }}</span> — 
            {{ $section->gradeLevel->name ?? 'N/A' }} {{ $section->name ?? '' }}
          </h5>
          <button type="button" class="btn btn-light border btn-sm" onclick="window.scrollTo(0,0)">
            <i class="bi bi-arrow-up-circle"></i> Back to Top
          </button>
        </div>

        <form action="{{ route('teachers.grades.store') }}" method="POST">
          @csrf
          <input type="hidden" name="assignment_id" value="{{ $selectedAssignment->assignment_id }}"> 
          <input type="hidden" name="subject_id" value="{{ $subject->id }}">
          <input type="hidden" name="section_id" value="{{ $section->id }}">

          <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
              <thead class="table-primary sticky-top">
                <tr>
                  <th class="text-start ps-3">Student</th>
                  <th>1st</th><th>2nd</th><th>3rd</th><th>4th</th>
                  <th>Final</th><th>Remarks</th>
                </tr>
              </thead>
              <tbody>
                @foreach($students as $student)
                  @php
                    $grades = [];
                    foreach (['1st','2nd','3rd','4th'] as $q) {
                      $gradeRecord = $student->grades->firstWhere('quarter',$q);
                      $grades[$q] = $gradeRecord?->grade;
                    }
                    $valid = array_filter($grades, fn($g)=>is_numeric($g)&&$g>=0&&$g<=100);
                    $final = count($valid)==4 ? round(array_sum($valid)/4) : null;
                    $remarks = $final ? ($final>=75?'PASSED':'FAILED') : null;
                  @endphp
                  <tr>
                    <td class="text-start ps-3 fw-semibold">
                      {{ $student->user->profile->last_name ?? '' }}, 
                      {{ $student->user->profile->first_name ?? '' }}
                      <input type="hidden" name="students[]" value="{{ $student->id }}">
                    </td>
                    @foreach(['1st','2nd','3rd','4th'] as $q)
                      <td>
                        <input 
                          type="number"
                          name="grades[{{ $student->id }}][{{ $q }}]"
                          value="{{ old('grades.'.$student->id.'.'.$q, $grades[$q]) }}"
                          class="form-control text-center grade-input"
                          min="0" max="100" step="1"
                          onchange="validateAndCompute(this)"
                          {{ $selectedAssignment->grade_status === 'approved' ? 'readonly disabled' : '' }}>
                      </td>
                    @endforeach
                    <td>
                      <input type="text"
                        class="form-control text-center fw-bold final-grade"
                        value="{{ $final ?? '' }}" readonly>
                    </td>
                    <td class="fw-bold remarks">
                      @if($remarks)
                        <span class="badge rounded-pill px-3 py-2 {{ $remarks == 'PASSED' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                          {{ $remarks }}
                        </span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Action Buttons --}}
            <div class="text-end mt-4">
            @if(in_array($selectedAssignment->grade_status ?? 'draft', ['draft','returned']))
                <div class="d-flex justify-content-end gap-2">
                {{-- Save Draft --}}
                <button type="submit" name="action" value="save" class="btn btn-outline-primary px-4 btn-lg shadow-sm">
                    <i class="bi bi-save me-1"></i> Save Draft
                </button>

                {{-- Submit for Review --}}
                <button type="submit" formaction="{{ route('teachers.grades.submit') }}" 
                        name="action" value="submit"
                        class="btn btn-outline-success px-4 btn-lg shadow-sm"
                        onclick="return confirm('Submit grades for review? You won’t be able to edit after submission.')">
                    <i class="bi bi-send-check me-1"></i> Submit Grades
                </button>
                </div>
            @else
                <button type="button" class="btn btn-secondary px-4 btn-lg" disabled>
                <i class="bi bi-lock me-1"></i> Grades Locked
                </button>
            @endif
            </div>
        </form>
      </div>
    </div>
  @else
    <div class="card border-0 shadow-sm text-center py-5">
      <div class="card-body">
        <i class="bi bi-journal-x text-secondary" style="font-size: 3rem;"></i>
        <h6 class="mt-3 text-muted">No subject selected</h6>
        <p class="small text-muted mb-0">Choose a subject above to load students and begin encoding grades.</p>
      </div>
    </div>
  @endif
</div>

{{-- JS --}}
<script>
function validateAndCompute(input) {
  const row = input.closest('tr');
  const gradeInputs = row.querySelectorAll('.grade-input');
  const finalInput = row.querySelector('.final-grade');
  const remarksSpan = row.querySelector('.remarks span');

  let val = parseFloat(input.value);
  if (!isNaN(val)) {
    if (val > 100) input.value = 100;
    if (val < 0) input.value = 0;
  } else input.value = '';

  let grades = [];
  gradeInputs.forEach(g => {
    let gVal = parseFloat(g.value);
    if (!isNaN(gVal) && gVal >= 0 && gVal <= 100) grades.push(gVal);
  });

  if (grades.length === 4) {
    const avg = Math.round(grades.reduce((a,b)=>a+b,0)/4);
    finalInput.value = avg;
    remarksSpan.textContent = avg >= 75 ? 'PASSED' : 'FAILED';
    remarksSpan.className = `badge rounded-pill px-3 py-2 ${avg >= 75 
      ? 'bg-success-subtle text-success border border-success-subtle' 
      : 'bg-danger-subtle text-danger border border-danger-subtle'}`;
  } else {
    finalInput.value = '';
    remarksSpan.textContent = '';
    remarksSpan.className = '';
  }
}
</script>
@endsection
