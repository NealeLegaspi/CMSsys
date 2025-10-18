@extends('layouts.student')

@section('title', 'Grades')
@section('header')
    <i class="bi bi-award me-2"></i> Grades
@endsection

@section('content')
<div class="container my-4">
  <div class="card border-0 shadow-sm p-4">
    
    @if($grades->count())
      @foreach($grades as $subject => $records)
        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-light fw-bold">
            <i class="bi bi-book"></i> {{ e($subject) }}
          </div>
          <div class="card-body table-responsive">
            <table class="table table-bordered align-middle mb-0">
              <thead class="table-light text-center">
                <tr>
                  <th>Quarter</th>
                  <th>Grade</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody class="text-center">
                @php
                  $final = $records->avg('grade');
                  $remarks = $final >= 75 ? 'PASSED' : 'FAILED';
                @endphp
                @foreach($records as $g)
                <tr>
                  <td>{{ e($g->quarter ?? '-') }}</td>
                  <td>
                    <span class="fw-semibold {{ $g->grade < 75 ? 'text-danger' : 'text-success' }}">
                      {{ e($g->grade ?? '-') }}
                    </span>
                  </td>
                  <td>
                    {{ $g->grade >= 75 ? 'PASSED' : 'FAILED' }}
                  </td>
                </tr>
                @endforeach
                <tr class="fw-bold table-light">
                  <td colspan="1" class="text-end">Final Average:</td>
                  <td>{{ number_format($final, 2) }}</td>
                  <td class="{{ $remarks === 'PASSED' ? 'text-success' : 'text-danger' }}">{{ $remarks }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      @endforeach
    @else
      <div class="alert alert-info text-center py-4">
        <i class="bi bi-info-circle fs-4"></i>
        <p class="mt-2 mb-0">No grades available yet.</p>
      </div>
    @endif
  </div>
</div>
@endsection
