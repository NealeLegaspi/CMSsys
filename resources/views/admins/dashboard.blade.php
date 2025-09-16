@extends('layouts.admin') 
{{-- Gumamit ng layout mo (yung sidebar + header) --}}

@section('title','Admin Dashboard')
@section('header','Dashboard')

@section('content')
<div class="row g-3">

  <!-- Quick Stats -->
  <div class="col-md-3">
    <div class="card text-center shadow-sm border-0">
      <div class="card-body">
        <i class="bi bi-person-check fs-1 text-primary"></i>
        <h6 class="mt-2">Administrators</h6>
        <h3>{{ $adminCount }}</h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center shadow-sm border-0">
      <div class="card-body">
        <i class="bi bi-person-lines-fill fs-1 text-success"></i>
        <h6 class="mt-2">Registrars</h6>
        <h3>{{ $registrarCount }}</h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center shadow-sm border-0">
      <div class="card-body">
        <i class="bi bi-mortarboard fs-1 text-warning"></i>
        <h6 class="mt-2">Teachers</h6>
        <h3>{{ $teacherCount }}</h3>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center shadow-sm border-0">
      <div class="card-body">
        <i class="bi bi-people fs-1 text-danger"></i>
        <h6 class="mt-2">Students</h6>
        <h3>{{ $studentCount }}</h3>
      </div>
    </div>
  </div>

</div>

<!-- Activity & Announcements -->
<div class="row g-3 mt-3">
  <div class="col-md-6">
    <div class="card shadow-sm border-0">
      <div class="card-header fw-bold">Recent Activity Logs</div>
      <ul class="list-group list-group-flush">
        @forelse($logs as $log)
          <li class="list-group-item">
            <small class="text-muted">
              {{ $log->created_at->format('M d, Y h:i A') }} - 
              <strong>{{ optional($log->user)->email ?? 'System' }}</strong> 
              {{ $log->action }}
            </small>
          </li>
        @empty
          <li class="list-group-item text-muted">No recent activity.</li>
        @endforelse
      </ul>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow-sm border-0">
      <div class="card-header fw-bold">Latest Announcements</div>
      <ul class="list-group list-group-flush">
        @forelse($announcements as $a)
          <li class="list-group-item">
            <h6 class="mb-1">{{ $a->title }}</h6>
            <p class="mb-1 text-secondary">{{ Str::limit($a->content, 100) }}</p>
            <small class="text-muted">
              By {{ $a->user->profile->first_name ?? '' }} {{ $a->user->profile->last_name ?? '' }} 
              on {{ $a->created_at->format('M d, Y h:i A') }}
            </small>
          </li>
        @empty
          <li class="list-group-item text-muted">No announcements yet.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
