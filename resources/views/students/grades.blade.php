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
    <div class="d-flex justify-content-between align-items-center mb-3 small text-muted">
      <div>
        @isset($section)
          Section:
          <strong>
            {{ $section->gradeLevel->name ?? 'N/A' }}
            @if($section->name)
              - {{ $section->name }}
            @endif
          </strong>
        @else
          Section: <strong>N/A</strong>
        @endisset
      </div>
      <div class="d-flex align-items-center">
        @isset($schoolYears)
          <form method="GET" action="{{ route('students.grades') }}" class="d-flex align-items-center">
            <label for="school_year_id" class="me-2">School Year:</label>
            <select name="school_year_id" id="school_year_id" class="form-select form-select-sm" onchange="this.form.submit()">
              @foreach($schoolYears as $sy)
                <option value="{{ $sy->id }}"
                  {{ (isset($selectedSchoolYearId) && $selectedSchoolYearId == $sy->id) ? 'selected' : '' }}>
                  {{ $sy->name ?? ($sy->start_date . ' - ' . $sy->end_date) }}
                </option>
              @endforeach
            </select>
            <noscript>
              <button type="submit" class="btn btn-sm btn-primary ms-2">Go</button>
            </noscript>
          </form>
        @else
          School Year:
          <strong>{{ $activeSY->name ?? ($activeSY->start_date . ' - ' . $activeSY->end_date) }}</strong>
        @endisset
      </div>
    </div>

    @if($subjects->count())
      @foreach($subjects as $item)
        @php
          $subject = $item['subject'];
          $teacher = $item['teacher'];
          $records = $item['grades'];

          // Compute final only when all 4 quarters have grades
          $quartersCompleted = $records->pluck('quarter')->unique()->count();
          $final = $quartersCompleted === 4 ? $records->avg('grade') : null;
          $remarks = $final !== null ? ($final >= 75 ? 'PASSED' : 'FAILED') : null;
        @endphp

        <div class="card mb-4 border-0 shadow-sm">
          <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
            <div>
              <i class="bi bi-book"></i>
              {{ e($subject->name ?? 'Subject') }}
            </div>
            <div class="text-muted small">
              <i class="bi bi-person-badge me-1"></i>
              @if($teacher)
                {{ $teacher->profile->full_name ?? $teacher->full_name ?? $teacher->email }}
              @else
                No Subject Teacher Assigned
              @endif
            </div>
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
                  $quartersOrder = ['1st', '2nd', '3rd', '4th'];
                @endphp

                @foreach($quartersOrder as $q)
                  @php
                    $g = $records->firstWhere('quarter', $q);
                  @endphp
                  <tr>
                    <td>{{ $q }}</td>

                    <td>
                      @if($g && $g->grade !== null)
                        <span class="fw-semibold {{ $g->grade < 75 ? 'text-danger' : 'text-success' }}">
                          {{ number_format($g->grade, 2) }}
                        </span>
                      @else
                        -
                      @endif
                    </td>

                    <td>
                      @if($g && $g->grade !== null)
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
        <p class="mt-2 mb-0">No subjects assigned to this section yet.</p>
      </div>
    @endif
  </div>
</div>
@endif
@endsection