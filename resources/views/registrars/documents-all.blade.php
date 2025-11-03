@extends('layouts.registrar')

@section('title','Student Documents')
@section('header')
    <i class="bi bi-file-earmark-text-fill me-2"></i> Student Documents
@endsection

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-body">
    @include('partials.alerts')

    <form method="GET" action="{{ route('registrars.documents.all') }}" class="row g-2 mb-3">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search by student name..." value="{{ request('search') }}">
      </div>
      <div class="col-md-3">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
        <a href="{{ route('registrars.documents.all') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Student</th>
            <th>Document Type</th>
            <th>File</th>
            <th>Status</th>
            <th>Uploaded</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($documents as $index => $doc)
            <tr>
              <td>{{ $documents->firstItem() + $index }}</td>
              <td>
                {{ optional($doc->student->user->profile)->last_name ?? '' }},
                {{ optional($doc->student->user->profile)->first_name ?? '' }}
                {{ optional($doc->student->user->profile)->middle_name ?? '' }}.
              </td>
              <td>{{ $doc->type ?? $doc->document_type }}</td>
              <td>
                @if($doc->file_path)
                  <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-file-earmark-text"></i> View
                  </a>
                @else
                  <span class="text-muted">No file</span>
                @endif
              </td>
              <td>
                <span class="badge 
                  @if($doc->status === 'Verified') bg-success
                  @elseif($doc->status === 'Pending') bg-warning text-dark
                  @else bg-secondary @endif">
                  {{ $doc->status }}
                </span>
              </td>
              <td>{{ $doc->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>
              <td>
                {{-- Verify Button --}}
                @if($doc->status !== 'Verified')
                  <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $doc->id }}">
                    <i class="bi bi-check-circle"></i>
                  </button>
                @endif

                {{-- Delete Button --}}
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $doc->id }}">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>

            {{-- Verify Modal --}}
            <div class="modal fade" id="verifyModal{{ $doc->id }}" tabindex="-1" aria-labelledby="verifyModalLabel{{ $doc->id }}" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                  <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="verifyModalLabel{{ $doc->id }}">
                      <i class="bi bi-check-circle-fill me-2"></i> Verify Document
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to verify this document?
                    <div class="mt-2 text-muted small">
                      <strong>{{ optional($doc->student->user->profile)->last_name }}, {{ optional($doc->student->user->profile)->first_name }}</strong> — {{ $doc->type }}
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('registrars.documents.verify', $doc->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('PUT')
                      <button class="btn btn-success">Verify</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            {{-- Delete Modal --}}
            <div class="modal fade" id="deleteModal{{ $doc->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $doc->id }}" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                  <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel{{ $doc->id }}">
                      <i class="bi bi-trash-fill me-2"></i> Delete Document
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to delete this document? This action cannot be undone.
                    <div class="mt-2 text-muted small">
                      <strong>{{ optional($doc->student->user->profile)->last_name }}, {{ optional($doc->student->user->profile)->first_name }}</strong> — {{ $doc->type }}
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('registrars.documents.destroy', $doc->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-danger">Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

          @empty
            <tr><td colspan="7" class="text-center text-muted">No documents uploaded yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end">
      {{ $documents->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection
