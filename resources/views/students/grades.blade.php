@extends('layouts.student')

@section('title','My Grades')
@section('header','Grades')

@section('content')
<div class="row g-3">
  <div class="col-md-12">
    <div class="card card-custom p-4 shadow-sm">
      <h4 class="fw-bold mb-4">
        <i class='bx bx-award'></i> My Grades
      </h4>

      @if($grades->count())
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle">
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
                <td>{{ e($g->subject?->name ?? 'N/A') }}</td>
                <td>{{ e($g->quarter ?? '-') }}</td>
                <td>
                  <span class="badge bg-{{ ($g->grade && $g->grade >= 75) ? 'success' : 'danger' }}">
                    {{ e($g->grade ?? '-') }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-info text-center m-3">
          <i class='bx bx-info-circle'></i> No grades available yet.
        </div>
      @endif

    </div>
  </div>
</div>
@endsection
