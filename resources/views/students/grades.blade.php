@extends('layouts.student')

@section('title', 'Grades')
@section('header')
    <i class="bi bi-award me-2"></i> Grades
@endsection

@section('content')

@if(isset($noActiveSY) && $noActiveSY)
  <div class="alert alert-warning text-center py-4">
    <i class="bi bi-exclamation-triangle fs-4"></i>
    <p class="mt-2 mb-0 fw-semibold">
      The current school year is closed. Grades are unavailable until a new school year starts.
    </p>
  </div>
@else
<div class="container my-4">
  <div class="card border-0 shadow-sm p-4">
    
    @if($grades->count())
      @foreach($grades as $subject => $records)
        @php
          $quartersCompleted = $records->pluck('quarter')->unique()->count();
          $final = $quartersCompleted === 4 ? $records->avg('grade') : null;
          $remarks = $final !== null ? ($final >= 75 ? 'PASSED' : 'FAILED') : null;
        @endphp

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
                
                @foreach($records->sortBy('quarter') as $g)
                <tr>
                  <td>{{ e($g->quarter ?? '-') }}</td>

                  <td>
                    @if($g->grade !== null)
                      <span class="fw-semibold {{ $g->grade < 75 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($g->grade, 2) }}
                      </span>
                    @else
                      -
                    @endif
                  </td>

                  <td>
                    @if($g->grade !== null)
                      {{ $g->grade >= 75 ? 'PASSED' : 'FAILED' }}
                    @else
                      -
                    @endif
                  </td>
                </tr>
                @endforeach

                @if($final !== null)
                <tr class="fw-bold table-light">
                  <td class="text-end" colspan="1">Final Average:</td>
                  <td>{{ number_format($final, 2) }}</td>
                  <td class="{{ $remarks === 'PASSED' ? 'text-success' : 'text-danger' }}">
                    {{ $remarks }}
                  </td>
                </tr>
                @endif

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
@endif
@endsection