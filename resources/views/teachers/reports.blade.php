@extends('layouts.teacher')

@section('title','Reports')
@section('header')
    <i class="bi bi-file-earmark-bar-graph-fill me-2"></i> Reports
@endsection

@section('content')
<div class="container-fluid my-4">

  {{-- 🔍 Filters --}}
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <form method="POST" action="{{ route('teachers.filterReports') }}" class="row g-3 align-items-end">
        @csrf

        {{-- Grade Level --}}
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

        {{-- Section --}}
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

        {{-- School Year --}}
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

        {{-- Buttons --}}
        <div class="col-md-3 d-flex justify-content-end flex-wrap gap-2">
          <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-search me-1"></i> Search
          </button>
          <a href="{{ route('teachers.reports') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise me-1"></i> Reset
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- 📊 Summary Cards (Optional if you want parity with admin) --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-people fs-2 text-primary"></i>
          <p class="fw-semibold text-muted mb-1">Total Students</p>
          <h3 class="fw-bold">{{ $students->count() }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-collection fs-2 text-success"></i>
          <p class="fw-semibold text-muted mb-1">Sections Handled</p>
          <h3 class="fw-bold">{{ $sections->count() }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-journal-text fs-2 text-warning"></i>
          <p class="fw-semibold text-muted mb-1">Grade Levels</p>
          <h3 class="fw-bold">{{ $gradeLevels->count() }}</h3>
        </div>
      </div>
    </div>
  </div>

  {{-- 📑 Reports Table --}}
  <div class="card shadow-sm border-0">
    <div class="card-header bg-light border-bottom-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
      <h5 class="fw-semibold mb-0">
        <i class="bi bi-table me-1"></i> Student Reports
      </h5>

      {{-- Export Buttons --}}
      <div class="btn-group">
        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
          <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export
        </button>
        <ul class="dropdown-menu shadow-sm">
          <li>
            <button class="dropdown-item" type="button" onclick="exportTableToCSV('students_report.csv')">
              CSV
            </button>
          </li>
          <li>
            <form method="POST" action="{{ route('teachers.reports.export.pdf') }}">
              @csrf
              <button type="submit" class="dropdown-item">PDF</button>
            </form>
          </li>
        </ul>
      </div>
    </div>

    <div class="card-body">
      @if($students->isEmpty())
        <div class="text-center py-5 text-muted">
          <i class="bi bi-info-circle" style="font-size: 3rem;"></i>
          <p class="mt-3 mb-0">No students found. Try adjusting the filters above.</p>
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-hover align-middle text-center">
            <thead class="table-primary">
              <tr>
                <th>LRN</th>
                <th>Name</th>
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
                    $enrollment = $student->activeEnrollment;
                    $gradeName = optional(optional($enrollment)->section)->gradeLevel->name ?? 'N/A';
                    $sectionName = optional(optional($enrollment)->section)->name ?? 'N/A';
                @endphp
                <tr>
                  <td>{{ $student->student_number ?? 'N/A' }}</td>
                  <td class="text-start">{{ $profile->last_name ?? '—' }}, {{ $profile->first_name ?? '—' }}</td>
                  <td class="text-start">{{ $profile->address ?? '—' }}</td>
                  <td>{{ $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('M d, Y') : '—' }}</td>
                  <td>{{ $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->age : '—' }}</td>
                  <td>{{ $profile->contact_number ?? '—' }}</td>
                  <td>{{ $gradeName }} / {{ $sectionName }}</td>
                  <td>
                    <span class="badge rounded-pill bg-{{ ($student->status ?? 'Enrolled') == 'Enrolled' ? 'success' : 'danger' }}">
                      {{ $student->status ?? 'Enrolled' }}
                    </span>
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

{{-- 📤 CSV Export Script --}}
<script>
function downloadCSV(csv, filename) {
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.download = filename;
  link.click();
}

function exportTableToCSV(filename) {
  const rows = document.querySelectorAll("table tr");
  const csv = Array.from(rows).map(row =>
    Array.from(row.querySelectorAll("td, th"))
      .map(cell => `"${cell.innerText.replace(/"/g, '""').trim()}"`)
      .join(",")
  ).join("\n");
  downloadCSV(csv, filename);
}
</script>
@endsection
