@extends('layouts.teacher')

@section('title','Announcements')
@section('header','Announcements')

@section('content')
<div class="container my-4">

  <!-- Flash messages -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Tabs -->
  <ul class="nav nav-tabs" id="announcementTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab">
        <i class="bi bi-plus-circle"></i> Create Announcement
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="my-tab" data-bs-toggle="tab" data-bs-target="#my" type="button" role="tab">
        <i class="bi bi-megaphone"></i> My Announcements
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="global-tab" data-bs-toggle="tab" data-bs-target="#global" type="button" role="tab">
        <i class="bi bi-broadcast"></i> Global Announcements
      </button>
    </li>
  </ul>

  <div class="tab-content mt-3">

    <!-- Create Tab -->
    <div class="tab-pane fade show active" id="create" role="tabpanel">
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body">
          <form action="{{ route('teachers.announcements.store') }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Content</label>
              <textarea name="content" class="form-control" rows="3" required></textarea>
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
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-send"></i> Post
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- My Announcements Tab -->
    <div class="tab-pane fade" id="my" role="tabpanel">
      <!-- Search + Filter -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
          <form method="GET" action="{{ route('teachers.announcements') }}" class="row g-2">
            <div class="col-md-4">
              <input type="text" name="search" class="form-control" placeholder="🔍 Search by title or content" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
              <select name="section_filter" class="form-select">
                <option value="">All Sections</option>
                @foreach(\App\Models\Section::all() as $section)
                  <option value="{{ $section->id }}" {{ request('section_filter') == $section->id ? 'selected' : '' }}>
                    {{ $section->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
              <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
              <a href="{{ route('teachers.announcements') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
          </form>
        </div>
      </div>

      @if($myAnnouncements->count() > 0)
        @foreach($myAnnouncements as $ann)
          <div class="card shadow-sm border-0 rounded-3 mb-3">
            <div class="card-header bg-light d-flex align-items-center">
              <i class="bi bi-megaphone text-primary fs-5 me-2"></i>
              <h6 class="fw-bold mb-0">{{ $ann->title }}</h6>
            </div>
            <div class="card-body">
              <p class="mb-2">{{ $ann->content }}</p>
              <small class="text-secondary d-block">
                <i class="bi bi-calendar-event"></i> {{ $ann->created_at->format('M d, Y h:i A') }}
                | <i class="bi bi-people"></i> Section: {{ $ann->section->name ?? 'N/A' }}
              </small>
              <div class="mt-3 d-flex gap-2">
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $ann->id }}">
                  <i class="bi bi-pencil"></i> Edit
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $ann->id }}">
                  <i class="bi bi-trash"></i> Delete
                </button>
              </div>
            </div>
          </div>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal{{ $ann->id }}" tabindex="-1">
            <div class="modal-dialog">
              <form method="POST" action="{{ route('teachers.announcements.update',$ann) }}">
                @csrf
                @method('PUT')
                <div class="modal-content">
                  <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label">Title</label>
                      <input type="text" name="title" class="form-control" value="{{ $ann->title }}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Content</label>
                      <textarea name="content" class="form-control" rows="3" required>{{ $ann->content }}</textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Update</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Delete Modal -->
          <div class="modal fade" id="deleteModal{{ $ann->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                  <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirm Delete</h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  Are you sure you want to delete this announcement?
                </div>
                <div class="modal-footer">
                  <form method="POST" action="{{ route('teachers.announcements.destroy',$ann) }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Delete</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        @endforeach

        <div class="mt-3">
          {{ $myAnnouncements->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
      @else
        <div class="card shadow-sm border-0 text-center p-4">
          <p class="text-muted mb-0"><i class="bi bi-exclamation-circle"></i> No announcements found.</p>
        </div>
      @endif
    </div>

    <!-- Global Announcements Tab -->
    <div class="tab-pane fade" id="global" role="tabpanel">
      @if($globalAnnouncements->count() > 0)
        @foreach($globalAnnouncements as $ann)
          <div class="card shadow-sm border-0 rounded-3 mb-3">
            <div class="card-header bg-light d-flex align-items-center">
              <i class="bi bi-broadcast text-danger fs-5 me-2"></i>
              <h6 class="fw-bold mb-0">{{ $ann->title }}</h6>
            </div>
            <div class="card-body">
              <p class="mb-2">{{ $ann->content }}</p>
              <small class="text-secondary d-block">
                <i class="bi bi-calendar-event"></i> {{ $ann->created_at->format('M d, Y h:i A') }}
                | <i class="bi bi-person-badge"></i> Posted by:
                {{ $ann->user?->profile?->first_name ?? $ann->user?->name ?? 'Admin' }}
                {{ $ann->user?->profile?->last_name ?? '' }}
              </small>
            </div>
          </div>
        @endforeach

        <div class="mt-3">
          {{ $globalAnnouncements->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
      @else
        <div class="card shadow-sm border-0 text-center p-4">
          <p class="text-muted mb-0"><i class="bi bi-exclamation-circle"></i> No global announcements found.</p>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
