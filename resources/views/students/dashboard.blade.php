@extends('layouts.student')

@section('title','Student Dashboard')
@section('header','Dashboard')

@section('content')
<div class="row g-4">

  <!-- Announcements -->
  <div class="col-md-6">
    <div class="card shadow-sm border-0 p-4">
      <h4 class="fw-bold mb-3">
        <i class='bx bx-bell me-1'></i> Latest Announcements
      </h4>

      @forelse($announcements as $a)
        <div class="card mb-3 border-0 shadow-sm">
          <div class="card-body">
            <h5 class="card-title fw-bold text-dark">
              {{ e($a->title ?? 'Untitled Announcement') }}
            </h5>
            <p class="card-text text-secondary">
              {!! nl2br(e($a->body ?? '')) !!}
            </p>
            <small class="text-muted d-block">
              📢 Posted by <strong>{{ e($a->user?->name ?? 'System') }}</strong> 
              on {{ $a->created_at?->format('M d, Y h:i A') ?? 'N/A' }}
              <br>
              <span class="badge bg-info text-dark mt-1">
                {{ !empty($a->section?->name) ? 'For Section: '.e($a->section->name) : 'For: All Sections' }}
              </span>
            </small>
          </div>
        </div>
      @empty
        <div class="alert alert-info text-center">
          <i class='bx bx-info-circle'></i> No announcements available.
        </div>
      @endforelse

      <div class="text-end mt-3">
        <a href="{{ route('students.announcements') }}" class="btn btn-sm btn-outline-primary">
          View All Announcements
        </a>
      </div>
    </div>
  </div>

  <!-- Grades -->
  <div class="col-md-6">
    <div class="card shadow-sm border-0 p-4">
      <h4 class="fw-bold mb-3">
        <i class='bx bx-book me-1'></i> Your Grades
      </h4>

      @if($grades->count())
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
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
                <td><strong>{{ $g->grade ?? '-' }}</strong></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-info text-center">
          <i class='bx bx-info-circle'></i> No grades available yet.
        </div>
      @endif

      <div class="text-end mt-3">
        <a href="{{ route('students.grades') }}" class="btn btn-sm btn-outline-primary">
          View Full Grades
        </a>
      </div>
    </div>
  </div>

</div>
@endsection
