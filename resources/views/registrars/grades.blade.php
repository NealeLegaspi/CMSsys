@extends('layouts.registrar')

@section('title','Grade Submissions')
@section('header')
  <i class="bi bi-clipboard-check me-2"></i> Grade Submissions
@endsection

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-body">
    @include('partials.alerts')

    <!-- Filters -->
    <form method="GET" action="{{ route('registrars.grades') }}" class="row g-2 mb-3">
      <div class="col-md-6">
        <input type="text" name="search" class="form-control" 
              placeholder="Search by teacher or subject"
              value="{{ request('search') }}">
      </div>
      <div class="col-md-6">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> Search
        </button>
        <a href="{{ route('registrars.grades') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-clockwise"></i> Reset
        </a>
      </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Teacher</th>
            <th>Subject</th>
            <th>Section</th>
            <th>Quarter</th>
            <th>Status</th>
            <th width="150" class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($assignments as $index => $assignment)
          <tr>
            <td>{{ $assignments->firstItem() + $index }}</td>
            <td>{{ $assignment->teacher->full_name ?? 'N/A' }}</td>
            <td>{{ $assignment->subject->name ?? 'N/A' }}</td>
            <td>{{ $assignment->section->name ?? 'N/A' }}</td>
            <td>{{ $assignment->quarter ?? 'All' }}</td>
            <td>
              @php $status = $assignment->grade_status ?? 'draft'; @endphp
              @switch($status)
                  @case('draft')
                      <span class="badge bg-secondary">Draft</span>
                      @break
                  @case('submitted')
                      <span class="badge bg-info text-dark">Submitted</span>
                      @break
                  @case('returned')
                      <span class="badge bg-warning text-dark">Returned</span>
                      @break
                  @case('approved')
                      <span class="badge bg-success">Approved</span>
                      @break
                  @default
                      <span class="badge bg-light text-dark">Unknown</span>
              @endswitch
            </td>
            <td class="text-center">
              <a href="{{ route('registrars.viewSubmission', $assignment->id) }}" 
                 class="btn btn-sm btn-info text-white">
                <i class="bi bi-eye"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              <i class="bi bi-inbox me-2"></i> No grade submissions found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
      {{ $assignments->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection
