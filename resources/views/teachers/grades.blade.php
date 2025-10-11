@extends('layouts.teacher')

@section('title', 'Encode Grades')
@section('header', 'Encode Grades')

@section('content')
<div class="container-fluid my-4">

  {{-- Flash Message --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Subject Selection -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <h6 class="fw-bold mb-3">
        <i class="bi bi-book-half me-2"></i> Select Subject
      </h6>
      <form method="GET" action="{{ route('teachers.grades') }}">
        <div class="row g-2">
          <div class="col-md-9">
            <select name="subject_id" class="form-select form-select-lg" required>
              <option value="">-- Choose Subject --</option>
              @foreach($subjects as $subj)
                <option value="{{ $subj->subject_id }}"
                  {{ $selectedSubject && $selectedSubject->id == $subj->subject_id ? 'selected' : '' }}>
                  {{ $subj->subject_name }} —
                  {{ $subj->gradelevel ?? 'N/A' }} 
                  ({{ $subj->section_name ?? 'No Section' }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-arrow-repeat me-1"></i> Load Students
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Grades Table -->
  @if(isset($selectedSubject))
    @php
      $subject = $selectedSubject;
      $section = $subject->section;
      $students = $section->students()->with(['grades' => function ($q) use ($subject) {
        $q->where('subject_id', $subject->id);
      }])->get();
    @endphp

    <div class="card border-0 shadow-sm rounded-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
          <h5 class="fw-bold text-dark mb-2">
            <i class="bi bi-journal-text text-primary me-2"></i>
            Encoding Grades for 
            <span class="text-primary">{{ $subject->name }}</span> —
            {{ $section->gradeLevel->name ?? 'No Grade Level' }} 
            {{ $section->name ?? '' }}
          </h5>
          <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.scrollTo(0,0)">
            <i class="bi bi-arrow-up-circle"></i> Back to Top
          </button>
        </div>

        <form action="{{ route('teachers.grades.store') }}" method="POST">
          @csrf
          <input type="hidden" name="subject_id" value="{{ $subject->id }}">
          <input type="hidden" name="section_id" value="{{ $section->id }}">

          <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
              <thead class="table-primary sticky-top">
                <tr>
                  <th class="text-start">Student</th>
                  <th>1st Quarter</th>
                  <th>2nd Quarter</th>
                  <th>3rd Quarter</th>
                  <th>4th Quarter</th>
                  <th>Final Grade</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
                @foreach($students as $student)
                  @php
                    $grades = [];
                    foreach (['1st', '2nd', '3rd', '4th'] as $q) {
                      $gradeRecord = $student->grades->firstWhere('quarter', $q);
                      $grades[$q] = $gradeRecord ? $gradeRecord->grade : null;
                    }
                    $final = null;
                    $remarks = null;
                    if (count(array_filter($grades)) === 4) {
                      $final = round(array_sum($grades) / 4);
                      $remarks = $final >= 75 ? 'PASSED' : 'FAILED';
                    }
                  @endphp
                  <tr>
                    <td class="text-start fw-semibold">
                      {{ $student->user->profile->first_name ?? '' }} 
                      {{ $student->user->profile->last_name ?? '' }}
                      <input type="hidden" name="students[]" value="{{ $student->id }}">
                    </td>

                    @foreach(['1st', '2nd', '3rd', '4th'] as $q)
                      <td>
                        <input 
                          type="number"
                          name="grades[{{ $student->id }}][{{ $q }}]" 
                          value="{{ old('grades.'.$student->id.'.'.$q, $grades[$q]) ?? '' }}"
                          class="form-control text-center grade-input"
                          min="0" max="100" step="1"
                          onchange="validateAndCompute(this)">
                      </td>
                    @endforeach

                    <td>
                      <input type="text"
                        class="form-control text-center fw-bold final-grade"
                        value="{{ $final ?? '' }}"
                        readonly>
                    </td>
                    <td class="fw-bold remarks">
                      <span class="{{ $remarks === 'PASSED' ? 'text-success' : ($remarks === 'FAILED' ? 'text-danger' : '') }}">
                        {{ $remarks ?? '' }}
                      </span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary px-4">
              <i class="bi bi-save me-1"></i> Save Grades
            </button>
          </div>
        </form>
      </div>
    </div>
  @else
    <!-- Empty State -->
    <div class="card border-0 shadow-sm text-center py-5">
      <div class="card-body">
        <i class="bi bi-journal-x text-secondary" style="font-size: 3rem;"></i>
        <h6 class="mt-3 text-muted">No subject selected</h6>
        <p class="small text-muted">Choose a subject above to load students and begin encoding grades.</p>
      </div>
    </div>
  @endif
</div>

<!-- JS: Real-time Validation & Computation -->
<script>
function validateAndCompute(input) {
  const row = input.closest('tr');
  const gradeInputs = row.querySelectorAll('.grade-input');
  const finalInput = row.querySelector('.final-grade');
  const remarksSpan = row.querySelector('.remarks span');

  let grades = [];
  gradeInputs.forEach(g => {
    let val = parseFloat(g.value);
    if (!isNaN(val)) grades.push(val);
  });

  if (grades.length === 4) {
    const avg = Math.round(grades.reduce((a,b) => a+b, 0) / 4);
    finalInput.value = avg;
    remarksSpan.textContent = avg >= 75 ? 'PASSED' : 'FAILED';
    remarksSpan.className = avg >= 75 ? 'text-success' : 'text-danger';
  } else {
    finalInput.value = '';
    remarksSpan.textContent = '';
    remarksSpan.className = '';
  }

  if (input.value > 100) input.value = 100;
  if (input.value < 0) input.value = 0;
}
</script>
@endsection
