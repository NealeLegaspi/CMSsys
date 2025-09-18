@extends('layouts.registrar')

@section('title','Student Records')
@section('header','Student Records')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">📘 Student List</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
      <i class="bi bi-plus-circle me-1"></i> Add Student
    </button>
  </div>

  <div class="card-body">
    @include('partials.alerts')

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
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-search"></i> Search
        </button>
        <a href="{{ route('registrars.students') }}" class="btn btn-secondary">
          <i class="bi bi-arrow-repeat"></i> Reset
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
                {{ optional($student->student)->student_number ?? 'N/A' }}
              </td>
              <td>
                @php
                  $fname = optional($student->profile)->first_name;
                  $mname = optional($student->profile)->middle_name;
                  $lname = optional($student->profile)->last_name;
                  $fullName = trim($fname . ' ' . ($mname ? substr($mname,0,1).'. ' : '') . $lname);
                @endphp
                {{ $fullName ?: 'N/A' }}
              </td>
              <td>{{ $student->email }}</td>
              <td>{{ optional($student->profile)->sex ?? 'N/A' }}</td>
              <td>{{ optional($student->profile)->contact_number ?? 'N/A' }}</td>
              <td>{{ optional(optional($student->student)->section)->name ?? 'Not Enrolled' }}</td>
              <td>
                <!-- View Button -->
                <button class="btn btn-sm btn-info text-white" 
                        data-bs-toggle="modal" 
                        data-bs-target="#viewStudentModal{{ $student->id }}">
                  <i class="bi bi-eye"></i>
                </button>

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
                      <dd class="col-sm-9">{{ optional($student->student)->student_number ?? 'N/A' }}</dd>

                      <dt class="col-sm-3">Full Name</dt>
                      <dd class="col-sm-9">{{ $fullName ?: 'N/A' }}</dd>

                      <dt class="col-sm-3">Email</dt>
                      <dd class="col-sm-9">{{ $student->email }}</dd>

                      <dt class="col-sm-3">Gender</dt>
                      <dd class="col-sm-9">{{ optional($student->profile)->sex ?? 'N/A' }}</dd>

                      <dt class="col-sm-3">Contact</dt>
                      <dd class="col-sm-9">{{ optional($student->profile)->contact_number ?? 'N/A' }}</dd>

                      <dt class="col-sm-3">Section</dt>
                      <dd class="col-sm-9">{{ optional(optional($student->student)->section)->name ?? 'Not Enrolled' }}</dd>
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
                                 value="{{ old('first_name', optional($student->profile)->first_name) }}" required>
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Middle Name</label>
                          <input type="text" name="middle_name" class="form-control" 
                                 value="{{ old('middle_name', optional($student->profile)->middle_name) }}">
                        </div>
                        <div class="col-md-4">
                          <label class="form-label">Last Name</label>
                          <input type="text" name="last_name" class="form-control" 
                                 value="{{ old('last_name', optional($student->profile)->last_name) }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Email</label>
                          <input type="email" name="email" class="form-control" 
                                 value="{{ old('email', $student->email) }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Section</label>
                          <select name="section_id" class="form-select">
                            <option value="">-- Select Section --</option>
                            @foreach($sections->groupBy('gradelevel_id') as $gradeId => $gradeSections)
                              <optgroup label="{{ $gradeSections->first()->gradeLevel->name }}">
                                @foreach($gradeSections as $sec)
                                  <option value="{{ $sec->id }}" 
                                    @if(optional(optional($student->student)->section)->id == $sec->id) selected @endif>
                                    {{ $sec->name }}
                                  </option>
                                @endforeach
                              </optgroup>
                            @endforeach
                          </select>
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
              <td colspan="8" class="text-center text-muted">No students found.</td>
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

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('registrars.students.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i> Add New Student</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" name="middle_name" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Last Name <span class="text-danger">*</span></label>
              <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Section</label>
              <select name="section_id" class="form-select">
                <option value="">-- Select Section --</option>
                @foreach($sections->groupBy('gradelevel_id') as $gradeId => $gradeSections)
                  <optgroup label="{{ $gradeSections->first()->gradeLevel->name }}">
                    @foreach($gradeSections as $sec)
                      <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
            </div>
            <div class="col-md-12">
              <small class="text-muted">
                🔑 LRN will be <strong>auto-generated</strong> once the student is saved.
              </small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Student</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
