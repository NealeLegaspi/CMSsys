@extends('layouts.registrar')

@section('title','School Year')
@section('header','School Year')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">📅 School Years</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSchoolYearModal">
      <i class="bi bi-plus-circle me-1"></i> Add School Year
    </button>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <table class="table table-bordered table-striped align-middle">
      <thead class="table-primary">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Start</th>
          <th>End</th>
          <th>Status</th>
          <th width="120">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($schoolYears as $i => $sy)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $sy->name ?? $sy->start_date.' - '.$sy->end_date }}</td>
          <td>{{ $sy->start_date }}</td>
          <td>{{ $sy->end_date }}</td>
          <td>
            <span class="badge bg-{{ $sy->status === 'active' ? 'success' : 'secondary' }}">
              {{ ucfirst($sy->status) }}
            </span>
          </td>
          <td>
            @if($sy->status === 'active')
              <form action="{{ route('registrars.schoolyear.close',$sy->id) }}" method="POST">
                @csrf
                <button class="btn btn-sm btn-warning">Close</button>
              </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted">No school years yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="mt-3">{{ $schoolYears->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add School Year Modal -->
<div class="modal fade" id="addSchoolYearModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.schoolyear.store') }}">
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
