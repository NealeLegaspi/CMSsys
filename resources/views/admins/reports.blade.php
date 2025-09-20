@extends('layouts.admin')

@section('title','Reports')
@section('header','Reports')

@section('content')
<div class="container-fluid">

  <!-- Filter & Export -->
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form method="GET" class="row g-3 align-items-end">
        <!-- School Year -->
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

        <!-- Status -->
        <div class="col-md-3">
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select">
            <option value="all" {{ $status=='all'?'selected':'' }}>All</option>
            <option value="active" {{ $status=='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ $status=='inactive'?'selected':'' }}>Inactive</option>
            <option value="graduating" {{ $status=='graduating'?'selected':'' }}>Graduating</option>
          </select>
        </div>

        <!-- Filter Button -->
        <div class="col-md-2">
          <button class="btn btn-primary w-100">
            <i class="bi bi-funnel me-1"></i> Filter
          </button>
        </div>

        <!-- Export Buttons -->
        <div class="col-md-3 d-flex flex-wrap justify-content-end gap-2">
          <div class="btn-group">
            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-file-earmark-excel me-1"></i> Export Enrollment
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{ route('admins.export.enrollment',['format'=>'xlsx','school_year_id'=>$sy,'status'=>$status]) }}">Excel</a></li>
              <li><a class="dropdown-item" href="{{ route('admins.export.enrollment',['format'=>'csv','school_year_id'=>$sy,'status'=>$status]) }}">CSV</a></li>
              <li><a class="dropdown-item" href="{{ route('admins.export.enrollment.pdf',['school_year_id'=>$sy,'status'=>$status]) }}">PDF</a></li>
            </ul>
          </div>

          <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-file-earmark-text me-1"></i> Export Grading
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{ route('admins.export.grading',['format'=>'xlsx','school_year_id'=>$sy]) }}">Excel</a></li>
              <li><a class="dropdown-item" href="{{ route('admins.export.grading',['format'=>'csv','school_year_id'=>$sy]) }}">CSV</a></li>
              <li><a class="dropdown-item" href="{{ route('admins.export.grading.pdf',['school_year_id'=>$sy]) }}">PDF</a></li>
            </ul>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row mb-4 g-3">
    <div class="col-md-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-people fs-1 text-primary"></i>
          <h6 class="mt-2">Total Students</h6>
          <h3>{{ $totalStudents }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-mortarboard fs-1 text-success"></i>
          <h6 class="mt-2">Total Teachers</h6>
          <h3>{{ $totalTeachers }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-journal-text fs-1 text-warning"></i>
          <h6 class="mt-2">Enrollments</h6>
          <h3>{{ $totalEnrollments }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-collection fs-1 text-danger"></i>
          <h6 class="mt-2">Sections</h6>
          <h3>{{ $totalSections }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <i class="bi bi-book fs-1 text-info"></i>
          <h6 class="mt-2">Subjects</h6>
          <h3>{{ $totalSubjects }}</h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Reports with Tabs -->
  <div class="card shadow-sm">
    <div class="card-header bg-light">
      <ul class="nav nav-tabs card-header-tabs" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="enrollment-tab" data-bs-toggle="tab" data-bs-target="#enrollment" type="button" role="tab">
            Enrollment Report
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="grading-tab" data-bs-toggle="tab" data-bs-target="#grading" type="button" role="tab">
            Grading Report
          </button>
        </li>
      </ul>
    </div>
    <div class="card-body tab-content">

      <!-- Enrollment Report -->
      <div class="tab-pane fade show active" id="enrollment" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light text-center">
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
                  <td>{{ optional($e->student->user->profile)->first_name }} {{ optional($e->student->user->profile)->last_name }}</td>
                  <td>{{ $e->section->name ?? '-' }}</td>
                  <td>{{ $e->section->gradeLevel->name ?? '-' }}</td>
                  <td>{{ $e->schoolYear->name ?? '-' }}</td>
                  <td>
                    <span class="badge bg-{{ $e->status=='active'?'success':'secondary' }}">
                      {{ ucfirst($e->status) }}
                    </span>
                  </td>
                  <td>{{ $e->created_at->format('Y-m-d') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted">No records found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
          {{ $enrollments->links('pagination::bootstrap-5') }}
        </div>
      </div>

      <!-- Grading Report -->
      <div class="tab-pane fade" id="grading" role="tabpanel">
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light text-center">
              <tr>
                <th>Student No</th>
                <th>Name</th>
                <th>Subject</th>
                <th>Grade</th>
                <th>School Year</th>
                <th>Recorded At</th>
              </tr>
            </thead>
            <tbody>
              @forelse($gradingData as $subjectId => $avg)
                <tr>
                  <td colspan="2">--</td>
                  <td>{{ $subjects[$subjectId]->name ?? 'N/A' }}</td>
                  <td>{{ number_format($avg,2) }}</td>
                  <td>{{ $schoolYears->firstWhere('id',$sy)->name ?? '-' }}</td>
                  <td>-</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted">No grading data available.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination (optional kung gagawin mong paginated ang gradingData) -->
        <div class="mt-3">
          {{ $gradingData->links('pagination::bootstrap-5') }}
        </div>
      </div>

    </div>
  </div>

</div>
@endsection
