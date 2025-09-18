@extends('layouts.registrar')

@section('title','Reports')
@section('header','Reports')

@section('content')
<div class="row g-3">
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3 shadow-sm">
      <h6 class="fw-bold">👩‍🎓 Students</h6>
      <p class="fs-4 text-primary fw-bold">{{ $students ?? 0 }}</p>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3 shadow-sm">
      <h6 class="fw-bold">👩‍🏫 Teachers</h6>
      <p class="fs-4 text-success fw-bold">{{ $teachers ?? 0 }}</p>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3 shadow-sm">
      <h6 class="fw-bold">📚 Sections</h6>
      <p class="fs-4 text-warning fw-bold">{{ $sections ?? 0 }}</p>
    </div>
  </div>
</div>

<div class="card card-custom mt-4 shadow-sm p-3">
  <h6 class="fw-bold mb-3">📊 Overview Chart</h6>
  <canvas id="overviewChart" style="min-height:300px; width:100%;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const studentsCount = {{ intval($students ?? 0) }};
  const teachersCount = {{ intval($teachers ?? 0) }};
  const sectionsCount = {{ intval($sections ?? 0) }};

  new Chart(document.getElementById('overviewChart'), {
    type: 'bar',
    data: {
      labels: ['Students','Teachers','Sections'],
      datasets: [{
        label: 'Total',
        data: [studentsCount, teachersCount, sectionsCount],
        backgroundColor: ['#36A2EB','#4CAF50','#FFC107'],
        borderRadius: 5,
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
