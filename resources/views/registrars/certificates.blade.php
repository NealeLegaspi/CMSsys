@extends('layouts.registrar')

@section('title','Certificates & Document Issuance')
@section('header')
  <i class="bi bi-award-fill me-2"></i> Certificates & Document Issuance
@endsection

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-body">
    @include('partials.alerts')

    <form method="GET" action="{{ route('registrars.certificates') }}" class="row g-2 mb-3">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search by student name..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('registrars.certificates') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
      </div>
      <div class="col-md-5 d-flex justify-content-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issueCertificateModal">
          <i class="bi bi-plus-circle me-1"></i> Issue Certificate
        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Student</th>
            <th>Type</th>
            <th>Purpose</th>
            <th>Issued On</th>
            <th>File</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($certificates as $index => $cert)
            <tr>
              <td>{{ $certificates->firstItem() + $index }}</td>
              <td>{{ $cert->student->user->profile->last_name }}, {{ $cert->student->user->profile->first_name }}</td>
              <td>{{ $cert->type }}</td>
              <td>{{ $cert->purpose ?? 'N/A' }}</td>
              <td>{{ $cert->created_at->format('M d, Y') }}</td>
              <td>
                @if($cert->file_path)
                  <a href="{{ asset('storage/' . $cert->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-file-earmark-pdf"></i> View
                  </a>
                @else
                  <span class="text-muted">No file</span>
                @endif
              </td>
              <td>
                <!-- Delete Button -->
                <button 
                  type="button" 
                  class="btn btn-sm btn-danger" 
                  data-bs-toggle="modal" 
                  data-bs-target="#deleteModal{{ $cert->id }}">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal{{ $cert->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to delete the certificate for 
                    <strong>{{ $cert->student->user->profile->last_name }}, {{ $cert->student->user->profile->first_name }}</strong>?
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('registrars.certificates.destroy', $cert->id) }}" method="POST" class="d-inline">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          @empty
            <tr><td colspan="7" class="text-center text-muted">No certificates issued yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end">
      {{ $certificates->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

<!-- Issue Certificate Modal -->
<div class="modal fade" id="issueCertificateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.certificates.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-award me-2"></i> Issue Certificate</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Student</label>
            <select name="student_id" class="form-select" required>
              <option value="">-- Select Student --</option>
              @foreach($students as $s)
                <option value="{{ $s->id }}">
                  {{ $s->user->profile->last_name }}, {{ $s->user->profile->first_name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Certificate Type</label>
            <select name="type" class="form-select" required>
              <option value="">-- Select Type --</option>
              <option value="Enrollment">Certificate of Enrollment</option>
              <option value="Good Moral">Certificate of Good Moral Character</option>
              <option value="Completion">Certificate of Completion</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Purpose / Remarks (optional)</label>
            <input type="text" name="purpose" class="form-control" placeholder="For employment, transfer, etc.">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Issue</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- PDF Loading Modal -->
<div class="modal fade" id="loadingPdfModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center py-4">
      <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
      <h6>Generating PDF... Please wait</h6>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function openPdf(url) {
  const loadingModal = new bootstrap.Modal(document.getElementById('loadingPdfModal'));
  loadingModal.show();

  const newTab = window.open(url, '_blank');

  // Close loading modal after 2 seconds
  setTimeout(() => loadingModal.hide(), 2000);
}
</script>
@endpush
