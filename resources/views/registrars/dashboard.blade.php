@extends('layouts.registrar')

@section('title','Registrar Dashboard')
@section('header')
  <i class="bi bi-speedometer2 me-2"></i> Dashboard
@endsection

@section('content')

@if(isset($noActiveSY) && $noActiveSY)
  <div class="alert alert-warning text-center py-5">
    <i class="bi bi-exclamation-triangle fs-4"></i>
    <p class="mt-2 mb-0 fw-semibold">
      The current school year is closed. Dashboard features are unavailable until a new school year starts.
    </p>
  </div>
@else
<div class="row g-3">
  <!-- Quick Cards -->
  <div class="col-md-3">
    <div class="card card-custom p-3 shadow-sm border-0">
      <h6 class="fw-bold text-primary">
        <i class="bi bi-mortarboard-fill me-2"></i> Students
      </h6>
      <hr>
      <p class="mb-1">Total: <strong>{{ $studentCount ?? 0 }}</strong></p>
      <a href="{{ route('registrars.students') }}" class="text-decoration-none">View Students →</a>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card card-custom p-3 shadow-sm border-0">
      <h6 class="fw-bold text-success">
        <i class="bi bi-person-workspace me-2"></i> Teachers
      </h6>
      <hr>
      <p class="mb-1">Registered: <strong>{{ $teacherCount ?? 0 }}</strong></p>
      <a href="{{ route('registrars.teachers') }}" class="text-decoration-none">View Teachers →</a>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card card-custom p-3 shadow-sm border-0">
      <h6 class="fw-bold text-warning">
        <i class="bi bi-building me-2"></i> Sections
      </h6>
      <hr>
      <p class="mb-1">Total: <strong>{{ $sectionCount ?? 0 }}</strong></p>
      <a href="{{ route('registrars.sections') }}" class="text-decoration-none">Manage Sections →</a>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card card-custom shadow-sm border-0 p-3 text-center">
      <h6 class="fw-bold text-muted">
        <i class="bi bi-calendar-event me-2"></i> Active S.Y.
      </h6>
      <p class="fs-6 fw-bold text-danger mb-1">
        {{ $activeSY->name ?? 'None' }}
      </p>
      <small>Current School Year</small>
    </div>
  </div>
</div>

<!-- Charts -->
<div class="row g-3 mt-4">
  <div class="col-md-8">
    <div class="card card-custom p-3 shadow-sm border-0">
      <h6 class="fw-bold">
        <i class="bi bi-bar-chart-line-fill me-2"></i> Students per Section
      </h6>
      @if(!empty($sections) && count($sections) > 0)
        <canvas id="sectionChart"></canvas>
      @else
        <p class="text-muted">No section data available yet.</p>
      @endif
    </div>
  </div>

  <div class="col-md-4">
    <div class="card card-custom p-3 shadow-sm border-0">
      <h6 class="fw-bold">
        <i class="bi bi-gender-ambiguous me-2"></i> Gender Distribution
      </h6>
      @if(!empty($genderLabels) && !empty($genderData))
        <canvas id="genderChart"></canvas>
      @else
        <p class="text-muted">No gender data available yet.</p>
      @endif
    </div>
  </div>
</div>

<!-- Chart.js scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if(!empty($sections) && count($sections) > 0)
<script>
  new Chart(document.getElementById('sectionChart').getContext('2d'), {
    type: 'bar',
    data: {
      labels: @json($sections),
      datasets: [{
        label: 'Students',
        data: @json($totals),
        backgroundColor: 'rgba(75, 192, 192, 0.6)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
      }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });
</script>
@endif

@if(!empty($genderLabels) && !empty($genderData))
<script>
  new Chart(document.getElementById('genderChart').getContext('2d'), {
    type: 'pie',
    data: {
      labels: @json($genderLabels),
      datasets: [{
        data: @json($genderData),
        backgroundColor: ['#36A2EB', '#FF6384']
      }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });
</script>
@endif
@endif
@endsection
