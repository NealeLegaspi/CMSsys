@extends('layouts.registrar')

@section('title','Subjects')
@section('header','Subjects')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">📖 Subjects</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
      <i class="bi bi-plus-circle me-1"></i> Add Subject
    </button>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <!-- Search & Filter -->
    <form method="GET" action="{{ route('registrars.subjects') }}" class="row g-2 mb-3">
      <div class="col-md-5">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search subject...">
      </div>
      <div class="col-md-4">
        <select name="grade_level_id" class="form-select">
          <option value="">All Grade Levels</option>
          @foreach($gradeLevels as $gl)
            <option value="{{ $gl->id }}" {{ request('grade_level_id') == $gl->id ? 'selected' : '' }}>
              {{ $gl->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3 d-flex">
        <button type="submit" class="btn btn-primary me-2">
          <i class="bi bi-search"></i>
        </button>
        <a href="{{ route('registrars.subjects') }}" class="btn btn-secondary">
          <i class="bi bi-arrow-clockwise"></i>
        </a>
      </div>
    </form>

    <table class="table table-bordered table-striped align-middle">
      <thead class="table-primary">
        <tr>
          <th style="width: 50px;">#</th>
          <th>Name</th>
          <th>Grade Level</th>
          <th style="width: 140px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($subjects as $i => $subj)
        <tr>
          <td>{{ $subjects->firstItem() + $i }}</td>
          <td>{{ $subj->name }}</td>
          <td>{{ $subj->gradeLevel->name ?? '-' }}</td>
          <td>
            <!-- Edit Button -->
            <button 
              class="btn btn-sm btn-warning me-1" 
              data-bs-toggle="modal" 
              data-bs-target="#editSubjectModal{{ $subj->id }}">
              <i class="bi bi-pencil"></i>
            </button>

            <!-- Delete -->
            <form action="{{ route('registrars.subjects.destroy',$subj->id) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Delete subject?')">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </td>
        </tr>

        <!-- Edit Modal -->
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
                    <input type="text" class="form-control" name="name" value="{{ $subj->name }}" required>
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
        <!-- End Edit Modal -->

        @empty
        <tr>
          <td colspan="4" class="text-center text-muted">No subjects yet.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
      <div class="mt-3">{{ $subjects->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.subjects.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add Subject</h5>
          <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Subject Name</label>
            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
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
