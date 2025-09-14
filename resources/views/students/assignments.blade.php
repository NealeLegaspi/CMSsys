@extends('layouts.student')

@section('title','Assignments')
@section('header','Assignments')

@section('content')
<div class="row g-3">
  <div class="col-md-12">
    <div class="card card-custom p-4 shadow-sm">
      <h4 class="fw-bold mb-4">
        <i class='bx bx-book'></i> Latest Assignments
      </h4>

      @forelse($assignments as $assignment)
        <div class="card mb-3 border-0 shadow-sm">
          <div class="card-body">
            <h5 class="card-title fw-bold text-primary">
              {{ e($assignment->title) }}
            </h5>
            <p class="card-text text-secondary">
              {!! nl2br(e(Str::limit($assignment->instructions, 200))) !!}
            </p>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
              <small class="text-muted d-block">
                📘 <strong>Subject:</strong> {{ e($assignment->subject->name ?? 'N/A') }} <br>
                🏫 <strong>Section:</strong> {{ e($assignment->section->name ?? 'N/A') }} <br>
                👨‍🏫 <strong>Teacher:</strong> {{ e($assignment->teacher->profile->first_name ?? $assignment->teacher->name ?? 'N/A') }} <br>
                ⏰ <strong>Due:</strong> {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') : 'No deadline' }}
              </small>
              <a href="{{ route('student.assignments.show', $assignment->id) }}" 
                 class="btn btn-sm btn-outline-primary mt-2 mt-md-0">
                View Details
              </a>
            </div>
          </div>
        </div>
      @empty
        <div class="alert alert-info text-center m-3">
          <i class='bx bx-info-circle'></i> No assignments available at the moment.
        </div>
      @endforelse

    </div>
  </div>
</div>
@endsection
