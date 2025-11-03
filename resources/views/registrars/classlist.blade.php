@extends('layouts.registrar')

@section('title', 'Class List')
@section('header')
    <i class="bi bi-people-fill me-2"></i> Class List
@endsection

@section('content')
<div class="card shadow-sm border-0">
  <!-- Header -->
  <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <!-- 🔙 Back Button -->
      <a href="{{ route('registrars.sections') }}" class="btn btn-outline-secondary btn-sm me-3" title="Back to Sections">
        <i class="bi bi-arrow-left"></i>
      </a>
      <div>
        <h6 class="fw-bold mb-0">
          {{ $section->name }} — {{ $section->gradeLevel->name ?? 'N/A' }}
        </h6>
        <small class="text-muted">
          Adviser:
          <strong>{{ optional($section->adviser->profile)->first_name }}
          {{ optional($section->adviser->profile)->last_name ?? 'N/A' }}</strong><br>
          Capacity:
          <strong>{{ $section->capacity ?? '∞' }}</strong> |
          Enrolled:
          <strong>{{ $section->enrollments->count() }}</strong>
        </small>
      </div>
    </div>
    <a href="{{ route('registrars.classlist.pdf', $section->id) }}" class="btn btn-danger btn-sm shadow-sm">
      <i class="bi bi-file-earmark-pdf"></i> Download Masterlist
    </a>
  </div>

  <!-- Body -->
  <div class="card-body">
    @include('partials.alerts')

    <!-- Section Info Summary -->
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
      <div>
        <i class="bi bi-info-circle me-2"></i>
        <strong>Class Overview:</strong>
        {{ $section->gradeLevel->name ?? 'N/A' }} - {{ $section->name }}
      </div>
      <div>
        <span class="badge bg-primary">Adviser: {{ optional($section->adviser->profile)->last_name ?? 'N/A' }}</span>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover align-middle">
          <thead class="table-primary text-center">
              <tr>
                  <th style="width: 5%">#</th>
                  <th style="width: 15%">LRN</th>
                  <th style="width: 30%">Full Name</th>
                  <th style="width: 15%">Gender</th>
                  <th style="width: 20%">Contact</th>
              </tr>
          </thead>
          <tbody>
              @forelse($students as $index => $s)
                  <tr>
                      <td class="text-center">{{ $index + 1 }}</td> 
                      
                      <td class="fw-bold text-primary text-center">{{ $s->student->student_number ?? 'N/A' }}</td>

                      <td class="text-center">{{ $s->profile->last_name }}, {{ $s->profile->first_name }} {{ $s->profile->middle_name }}</td>

                      <td class="text-center">{{ $s->profile->sex ?? 'N/A' }}</td>
                      
                      <td class="text-center">{{ $s->profile->contact_number ?? 'N/A' }}</td>
                  </tr>
              @empty
                  <tr>
                      <td colspan="5" class="text-center text-muted py-4">
                          <i class="bi bi-people"></i><br>
                          No enrolled students yet in this section.
                      </td>
                  </tr>
              @endforelse
          </tbody>
      </table>
  </div>

    <!-- Footer Summary -->
    <div class="d-flex justify-content-between mt-3 small text-muted">
      <div>
        Total Students: <strong>{{ $section->enrollments->count() }}</strong>
      </div>
      <div>
        Generated on: {{ now()->format('F d, Y h:i A') }}
      </div>
    </div>
  </div>
</div>
@endsection
