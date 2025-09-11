@extends('layouts.student')

@section('title','Student Dashboard')
@section('header','Dashboard')

@section('content')
<div class="row g-3">
  <!-- Announcements -->
  <div class="col-md-6">
    <div class="card card-custom p-3">
      <h4 class="mb-3">Latest Announcements</h4>
      @forelse($announcements as $a)
        <div class="card mb-3 p-3">
          <h5>{{ $a->title ?? 'No title' }}</h5>
          <p>{{ $a->body ?? '' }}</p>
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
        <p>No announcements yet.</p>
      @endforelse
    </div>
  </div>

  <!-- Grades -->
  <div class="col-md-6">
    <div class="card card-custom p-3">
      <h4 class="mb-3">Your Grades</h4>
      @if($grades->count())
        <table class="table table-bordered">
          <thead>
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
              <td>{{ $g->grade ?? '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      @else
        <p>No grades available yet.</p>
      @endif
    </div>
  </div>
</div>
@endsection
