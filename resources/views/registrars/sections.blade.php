@extends('layouts.registrar')

@section('title', 'Section Management')
@section('header')
    <i class="bi bi-building me-2"></i> Section Management
@endsection

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-body">
    @include('partials.alerts')

    <form method="GET" action="{{ route('registrars.sections') }}" class="row g-2 mb-3">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search section or adviser..." value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('registrars.sections') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
      </div>
      <div class="col-md-6 d-flex justify-content-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
          <i class="bi bi-plus-circle me-1"></i> Add Section
        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Section Name</th>
            <th>Grade Level</th>
            <th>Adviser</th>
            <th>Capacity</th>
            <th>Enrolled</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sections as $index => $sec)
            <tr>
              <td>{{ $sections->firstItem() + $index }}</td>
              <td class="fw-bold">{{ $sec->name }}</td>
              <td>{{ $sec->gradeLevel->name ?? 'N/A' }}</td>
              <td>
                {{ $sec->adviser?->profile?->first_name ?? 'N/A' }}
                {{ $sec->adviser?->profile?->last_name ?? '' }}
              </td>
              <td>{{ $sec->capacity ?? '∞' }}</td>
              <td>{{ $sec->enrollments->count() }}</td>
             <td>
                <div class="d-flex justify-content-center gap-1">
                <a href="{{ route('registrars.sections.subjects', ['id' => $sec->id]) }}" class="btn btn-sm btn-dark" title="Manage Subject Load">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </a>
                  <a href="{{ route('registrars.classlist', $sec->id) }}" class="btn btn-sm btn-info text-white">
                    <i class="bi bi-people"></i>
                  </a>
                  <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSectionModal{{ $sec->id }}">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteSectionModal{{ $sec->id }}">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>

            <!-- Edit Section Modal -->
            <div class="modal fade" id="editSectionModal{{ $sec->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form method="POST" action="{{ route('registrars.sections.update', $sec->id) }}">
                  @csrf @method('PUT')
                  <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                      <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Section</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Section Name</label>
                        <input type="text" name="name" value="{{ $sec->name }}" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Grade Level</label>
                        <select name="gradelevel_id" class="form-select" required>
                          @foreach($gradeLevels as $gl)
                            <option value="{{ $gl->id }}" {{ $sec->gradelevel_id == $gl->id ? 'selected' : '' }}>
                              {{ $gl->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">School Year</label>
                        <select name="school_year_id" class="form-select" required>
                          @foreach($schoolYears as $sy)
                            <option value="{{ $sy->id }}" {{ $sec->school_year_id == $sy->id ? 'selected' : '' }}>
                              {{ $sy->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Adviser</label>
                        <select name="adviser_id" class="form-select">
                          <option value="">-- None --</option>
                          @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ $sec->adviser_id == $t->id ? 'selected' : '' }}>
                              {{ $t->profile->first_name }} {{ $t->profile->last_name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Capacity</label>
                        <input type="number" name="capacity" value="{{ $sec->capacity }}" class="form-control" min="1">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button class="btn btn-warning text-white">Update</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteSectionModal{{ $sec->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body text-center">
                    <p>Are you sure you want to delete the section <strong class="text-danger">"{{ $sec->name }}"</strong>?</p>
                  </div>
                  <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('registrars.sections.destroy', $sec->id) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted">No sections available.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
      {{ $sections->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.sections.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Section</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Section Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Grade Level</label>
            <select name="gradelevel_id" class="form-select" required>
              <option value="">-- Select --</option>
              @foreach($gradeLevels as $gl)
                <option value="{{ $gl->id }}">{{ $gl->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">School Year</label>
            <select name="school_year_id" class="form-select" required>
              <option value="">-- Select School Year --</option>
              @foreach($schoolYears as $sy)
                <option value="{{ $sy->id }}">{{ $sy->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Adviser</label>
            <select name="adviser_id" class="form-select">
              <option value="">-- None --</option>
              @foreach($teachers as $t)
                <option value="{{ $t->id }}">
                  {{ $t->profile?->first_name ?? 'N/A' }} {{ $t->profile?->last_name ?? '' }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control" min="1">
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
