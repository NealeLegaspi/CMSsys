@extends('layouts.teacher')

@section('title','Announcements')
@section('header')
    <i class="bi bi-megaphone me-2"></i> Announcements
@endsection

@section('content')
<div class="container-fluid my-4">

  <!-- Flash message -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
      <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-3" id="announcementTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active fw-semibold" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab">
        <i class="bi bi-plus-circle text-primary me-1"></i> Create
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link fw-semibold" id="my-tab" data-bs-toggle="tab" data-bs-target="#my" type="button" role="tab">
        <i class="bi bi-megaphone text-success me-1"></i> My Announcements
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link fw-semibold" id="global-tab" data-bs-toggle="tab" data-bs-target="#global" type="button" role="tab">
        <i class="bi bi-broadcast text-danger me-1"></i> Global
      </button>
    </li>
  </ul>

  <div class="tab-content" id="announcementTabsContent">
    
    <!-- CREATE TAB -->
    <div class="tab-pane fade show active" id="create" role="tabpanel">
      <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body">
          <form method="POST" action="{{ route('teachers.announcements.store') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Title</label>
                <input type="text" name="title" class="form-control" placeholder="Enter title..." required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Section</label>
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
              <div class="col-12">
                <label class="form-label fw-semibold">Content</label>
                <textarea name="content" class="form-control" rows="4" placeholder="Write your announcement..." required></textarea>
              </div>
            </div>
            <div class="mt-4 text-end">
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-send me-1"></i> Post
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- MY ANNOUNCEMENTS TAB -->
    <div class="tab-pane fade" id="my" role="tabpanel">
      <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-body">
          <form method="GET" action="{{ route('teachers.announcements') }}" class="row g-2">
            <div class="col-md-5">
              <input type="text" name="search" class="form-control" placeholder="Search by title or content..." value="{{ request('search') }}">
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
            <div class="col-md-3 d-flex gap-2">
              <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
              <a href="{{ route('teachers.announcements') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
            </div>
          </form>
        </div>
      </div>

      @if($myAnnouncements->count())
        @foreach($myAnnouncements as $ann)
          <div class="card shadow-sm border-0 rounded-3 mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="bi bi-megaphone text-success fs-5 me-2"></i>
                <h6 class="fw-semibold mb-0">{{ $ann->title }}</h6>
              </div>
              <small class="text-muted">{{ $ann->created_at->format('M d, Y h:i A') }}</small>
            </div>
            <div class="card-body">
              <p class="mb-2">{{ $ann->content }}</p>
              <small class="text-secondary">
                <i class="bi bi-people me-1"></i> Section: <strong>{{ $ann->section->name ?? 'N/A' }}</strong>
              </small>
              <div class="mt-3 d-flex gap-2">
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $ann->id }}">
                  <i class="bi bi-pencil"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $ann->id }}">
                  <i class="bi bi-trash"></i> Delete
                </button>
              </div>
            </div>
          </div>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal{{ $ann->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <form method="POST" action="{{ route('teachers.announcements.update',$ann) }}">
                @csrf
                @method('PUT')
                <div class="modal-content">
                  <div class="modal-header bg-light">
                    <h5 class="modal-title fw-semibold"><i class="bi bi-pencil-square me-2"></i>Edit Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Title</label>
                      <input type="text" name="title" class="form-control" value="{{ $ann->title }}" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Content</label>
                      <textarea name="content" class="form-control" rows="3" required>{{ $ann->content }}</textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Update</button>
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
                  <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-1"></i> Confirm Delete</h5>
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
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Delete</button>
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
        <div class="card text-center border-0 shadow-sm p-4">
          <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i>No announcements found.</p>
        </div>
      @endif
    </div>

    <!-- GLOBAL ANNOUNCEMENTS TAB -->
    <div class="tab-pane fade" id="global" role="tabpanel">
      @if($globalAnnouncements->count())
        @foreach($globalAnnouncements as $ann)
          <div class="card shadow-sm border-0 rounded-3 mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="bi bi-broadcast text-danger fs-5 me-2"></i>
                <h6 class="fw-semibold mb-0">{{ $ann->title }}</h6>
              </div>
              <small class="text-muted">{{ $ann->created_at->format('M d, Y h:i A') }}</small>
            </div>
            <div class="card-body">
              <p class="mb-2">{{ $ann->content }}</p>
              <small class="text-secondary">
              <i class="bi bi-person-circle"></i>
              <strong>
                @if($ann->user)
                  {{ $ann->user->role->name ?? 'System' }}
                @else
                  System
                @endif
              </strong>
              &nbsp;|&nbsp;
              <i class="bi bi-calendar3"></i>
              {{ $ann->created_at?->format('M d, Y h:i A') ?? 'N/A' }}
            </small>
            </div>
          </div>
        @endforeach

        <div class="mt-3">
          {{ $globalAnnouncements->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
      @else
        <div class="card text-center border-0 shadow-sm p-4">
          <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i>No global announcements found.</p>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
