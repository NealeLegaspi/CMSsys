@extends('layouts.registrar')

@section('title', 'Subject Load: ' . $section->name)
@section('header')
    <i class="bi bi-journal-bookmark me-2"></i> Subject Load Management
@endsection

@section('content')
<div class="container my-4">
    @include('partials.alerts')

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

    <div class="card shadow-sm border-0">
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

        <div class="card-body tab-content" id="subjectLoadTabsContent">
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
                                        <button type="button" 
                                                class="btn btn-sm btn-danger shadow-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteSubjectModal{{ $assign->id }}">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>

                                        <div class="modal fade" id="deleteSubjectModal{{ $assign->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-exclamation-triangle me-2"></i> Confirm Delete
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body text-center">
                                                <p class="mb-2">Are you sure you want to remove this subject assignment?</p>
                                                <p class="fw-semibold mb-0 text-danger">{{ $assign->subject->name }}</p>
                                                <small class="text-muted">
                                                    Teacher: {{ $assign->teacher->profile->first_name ?? '' }} {{ $assign->teacher->profile->last_name ?? '' }}
                                                </small>
                                                </div>

                                                <div class="modal-footer justify-content-center">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <form action="{{ route('registrars.sections.subjects.destroy', ['id' => $section->id, 'subjectId' => $assign->id]) }}" 
                                                        method="POST" 
                                                        class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        Remove
                                                    </button>
                                                </form>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
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