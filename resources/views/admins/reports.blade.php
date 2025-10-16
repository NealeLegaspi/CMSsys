@extends('layouts.admin')

@section('title','Reports')
@section('header','Reports')

@section('content')
<div class="container-fluid my-4">

  {{-- 🔍 Filter + Export --}}
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <form method="GET" class="row g-3 align-items-end">
        {{-- School Year --}}
        <div class="col-md-4">
          <label class="form-label fw-semibold">School Year</label>
          <select name="school_year_id" class="form-select">
            @foreach($schoolYears as $year)
              <option value="{{ $year->id }}" {{ $sy == $year->id ? 'selected' : '' }}>
                {{ $year->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Status --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select">
            <option value="all" {{ $status=='all'?'selected':'' }}>All</option>
            <option value="active" {{ $status=='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ $status=='inactive'?'selected':'' }}>Inactive</option>
            <option value="graduating" {{ $status=='graduating'?'selected':'' }}>Graduating</option>
          </select>
        </div>

        {{-- Filter --}}
        <div class="col-md-2">
          <button class="btn btn-outline-primary flex-fill">
            <i class="bi bi-search"></i> Search
          </button>
          <a href="{{ route('admins.reports') }}" class="btn btn-outline-secondary flex-fill">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </a>
        </div>

        {{-- Export Buttons --}}
        <div class="col-md-3 d-flex justify-content-end flex-wrap gap-2">
          <div class="btn-group">
            <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-file-earmark-spreadsheet me-1"></i> Enrollment
            </button>
            {{-- Enrollment Export --}}
            <ul class="dropdown-menu shadow-sm">
              <li>
                <a class="dropdown-item" 
                  href="{{ route('admins.reports.export', ['type' => 'enrollment', 'format' => 'xlsx']) }}?school_year_id={{ $sy }}&status={{ $status }}">
                  Excel (.xlsx)
                </a>
              </li>
              <li>
                <a class="dropdown-item" 
                  href="{{ route('admins.reports.export', ['type' => 'enrollment', 'format' => 'csv']) }}?school_year_id={{ $sy }}&status={{ $status }}">
                  CSV
                </a>
              </li>
              <li>
                <a class="dropdown-item" 
                  href="{{ route('admins.reports.export', ['type' => 'enrollment', 'format' => 'pdf']) }}?school_year_id={{ $sy }}&status={{ $status }}">
                  PDF
                </a>
              </li>
            </ul>

          </div>
          <div class="btn-group">
            <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-file-earmark-spreadsheet me-1"></i> Grading
            </button>
            {{-- Grading Export --}}
            <ul class="dropdown-menu shadow-sm">
              <li>
                <a class="dropdown-item" 
                  href="{{ route('admins.reports.export', ['type' => 'grades', 'format' => 'xlsx']) }}?school_year_id={{ $sy }}">
                  Excel (.xlsx)
                </a>
              </li>
              <li>
                <a class="dropdown-item" 
                  href="{{ route('admins.reports.export', ['type' => 'grades', 'format' => 'csv']) }}?school_year_id={{ $sy }}">
                  CSV
                </a>
              </li>
              <li>
                <a class="dropdown-item" 
                  href="{{ route('admins.reports.export', ['type' => 'grades', 'format' => 'pdf']) }}?school_year_id={{ $sy }}">
                  PDF
                </a>
              </li>
            </ul>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- 📊 Summary Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-people fs-2 text-primary"></i>
          <p class="fw-semibold text-muted mb-1">Total Students</p>
          <h3 class="fw-bold">{{ $totalStudents }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-mortarboard fs-2 text-success"></i>
          <p class="fw-semibold text-muted mb-1">Total Teachers</p>
          <h3 class="fw-bold">{{ $totalTeachers }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-journal-text fs-2 text-warning"></i>
          <p class="fw-semibold text-muted mb-1">Enrollments</p>
          <h3 class="fw-bold">{{ $totalEnrollments }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-collection fs-2 text-danger"></i>
          <p class="fw-semibold text-muted mb-1">Sections</p>
          <h3 class="fw-bold">{{ $totalSections }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-book fs-2 text-info"></i>
          <p class="fw-semibold text-muted mb-1">Subjects</p>
          <h3 class="fw-bold">{{ $totalSubjects }}</h3>
        </div>
      </div>
    </div>
  </div>

  {{-- 📑 Reports Tabs --}}
  <div class="card shadow-sm border-0">
    <div class="card-header bg-light border-bottom-0">
      <ul class="nav nav-tabs card-header-tabs" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active fw-semibold" id="enrollment-tab" data-bs-toggle="tab" data-bs-target="#enrollment" type="button" role="tab">
            <i class="bi bi-person-lines-fill me-1"></i> Enrollment Report
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link fw-semibold" id="grading-tab" data-bs-toggle="tab" data-bs-target="#grading" type="button" role="tab">
            <i class="bi bi-clipboard-data me-1"></i> Grading Report
          </button>
        </li>
      </ul>
    </div>

    <div class="card-body tab-content">
      {{-- Enrollment Report --}}
      <div class="tab-pane fade show active" id="enrollment" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover align-middle text-center">
            <thead class="table-primary">
              <tr>
                <th>Student No</th>
                <th>Name</th>
                <th>Section</th>
                <th>Grade Level</th>
                <th>School Year</th>
                <th>Status</th>
                <th>Enrolled At</th>
              </tr>
            </thead>
            <tbody>
              @forelse($enrollments as $e)
              <tr>
                <td>{{ $e->student->student_number }}</td>
                <td>{{ optional($e->student->user->profile)->last_name }}, {{ optional($e->student->user->profile)->first_name }}</td>
                <td>{{ $e->section->name ?? '-' }}</td>
                <td>{{ $e->section->gradeLevel->name ?? '-' }}</td>
                <td>{{ $e->schoolYear->name ?? '-' }}</td>
                <td>
                  <span class="badge rounded-pill bg-{{ $e->status=='active'?'success':'secondary' }}">
                    {{ ucfirst($e->status) }}
                  </span>
                </td>
                <td>{{ $e->created_at->format('M d, Y') }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-3"><i class="bi bi-info-circle"></i> No records found.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
          {{ $enrollments->links('pagination::bootstrap-5') }}
        </div>
      </div>

      {{-- Grading Report --}}
      <div class="tab-pane fade" id="grading" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-hover align-middle text-center">
            <thead class="table-primary">
              <tr>
                <th>Subject</th>
                <th>Average Grade</th>
                <th>School Year</th>
              </tr>
            </thead>
            <tbody>
              @forelse($gradingData as $subjectId => $avg)
              <tr>
                <td>{{ $subjects[$subjectId]->name ?? 'N/A' }}</td>
                <td>{{ number_format($avg, 2) }}</td>
                <td>{{ $schoolYears->firstWhere('id',$sy)->name ?? '-' }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-3">
                  <i class="bi bi-info-circle"></i> No grading data available.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
