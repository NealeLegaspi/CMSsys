@extends('layouts.teacher')

@section('title','Assignments')
@section('header','Assignments')

@section('content')
<div class="container my-4">

  <!-- Success / Error Messages -->
  <div id="alert-container">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-3" id="assignmentTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab">
        Create Assignment
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="list-tab" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">
        My Assignments
      </button>
    </li>
  </ul>

  <div class="tab-content" id="assignmentTabsContent">
    <!-- Create Assignment Tab -->
    <div class="tab-pane fade show active" id="create" role="tabpanel" aria-labelledby="create-tab">
      <div class="card card-custom mb-4">
        <div class="card-body">
          <form action="{{ route('teachers.assignments.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Instructions</label>
              <textarea name="instructions" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Due Date</label>
              <input type="date" name="due_date" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Section</label>
              <select name="section_id" id="sectionSelect" class="form-select" required>
                <option value="">-- Select Section --</option>
                @foreach($sections->groupBy('gradelevel_id') as $gradeId => $gradeSections)
                  <optgroup label="{{ $gradeSections->first()->gradeLevel->name }}">
                    @foreach($gradeSections as $sec)
                      <option value="{{ $sec->id }}" data-grade="{{ $sec->gradelevel_id }}">
                        {{ $sec->name }}
                      </option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Subject <span class="text-danger">*</span></label>
              <select name="subject_id" id="subjectSelect" class="form-select" required>
                <option value="">-- Select Subject --</option>
              </select>
            </div>
            <button type="submit" class="btn btn-success">Post</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Assignments List Tab -->
    <div class="tab-pane fade" id="list" role="tabpanel" aria-labelledby="list-tab">
      <div class="card card-custom">
        <div class="card-body">
          @if($assignments->count() > 0)
            <table class="table table-bordered" id="assignmentsTable">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Instructions</th>
                  <th>Section</th>
                  <th>Subject</th>
                  <th>Due Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($assignments as $a)
                  <tr id="assignmentRow{{ $a->id }}">
                  <td>{{ $a->title }}</td>
                  <td>{{ $a->instructions }}</td>
                  <td>{{ $a->section->name ?? 'N/A' }}</td>
                  <td>{{ $a->subject->name ?? 'N/A' }}</td>
                  <td>{{ $a->due_date ? \Carbon\Carbon::parse($a->due_date)->format('M d, Y') : 'No due date' }}</td>
                    <td>
                      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $a->id }}">
                        Edit
                      </button>
                      <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $a->id }}">
                        Delete
                      </button>
                    </td>
                  </tr>

                  <!-- Edit Modal -->
                  <div class="modal fade" id="editModal{{ $a->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                      <form class="edit-assignment-form" data-id="{{ $a->id }}" method="POST" action="{{ route('teachers.assignments.update', $a->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Edit Assignment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                          </div>
                          <div class="modal-body">
                            <div class="mb-3">
                              <label class="form-label">Title</label>
                              <input type="text" name="title" class="form-control" value="{{ $a->title }}" required>
                            </div>
                            <div class="mb-3">
                              <label class="form-label">Instructions</label>
                              <textarea name="instructions" class="form-control" rows="3">{{ $a->instructions }}</textarea>
                            </div>
                            <div class="mb-3">
                              <label class="form-label">Due Date</label>
                              <input type="date" name="due_date" class="form-control" value="{{ $a->due_date }}">
                            </div>
                            <div class="mb-3">
                              <label class="form-label">Section</label>
                              <select name="section_id" class="form-select">
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
                              <label class="form-label">Subject</label>
                              <select name="subject_id" class="form-select" required>
                                @foreach($subjects[$a->section->gradelevel_id] ?? [] as $sub)
                                  <option value="{{ $sub->id }}" {{ $a->subject_id == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                  </option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>

                  <!-- Delete Modal -->
                  <div class="modal fade" id="deleteModal{{ $a->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                          <h5 class="modal-title">Confirm Delete</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          Are you sure you want to delete the assignment <strong>{{ $a->title }}</strong>?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button 
                            type="button" 
                            class="btn btn-danger btn-delete-assignment" 
                            data-id="{{ $a->id }}" 
                            data-url="{{ route('teachers.assignments.destroy', $a->id) }}">
                            Delete
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </tbody>
            </table>
          @else
            <p>No assignments yet. Post one above.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const subjectsByGrade = @json($subjects);

  // Dependent dropdown for create form
  const sectionSelect = document.getElementById('sectionSelect');
  const subjectSelect = document.getElementById('subjectSelect');
  if(sectionSelect){
    sectionSelect.addEventListener('change', function () {
      const gradeId = this.options[this.selectedIndex].dataset.grade;
      subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
      if (subjectsByGrade[gradeId]) {
        subjectsByGrade[gradeId].forEach(sub => {
          const opt = document.createElement('option');
          opt.value = sub.id;
          opt.textContent = sub.name;
          subjectSelect.appendChild(opt);
        });
      }
    });
  }

  // Handle Edit via AJAX
  document.querySelectorAll('.edit-assignment-form').forEach(form => {
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      const id = this.dataset.id;
      const formData = new FormData(this);
      const res = await fetch(this.action, {
        method: "POST", // Laravel spoofing
        headers: {'X-CSRF-TOKEN': formData.get('_token')},
        body: formData
      });
      if(res.ok){
        location.reload(); // simple refresh for now
      }
    });
  });

  // Handle Delete via AJAX
  document.querySelectorAll('.btn-delete-assignment').forEach(btn => {
    btn.addEventListener('click', async function(){
      const id = this.dataset.id;
      const url = this.dataset.url;

      const res = await fetch(url, {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: new URLSearchParams({ _method: "DELETE" })
      });

      if(res.ok){
        // Remove row
        const row = document.getElementById('assignmentRow'+id);
        if(row) row.remove();

        // Hide modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'+id));
        modal.hide();

        // Check if table has rows left
        const tbody = document.querySelector("table tbody");
        if(tbody && tbody.children.length === 0){
          tbody.innerHTML = `<tr>
            <td colspan="6" class="text-center text-muted">No assignments yet. Post one above.</td>
          </tr>`;
        }

        // Show success alert
        const alertBox = document.createElement('div');
        alertBox.className = 'alert alert-success mt-2';
        alertBox.innerText = 'Assignment deleted successfully!';
        document.getElementById('alert-container')?.appendChild(alertBox);
      }
    });
  });
</script>
@endsection
