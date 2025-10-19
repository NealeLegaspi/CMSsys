@extends('layouts.registrar')

@section('title', 'Subject Load: ' . $section->name)
@section('header')
    <i class="bi bi-journal-bookmark me-2"></i> Subject Load Management
@endsection

@section('content')
<div class="container my-4">
    @include('partials.alerts')

    {{-- Section Info Card --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0">
                    Section: <span class="text-primary">{{ $section->gradeLevel->name }} - {{ $section->name }}</span>
                </h4>
                <p class="text-muted mb-0">Adviser: {{ $section->adviser?->profile?->first_name ?? 'N/A' }} {{ $section->adviser?->profile?->last_name ?? '' }}</p>
            </div>
            <a href="{{ route('registrars.sections') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Sections
            </a>
        </div>
    </div>

    {{-- Main Card Container for Tabs --}}
    <div class="card shadow-sm border-0">
        {{-- Tabs Navigation --}}
        <div class="card-header bg-light border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs" id="subjectLoadTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold" id="assign-tab" data-bs-toggle="tab" data-bs-target="#assign-pane" type="button" role="tab" aria-controls="assign-pane" aria-selected="true">
                        <i class="bi bi-person-plus-fill me-1"></i> Assign Subject Teacher
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold" id="current-tab" data-bs-toggle="tab" data-bs-target="#current-pane" type="button" role="tab" aria-controls="current-pane" aria-selected="false">
                        <i class="bi bi-list-task me-1"></i> Current Subject Load ({{ $sectionSubjects->count() }})
                    </button>
                </li>
            </ul>
        </div>

        {{-- Tabs Content: Gumagamit na ng default padding ng card-body --}}
        <div class="card-body tab-content" id="subjectLoadTabsContent">
            
            {{-- TAB 1: Assign Subject Teacher Form --}}
            {{-- Walang p-class sa tab-pane, umaasa sa default padding ng card-body --}}
            <div class="tab-pane fade show active" id="assign-pane" role="tabpanel" aria-labelledby="assign-tab" tabindex="0">
                <h5 class="fw-bold text-success mb-3">Add New Assignment</h5>
                <form action="{{ route('registrars.sections.subjects.store', ['id' => $section->id]) }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Subject</label>
                        <select name="subject_id" class="form-select" required>
                            <option value="">-- Select Subject to Assign --</option>
                            @foreach($availableSubjects as $subject)
                                <option value="{{ $subject->id }}" 
                                    @if($sectionSubjects->contains('subject_id', $subject->id)) disabled @endif>
                                    {{ $subject->name }} 
                                    @if($sectionSubjects->contains('subject_id', $subject->id)) (Already Assigned) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Subject Teacher</label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">-- Select Teacher --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->profile?->last_name ?? 'N/A' }}, {{ $teacher->profile?->first_name ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100 shadow-sm"><i class="bi bi-save me-1"></i> Save Assignment</button>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="current-pane" role="tabpanel" aria-labelledby="current-tab" tabindex="0">
                <h5 class="fw-bold text-primary mb-3">Assigned Subjects List</h5>
                @if($sectionSubjects->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-journal-x" style="font-size: 3rem;"></i>
                        <p class="mt-3">No subjects assigned to this section yet.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Subject Name</th>
                                    <th>Assigned Teacher</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sectionSubjects as $i => $assign)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $assign->subject->name }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                {{ $assign->teacher->profile->last_name ?? '' }}, 
                                                {{ $assign->teacher->profile->first_name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('registrars.sections.subjects.destroy', ['id' => $section->id, 'subjectId' => $assign->id]) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger shadow-sm" title="Remove Assignment" onclick="return confirm('Are you sure you want to remove this subject assignment?')">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection