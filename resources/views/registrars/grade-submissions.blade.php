@extends('layouts.registrar')

@section('title', 'Grade Submissions')
@section('header')
    <i class="bi bi-file-earmark-text me-2"></i> Grade Submissions
@endsection

@section('content')
<div class="container-fluid my-4">

    @include('partials.alerts')

    <div class="d-flex flex-wrap gap-3 mb-3">
        <div class="badge bg-info text-dark fs-6 shadow-sm p-2 px-3">
            <i class="bi bi-upload me-1"></i> Submitted: {{ $summary['submitted'] ?? 0 }}
        </div>
        <div class="badge bg-success fs-6 shadow-sm p-2 px-3">
            <i class="bi bi-check-circle me-1"></i> Approved: {{ $summary['approved'] ?? 0 }}
        </div>
        <div class="badge bg-warning text-dark fs-6 shadow-sm p-2 px-3">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Returned: {{ $summary['returned'] ?? 0 }}
        </div>
        <div class="badge bg-secondary fs-6 shadow-sm p-2 px-3">
            <i class="bi bi-list-task me-1"></i> Total: {{ $summary['total'] ?? 0 }}
        </div>
    </div>

    <form method="GET" action="{{ route('registrars.gradeSubmissions') }}" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by teacher or subject"
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-search"></i> Filter
            </button>
            <a href="{{ route('registrars.gradeSubmissions') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Subject</th>
                        <th>Section</th>
                        <th>Teacher</th>
                        <th>Status</th>
                        <th>Submitted On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $s)
                        <tr>
                            <td>{{ $s->subject_name }}</td>
                            <td>{{ $s->section_name }}</td>
                            <td>{{ $s->teacher_name }}</td>
                            <td>
                                <span class="badge bg-{{ 
                                    $s->grade_status == 'approved' ? 'success' :
                                    ($s->grade_status == 'submitted' ? 'info' :
                                    ($s->grade_status == 'returned' ? 'warning' : 'secondary'))
                                }}">
                                    {{ ucfirst($s->grade_status) }}
                                </span>
                            </td>
                            <td>
                                {{ $s->updated_at ? \Carbon\Carbon::parse($s->updated_at)->format('M d, Y h:i A') : '—' }}
                            </td>
                            <td>
                                <a href="{{ route('registrar.viewSubmission', $s->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                   <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                No grade submissions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="confirmTitle"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="confirmMessage"></div>
      <div class="modal-footer">
        <form id="confirmForm" method="POST">
          @csrf
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="confirmBtn">Confirm</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function openConfirmModal(actionUrl, title, message, buttonText, buttonClass) {
    const modal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
    document.getElementById('confirmForm').action = actionUrl;
    document.getElementById('confirmTitle').innerText = title;
    document.getElementById('confirmMessage').innerText = message;
    const btn = document.getElementById('confirmBtn');
    btn.innerText = buttonText;
    btn.className = 'btn ' + buttonClass;
    modal.show();
}
</script>
@endsection
