@extends('layouts.registrar')

@section('title','Archived Subjects')
@section('header')
    <i class="bi bi-archive me-2"></i> Archived Subjects
@endsection

@section('content')
<div class="container-fluid my-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      @include('partials.alerts')

      {{-- 🔍 Search & Filter --}}
      <form method="GET" action="{{ route('registrars.subjects.archived') }}" class="row g-2 align-items-end mb-4">
        <div class="col-md-5">
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
          <a href="{{ route('registrars.subjects.archived') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </a>
        </div>
        <div class="col-md-2 d-flex justify-content-end">
          <a href="{{ route('registrars.subjects') }}" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left"></i> Back to Active
          </a>
        </div>
      </form>

      {{-- 📋 Archived Table --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead class="table-secondary">
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
                <form id="restoreForm{{ $subj->id }}" action="{{ route('registrars.subjects.restore', $subj->id) }}" method="POST" style="display:inline;">
                @csrf @method('PUT')
                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#restoreModal{{ $subj->id }}">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                </button>
                </form>

                {{-- 🔁 Restore Modal --}}
                <div class="modal fade" id="restoreModal{{ $subj->id }}" tabindex="-1" aria-labelledby="restoreModalLabel{{ $subj->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="restoreModalLabel{{ $subj->id }}">
                        <i class="bi bi-arrow-counterclockwise me-2"></i> Confirm Restore
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        Are you sure you want to restore the subject 
                        <strong>{{ $subj->name }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" form="restoreForm{{ $subj->id }}" class="btn btn-success">
                        Restore
                        </button>
                    </div>
                    </div>
                </div>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-3">
                <i class="bi bi-info-circle"></i> No archived subjects.
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
@endsection
