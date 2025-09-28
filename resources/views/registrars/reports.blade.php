@extends('layouts.registrar')

@section('title','Reports')
@section('header','Official Reports')

@section('content')
<div class="row g-3">
  <!-- Summary Cards -->
  <div class="col-12 col-md-3">
    <div class="card card-custom shadow-sm border-0 p-3 text-center">
      <h6 class="fw-bold text-muted">👩‍🎓 Students</h6>
      <p class="fs-4 fw-bold text-primary mb-1">{{ $students ?? 0 }}</p>
      <small>Total Registered</small>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card card-custom shadow-sm border-0 p-3 text-center">
      <h6 class="fw-bold text-muted">👩‍🏫 Teachers</h6>
      <p class="fs-4 fw-bold text-success mb-1">{{ $teachers ?? 0 }}</p>
      <small>Total Active</small>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card card-custom shadow-sm border-0 p-3 text-center">
      <h6 class="fw-bold text-muted">📚 Sections</h6>
      <p class="fs-4 fw-bold text-warning mb-1">{{ $sections ?? 0 }}</p>
      <small>Available Sections</small>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card card-custom shadow-sm border-0 p-3 text-center">
      <h6 class="fw-bold text-muted">📅 Active S.Y.</h6>
      <p class="fs-6 fw-bold text-danger mb-1">
        {{ $activeSY->name ?? 'None' }}
      </p>
      <small>Current School Year</small>
    </div>
  </div>
</div>

<!-- Report Generator -->
<div class="card card-custom mt-4 shadow-sm border-0">
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h6 class="fw-bold mb-0">📑 Generate Reports</h6>
  </div>
  <div class="card-body">
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('registrars.reports.masterlist') }}" class="btn btn-outline-primary">
        <i class="bi bi-people-fill me-1"></i> Masterlist
      </a>
      <a href="{{ route('registrars.reports.enrollment.summary') }}" class="btn btn-outline-success">
        <i class="bi bi-journal-check me-1"></i> Enrollment Summary
      </a>
      <a href="{{ route('registrars.reports.grade.logs') }}" class="btn btn-outline-danger">
        <i class="bi bi-clipboard-data me-1"></i> Grade Validation Logs
      </a>
    </div>
  </div>
</div>

<!-- Enrollment Trend Chart -->
<div class="card card-custom mt-4 shadow-sm border-0 p-3">
  <h6 class="fw-bold mb-3">📊 Enrollment Trend</h6>
  <canvas id="trendChart" style="min-height:320px; width:100%;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('trendChart');

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Students','Teachers','Sections'],
      datasets: [{
        label: 'Total',
        data: [{{ $students ?? 0 }}, {{ $teachers ?? 0 }}, {{ $sections ?? 0 }}],
        borderColor: '#36A2EB',
        backgroundColor: 'rgba(54,162,235,0.2)',
        borderWidth: 2,
        tension: 0.3,
        fill: true,
        pointBackgroundColor: '#36A2EB'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: { enabled: true }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { precision:0 }
        }
      }
    }
  });
</script>
@endsection
