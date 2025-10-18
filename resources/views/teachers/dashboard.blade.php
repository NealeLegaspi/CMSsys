@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')
@section('header')
    <i class="bi bi-speedometer2 me-2"></i> Dashboard
@endsection

@section('content')
<div class="container-fluid my-4">

  <!-- Overview Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold text-primary mb-0">
              <i class="bi bi-journal-text me-2"></i>Class List
            </h6>
            <span class="badge bg-primary-subtle text-primary">{{ $sectionCount ?? 0 }}</span>
          </div>
          <p class="text-muted small mb-3">Sections you currently handle</p>
          <a href="{{ route('teachers.classlist') }}" class="btn btn-sm btn-outline-primary w-100">
            View Classes
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold text-success mb-0">
              <i class="bi bi-megaphone me-2"></i>Announcements
            </h6>
            <span class="badge bg-success-subtle text-success">{{ $announcements->count() ?? 0 }}</span>
          </div>
          <p class="text-muted small mb-3">Manage and post announcements</p>
          <a href="{{ route('teachers.announcements') }}" class="btn btn-sm btn-outline-success w-100">
            View Announcements
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold text-warning mb-0">
              <i class="bi bi-pencil-square me-2"></i>Grades
            </h6>
            <i class="bi bi-clipboard-data text-warning fs-5"></i>
          </div>
          <p class="text-muted small mb-3">View and encode student grades</p>
          <a href="{{ route('teachers.grades') }}" class="btn btn-sm btn-outline-warning w-100 text-dark">
            Manage Grades
          </a>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <h6 class="fw-bold text-danger mb-0">
              <i class="bi bi-graph-up-arrow me-2"></i>Reports
            </h6>
            <i class="bi bi-file-earmark-bar-graph text-danger fs-5"></i>
          </div>
          <p class="text-muted small mb-3">Generate performance summaries</p>
          <a href="{{ route('teachers.reports') }}" class="btn btn-sm btn-outline-danger w-100">
            Generate Reports
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts and Announcements -->
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card shadow-sm border-0 rounded-3 p-3 h-100">
        <h6 class="fw-bold text-secondary mb-3">
          <i class="bi bi-people me-2"></i>Total Students per Section
        </h6>
        @if(!empty($sections) && count($sections) > 0)
          <canvas id="sectionChart" height="130"></canvas>
        @else
          <p class="text-muted small">No section data available yet.</p>
        @endif
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card shadow-sm border-0 rounded-3 p-3 h-100">
        <h6 class="fw-bold text-secondary mb-3">
          <i class="bi bi-gender-ambiguous me-2"></i>Gender Distribution
        </h6>
        @if(!empty($genderLabels) && !empty($genderData))
          <canvas id="genderChart" height="180"></canvas>
        @else
          <p class="text-muted small">No gender data available yet.</p>
        @endif
      </div>
    </div>
  </div>

  <!-- Recent Announcements -->
  <div class="card shadow-sm border-0 rounded-3 mt-4">
    <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
      <span><i class="bi bi-broadcast me-2 text-primary"></i>Recent Announcements</span>
      <a href="{{ route('teachers.announcements') }}" class="text-decoration-none small text-primary">View All</a>
    </div>
    <div class="card-body">
      @if($announcements->count() > 0)
        <ul class="list-group list-group-flush">
          @foreach($announcements as $ann)
            <li class="list-group-item d-flex justify-content-between align-items-start">
              <div>
                <div class="fw-semibold">{{ $ann->title }}</div>
                <div class="text-muted small">{{ Str::limit($ann->content, 80) }}</div>
              </div>
              <span class="badge bg-secondary-subtle text-dark small">{{ $ann->created_at->format('M d, Y') }}</span>
            </li>
          @endforeach
        </ul>
      @else
        <p class="text-muted small mb-0">No announcements found.</p>
      @endif
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(!empty($sections) && count($sections) > 0)
<script>
const ctx1 = document.getElementById('sectionChart');
new Chart(ctx1, {
  type: 'bar',
  data: {
    labels: @json(array_values($sections)),
    datasets: [{
      label: 'Students',
      data: @json($totals),
      backgroundColor: 'rgba(54, 162, 235, 0.7)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1,
      borderRadius: 4
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      title: { display: false }
    },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 } }
    }
  }
});
</script>
@endif

@if(!empty($genderLabels) && !empty($genderData))
<script>
const ctx2 = document.getElementById('genderChart');
new Chart(ctx2, {
  type: 'doughnut',
  data: {
    labels: @json($genderLabels),
    datasets: [{
      data: @json($genderData),
      backgroundColor: ['#4e73df', '#e74a3b'],
      hoverOffset: 8
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});
</script>
@endif
@endsection
