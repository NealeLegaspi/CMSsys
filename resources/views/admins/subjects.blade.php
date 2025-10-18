@extends('layouts.admin')

@section('title','Subjects')
@section('header')
    <i class="bi bi-book me-2"></i> Subject Management
@endsection

@section('content')
<div class="container-fluid my-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      @include('partials.alerts')

      {{-- 🔍 Search & Filter --}}
      <form method="GET" action="{{ route('admins.subjects') }}" class="row g-2 align-items-end mb-4">
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
          <a href="{{ route('admins.subjects') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </a>
        </div>
        <div class="col-md-2 d-flex justify-content-end">
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
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
                    data-bs-target="#editSubjectModal{{ $subj->id }}">
                    <i class="bi bi-pencil"></i>
                  </button>

                  {{-- 🗑 Delete --}}
                  <form action="{{ route('admins.subjects.destroy',$subj->id) }}" method="POST" onsubmit="return confirm('Delete subject?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>

            {{-- 🟡 Edit Modal --}}
            <div class="modal fade" id="editSubjectModal{{ $subj->id }}" tabindex="-1">
              <div class="modal-dialog">
                <form method="POST" action="{{ route('admins.subjects.update',$subj->id) }}">
                  @csrf @method('PUT')
                  <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                      <h5 class="modal-title"><i class="bi bi-pencil me-2"></i> Edit Subject</h5>
                      <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" name="name" value="{{ $subj->name }}" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Grade Level</label>
                        <select name="grade_level_id" class="form-select" required>
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
                      <button class="btn btn-warning">Update</button>
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
    <form method="POST" action="{{ route('admins.subjects.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add Subject</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Subject Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Grade Level</label>
            <select name="grade_level_id" class="form-select" required>
              <option value="">-- Select Grade Level --</option>
              @foreach($gradeLevels as $gl)
                <option value="{{ $gl->id }}">{{ $gl->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
