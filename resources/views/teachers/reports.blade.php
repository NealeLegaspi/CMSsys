@extends('layouts.teacher')

@section('title','Reports')
@section('header','Reports')

@section('content')
<div class="container my-4">

  {{-- Flash Messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Filters Card --}}
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-funnel"></i> Filter Reports</h5>
      <form class="row g-3" method="POST" action="{{ route('teachers.filterReports') }}">
        @csrf
        <div class="col-md-3">
          <label class="form-label fw-semibold">Grade Level</label>
          <select name="gradelevel_id" class="form-select">
            <option value="">All</option>
            @foreach($gradeLevels as $gl)
              <option value="{{ $gl->id }}" {{ request('gradelevel_id') == $gl->id ? 'selected' : '' }}>
                {{ $gl->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold">Section</label>
          <select name="section_id" class="form-select">
            <option value="">All</option>
            @foreach($sections as $section)
              <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                {{ $section->gradeLevel->name }} - {{ $section->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold">School Year</label>
          <select name="school_year" class="form-select">
            <option value="">All</option>
            @foreach($schoolYears as $sy)
              <option value="{{ $sy }}" {{ request('school_year') == $sy ? 'selected' : '' }}>
                {{ $sy }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3 d-flex align-items-end justify-content-end gap-2">
          <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
          <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Student Table Card --}}
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-primary mb-0">
          <i class="bi bi-people-fill"></i> Student Reports
        </h5>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="bi bi-printer"></i> Print
          </button>
          <button type="button" class="btn btn-outline-success" onclick="exportTableToCSV('students_report.csv')">
            <i class="bi bi-file-earmark-spreadsheet"></i> CSV
          </button>
          <form method="POST" action="{{ route('teachers.reports.export.pdf') }}">
            @csrf
            <button type="submit" class="btn btn-outline-danger">
              <i class="bi bi-file-earmark-pdf"></i> PDF
            </button>
          </form>
        </div>
      </div>

      @if($students->isEmpty())
        <div class="text-center py-5 text-muted">
          <i class="bi bi-person-lines-fill" style="font-size: 3rem;"></i>
          <p class="mt-3">No students found. Try adjusting the filters above.</p>
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-hover align-middle text-center" id="studentsTable">
            <thead class="table-light">
              <tr>
                <th>LRN</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Address</th>
                <th>Birthdate</th>
                <th>Age</th>
                <th>Contact</th>
                <th>Grade / Section</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($students as $student)
                @php
                  $profile = $student->user->profile ?? null;
                @endphp
                <tr>
                  <td>{{ $student->student_number ?? 'N/A' }}</td>
                  <td>{{ $profile->last_name ?? '—' }}</td>
                  <td>{{ $profile->first_name ?? '—' }}</td>
                  <td>{{ $profile->middle_name ?? '—' }}</td>
                  <td>{{ $profile->address ?? '—' }}</td>
                  <td>{{ $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('M d, Y') : '—' }}</td>
                  <td>{{ $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->age : '—' }}</td>
                  <td>{{ $profile->contact ?? '—' }}</td>
                  <td>
                    {{ $student->section?->gradeLevel?->name ?? 'N/A' }} - 
                    {{ $student->section?->name ?? 'N/A' }}
                  </td>
                  <td>
                    @if(($student->status ?? 'Enrolled') === 'Enrolled')
                      <span class="badge bg-success">Enrolled</span>
                    @else
                      <span class="badge bg-danger">{{ $student->status }}</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- CSV Export Script --}}
<script>
  function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], { type: "text/csv" });
    const downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    document.body.appendChild(downloadLink);
    downloadLink.click();
  }

  function exportTableToCSV(filename) {
    const rows = document.querySelectorAll("#studentsTable tr");
    const csv = Array.from(rows).map(row => {
      const cols = row.querySelectorAll("td, th");
      return Array.from(cols).map(col => `"${col.innerText.replace(/"/g, '""')}"`).join(",");
    }).join("\n");
    downloadCSV(csv, filename);
  }
</script>
@endsection
