@extends('layouts.registrar')

@section('title','Archived Teachers')
@section('header','Archived Teachers')

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-header bg-light">
    <div class="d-flex justify-content-between align-items-center">
      <h6 class="fw-bold mb-0">
        <i class="bi bi-person-workspace me-2"></i> Teachers
      </h6>
    </div>
    <ul class="nav nav-tabs mt-3">
      <li class="nav-item">
        <a class="nav-link" href="{{ route('registrars.teachers') }}">
          Active
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link active" href="{{ route('registrars.teachers.archived') }}">
          Archived
        </a>
      </li>
    </ul>
  </div>

  <div class="card-body">
    @include('partials.alerts')

    <!-- 🔍 Search -->
    <form method="GET" action="{{ route('registrars.teachers.archived') }}" class="row g-2 mb-3">
      <div class="col-md-6">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or email...">
      </div>
      <div class="col-md-4">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> Search
        </button>
        <a href="{{ route('registrars.teachers.archived') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-clockwise"></i> Reset
        </a>
      </div>
    </form>

    <!-- 🧾 Archived Teachers Table -->
    <div class="table-responsive">
      <table class="table table-hover table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th style="width:50px;">#</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Contact</th>
            <th class="text-center" style="width:140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($teachers as $i => $teacher)
          <tr>
            <td>{{ $teachers->firstItem() + $i }}</td>
            <td>{{ $teacher->profile->last_name }}, {{ $teacher->profile->first_name }} {{ $teacher->profile->middle_name }}</td>
            <td>{{ $teacher->email }}</td>
            <td>{{ $teacher->profile->contact_number ?? '—' }}</td>
            <td class="text-center">
              <form method="POST" action="{{ route('registrars.teachers.restore', $teacher->id) }}" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-sm btn-success">
                  <i class="bi bi-arrow-counterclockwise me-1"></i> Unarchive
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center text-muted py-4">
              <i class="bi bi-exclamation-circle"></i> No archived teachers.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
      {{ $teachers->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection


