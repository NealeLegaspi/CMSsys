@extends('layouts.teacher')

@section('title','Assignments')
@section('header','Assignments')

@section('content')
<div class="container my-4">

  <!-- Alerts -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <ul class="mb-0">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-3" id="assignmentTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab">
        <i class="bi bi-plus-circle"></i> Create Assignment
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">
        <i class="bi bi-list-task"></i> My Assignments
      </button>
    </li>
  </ul>

  <div class="tab-content" id="assignmentTabsContent">

    <!-- Create Assignment -->
    <div class="tab-pane fade show active" id="create" role="tabpanel">
      <div class="card card-custom shadow-sm">
        <div class="card-body">
          <form action="{{ route('teachers.assignments.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
              <input type="text" name="title" class="form-control" placeholder="Enter assignment title" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Instructions</label>
              <textarea name="instructions" class="form-control" rows="3" placeholder="Write assignment details..."></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Due Date</label>
              <input type="date" name="due_date" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Section <span class="text-danger">*</span></label>
              <select name="section_id" class="form-select" required>
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
            <div class="mb-3">
              <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
              <select name="subject_id" class="form-select" required>
                <option value="">-- Select Subject --</option>
                @foreach($subjects as $sub)
                  <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-success px-4">
                <i class="bi bi-send"></i> Post Assignment
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Assignment List -->
    <div class="tab-pane fade" id="list" role="tabpanel">
      <div class="card card-custom shadow-sm">
        <div class="card-body">
          @if($assignments->count() > 0)
            <div class="table-responsive">
              <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Title</th>
                    <th>Section</th>
                    <th>Subject</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($assignments as $a)
                    <tr>
                      <td class="fw-semibold">{{ $a->title }}</td>
                      <td><span class="badge bg-primary">{{ $a->section->name ?? 'N/A' }}</span></td>
                      <td><span class="badge bg-secondary">{{ $a->subject->name ?? 'N/A' }}</span></td>
                      <td>
                        @if($a->due_date)
                          <span class="badge bg-info">
                            {{ \Carbon\Carbon::parse($a->due_date)->format('M d, Y') }}
                          </span>
                        @else
                          <span class="badge bg-dark">No due date</span>
                        @endif
                      </td>
                      <td>
                        <!-- Edit -->
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $a->id }}">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <!-- Delete -->
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $a->id }}">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $a->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                      <div class="modal-dialog modal-lg modal-dialog-centered">
                        <form method="POST" action="{{ route('teachers.assignments.update', $a->id) }}">
                          @csrf
                          @method('PUT')
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Edit Assignment</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <div class="mb-3">
                                <label class="form-label fw-bold">Title</label>
                                <input type="text" name="title" class="form-control" value="{{ $a->title }}" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-bold">Instructions</label>
                                <textarea name="instructions" class="form-control" rows="3">{{ $a->instructions }}</textarea>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-bold">Due Date</label>
                                <input type="date" name="due_date" class="form-control" value="{{ $a->due_date }}">
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-bold">Section</label>
                                <select name="section_id" class="form-select" required>
                                  @foreach($sections->groupBy('gradelevel_id') as $gradeId => $gradeSections)
                                    <optgroup label="{{ $gradeSections->first()->gradeLevel->name }}">
                                      @foreach($gradeSections as $sec)
                                        <option value="{{ $sec->id }}" {{ $a->section_id == $sec->id ? 'selected' : '' }}>
                                          {{ $sec->name }}
                                        </option>
                                      @endforeach
                                    </optgroup>
                                  @endforeach
                                </select>
                              </div>
                              <div class="mb-3">
                                <label class="form-label fw-bold">Subject</label>
                                <select name="subject_id" class="form-select" required>
                                  @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}" {{ $a->subject_id == $sub->id ? 'selected' : '' }}>
                                      {{ $sub->name }}
                                    </option>
                                  @endforeach
                                </select>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save"></i> Save Changes
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal{{ $a->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            Are you sure you want to delete <strong>{{ $a->title }}</strong>?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form method="POST" action="{{ route('teachers.assignments.destroy', $a->id) }}">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger px-4">
                                <i class="bi bi-trash"></i> Delete
                              </button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center text-muted py-5">
              <i class="bi bi-journal-x fs-1"></i>
              <p class="mt-2">No assignments yet. Create one above.</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection