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
                                    {{ request('subject_id') == $subj->id ? 'selected' : '' }}>
                                    {{ $subj->name }} ({{ $subj->section->name ?? 'No Section' }})
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
    @if($subject && $section)
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold mb-3">
                {{ $subject->name }} - {{ $section->name }}
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
                                    $grades = $student->grades
                                        ->where('subject_id', $subject->id)
                                        ->pluck('grade', 'quarter')
                                        ->toArray();

                                    $final = isset($grades['1st'], $grades['2nd'], $grades['3rd'], $grades['4th'])
                                        ? round(array_sum([
                                            $grades['1st'], $grades['2nd'],
                                            $grades['3rd'], $grades['4th']
                                        ]) / 4, 2)
                                        : null;

                                    $remarks = $final !== null ? ($final >= 75 ? 'PASSED' : 'FAILED') : null;
                                @endphp
                                <tr>
                                    <td class="text-start fw-semibold">
                                        {{ $student->name }}
                                    </td>
                                    @foreach(['1st', '2nd', '3rd', '4th'] as $q)
                                        <td>
                                            <input type="number" 
                                                name="grades[{{ $student->id }}][{{ $q }}]"
                                                value="{{ $grades[$q] ?? '' }}"
                                                class="form-control text-center"
                                                min="0" max="100">
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
