@extends('layouts.student')

@section('title','Announcements')
@section('header','Announcements')

@section('content')
<div class="row g-3">
  <div class="col-md-12">
    <div class="card card-custom p-4 shadow-sm">
      <h4 class="fw-bold mb-4">
        <i class='bx bx-bell'></i> Latest School Announcements
      </h4>

      @forelse($announcements as $a)
        <div class="card mb-3 border-0 shadow-sm">
          <div class="card-body">
            <h5 class="card-title fw-bold text-dark">
              {{ e($a->title ?? 'Untitled Announcement') }}
            </h5>
            <p class="card-text text-secondary">
              {!! nl2br(e($a->content ?? '')) !!}
            </p>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">
                📢 Posted by <strong>{{ e($a->user?->name ?? 'System') }}</strong>
                on {{ $a->created_at?->format('M d, Y h:i A') ?? 'N/A' }}
              </small>
              <small class="badge bg-info text-dark">
                {{ !empty($a->section?->name) ? 'For Section: '.e($a->section->name) : 'For: All Sections' }}
              </small>
            </div>
          </div>
        </div>
      @empty
        <div class="alert alert-info text-center m-3">
          <i class='bx bx-info-circle'></i> No announcements available at the moment.
        </div>
      @endforelse

    </div>
  </div>
</div>
@endsection
