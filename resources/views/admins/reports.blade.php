@extends('layouts.admin')
@section('title','Reports')
@section('header','Reports')

@section('content')
@include('partials.alerts')

<div class="row g-3">
  <div class="col-md-3">
    <div class="card p-3 shadow-sm">
      <h6>👩‍🎓 Students</h6>
      <h4 class="fw-bold">{{ $students }}</h4>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm">
      <h6>👨‍🏫 Teachers</h6>
      <h4 class="fw-bold">{{ $teachers }}</h4>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm">
      <h6>📋 Registrars</h6>
      <h4 class="fw-bold">{{ $registrars }}</h4>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm">
      <h6>👨‍💼 Admins</h6>
      <h4 class="fw-bold">{{ $admins }}</h4>
    </div>
  </div>
</div>

<div class="row g-3 mt-2">
  <div class="col-md-4">
    <div class="card p-3 shadow-sm">
      <h6>📚 Sections</h6>
      <h4 class="fw-bold">{{ $sections }}</h4>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 shadow-sm">
      <h6>📖 Subjects</h6>
      <h4 class="fw-bold">{{ $subjects }}</h4>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 shadow-sm">
      <h6>📅 School Years</h6>
      <h4 class="fw-bold">{{ $schoolYears }}</h4>
    </div>
  </div>
</div>
@endsection