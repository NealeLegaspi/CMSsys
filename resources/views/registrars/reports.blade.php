@extends('layouts.registrar')

@section('title', 'Reports & Analytics')
@section('header', 'Reports & Analytics')

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h6 class="fw-bold mb-0">📈 Enrollment Reports</h6>
    <a href="{{ route('registrars.reports.pdf', ['school_year_id' => $schoolYearId]) }}" class="btn btn-sm btn-danger">
      <i class="bi bi-file-earmark-pdf"></i> Export PDF
    </a>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <form method="GET" class="row g-2 mb-4">
      <div class="col-md-4">
        <label class="form-label fw-bold">School Year</label>
        <select name="school_year_id" class="form-select" onchange="this.form.submit()">
          @foreach($schoolYears as $sy)
            <option value="{{ $sy->id }}" {{ $schoolYearId == $sy->id ? 'selected' : '' }}>
              {{ $sy->name }}
            </option>
          @endforeach
        </select>
      </div>
    </form>

    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="card border-success shadow-sm text-center p-3">
          <h5 class="text-success fw-bold mb-1">{{ $totalEnrolled }}</h5>
          <p class="mb-0 text-muted">Total Enrolled Students</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-primary shadow-sm text-center p-3">
          <h5 class="text-primary fw-bold mb-1">{{ $maleCount }}</h5>
          <p class="mb-0 text-muted">Male Students</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-pink shadow-sm text-center p-3">
          <h5 class="text-danger fw-bold mb-1">{{ $femaleCount }}</h5>
          <p class="mb-0 text-muted">Female Students</p>
        </div>
      </div>
    </div>

    <h6 class="fw-bold mb-3">📊 Enrollment by Grade Level</h6>
    <div class="table-responsive">
      <table class="table table-bordered align-middle">
        <thead class="table-primary">
          <tr>
            <th>Grade Level</th>
            <th>Total Students</th>
          </tr>
        </thead>
        <tbody>
          @forelse($byGradeLevel as $gl)
            <tr>
              <td>{{ $gl->grade }}</td>
              <td>{{ $gl->total }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="2" class="text-center text-muted">No data available.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
