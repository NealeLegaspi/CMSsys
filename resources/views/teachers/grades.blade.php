@extends('layouts.teacher')

@section('title', 'Grades')
@section('header')
  <i class="bi bi-pencil-square me-2"></i> Grades
@endsection

@section('content')

@if($syClosed)
  <div class="alert alert-warning d-flex align-items-center" role="alert">
    <i class="bi bi-lock-fill me-2 fs-5"></i>
    <div>
      The school year <strong>{{ $currentSY->name ?? 'N/A' }}</strong> is closed.
      Grade encoding and submission are disabled.
    </div>
  </div>
@endif

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

        <form id="gradesForm" action="{{ route('teachers.grades.store') }}" method="POST">
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

                    $valid = array_filter($grades, fn($g)=>is_numeric($g) && $g >= 0 && $g <= 100);

                    $final = count($valid) == 4
                      ? number_format(array_sum($valid) / 4, 2, '.', '')
                      : null;

                    $remarks = $final !== null ? ($final >= 75 ? 'PASSED' : 'FAILED') : null;
                  @endphp

                  <tr>
                    <td class="text-start ps-3 fw-semibold">
                      {{ $student->user->profile->last_name ?? '' }}, 
                      {{ $student->user->profile->first_name ?? '' }}
                      <input type="hidden" name="students[]" value="{{ $student->id }}">
                    </td>

                    @foreach(['1st','2nd','3rd','4th'] as $q)
                      @php
                        $quarterNumberMap = ['1st' => 1, '2nd' => 2, '3rd' => 3, '4th' => 4];
                        $qNum = $quarterNumberMap[$q] ?? 0;
                        $isFutureQuarter = $qNum > ($activeQuarter ?? 0);
                        $isLocked = in_array($q, $lockedQuarters ?? []);
                        $disabled = $syClosed || $isFutureQuarter || $isLocked;
                      @endphp
                      <td>
                        <input 
                          type="number"
                          name="grades[{{ $student->id }}][{{ $q }}]"
                          value="{{ old('grades.'.$student->id.'.'.$q, $grades[$q]) }}"
                          class="form-control text-center grade-input"
                          min="0" max="100" step="1"
                          {{ $disabled ? 'readonly disabled' : '' }}
                          oninput="validateAndCompute(this)">
                      </td>
                    @endforeach

                    <td>
                      <input type="text"
                        class="form-control text-center fw-bold final-grade"
                        value="{{ $final ?? '' }}" readonly>
                    </td>

                    <td class="fw-bold remarks">
                      <span class="badge rounded-pill px-3 py-2 
                          {{ $remarks == 'PASSED' 
                              ? 'bg-success-subtle text-success border border-success-subtle' 
                              : ($remarks == 'FAILED' 
                                  ? 'bg-danger-subtle text-danger border border-danger-subtle' 
                                  : '') }}">
                        {{ $remarks ?? '' }}
                      </span>
                    </td>

                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Action Buttons --}}
          <div class="text-end mt-4">
            @if(!$syClosed && in_array($selectedAssignment->grade_status ?? 'draft', ['draft','returned']))
              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-success px-4 btn-lg shadow-sm"
                        data-bs-toggle="modal" data-bs-target="#confirmSubmitModal">
                  <i class="bi bi-send-check me-1"></i> Submit Grades
                </button>

                <button type="button" 
                        class="btn btn-outline-dark btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#importGradesModal">
                    <i class="bi bi-upload me-1"></i> Import Grades
                </button>

              </div>
            @elseif($syClosed)
              <button type="button" class="btn btn-secondary px-4 btn-lg" disabled>
                <i class="bi bi-lock me-1"></i> School Year Closed
              </button>
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

{{-- Confirm Submit Modal --}}
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="confirmSubmitModalLabel"><i class="bi bi-send-check me-2"></i> Confirm Submission</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0 fs-5 text-center">
          Are you sure you want to <strong>submit these grades</strong> for review?<br>
        </p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
          <i class="bi bi-x-circle"></i> Cancel
        </button>
        <button type="submit" form="gradesForm" name="action" value="save"
                class="btn btn-success px-4">
          <i class="bi bi-send-check"></i> Confirm 
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="importGradesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title"><i class="bi bi-upload me-1"></i> Import Grades</h5>
        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>You can upload a CSV file containing student grades for this subject.</p>

        <a href="{{ route('teachers.grades.import.template') }}"
           class="btn btn-sm btn-outline-primary mb-3">
           <i class="bi bi-download"></i> Download Template
        </a>

        @if($subject && $section)
        <form action="{{ route('teachers.grades.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
            <input type="hidden" name="section_id" value="{{ $section->id }}">

            <div class="mb-3">
                <label class="form-label fw-semibold">Upload CSV File</label>
                <input type="file" name="file" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Import Grades</button>
        </form>
        @endif
      </div>
    </div>
  </div>
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
        // Force whole-number quarter grades, clamp only to 0–100 while typing
        val = Math.round(val);
        if (val > 100) val = 100;
        if (val < 0)   val = 0;
        input.value = val;
    } else {
        input.value = '';
    }

    let grades = [];
    gradeInputs.forEach(g => {
        let gVal = parseFloat(g.value);
        if (!isNaN(gVal) && gVal >= 0 && gVal <= 100) {
            grades.push(gVal);
        }
    });

    if (grades.length === 4) {
        const sum = grades.reduce((a, b) => a + b, 0);
        const avg = sum / 4;

        // Final grade: AVERAGE of 1st–4th, 2 decimal places
        finalInput.value = avg.toFixed(2);

        // Remarks based on average
        remarksSpan.textContent = avg >= 75 ? 'PASSED' : 'FAILED';
        remarksSpan.className = `badge rounded-pill px-3 py-2 ${
            avg >= 75 
            ? 'bg-success-subtle text-success border border-success-subtle' 
            : 'bg-danger-subtle text-danger border border-danger-subtle'
        }`;
    } else {
        finalInput.value = '';
        remarksSpan.textContent = '';
        remarksSpan.className = '';
    }
}
</script>

@endsection
