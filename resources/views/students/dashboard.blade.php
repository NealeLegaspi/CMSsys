@extends('layouts.student')

@section('title','Student Dashboard')
@section('header','Dashboard')

@section('content')
<div class="container my-4">

  <!-- 🔹 Quick Summary Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm p-3 h-100">
        <h6 class="fw-bold text-primary mb-2">
          <i class="bi bi-bell-fill"></i> Announcements
        </h6>
        <p class="text-muted mb-1">
          You have <strong>{{ $announcements->count() ?? 0 }}</strong> recent announcement(s).
        </p>
        <a href="{{ route('students.announcements') }}" class="text-decoration-none small">
          View All →
        </a>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card border-0 shadow-sm p-3 h-100">
        <h6 class="fw-bold text-warning mb-2">
          <i class="bi bi-bar-chart-line-fill"></i> Grades
        </h6>
        <p class="text-muted mb-1">
          Monitor your latest academic performance.
        </p>
        <a href="{{ route('students.grades') }}" class="text-decoration-none small">
          View Full Grades →
        </a>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- 🔸 Announcements Section -->
    <div class="col-md-6">
      <div class="card shadow-sm border-0 p-4 h-100">
        <h5 class="fw-bold mb-3 text-primary">
          <i class="bi bi-megaphone-fill"></i> Latest Announcements
        </h5>

        @forelse($announcements as $a)
          <div class="border rounded-3 p-3 mb-3 bg-light">
            <h6 class="fw-bold text-dark mb-1">{{ e($a->title ?? 'Untitled') }}</h6>
            <p class="text-muted small mb-2">
              {!! nl2br(e($a->content ?? 'No content available.')) !!}
            </p>
            <small class="text-secondary d-block">
              <i class="bi bi-person-circle"></i> {{ e($a->user?->name ?? 'System') }}
              | <i class="bi bi-calendar3"></i> {{ $a->created_at?->format('M d, Y h:i A') ?? 'N/A' }}<br>
              <span class="badge bg-info text-dark mt-1">
                {{ $a->section?->name ? 'Section: '.$a->section->name : 'All Sections' }}
              </span>
            </small>
          </div>
        @empty
          <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> No announcements available.
          </div>
        @endforelse
      </div>
    </div>

    <!-- 🔸 Grades Overview -->
    <div class="col-md-6">
      <div class="card shadow-sm border-0 p-4 h-100">
        <h5 class="fw-bold mb-3 text-warning">
          <i class="bi bi-bar-chart-fill"></i> Grade Overview
        </h5>

        @if($grades->count())
          <div class="table-responsive mb-3">
            <table class="table table-bordered table-sm align-middle">
              <thead class="table-light">
                <tr>
                  <th>Subject</th>
                  <th>Quarter</th>
                  <th>Grade</th>
                </tr>
              </thead>
              <tbody>
                @foreach($grades as $g)
                  <tr>
                    <td>{{ $g->subject?->name ?? 'N/A' }}</td>
                    <td>{{ $g->quarter ?? '-' }}</td>
                    <td class="fw-semibold {{ $g->grade < 75 ? 'text-danger' : 'text-success' }}">
                      {{ $g->grade ?? '-' }}
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- 📊 Chart Display -->
          <canvas id="gradeChart" height="150"></canvas>
        @else
          <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> No grades available yet.
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@if($grades->count())
<script>
  const ctx = document.getElementById('gradeChart').getContext('2d');
  const gradeData = @json($grades->groupBy('subject.name')->map->avg('grade'));

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: Object.keys(gradeData),
      datasets: [{
        label: 'Average Grade',
        data: Object.values(gradeData),
        backgroundColor: 'rgba(255, 193, 7, 0.6)',
        borderColor: 'rgba(255, 193, 7, 1)',
        borderWidth: 1,
        borderRadius: 5
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          max: 100,
          ticks: { stepSize: 10 }
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: { enabled: true }
      }
    }
  });
</script>
@endif
@endsection
