@extends('layouts.registrar')

@section('title', 'Student Record')
@section('header')
    <i class="bi bi-journal-bookmark-fill me-2"></i> Student Record
@endsection

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h6 class="fw-bold mb-0">
      {{ $student->user->profile->first_name ?? '' }} {{ $student->user->profile->middle_name ?? '' }} {{ $student->user->profile->last_name ?? '' }}
      <span class="text-muted small">({{ $student->student_number ?? 'N/A' }})</span>
    </h6>
    <div>
      <a href="{{ route('registrars.student.record.pdf', $student->id) }}" class="btn btn-sm btn-danger">
        <i class="bi bi-file-pdf"></i> Export PDF
      </a>
      <a href="{{ route('registrars.enrollment') }}" class="btn btn-sm btn-secondary">
        <i class="bi bi-arrow-left"></i> Back
      </a>
    </div>
  </div>

  <div class="card-body">
    <!-- Student Profile -->
    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-circle"></i> Profile Information</h6>
    <div class="row mb-4">
      <div class="col-md-3 text-center">
        <img src="{{ $student->user->profile->profile_picture 
                    ? asset('storage/' . $student->user->profile->profile_picture) 
                    : asset('images/default.png') }}"
             class="rounded-circle border mb-2" width="120" height="120" style="object-fit: cover;">
        <div class="fw-bold">{{ $student->user->email }}</div>
      </div>
      <div class="col-md-9">
        <div class="row">
          <div class="col-md-4"><strong>First Name:</strong><br>{{ $student->user->profile->first_name ?? 'N/A' }}</div>
          <div class="col-md-4"><strong>Middle Name:</strong><br>{{ $student->user->profile->middle_name ?? 'N/A' }}</div>
          <div class="col-md-4"><strong>Last Name:</strong><br>{{ $student->user->profile->last_name ?? 'N/A' }}</div>
          <div class="col-md-4 mt-3"><strong>Gender:</strong><br>{{ $student->user->profile->sex ?? 'N/A' }}</div>
          <div class="col-md-4 mt-3"><strong>Contact:</strong><br>{{ $student->user->profile->contact_number ?? 'N/A' }}</div>
          <div class="col-md-4 mt-3"><strong>Address:</strong><br>{{ $student->user->profile->address ?? 'N/A' }}</div>
        </div>
      </div>
    </div>

    <hr>

    <!-- Enrollment Info -->
    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-mortarboard"></i> Enrollment History</h6>
    <div class="table-responsive mb-4">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>School Year</th>
            <th>Grade Level</th>
            <th>Section</th>
            <th>Status</th>
            <th>Enrolled By</th>
          </tr>
        </thead>
        <tbody>
          @forelse($student->enrollments as $enroll)
            <tr>
              <td>{{ $enroll->schoolYear->name ?? 'N/A' }}</td>
              <td>{{ $enroll->section->gradeLevel->name ?? 'N/A' }}</td>
              <td>{{ $enroll->section->name ?? 'N/A' }}</td>
              <td>
                <span class="badge 
                  @if($enroll->status === 'enrolled') bg-success
                  @elseif($enroll->status === 'pending') bg-warning text-dark
                  @else bg-secondary @endif">
                  {{ ucfirst($enroll->status) }}
                </span>
              </td>
              <td>
                @php
                  $registrarProfile = $enroll->createdBy->profile ?? null;
                @endphp
                {{ $registrarProfile ? ($registrarProfile->first_name . ' ' . ($registrarProfile->middle_name ? $registrarProfile->middle_name . ' ' : '') . $registrarProfile->last_name) : 'N/A' }}
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted">No enrollment records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <hr>

    <!-- Documents -->
    <h6 class="fw-bold text-primary mb-3">
      <i class="bi bi-folder2"></i>
      Submitted Documents
      @if($currentSY)
        <span class="badge bg-success ms-2">{{ $currentSY->name }}</span>
      @endif
    </h6>
    <div class="table-responsive mb-4">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Type</th>
            <th>Status</th>
            <th>File</th>
            <th>Uploaded</th>
          </tr>
        </thead>
        <tbody>
          @forelse($documents as $doc)
            <tr>
              <td>{{ $doc->type ?? $doc->document_type ?? 'N/A' }}</td>
              <td>
                <span class="badge 
                  @if($doc->status === 'Verified') bg-success
                  @elseif($doc->status === 'Pending') bg-warning text-dark
                  @else bg-secondary @endif">
                  {{ $doc->status }}
                </span>
              </td>
              <td>
                @if($doc->file_path)
                  <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-file-earmark-text"></i> View
                  </a>
                @else
                  <span class="text-muted">No file</span>
                @endif
              </td>
              <td>{{ $doc->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">No documents uploaded.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <hr>

    <!-- Certificates -->
    <h6 class="fw-bold text-primary mb-3">
      <i class="bi bi-award"></i>
      Issued Certificates
      @if($currentSY)
        <span class="badge bg-success ms-2">{{ $currentSY->name }}</span>
      @endif
    </h6>
    <div class="table-responsive mb-4">
      <table class="table table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Type</th>
            <th>Purpose</th>
            <th>Issued On</th>
            <th>File</th>
          </tr>
        </thead>
        <tbody>
          @forelse($certificates as $cert)
            <tr>
              <td>{{ $cert->type ?? 'N/A' }}</td>
              <td>{{ $cert->purpose ?? $cert->remarks ?? 'N/A' }}</td>
              <td>{{ $cert->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>
              <td>
                @if($cert->file_path)
                  <a href="{{ asset('storage/' . $cert->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-file-earmark-pdf"></i> View
                  </a>
                @else
                  <span class="text-muted">No file</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">No certificates issued for this school year.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <hr>

    <!-- Grades -->
    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-journal-text"></i> Academic Records</h6>
    
    @forelse($gradesBySchoolYear as $schoolYearIndex => $schoolYearData)
      <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
          <h6 class="mb-0">
            <i class="bi bi-calendar3 me-2"></i>
            School Year: {{ $schoolYearData['school_year']->name ?? 'N/A' }}
          </h6>
          <button type="button" 
                  class="btn btn-sm btn-light" 
                  data-bs-toggle="modal" 
                  data-bs-target="#schoolYearGradesModal{{ $schoolYearIndex }}">
            <i class="bi bi-eye"></i> View All Quarters
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Subject</th>
                  <th>Grade Level</th>
                  <th>Section</th>
                  <th>Average</th>
                </tr>
              </thead>
              <tbody>
                @forelse($schoolYearData['subjects'] as $subjectIndex => $gradeData)
                  <tr>
                    <td>{{ $gradeData['subject']->name ?? 'N/A' }}</td>
                    <td>{{ $gradeData['enrollment']->section->gradeLevel->name ?? 'N/A' }}</td>
                    <td>{{ $gradeData['enrollment']->section->name ?? 'N/A' }}</td>
                    <td>
                      @if($gradeData['average'])
                        <strong>{{ number_format($gradeData['average'], 2) }}</strong>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted">No subjects recorded for this school year.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @empty
      <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>No grades recorded yet.
      </div>
    @endforelse

    <!-- School Year Grade Details Modals -->
    @foreach($gradesBySchoolYear as $schoolYearIndex => $schoolYearData)
      <div class="modal fade" id="schoolYearGradesModal{{ $schoolYearIndex }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header bg-info text-white">
              <h5 class="modal-title">
                <i class="bi bi-journal-text me-2"></i>
                All Subjects - {{ $schoolYearData['school_year']->name ?? 'N/A' }}
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <strong>School Year:</strong> {{ $schoolYearData['school_year']->name ?? 'N/A' }}
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead class="table-light">
                    <tr>
                      <th>Subject</th>
                      <th>Grade Level</th>
                      <th>Section</th>
                      <th>1st Quarter</th>
                      <th>2nd Quarter</th>
                      <th>3rd Quarter</th>
                      <th>4th Quarter</th>
                      <th>Average</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $quarters = ['1st', '2nd', '3rd', '4th'];
                    @endphp
                    @forelse($schoolYearData['subjects'] as $gradeData)
                      <tr>
                        <td><strong>{{ $gradeData['subject']->name ?? 'N/A' }}</strong></td>
                        <td>{{ $gradeData['enrollment']->section->gradeLevel->name ?? 'N/A' }}</td>
                        <td>{{ $gradeData['enrollment']->section->name ?? 'N/A' }}</td>
                        @foreach($quarters as $quarter)
                          @php
                            $quarterGrade = $gradeData['quarters']->get($quarter);
                          @endphp
                          <td class="text-center">
                            @if($quarterGrade && $quarterGrade->grade)
                              <span class="badge bg-primary">{{ number_format($quarterGrade->grade, 2) }}</span>
                            @else
                              <span class="text-muted">-</span>
                            @endif
                          </td>
                        @endforeach
                        <td class="text-center">
                          @if($gradeData['average'])
                            <strong class="text-primary">{{ number_format($gradeData['average'], 2) }}</strong>
                          @else
                            <span class="text-muted">N/A</span>
                          @endif
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="8" class="text-center text-muted">No subjects recorded for this school year.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    @endforeach

  </div>
</div>
@endsection
