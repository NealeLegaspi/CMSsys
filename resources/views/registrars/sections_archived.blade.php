@extends('layouts.registrar')

@section('title', 'Archived Sections')
@section('header')
  <i class="bi bi-archive me-2"></i> Archived Sections
@endsection

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-body">
    @include('partials.alerts')

    <!-- Header + Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-bold text-secondary mb-0">Archived Sections List</h5>
      <a href="{{ route('registrars.sections') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Sections
      </a>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center">
        <thead class="table-secondary">
          <tr>
            <th>#</th>
            <th>Section Name</th>
            <th>Grade Level</th>
            <th>Adviser</th>
            <th>School Year</th>
            <th>Archived At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sections as $i => $section)
          <tr>
            <td>{{ $sections->firstItem() + $i }}</td>
            <td class="fw-bold text-secondary">{{ $section->name }}</td>
            <td>{{ $section->gradeLevel->name ?? 'N/A' }}</td>
            <td>
              {{ $section->adviser?->profile?->first_name ?? 'N/A' }}
              {{ $section->adviser?->profile?->last_name ?? '' }}
            </td>
            <td>{{ $section->schoolYear->name ?? 'N/A' }}</td>
            <td>{{ $section->deleted_at->format('M d, Y') }}</td>
            <td>
              <button type="button"
                      class="btn btn-sm btn-success"
                      data-bs-toggle="modal"
                      data-bs-target="#restoreSectionModal{{ $section->id }}">
                <i class="bi bi-arrow-counterclockwise"></i> Restore
              </button>
            </td>
          </tr>

          <!-- Restore Confirmation Modal -->
          <div class="modal fade" id="restoreSectionModal{{ $section->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header bg-success text-white">
                  <h5 class="modal-title">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Restore Section
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                  <p>Are you sure you want to restore the section 
                    <strong class="text-success">"{{ $section->name }}"</strong>?
                  </p>
                </div>
                <div class="modal-footer justify-content-center">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                  <form action="{{ route('registrars.sections.restore', $section->id) }}" method="POST" class="d-inline">
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
            <td colspan="7" class="text-center text-muted py-4">
              <i class="bi bi-info-circle"></i> No archived sections found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-end mt-3">
      {{ $sections->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection
