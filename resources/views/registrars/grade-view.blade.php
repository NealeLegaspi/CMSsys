@extends('layouts.registrar')

@section('title', 'Review Grades')
@section('header')
  <i class="bi bi-check2-square me-2"></i> Review Grades
@endsection

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-body">
    @include('partials.alerts')

    <!-- Back Button -->
    <div class="mb-3">
      <a href="{{ route('registrars.grades') }}" class="btn btn-light border d-inline-flex align-items-center">
        <i class="bi bi-arrow-left me-2"></i> Back to Grade Submissions
      </a>
    </div>

    <!-- Header Info -->
    <div class="alert alert-primary d-flex justify-content-between align-items-center mb-4">
      <div>
        <strong>Subject:</strong> {{ $subject->name }} <br>
        <strong>Section:</strong> {{ $section->name }}
      </div>
      <span class="badge bg-info text-dark px-3 py-2">
        {{ strtoupper($assignment->grade_status ?? 'DRAFT') }}
      </span>
    </div>

    <!-- Grades Table -->
    <div class="table-responsive mb-4">
      <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th class="text-start ps-3">Student Name</th>
            <th>1st</th>
            <th>2nd</th>
            <th>3rd</th>
            <th>4th</th>
            <th>Final</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $s)
            @php
              $grades = [];
              foreach (['1st','2nd','3rd','4th','Final'] as $q) {
                  $grades[$q] = $s->grades->firstWhere('quarter', $q)?->grade;
              }
              $valid = array_filter($grades, fn($g) => is_numeric($g));
              $final = count($valid) == 4 ? round(array_sum($valid) / 4) : null;
            @endphp

            <tr>
              <td class="text-start ps-3">
                {{ optional($s->user->profile)->last_name ?? 'N/A' }},
                {{ optional($s->user->profile)->first_name ?? '' }}
                {{ optional($s->user->profile)->middle_name ?? ''}}.
              </td>
              @foreach(['1st','2nd','3rd','4th'] as $q)
                <td>{{ $grades[$q] ?? '-' }}</td>
              @endforeach
              <td class="fw-bold {{ $final >= 75 ? 'text-success' : 'text-danger' }}">
                {{ $final ?? '-' }}
              </td>
              <td>
                @if ($final)
                  <span class="badge rounded-pill px-3 py-2 {{ $final >= 75 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                    {{ $final >= 75 ? 'PASSED' : 'FAILED' }}
                  </span>
                @else
                  <span class="text-muted">–</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">
                <i class="bi bi-inbox me-2"></i> No students found for this section.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-end gap-2">
      <button type="button" class="btn btn-outline-warning px-4" data-bs-toggle="modal" data-bs-target="#returnModal">
        <i class="bi bi-arrow-counterclockwise me-1"></i> Return to Teacher
      </button>

      <button type="button" class="btn btn-outline-success px-4" data-bs-toggle="modal" data-bs-target="#approveModal">
        <i class="bi bi-check-circle me-1"></i> Approve Grades
      </button>
    </div>
  </div>
</div>

<!-- Return to Teacher Modal -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-sm">
      <div class="modal-header bg-warning text-white border-0">
        <h5 class="modal-title fw-semibold" id="returnModalLabel">
          <i class="bi bi-arrow-counterclockwise me-2"></i> Return to Teacher
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to return these grades to the teacher for revision?
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('registrars.updateStatus', $assignment->id) }}">
          @csrf
          @method('PUT')
          <input type="hidden" name="status" value="returned">
          <button type="submit" class="btn btn-warning">Confirm</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Approve Grades Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-sm">
      <div class="modal-header bg-success text-white border-0">
        <h5 class="modal-title fw-semibold" id="approveModalLabel">
          <i class="bi bi-check-circle me-2"></i> Approve
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Approving these grades will finalize them and lock further edits. Proceed?
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="{{ route('registrars.updateStatus', $assignment->id) }}">
          @csrf
          @method('PUT')
          <input type="hidden" name="status" value="approved">
          <button type="submit" class="btn btn-success">Confirm</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
