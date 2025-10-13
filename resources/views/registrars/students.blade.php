@extends('layouts.registrar')

@section('title','Student Records')
@section('header','Student Records')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">
      <i class="bi bi-journal-check me-2"></i> Enrolled Students
    </h6>
    {{-- Optional: Uncomment if you still want to manually add students --}}
    {{-- 
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
      <i class="bi bi-plus-circle me-1"></i> Register New Student
    </button>
    --}}
  </div>

  <div class="card-body">
    @include('partials.alerts')

    @if(isset($activeSY))
      <div class="alert alert-info mb-3">
        Showing students enrolled in 
        <strong>{{ $activeSY->name ?? 'N/A' }}</strong> school year.
      </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('registrars.students') }}" class="row g-2 mb-3">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" 
              placeholder="Search by name, LRN, or email"
              value="{{ request('search') }}">
      </div>
      <div class="col-md-4">
        <select name="section_id" id="sectionSelect" class="form-select">
          <option value="">-- All Sections --</option>
          @foreach($sections->groupBy('gradelevel_id') as $gradeId => $gradeSections)
            <optgroup label="{{ $gradeSections->first()->gradeLevel->name }}">
              @foreach($gradeSections as $sec)
                <option value="{{ $sec->id }}" 
                  @if(request('section_id') == $sec->id) selected @endif>
                  {{ $sec->name }}
                </option>
              @endforeach
            </optgroup>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> Search
        </button>
        <a href="{{ route('registrars.students') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-clockwise"></i> Reset
        </a>
      </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>LRN</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Contact</th>
            <th>Section</th>
            <th width="180">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $index => $student)
            <tr>
              <td>{{ $students->firstItem() + $index }}</td>
              <td class="fw-bold text-primary">
                {{ $student->student->student_number ?? 'N/A' }}
              </td>
              <td>
                @php
                  $fname = $student->profile->first_name ?? '';
                  $mname = $student->profile->middle_name ?? '';
                  $lname = $student->profile->last_name ?? '';
                  $fullName = trim($fname . ' ' . ($mname ? substr($mname,0,1).'. ' : '') . $lname);
                @endphp
                {{ $fullName ?: 'N/A' }}
              </td>
              <td>{{ $student->email }}</td>
              <td>{{ $student->profile->sex ?? 'N/A' }}</td>
              <td>{{ $student->profile->contact_number ?? 'N/A' }}</td>
              <td>{{ $student->student->section->name ?? 'N/A' }}</td>
              <td>
                <!-- View Record Button -->
                <a href="{{ route('registrars.student.record', $student->student->id) }}" 
                  class="btn btn-sm btn-info text-white">
                  <i class="bi bi-eye"></i>
                </a>
                <!-- Edit Button -->
                <button class="btn btn-sm btn-warning" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editStudentModal{{ $student->id }}">
                  <i class="bi bi-pencil"></i>
                </button>

                <!-- Delete Button -->
                <form action="{{ route('registrars.students.destroy', $student->id) }}" 
                      method="POST" 
                      class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" 
                          class="btn btn-sm btn-danger" 
                          onclick="return confirm('Are you sure you want to delete this student?')">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>

            <!-- View Student Modal -->
            <div class="modal fade" id="viewStudentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="bi bi-person-lines-fill me-2"></i>View Student</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <dl class="row">
                      <dt class="col-sm-3">LRN</dt>
                      <dd class="col-sm-9">{{ $student->student->student_number ?? 'N/A' }}</dd>

                      <dt class="col-sm-3">Full Name</dt>
                      <dd class="col-sm-9">{{ $fullName ?: 'N/A' }}</dd>

                      <dt class="col-sm-3">Email</dt>
                      <dd class="col-sm-9">{{ $student->email }}</dd>

                      <dt class="col-sm-3">Gender</dt>
                      <dd class="col-sm-9">{{ $student->profile->sex ?? 'N/A' }}</dd>

                      <dt class="col-sm-3">Contact</dt>
                      <dd class="col-sm-9">{{ $student->profile->contact_number ?? 'N/A' }}</dd>

                      <dt class="col-sm-3">Section</dt>
                      <dd class="col-sm-9">{{ $student->student->section->name ?? 'N/A' }}</dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Edit Student Modal -->
            <div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <form method="POST" action="{{ route('registrars.students.update', $student->id) }}">
                  @csrf
                  @method('PUT')
                  <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                      <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Student</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="row g-3">
                        <div class="col-md-4">
                          <label class="form-label">First Name</label>
                          <input type="text" name="first_name" class="form-control" 
                                 value="{{ old('first_name', $student->profile->first_name) }}" required>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Middle Name</label>
                          <input type="text" name="middle_name" class="form-control" 
                                 value="{{ old('middle_name', $student->profile->middle_name) }}">
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Last Name</label>
                          <input type="text" name="last_name" class="form-control" 
                                 value="{{ old('last_name', $student->profile->last_name) }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Email</label>
                          <input type="email" name="email" class="form-control" 
                                 value="{{ old('email', $student->email) }}" required>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-warning">Update</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

          @empty
            <tr>
              <td colspan="8" class="text-center text-muted">
                No enrolled students found. <br>
                <small>Students will appear here once they’ve been officially enrolled.</small>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
      {{ $students->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection
