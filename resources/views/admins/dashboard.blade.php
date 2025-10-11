@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="container-fluid mt-3">

  {{-- ===== SUMMARY CARDS ===== --}}
  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body text-center py-4">
          <i class="bi bi-person-check fs-1 text-primary"></i>
          <h6 class="fw-semibold text-muted mt-2">Administrators</h6>
          <h3 class="fw-bold text-dark">{{ $adminCount }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body text-center py-4">
          <i class="bi bi-person-lines-fill fs-1 text-success"></i>
          <h6 class="fw-semibold text-muted mt-2">Registrars</h6>
          <h3 class="fw-bold text-dark">{{ $registrarCount }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body text-center py-4">
          <i class="bi bi-mortarboard fs-1 text-warning"></i>
          <h6 class="fw-semibold text-muted mt-2">Teachers</h6>
          <h3 class="fw-bold text-dark">{{ $teacherCount }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body text-center py-4">
          <i class="bi bi-people fs-1 text-danger"></i>
          <h6 class="fw-semibold text-muted mt-2">Students</h6>
          <h3 class="fw-bold text-dark">{{ $studentCount }}</h3>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== ACTIVITY LOGS & ANNOUNCEMENTS ===== --}}
  <div class="row g-3">
    <div class="col-md-6">
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header fw-bold bg-white border-0">Recent Activity Logs</div>
        <ul class="list-group list-group-flush">
          @forelse($logs as $log)
            <li class="list-group-item small">
              <i class="bi bi-clock text-secondary me-1"></i>
              <span class="text-muted">{{ $log->created_at->format('M d, Y h:i A') }}</span><br>
              <strong>{{ optional($log->user)->email ?? 'System' }}</strong>
              <span class="text-secondary">— {{ $log->action }}</span>
            </li>
          @empty
            <li class="list-group-item text-muted">No recent activity.</li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header fw-bold bg-white border-0">Latest Announcements</div>
        <ul class="list-group list-group-flush">
          @forelse($announcements as $a)
            <li class="list-group-item">
              <h6 class="mb-1 fw-semibold text-dark">{{ $a->title }}</h6>
              <p class="mb-1 text-secondary small">{{ Str::limit($a->content, 100) }}</p>
              <small class="text-muted">
                <i class="bi bi-person-circle me-1"></i>
                {{ $a->user->profile->first_name ?? '' }} {{ $a->user->profile->last_name ?? '' }} 
                • {{ $a->created_at->format('M d, Y h:i A') }}
              </small>
            </li>
          @empty
            <li class="list-group-item text-muted">No announcements yet.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
