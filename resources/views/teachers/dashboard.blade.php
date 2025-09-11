@extends('layouts.teacher')

@section('title','Teacher Dashboard')
@section('header','Dashboard')

@section('content')
<div class="row g-3">
  <!-- Quick Cards -->
  <div class="col-md-3">
    <div class="card card-custom p-3">
      <h6 class="fw-bold">Class List</h6>
      <hr>
      <p>You are handling {{ $sectionCount ?? 0 }} section(s).</p>
      <a href="{{ route('teachers.classlist') }}" class="text-decoration-none text-primary">View Classes →</a>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-custom p-3">
      <h6 class="fw-bold">Announcements</h6>
      <hr>
      <p>You have posted {{ $announcements->count() ?? 0 }} announcement(s).</p>
      <a href="{{ route('teachers.announcements') }}" class="text-decoration-none text-primary">View Announcements →</a>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-custom p-3">
      <h6 class="fw-bold">Grades</h6>
      <hr>
      <p>View and encode student grades.</p>
      <a href="{{ route('teachers.grades') }}" class="text-decoration-none text-primary">Manage Grades →</a>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-custom p-3">
      <h6 class="fw-bold">Reports</h6>
      <hr>
      <p>You can generate reports for your classes.</p>
      <a href="{{ route('teachers.reports') }}" class="text-decoration-none text-primary">Generate Reports →</a>
    </div>
  </div>
</div>

<!-- Charts -->
<div class="row g-3 mt-4">
  <div class="col-md-8">
    <div class="card card-custom p-3">
      <h6 class="fw-bold">Total Students per Section</h6>
      <canvas id="sectionChart"></canvas>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-3">
      <h6 class="fw-bold">Gender Distribution</h6>
      <canvas id="genderChart"></canvas>
    </div>
  </div>
</div>

<!-- Table Summary -->
<div class="card card-custom p-3 mt-4">
  <h6 class="fw-bold">Class Summary</h6>
  @if(!empty($sections) && count($sections) > 0)
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Section</th>
          <th>Total Students</th>
        </tr>
      </thead>
      <tbody>
        @foreach($sections as $i => $sec)
          <tr>
            <td>{{ $sec }}</td>
            <td>{{ $totals[$i] ?? 0 }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p>No sections assigned yet.</p>
  @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Section Chart
  const ctx1 = document.getElementById('sectionChart').getContext('2d');
  new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: @json($sections),
      datasets: [{
        label: 'Students',
        data: @json($totals),
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } }
    }
  });

  // Gender Chart
  const ctx2 = document.getElementById('genderChart').getContext('2d');
  new Chart(ctx2, {
    type: 'pie',
    data: {
      labels: @json($genderLabels),
      datasets: [{
        data: @json($genderData),
        backgroundColor: ['#36A2EB', '#FF6384']
      }]
    },
    options: { responsive: true }
  });
</script>
@endsection
