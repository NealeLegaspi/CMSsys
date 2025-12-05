@extends('layouts.registrar')

@section('title', 'Curriculum Details')
@section('header')
    <i class="bi bi-journal-bookmark me-2"></i> Curriculum Details
@endsection

@section('content')
<div class="container-fluid my-4">
  <div class="card shadow-sm border-0">
    <div class="card-body">
      @include('partials.alerts')

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h4 class="mb-1">{{ $curriculum->name }}</h4>
          <p class="text-muted mb-0">
            <i class="bi bi-calendar"></i> School Year: <strong>{{ $curriculum->schoolYear->name ?? 'N/A' }}</strong>
          </p>
        </div>
        <div>
          <a href="{{ route('registrars.curriculum', ['school_year_id' => $curriculum->school_year_id]) }}" 
             class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Curriculum List
          </a>
        </div>
      </div>

      {{-- Subjects by Grade Level Table --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>Grade Level</th>
              <th>Subject Name</th>
            </tr>
          </thead>
          <tbody>
            @forelse($subjectsByGradeLevel as $gradeLevelName => $subjects)
              @foreach($subjects as $index => $subject)
                <tr>
                  @if($index === 0)
                    <td rowspan="{{ $subjects->count() }}" class="align-middle">
                      <strong class="text-primary">{{ $gradeLevelName }}</strong>
                    </td>
                  @endif
                  <td>{{ $subject['name'] }}</td>
                </tr>
              @endforeach
            @empty
              <tr>
                <td colspan="2" class="text-center text-muted py-4">
                  <i class="bi bi-info-circle"></i> No subjects assigned to this curriculum.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        <p class="text-muted small">
          <i class="bi bi-info-circle"></i> 
          Total Subjects: <strong>{{ $curriculum->subjects->count() }}</strong>
        </p>
      </div>
    </div>
  </div>
</div>
@endsection


