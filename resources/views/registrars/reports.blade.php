@extends('layouts.registrar')

@section('title','Reports')
@section('header','Reports')

@section('content')
<div class="row g-3">
  <div class="col-md-4">
    <div class="card card-custom p-3 shadow-sm">
      <h6 class="fw-bold">👩‍🎓 Students</h6>
      <p class="fs-4 text-primary fw-bold">{{ $students }}</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-3 shadow-sm">
      <h6 class="fw-bold">👩‍🏫 Teachers</h6>
      <p class="fs-4 text-success fw-bold">{{ $teachers }}</p>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-custom p-3 shadow-sm">
      <h6 class="fw-bold">📚 Sections</h6>
      <p class="fs-4 text-warning fw-bold">{{ $sections }}</p>
    </div>
  </div>
</div>

<div class="card card-custom mt-4 shadow-sm p-3">
  <h6 class="fw-bold mb-3">📊 Overview Chart</h6>
  <canvas id="overviewChart" style="height:300px;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  new Chart(document.getElementById('overviewChart'), {
    type: 'bar',
    data: {
      labels: ['Students','Teachers','Sections'],
      datasets: [{
        label: 'Total',
        data: [{{ $students }}, {{ $teachers }}, {{ $sections }}],
        backgroundColor: ['#36A2EB','#4CAF50','#FFC107']
      }]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } } }
  });
</script>
@endsection
