@extends('layouts.registrar')

@section('title','Sections')
@section('header','Sections')

@section('content')
<div class="card card-custom shadow-sm border-0">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">📚 Sections</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
      <i class="bi bi-plus-circle me-1"></i> Add Section
    </button>
  </div>
  <div class="card-body">
    @include('partials.alerts')

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Grade Level</th>
          <th>School Year</th>
          <th width="100">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sections as $i => $sec)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $sec->name }}</td>
          <td>{{ $sec->gradeLevel->name ?? '-' }}</td>
          <td>{{ $sec->schoolYear->name ?? ($sec->schoolYear ? $sec->schoolYear->start_date.' - '.$sec->schoolYear->end_date : '-') }}</td>
          <td>
            <form action="{{ route('registrars.sections.destroy',$sec->id) }}" method="POST">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Delete section?')"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted">No sections yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="mt-3">{{ $sections->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add Section -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('registrars.sections.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> Add Section</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Grade Level</label>
                <select name="gradelevel_id" class="form-select" required>
                    <option value="">-- Choose --</option>
                    @foreach($gradeLevels as $gl)
                    <option value="{{ $gl->id }}">{{ $gl->name }}</option>
                    @endforeach
                </select>
          </div>
          <div class="mb-3">
            <label class="form-label">School Year</label>
                <select name="school_year_id" class="form-select" required>
                    <option value="">-- Choose --</option>
                    @foreach($schoolYears as $sy)
                    <option value="{{ $sy->id }}">{{ $sy->name ?? ($sy->start_date . ' - ' . $sy->end_date) }}</option>
                    @endforeach
                </select>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
