@extends('layouts.teacher')

@section('title', 'Encode Grades')
@section('header', 'Encode Grades')

@section('content')
<div class="container my-4">

    {{-- Flash success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Subject Picker Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3">📘 Subject</h5>
            <form method="GET" action="{{ route('teachers.grades') }}">
                <div class="row g-2">
                    <div class="col-md-9">
                        <select name="subject_id" class="form-select" required>
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->id }}" 
                                    {{ (isset($selectedSubject) && $selectedSubject->id == $subj->id) ? 'selected' : '' }}>
                                    {{ $subj->name }} - 
                                    {{ $subj->section->gradeLevel->name ?? 'No Grade Level' }} 
                                    {{ $subj->section->name ?? 'No Section' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            Load Students
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Encode Grades Table -->
    @if(isset($selectedSubject))
        @php
            $subject = $selectedSubject;
            $section = $subject->section;
            $students = $section->students()->with(['grades' => function ($q) use ($subject) {
                $q->where('subject_id', $subject->id);
            }])->get();
        @endphp
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">
                📋 Encoding Grades for 
                {{ $subject->name }} - 
                {{ $section->gradeLevel->name ?? 'No Grade Level' }} 
                {{ $section->name ?? 'No Section' }}
            </h5>

            <form action="{{ route('teachers.grades.store') }}" method="POST">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                <input type="hidden" name="section_id" value="{{ $section->id }}">

                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle text-center">
                        <thead class="table-light sticky-top">
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
                                <tr>
                                    <td class="text-start fw-semibold">
                                        {{ $student->user->profile->first_name ?? '' }} 
                                        {{ $student->user->profile->last_name ?? '' }}
                                        <input type="hidden" name="students[]" value="{{ $student->id }}">
                                    </td>
                                    @foreach(['1st', '2nd', '3rd', '4th'] as $q)
                                        <td>
                                            <input type="number" name="grades[{{ $student->id }}][{{ $q }}]" 
                                                value="{{ old('grades.'.$student->id.'.'.$q, $grades[$q]) ?? '' }}"
                                                class="form-control text-center"
                                                min="0" max="100" step="1"
                                                onchange="this.value = Math.min(100, Math.max(0, this.value))">
                                        </td>
                                    @endforeach
                                    <td>
                                        <input type="text" 
                                            value="{{ $final ?? '' }}"
                                            class="form-control text-center fw-bold {{ $final !== null && $final < 75 ? 'text-danger' : '' }}"
                                            readonly>
                                    </td>
                                    <td>
                                        <span class="fw-bold 
                                            {{ $remarks === 'PASSED' ? 'text-success' : 'text-danger' }}">
                                            {{ $remarks ?? '' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        Save Grades
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
        <!-- Empty State -->
        <div class="card shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-table" style="font-size: 3rem;"></i>
                <p class="mt-3">No subject selected. Please choose a subject to load students.</p>
            </div>
        </div>
    @endif
</div>
@endsection
