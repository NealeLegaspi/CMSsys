@extends('layouts.registrar')

@section('title', 'Archived Enrollments')
@section('header')
  <i class="bi bi-archive me-2"></i> Archived Enrollments
@endsection

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-body">
    @include('partials.alerts')

    <!-- Header + Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-bold text-secondary mb-0">Archived Enrollment Records</h5>
      <a href="{{ route('registrars.enrollment') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Active
      </a>
    </div>

    <!-- Search -->
    <form method="GET" class="d-flex justify-content-start mb-3">
      <input type="text" name="search" class="form-control me-2 w-25"
             placeholder="Search student..." value="{{ $search }}">
      <button class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
    </form>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-secondary">
          <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Section</th>
            <th>School Year</th>
            <th>Archived At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($archived as $i => $enrollment)
          <tr>
            <td>{{ $archived->firstItem() + $i }}</td>
            <td class="fw-bold text-secondary">{{ $enrollment->student->user->profile->full_name ?? 'N/A' }}</td>
            <td>{{ $enrollment->section->name ?? 'N/A' }}</td>
            <td>{{ $enrollment->schoolYear->name ?? 'N/A' }}</td>
            <td>{{ $enrollment->deleted_at?->format('M d, Y') ?? 'N/A' }}</td>
            <td>
              <button type="button"
                      class="btn btn-sm btn-success"
                      data-bs-toggle="modal"
                      data-bs-target="#restoreEnrollmentModal{{ $enrollment->id }}">
                <i class="bi bi-arrow-counterclockwise"></i> Restore
              </button>
            </td>
          </tr>

          <!-- Restore Confirmation Modal -->
          <div class="modal fade" id="restoreEnrollmentModal{{ $enrollment->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header bg-success text-white">
                  <h5 class="modal-title">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Restore Enrollment
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                  <p>
                    Are you sure you want to restore the enrollment record for 
                    <strong class="text-success">{{ $enrollment->student->user->profile->full_name ?? 'N/A' }}</strong>?
                  </p>
                </div>
                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                  <form action="{{ route('registrars.enrollment.restore', $enrollment->id) }}" method="POST" class="d-inline">
                    @csrf @method('PUT')
                    <button type="submit" class="btn btn-success">Restore</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- End Restore Modal -->

          @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">
              <i class="bi bi-info-circle"></i> No archived enrollments found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-end mt-3">
      {{ $archived->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection
