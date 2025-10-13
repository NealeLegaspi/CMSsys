@extends('layouts.registrar')

@section('title','Student Documents')
@section('header','Student Documents')

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
  <h6 class="fw-bold mb-0">
    <i class="bi bi-file-earmark-text-fill me-2"></i> Student Documents
  </h6>
</div>


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
                @if($doc->status !== 'Verified')
                  <form action="{{ route('registrars.documents.verify', $doc->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button class="btn btn-sm btn-success" onclick="return confirm('Verify this document?')">
                      <i class="bi bi-check-circle"></i>
                    </button>
                  </form>
                @endif

                <form action="{{ route('registrars.documents.destroy', $doc->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this document?')">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
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
