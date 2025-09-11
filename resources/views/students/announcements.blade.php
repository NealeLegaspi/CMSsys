@extends('layouts.student')

@section('title','Announcements')
@section('header','Announcements')

@section('content')
<div class="row g-3">
  <div class="col-md-12">
    <div class="card card-custom p-3">
      <h4 class="fw-bold mb-3">Latest School Updates</h4>

      @forelse($announcements as $a)
        <div class="card mb-3 p-3">
          <h5>{{ $a->title ?? 'No title' }}</h5>
          <p>{{ $a->content ?? '' }}</p>
          <small class="text-muted">
            Posted by {{ $a->user?->name ?? 'System' }} 
            on {{ $a->created_at?->format('M d, Y h:i A') ?? 'N/A' }}
            @if(!empty($a->section?->name))
              | For Section: {{ $a->section?->name }}
            @else
              | For: All Sections
            @endif
          </small>
        </div>
      @empty
        <p>No announcements available.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
