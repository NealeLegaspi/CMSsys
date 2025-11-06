@extends('layouts.admin')

@section('title','Archived School Years')
@section('header')
  <i class="bi bi-archive me-2"></i> Archived School Years
@endsection

@section('content')
<div class="container-fluid my-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      @include('partials.alerts')

      {{-- 🔙 Back Button --}}
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-secondary mb-0">Archived School Year List</h5>
        <a href="{{ route('admins.schoolyear') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Back to School Years
        </a>
      </div>

      {{-- 🔍 Search --}}
      <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Search</label>
          <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search school year...">
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-outline-primary">
            <i class="bi bi-search"></i> Search
          </button>
          <a href="{{ route('admins.schoolyear.archived') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </a>
        </div>
      </form>

      {{-- 📋 Table --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead class="table-secondary">
            <tr>
              <th>#</th>
              <th>School Year</th>
              <th>Start</th>
              <th>End</th>
              <th>Status Before Archive</th>
              <th>Archived At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($schoolYears as $i => $sy)
              <tr>
                <td>{{ $schoolYears->firstItem() + $i }}</td>
                <td class="fw-semibold">{{ $sy->name ?? ($sy->start_date.' - '.$sy->end_date) }}</td>
                <td>{{ $sy->start_date }}</td>
                <td>{{ $sy->end_date }}</td>
                <td>
                  <span class="badge rounded-pill bg-{{ $sy->status=='active'?'success':'secondary' }}">
                    {{ ucfirst($sy->status) }}
                  </span>
                </td>
                <td>{{ $sy->deleted_at->format('M d, Y') }}</td>
                <td>
                  <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#restoreModal{{ $sy->id }}">
                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                  </button>
                </td>
              </tr>

              {{-- ♻️ Restore Modal --}}
              <div class="modal fade" id="restoreModal{{ $sy->id }}" tabindex="-1">
                <div class="modal-dialog">
                  <form method="POST" action="{{ route('admins.schoolyear.restore', $sy->id) }}">
                    @csrf @method('PATCH')
                    <div class="modal-content">
                      <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="bi bi-arrow-counterclockwise me-2"></i> Restore School Year</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        Are you sure you want to <strong>restore</strong> 
                        <span class="text-success">{{ $sy->name ?? $sy->start_date.' - '.$sy->end_date }}</span>?  
                        This will make it visible again in the active list.
                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-success">Restore</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-3">
                  <i class="bi bi-info-circle"></i> No archived school years found.
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
@endsection
