@extends('layouts.admin')

@section('title','Reports')
@section('header','Reports')

@section('content')
<div class="container-fluid">

  <!-- Filter Form -->
  <div class="card mb-4 shadow-sm">
    <div class="card-body">
      <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label">School Year</label>
          <select name="school_year_id" class="form-select">
            @foreach($schoolYears as $year)
              <option value="{{ $year->id }}" {{ $sy == $year->id ? 'selected' : '' }}>
                {{ $year->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="all" {{ $status=='all'?'selected':'' }}>All</option>
            <option value="active" {{ $status=='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ $status=='inactive'?'selected':'' }}>Inactive</option>
            <option value="graduating" {{ $status=='graduating'?'selected':'' }}>Graduating</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
        </div>
        <div class="col-md-3 d-flex justify-content-end">
          <!-- Export Buttons -->
          <div class="btn-group me-2">
            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-download"></i> Export Enrollment
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{ route('admins.export.enrollment',['format'=>'xlsx','school_year_id'=>$sy,'status'=>$status]) }}">Excel</a></li>
              <li><a class="dropdown-item" href="{{ route('admins.export.enrollment',['format'=>'csv','school_year_id'=>$sy,'status'=>$status]) }}">CSV</a></li>
              <li><a class="dropdown-item" href="{{ route('admins.export.enrollment.pdf',['school_year_id'=>$sy,'status'=>$status]) }}">PDF</a></li>
            </ul>
          </div>
          <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
              <i class="bi bi-download"></i> Export Grading
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
  <div class="row mb-4">
    @php
      $summary = [
        'Total Students' => $totalStudents,
        'Total Teachers' => $totalTeachers,
        'Total Enrollments' => $totalEnrollments,
        'Total Sections' => $totalSections,
        'Total Subjects' => $totalSubjects,
      ];
      $colors = ['primary','success','warning','info','secondary'];
    @endphp
    @foreach($summary as $label => $value)
      <div class="col-md-2 mb-2">
        <div class="card text-center shadow-sm border-0">
          <div class="card-body">
            <h6 class="mb-2">{{ $label }}</h6>
            <h3 class="mb-0">{{ $value }}</h3>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <!-- Enrollment Report -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white">Enrollment Report</div>
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover table-striped mb-0">
        <thead class="table-light">
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
              <td>{{ ucfirst($e->status) }}</td>
              <td>{{ $e->created_at->format('Y-m-d') }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">No records found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Grading Report -->
  <div class="card shadow-sm">
    <div class="card-header bg-info text-white">Grading Report</div>
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover table-striped mb-0">
        <thead class="table-light">
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
            <tr><td colspan="6" class="text-center text-muted">No grading data available.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
