@extends('layouts.admin')

@section('title','Admin Dashboard')
@section('header','Dashboard')

@section('content')
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card card-custom p-3 text-center">
      <h6>Total Users</h6>
      <h3>{{ $userCount }}</h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-custom p-3 text-center">
      <h6>Admins</h6>
      <h3>{{ $adminCount }}</h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-custom p-3 text-center">
      <h6>Teachers</h6>
      <h3>{{ $teacherCount }}</h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-custom p-3 text-center">
      <h6>Students</h6>
      <h3>{{ $studentCount }}</h3>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-8">
    <div class="card card-custom p-3">
      <h6 class="mb-3">📈 Enrollment Trends</h6>
      <canvas id="enrollmentChart" height="120"></canvas>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-3">
      <h6 class="mb-3">📊 User Role Distribution</h6>
      <canvas id="roleChart" height="250"></canvas>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card card-custom p-3">
      <h6 class="mb-3">🕒 Recent Activity Logs</h6>
      <ul class="list-group list-group-flush">
        @foreach($logs as $log)
          <li class="list-group-item small">
            <strong>{{ $log->user->name ?? 'Unknown' }}</strong> 
            {{ $log->action }} 
            <br>
            <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
          </li>
        @endforeach
        @if($logs->isEmpty())
          <li class="list-group-item text-muted">No recent logs</li>
        @endif
      </ul>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Enrollment Trends (line chart)
  const ctx = document.getElementById('enrollmentChart');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: @json(array_keys($enrollmentTrends->toArray())),
      datasets: [{
        label: 'Enrollments',
        data: @json(array_values($enrollmentTrends->toArray())),
        borderColor: '#0077b6',
        backgroundColor: 'rgba(0,119,182,0.2)',
        fill: true,
        tension: 0.3
      }]
    }
  });

  // User Role Distribution (pie chart)
  const roleCtx = document.getElementById('roleChart');
  new Chart(roleCtx, {
    type: 'pie',
    data: {
      labels: @json(array_keys($roleDistribution)),
      datasets: [{
        data: @json(array_values($roleDistribution)),
        backgroundColor: ['#0077b6','#66c2e0','#90e0ef','#caf0f8']
      }]
    }
  });
</script>
@endpush
