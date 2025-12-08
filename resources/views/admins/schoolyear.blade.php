@extends('layouts.admin')

@section('title','School Year')
@section('header')
    <i class="bi bi-calendar-event me-2"></i> School Year Management
@endsection

@section('content')
<div class="container-fluid my-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      @include('partials.alerts')

      {{-- 🔍 Search & Filter --}}
      <form method="GET" class="row g-2 align-items-end mb-4">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Search</label>
          <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search school year...">
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
            <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-outline-primary">
            <i class="bi bi-search"></i> Search
          </button>
          <a href="{{ route('admins.schoolyear') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </a>
        </div>
        <div class="col-md-3 d-flex justify-content-end gap-2">
          <a href="{{ route('admins.schoolyear.archived') }}" class="btn btn-outline-dark">
            <i class="bi bi-archive me-1"></i> Archived
          </a>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSchoolYearModal">
            <i class="bi bi-plus-circle me-1"></i> Add
          </button>
        </div>
      </form>

      {{-- 📋 Table --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th>School Year</th>
              <th>Start</th>
              <th>End</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($schoolYears as $i => $sy)
              <tr class="{{ $sy->status === 'active' ? 'table-success' : '' }}">
                <td>{{ $schoolYears->firstItem() + $i }}</td>
                <td>{{ $sy->name ?? ($sy->start_date.' - '.$sy->end_date) }}</td>
                <td>{{ $sy->start_date }}</td>
                <td>{{ $sy->end_date }}</td>
                <td>
                  <span class="badge rounded-pill bg-{{ $sy->status=='active'?'success':'secondary' }}">
                    {{ ucfirst($sy->status) }}
                  </span>
                </td>
                <td>
                  <div class="d-flex justify-content-center gap-2 flex-wrap">
                    {{-- ✏ Edit --}}
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editSchoolYearModal{{ $sy->id }}">
                      <i class="bi bi-pencil"></i>
                    </button>

                    {{-- 🗃 Archive (only if closed) --}}
                    @if($sy->status !== 'active')
                      <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#archiveSchoolYearModal{{ $sy->id }}">
                        <i class="bi bi-archive"></i>
                      </button>
                    @endif

                    {{-- ✅ Activate / ❌ Close --}}
                    @if($sy->status !== 'active')
                      <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateSchoolYearModal{{ $sy->id }}">
                        <i class="bi bi-check-circle"></i>
                      </button>
                    @else
                      <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#closeSchoolYearModal{{ $sy->id }}">
                        <i class="bi bi-x-circle"></i>
                      </button>
                    @endif
                  </div>
                </td>
              </tr>

              {{-- 🟡 Edit Modal --}}
              <div class="modal fade" id="editSchoolYearModal{{ $sy->id }}" tabindex="-1">
                <div class="modal-dialog">
                  <form method="POST" action="{{ route('admins.schoolyear.update',$sy->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-content">
                      <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="bi bi-pencil me-2"></i> Edit School Year</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label">Start Date</label>
                          <input type="date" name="start_date" class="form-control" value="{{ $sy->start_date }}" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">End Date</label>
                          <input type="date" name="end_date" class="form-control" value="{{ $sy->end_date }}" required>
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

              {{-- 🗃 Archive Modal --}}
              <div class="modal fade" id="archiveSchoolYearModal{{ $sy->id }}" tabindex="-1">
                <div class="modal-dialog">
                  <form method="POST" action="{{ route('admins.schoolyear.archive',$sy->id) }}">
                    @csrf @method('DELETE')
                    <div class="modal-content">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="bi bi-archive me-2"></i> Archive School Year</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        Are you sure you want to archive 
                        <strong>{{ $sy->name ?? $sy->start_date.' - '.$sy->end_date }}</strong>?
                        You can restore it anytime from the archived list.
                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger">Archive</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              {{-- 🟢 Activate Modal --}}
              <div class="modal fade" id="activateSchoolYearModal{{ $sy->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                      <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i> Activate School Year</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      Are you sure you want to <strong>activate</strong> 
                      <span class="text-success">{{ $sy->name }}</span>?  
                      This will automatically close the currently active school year.
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <form method="POST" action="{{ route('admins.schoolyear.activate',$sy->id) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">Activate</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              {{-- 🔴 Close School Year Modal --}}
              <div class="modal fade" id="closeSchoolYearModal{{ $sy->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                      <h5 class="modal-title">
                        <i class="bi bi-x-circle me-2"></i> Close School Year
                      </h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      Are you sure you want to <strong>close</strong> 
                      <span class="text-danger">{{ $sy->name }}</span>?  
                      <br><br>
                      <small class="text-muted">
                        This will mark all current enrollments under this school year as "Inactive".
                      </small>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                      <form method="POST" action="{{ route('admins.schoolyear.close', $sy->id) }}">
                        @csrf
                        <button class="btn btn-danger">Close</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-3">
                  <i class="bi bi-info-circle"></i> No school years found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div class="d-flex justify-content-end mt-3">
        {{ $schoolYears->appends(request()->query())->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>

{{-- ➕ Add School Year Modal --}}
<div class="modal fade" id="addSchoolYearModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admins.schoolyear.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i> Add School Year</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input type="checkbox" name="set_active" id="set_active" class="form-check-input">
              <label for="set_active" class="form-check-label">Set as Active</label>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Curriculum</label>
            <select name="curriculum_id" class="form-select">
              <option value="">-- Select Curriculum (Optional) --</option>
              @foreach($curriculumTemplates as $curriculum)
                <option value="{{ $curriculum->id }}">{{ $curriculum->name }}</option>
              @endforeach
            </select>
            <small class="text-muted d-block mt-1">Select a curriculum template to apply to this school year</small>
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
