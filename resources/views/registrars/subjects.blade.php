@extends('layouts.registrar')

@section('title','Subjects')
@section('header')
    <i class="bi bi-book me-2"></i> Subject Management
@endsection

@section('content')
@php
    $syClosed = !$currentSY; // true if no active school year
@endphp

<div class="container-fluid my-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      @include('partials.alerts')

      @if(!$currentSY || $currentSY->status !== 'active')
        <div class="alert alert-warning d-flex align-items-center">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <div>
            <strong>Note:</strong> No active school year detected.
            All management actions are disabled until a school year is activated.
          </div>
        </div>
      @endif

      {{-- 🔍 Search & Filter --}}
      <form method="GET" action="{{ route('registrars.subjects') }}" class="row g-2 align-items-end mb-4">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Search</label>
          <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search subject...">
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold">Grade Level</label>
          <select name="grade_level_id" class="form-select">
            <option value="">All Grade Levels</option>
            @foreach($gradeLevels as $gl)
              <option value="{{ $gl->id }}" {{ request('grade_level_id') == $gl->id ? 'selected' : '' }}>
                {{ $gl->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-search"></i> Search
          </button>
          <a href="{{ route('registrars.subjects') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </a>
        </div>
        <div class="col-md-3 d-flex justify-content-end gap-2">
          <a href="{{ route('registrars.subjects.archived') }}" class="btn btn-outline-dark">
            <i class="bi bi-archive"></i> View Archived
          </a>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal"
              @if($syClosed) disabled title="Cannot add subjects. No active school year." @endif>
            <i class="bi bi-plus-circle me-1"></i> Add Subject
          </button>
        </div>
      </form>

      {{-- 📋 Subject Table --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th>Subject Name</th>
              <th>Grade Level</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($subjects as $i => $subj)
            <tr>
              <td>{{ $subjects->firstItem() + $i }}</td>
              <td>{{ $subj->name }}</td>
              <td>{{ $subj->gradeLevel->name ?? '-' }}</td>
              <td>
                <div class="d-flex justify-content-center gap-2 flex-wrap">

                  {{-- ✏ Edit --}}
                  <button 
                    class="btn btn-sm btn-warning" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editSubjectModal{{ $subj->id }}"
                    @if($syClosed) disabled title="Cannot edit subjects. SY is closed." @endif>
                    <i class="bi bi-pencil"></i>
                  </button>

                  {{-- 🗄 Archive --}}
                  <form id="archiveForm{{ $subj->id }}" action="{{ route('registrars.subjects.archive', $subj->id) }}" method="POST" style="display:inline;">
                    @csrf @method('PUT')
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#archiveModal{{ $subj->id }}"
                      @if($syClosed) disabled title="Cannot archive subjects. SY is closed." @endif>
                      <i class="bi bi-archive"></i>
                    </button>
                  </form>

                  {{-- 🗂 Archive Modal --}}
                  <div class="modal fade" id="archiveModal{{ $subj->id }}" tabindex="-1" aria-labelledby="archiveModalLabel{{ $subj->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header bg-secondary text-white">
                          <h5 class="modal-title" id="archiveModalLabel{{ $subj->id }}">
                            <i class="bi bi-archive me-2"></i> Confirm Archive
                          </h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                          Are you sure you want to archive the subject 
                          <strong>{{ $subj->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" form="archiveForm{{ $subj->id }}" class="btn btn-secondary">
                            Archive
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </td>
            </tr>

            {{-- 🟡 Edit Modal --}}
            <div class="modal fade" id="editSubjectModal{{ $subj->id }}" tabindex="-1">
              <div class="modal-dialog">
                <form method="POST" action="{{ route('registrars.subjects.update',$subj->id) }}">
                  @csrf @method('PUT')
                  <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                      <h5 class="modal-title"><i class="bi bi-pencil me-2"></i> Edit Subject</h5>
                      <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" name="name" value="{{ $subj->name }}" class="form-control" required
                          @if($syClosed) disabled @endif>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level_id" class="form-select" required
                          @if($syClosed) disabled @endif>
                          <option value="">-- Select Grade Level --</option>
                          @foreach($gradeLevels as $gl)
                            <option value="{{ $gl->id }}" {{ $gl->id == $subj->grade_level_id ? 'selected' : '' }}>
                              {{ $gl->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button class="btn btn-warning" @if($syClosed) disabled @endif>Update</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-3">
                <i class="bi bi-info-circle"></i> No subjects found.
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div class="d-flex justify-content-end mt-3">
        {{ $subjects->appends(request()->query())->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>

{{-- ➕ Add Subject Modal --}}
<div class="modal fade" id="addSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.subjects.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add Subject</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Subject Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required
              @if($syClosed) disabled @endif>
          </div>
          <div class="mb-3">
            <label class="form-label">Grade Level</label>
            <select name="grade_level_id" class="form-select" required @if($syClosed) disabled @endif>
              <option value="">-- Select Grade Level --</option>
              @foreach($gradeLevels as $gl)
                <option value="{{ $gl->id }}">{{ $gl->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" @if($syClosed) disabled @endif>Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
