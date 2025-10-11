@extends('layouts.admin')

@section('title', 'Student Records')
@section('header', 'Student Records')

@section('content')
<div class="container-fluid my-4">

  <div class="card shadow-sm border-0">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <h6 class="fw-bold mb-0">
        <i class="bi bi-mortarboard"></i> Student Records
      </h6>
      <!---<div class="btn-group">
        <a href="{{ route('admins.exportStudents', ['format' => 'csv'] + request()->all()) }}" class="btn btn-success btn-sm">
          <i class="bi bi-file-earmark-spreadsheet"></i> CSV
        </a>
        <a href="{{ route('admins.exportStudents', ['format' => 'pdf'] + request()->all()) }}" class="btn btn-danger btn-sm">
          <i class="bi bi-file-earmark-pdf"></i> PDF
        </a>
      </div>---->
    </div>

    <div class="card-body">

      {{-- 🔍 Filter Toolbar --}}
      <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-3">
          <label class="form-label fw-semibold">Search</label>
          <input type="text" name="search" class="form-control" placeholder="Name or section..." value="{{ request('search') }}">
        </div>

        <div class="col-md-2">
          <label class="form-label fw-semibold">Grade Level</label>
          <select name="grade_level" class="form-select">
            <option value="">All</option>
            @foreach($gradeLevels as $level)
              <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>
                {{ $level }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label fw-semibold">School Year</label>
          <select name="school_year" class="form-select">
            <option value="">All</option>
            @foreach($schoolYears as $year)
              <option value="{{ $year }}" {{ request('school_year') == $year ? 'selected' : '' }}>
                {{ $year }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            <option value="Enrolled" {{ request('status') == 'Enrolled' ? 'selected' : '' }}>Enrolled</option>
            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Dropped" {{ request('status') == 'Dropped' ? 'selected' : '' }}>Dropped</option>
          </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-fill">
            <i class="bi bi-search"></i> Filter
          </button>
          <a href="{{ route('admins.student-records') }}" class="btn btn-outline-secondary flex-fill">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        </div>
      </form>

      {{-- 📋 Records Table --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th class="text-start">Student Name</th>
              <th>Grade Level</th>
              <th>Section</th>
              <th>School Year</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($records as $index => $record)
              <tr>
                <td>{{ $records->firstItem() + $index }}</td>
                <td class="text-start">
                  {{ $record->student->user->profile->last_name ?? '' }},
                  {{ $record->student->user->profile->first_name ?? '' }}
                </td>
                <td>{{ $record->section->gradeLevel->name ?? 'N/A' }}</td>
                <td>{{ $record->section->name ?? 'N/A' }}</td>
                <td>{{ $record->schoolYear->name ?? 'N/A' }}</td>
                <td>
                  <span class="badge rounded-pill
                    @if($record->status == 'Enrolled') bg-success
                    @elseif($record->status == 'Pending') bg-warning text-dark
                    @elseif($record->status == 'Dropped') bg-danger
                    @else bg-secondary @endif">
                    {{ $record->status }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewModal{{ $record->id }}">
                    <i class="bi bi-eye"></i>
                  </button>
                </td>
              </tr>

              {{-- 🔍 View Details Modal --}}
              <div class="modal fade" id="viewModal{{ $record->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $record->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                      <h5 class="modal-title">
                        <i class="bi bi-person-lines-fill me-2"></i> Student Details
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row g-3">
                        <div class="col-md-4 text-center">
                          <img src="{{ $record->student->user->profile->profile_picture 
                              ? asset('storage/'.$record->student->user->profile->profile_picture) 
                              : asset('images/default-student.png') }}" 
                              class="rounded-circle shadow-sm mb-3" 
                              style="width:120px;height:120px;object-fit:cover;">
                          <p class="fw-bold mb-0">
                            {{ $record->student->user->profile->first_name ?? '' }} 
                            {{ $record->student->user->profile->last_name ?? '' }}
                          </p>
                          <small class="text-muted">Student ID: {{ $record->student->student_number ?? 'N/A' }}</small>
                        </div>

                        <div class="col-md-8">
                          <div class="row">
                            <div class="col-md-6">
                              <p><strong>Grade Level:</strong> {{ $record->section->gradeLevel->name ?? 'N/A' }}</p>
                              <p><strong>Section:</strong> {{ $record->section->name ?? 'N/A' }}</p>
                              <p><strong>School Year:</strong> {{ $record->schoolYear->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                              <p><strong>Status:</strong>
                                <span class="badge 
                                  @if($record->status == 'Enrolled') bg-success
                                  @elseif($record->status == 'Pending') bg-warning text-dark
                                  @elseif($record->status == 'Dropped') bg-danger
                                  @else bg-secondary @endif">
                                  {{ $record->status }}
                                </span>
                              </p>
                              <p><strong>Email:</strong> {{ $record->student->user->email ?? 'N/A' }}</p>
                              <p><strong>Contact:</strong> {{ $record->student->user->profile->contact_number ?? 'N/A' }}</p>
                            </div>
                          </div>
                          <p><strong>Address:</strong> {{ $record->student->user->profile->address ?? 'N/A' }}</p>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                  <i class="bi bi-info-circle"></i> No student records found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      <div class="d-flex justify-content-end mt-3">
        {{ $records->appends(request()->query())->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
