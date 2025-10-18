@extends('layouts.registrar')

@section('title', isset($student) ? 'Documents - '.$student->user->profile->last_name : 'Student Documents')
@section('header')
    <i class="bi bi-file-earmark-text-fill me-2"></i> 
    @if(isset($student))
      Documents — {{ optional($student->user->profile)->last_name ?? '' }}, {{ optional($student->user->profile)->first_name ?? '' }}
    @else
      Student Documents
    @endif
@endsection

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h6 class="fw-bold mb-0">
      📁
      @if(isset($student))
        Documents — {{ optional($student->user->profile)->last_name ?? '' }}, {{ optional($student->user->profile)->first_name ?? '' }}
      @else
        Student Documents
      @endif
    </h6>

    @if(isset($student))
      <a href="{{ route('registrars.documents.all') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    @endif
  </div>

  <div class="card-body">
    @include('partials.alerts')

    @if(isset($student))
      <!-- Upload Form -->
      <form method="POST" action="{{ route('registrars.documents.store', $student->id) }}" enctype="multipart/form-data" class="row g-3 mb-4">
        @csrf
        <div class="col-md-5">
          <label class="form-label fw-bold">Document Type <span class="text-danger">*</span></label>
          <input type="text" name="type" class="form-control" placeholder="e.g., Birth Certificate" required>
        </div>
        <div class="col-md-5">
          <label class="form-label fw-bold">Upload File <span class="text-danger">*</span></label>
          <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" class="form-control" required>
        </div>
        <div class="col-md-2 d-grid align-items-end">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-upload"></i> Upload
          </button>
        </div>
      </form>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Student</th>
            <th>Document Type</th>
            <th>File</th>
            <th>Status</th>
            <th>Uploaded On</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($documents as $index => $doc)
            <tr>
              <td>{{ $loop->iteration + (isset($documents) && method_exists($documents,'firstItem') ? $documents->firstItem()-1 : 0) }}</td>
              <td>
                {{ optional($doc->student->user->profile)->last_name ?? 'N/A' }},
                {{ optional($doc->student->user->profile)->first_name ?? '' }}
              </td>
              <td>{{ $doc->type ?? $doc->document_type ?? 'N/A' }}</td>
              <td>
                @if($doc->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($doc->file_path))
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
                  @elseif($doc->status === 'Pending' || $doc->status === 'Submitted') bg-warning text-dark
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
            <tr>
              <td colspan="7" class="text-center text-muted">No documents uploaded yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if(isset($documents) && method_exists($documents,'links'))
      <div class="d-flex justify-content-end mt-3">
        {{ $documents->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
</div>
@endsection
