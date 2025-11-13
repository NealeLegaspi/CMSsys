@extends('layouts.registrar')

@section('title', 'Documents & Certificates Management')
@section('header')
    <i class="bi bi-file-earmark-ruled-fill me-2"></i> Documents & Certificates
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        @include('partials.alerts')

        <ul class="nav nav-tabs mb-4" id="documentCertTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents-pane" type="button" role="tab" aria-controls="documents-pane" aria-selected="true">
                    <i class="bi bi-file-earmark-text me-1"></i> Student Documents
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates-pane" type="button" role="tab" aria-controls="certificates-pane" aria-selected="false">
                    <i class="bi bi-award me-1"></i> Certificates & Issuance
                </button>
            </li>
        </ul>

        <div class="tab-content" id="documentCertTabsContent">

            <div class="tab-pane fade show active" id="documents-pane" role="tabpanel" aria-labelledby="documents-tab" tabindex="0">

                @php
                    $syClosed = !$currentSY; // true if no active school year
                @endphp

                <form method="GET" action="{{ route('registrars.documents.all') }}" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by student name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
                        <a href="{{ route('registrars.documents.all') }}?tab=documents" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                    </div>
                </form>

                @if($syClosed)
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Cannot upload or manage documents because there is no active school year.
                    </div>
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
                                        {{-- Verify Button --}}
                                        @if($doc->status !== 'Verified')
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $doc->id }}"
                                                @if($syClosed) disabled title="Cannot verify. No active school year." @endif>
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        @endif

                                        {{-- Delete Button --}}
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDocModal{{ $doc->id }}"
                                            @if($syClosed) disabled title="Cannot delete. No active school year." @endif>
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
                                                    <button class="btn btn-success" @if($syClosed) disabled @endif>Verify</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Delete Modal --}}
                                <div class="modal fade" id="deleteDocModal{{ $doc->id }}" tabindex="-1" aria-labelledby="deleteDocModalLabel{{ $doc->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="deleteDocModalLabel{{ $doc->id }}">
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
                                                    <button class="btn btn-danger" @if($syClosed) disabled @endif>Delete</button>
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

            <!-- Upload Document Modal -->
            <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('registrars.documents.store', 0) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2"></i> Upload Document</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Student</label>
                                    <select name="student_id" class="form-select" required @if($syClosed) disabled @endif>
                                        <option value="">-- Select Student --</option>
                                        @foreach($students as $s)
                                            <option value="{{ $s->id }}">
                                                {{ $s->user->profile->last_name }}, {{ $s->user->profile->first_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Document Type</label>
                                    <select name="type" class="form-select" required @if($syClosed) disabled @endif>
                                        <option value="">-- Select Document Type --</option>
                                        <option value="Birth Certificate">Birth Certificate</option>
                                        <option value="Form 137">Form 137</option>
                                        <option value="Good Moral">Good Moral</option>
                                        <option value="Report Card">Report Card</option>
                                        <option value="Transfer Credentials">Transfer Credentials</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">File</label>
                                    <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required @if($syClosed) disabled @endif>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary" @if($syClosed) disabled @endif>Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>



            <div class="tab-pane fade" id="certificates-pane" role="tabpanel" aria-labelledby="certificates-tab" tabindex="0">

                @php
                    $syClosed = !$currentSY; // true if no active school year
                @endphp

                <form method="GET" action="{{ route('registrars.certificates') }}" class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by student name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
                        <a href="{{ route('registrars.certificates') }}?tab=certificates" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                    </div>
                    <div class="col-md-5 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#issueCertificateModal"
                            @if($syClosed) disabled title="Cannot issue certificate. No active school year." @endif>
                            <i class="bi bi-plus-circle me-1"></i> Issue Certificate
                        </button>
                    </div>
                </form>

                @if($syClosed)
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Cannot issue certificates because there is no active school year.
                    </div>
                @endif

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
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteCertModal{{ $cert->id }}"
                                            @if($syClosed) disabled title="Cannot delete. No active school year." @endif>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="deleteCertModal{{ $cert->id }}" tabindex="-1" aria-hidden="true">
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
                                                    @csrf
                                                    @method('DELETE')
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
                        <select name="student_id" class="form-select" required @if($syClosed) disabled @endif>
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
                        <select name="type" class="form-select" required @if($syClosed) disabled @endif>
                            <option value="">-- Select Type --</option>
                            <option value="Enrollment">Certificate of Enrollment</option>
                            <option value="Good Moral">Certificate of Good Moral Character</option>
                            <option value="Completion">Certificate of Completion</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purpose / Remarks (optional)</label>
                        <input type="text" name="purpose" class="form-control" placeholder="For employment, transfer, etc." @if($syClosed) disabled @endif>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" @if($syClosed) disabled @endif>Issue</button>
                </div>
            </div>
        </form>
    </div>
</div>

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
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    
    if (activeTab === 'certificates') {
        const certificatesTab = document.getElementById('certificates-tab');
        if (certificatesTab) {
            new bootstrap.Tab(certificatesTab).show();
        }
    } else {
        const documentsTab = document.getElementById('documents-tab');
        if (documentsTab) {
            new bootstrap.Tab(documentsTab).show();
        }
    }
});
</script>
@endpush