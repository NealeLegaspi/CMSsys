@extends('layouts.admin')

@section('title', 'Student Records')
@section('header', 'Student Records')

@section('content')
<div class="container my-4">
  <div class="card shadow-sm border-0">
    <div class="card-body"> 

      <!-- Filters -->
      <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
          <input type="text" name="search" class="form-control" placeholder="Search name or section..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <select name="grade_level" class="form-select">
            <option value="">All Grade Levels</option>
            @foreach($gradeLevels as $level)
              <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>{{ $level }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="school_year" class="form-select">
            <option value="">All School Years</option>
            @foreach($schoolYears as $year)
              <option value="{{ $year }}" {{ request('school_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="Enrolled" {{ request('status') == 'Enrolled' ? 'selected' : '' }}>Enrolled</option>
            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Dropped" {{ request('status') == 'Dropped' ? 'selected' : '' }}>Dropped</option>
          </select>
        </div>
        <div class="col-md-2 d-grid">
          <button class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
        </div>
      </form>

      <!-- Records Table -->
      <div class="table-responsive">
        <table class="table table-bordered align-middle table-hover">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Student Name</th>
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
                <td>
                  {{ $record->student->user->profile->last_name ?? '' }},
                  {{ $record->student->user->profile->first_name ?? '' }}
                </td>
                <td>{{ $record->section->gradeLevel->name ?? 'N/A' }}</td>
                <td>{{ $record->section->name ?? 'N/A' }}</td>
                <td>{{ $record->schoolYear->name ?? 'N/A' }}</td>
                <td>
                  <span class="badge 
                    @if($record->status == 'Enrolled') bg-success
                    @elseif($record->status == 'Pending') bg-warning text-dark
                    @elseif($record->status == 'Dropped') bg-danger
                    @else bg-secondary @endif">
                    {{ $record->status }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-info" 
                          data-bs-toggle="modal" 
                          data-bs-target="#viewModal{{ $record->id }}">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>

              <!-- View Details Modal -->
              <div class="modal fade" id="viewModal{{ $record->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $record->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                      <h5 class="modal-title" id="viewModalLabel{{ $record->id }}">
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
                              class="rounded-circle mb-3" 
                              style="width:120px;height:120px;object-fit:cover;">
                        </div>
                        <div class="col-md-8">
                          <p><strong>Name:</strong> {{ $record->student->user->profile->first_name ?? '' }} {{ $record->student->user->profile->last_name ?? '' }}</p>
                          <p><strong>Grade Level:</strong> {{ $record->section->gradeLevel->name ?? 'N/A' }}</p>
                          <p><strong>Section:</strong> {{ $record->section->name ?? 'N/A' }}</p>
                          <p><strong>School Year:</strong> {{ $record->schoolYear->name ?? 'N/A' }}</p>
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
                          <p><strong>Address:</strong> {{ $record->student->user->profile->address ?? 'N/A' }}</p>
                          <p><strong>Contact:</strong> {{ $record->student->user->profile->contact_number ?? 'N/A' }}</p>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-3">
                  <i class='bx bx-info-circle'></i> No student records found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-end">
        {{ $records->appends(request()->query())->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
