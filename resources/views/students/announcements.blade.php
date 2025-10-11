@extends('layouts.student')

@section('title', 'Announcements')
@section('header', 'Announcements')

@section('content')
<div class="container my-4">
  <div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-bold mb-0">
        <i class="bi bi-megaphone-fill me-2"></i> School Announcements
      </h5>
    </div>

    @forelse($announcements as $a)
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h5 class="fw-bold text-dark mb-2">
            {{ e($a->title ?? 'Untitled Announcement') }}
          </h5>
          <p class="text-muted small mb-3">
            {!! nl2br(e($a->body ?? $a->content ?? 'No content available.')) !!}
          </p>

          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <small class="text-secondary">
              <i class="bi bi-person-circle"></i>
              <strong>{{ e($a->user?->name ?? 'System') }}</strong>
              &nbsp;|&nbsp;
              <i class="bi bi-calendar3"></i>
              {{ $a->created_at?->format('M d, Y h:i A') ?? 'N/A' }}
            </small>
            <span class="badge bg-info text-dark mt-2 mt-md-0">
              {{ $a->section?->name ? 'Section: '.e($a->section->name) : 'All Sections' }}
            </span>
          </div>
        </div>
      </div>
    @empty
      <div class="alert alert-info text-center py-4">
        <i class="bi bi-info-circle fs-4"></i>
        <p class="mt-2 mb-0">No announcements available at the moment.</p>
      </div>
    @endforelse
  </div>
</div>
@endsection
