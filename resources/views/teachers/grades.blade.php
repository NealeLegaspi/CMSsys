@extends('layouts.teacher')

@section('title', 'Grades')
@section('header')
    <i class="bi bi-pencil-square me-2"></i> Grades
@endsection

@section('content')
<div class="container-fluid my-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @error('assignment_id')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @enderror

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('teachers.grades') }}">
                <div class="row g-2">
                    <div class="col-md-9">
                        <select name="assignment_id" class="form-select form-select-lg" required>
                            <option value="">-- Choose Subject and Section --</option>
                            @foreach($assignments as $assignment)
                                <option value="{{ $assignment->assignment_id }}"
                                    {{-- Gamitin ang $selectedAssignment --}}
                                    {{ $selectedAssignment && $selectedAssignment->assignment_id == $assignment->assignment_id ? 'selected' : '' }}>
                                    {{ $assignment->subject_name }} —
                                    {{ $assignment->gradelevel_name ?? 'N/A' }} 
                                    ({{ $assignment->section_name ?? 'No Section' }})
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

    @if(isset($selectedAssignment))
        
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <h5 class="fw-bold text-dark mb-2">
                        <i class="bi bi-journal-text text-primary me-2"></i>
                        Encoding Grades for 
                        <span class="text-primary">{{ $subject->name ?? 'Subject' }}</span> 
                        —
                        {{ $section->gradeLevel->name ?? 'No Grade Level' }} 
                        {{ $section->name ?? '' }}
                    </h5>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.scrollTo(0,0)">
                        <i class="bi bi-arrow-up-circle"></i> Back to Top
                    </button>
                </div>

                <form action="{{ route('teachers.grades.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="assignment_id" value="{{ $selectedAssignment->assignment_id }}"> 
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
                                        
                                        // Filter para sa valid grades (numeric at 0-100)
                                        $valid_grades = array_filter($grades, fn($g) => is_numeric($g) && $g >= 0 && $g <= 100);

                                        if (count($valid_grades) === 4) {
                                            $final = round(array_sum($valid_grades) / 4);
                                            $remarks = $final >= 75 ? 'PASSED' : 'FAILED';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-start fw-semibold">
                                            {{ $student->user->profile->last_name ?? '' }}, 
                                            {{ $student->user->profile->first_name ?? '' }}
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
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <i class="bi bi-journal-x text-secondary" style="font-size: 3rem;"></i>
                <h6 class="mt-3 text-muted">No subject selected</h6>
                <p class="small text-muted">Choose a subject above to load students and begin encoding grades.</p>
            </div>
        </div>
    @endif
</div>

<script>
function validateAndCompute(input) {
    const row = input.closest('tr');
    const gradeInputs = row.querySelectorAll('.grade-input');
    const finalInput = row.querySelector('.final-grade');
    const remarksSpan = row.querySelector('.remarks span');

    // Limit ang input value sa 0 hanggang 100
    let val = parseFloat(input.value);
    if (!isNaN(val)) {
        if (val > 100) input.value = 100;
        if (val < 0) input.value = 0;
    } else {
        input.value = ''; 
    }

    let grades = [];
    // Kolektahin ang valid grades (numeric at 0-100)
    gradeInputs.forEach(g => {
        let g_val = parseFloat(g.value);
        if (!isNaN(g_val) && g_val >= 0 && g_val <= 100) {
             grades.push(g_val);
        }
    });

    if (grades.length === 4) {
        // Compute average at i-round off
        const avg = Math.round(grades.reduce((a,b) => a+b, 0) / 4);
        finalInput.value = avg;
        remarksSpan.textContent = avg >= 75 ? 'PASSED' : 'FAILED';
        remarksSpan.className = avg >= 75 ? 'text-success' : 'text-danger';
    } else {
        finalInput.value = '';
        remarksSpan.textContent = '';
        remarksSpan.className = '';
    }
}
</script>
@endsection