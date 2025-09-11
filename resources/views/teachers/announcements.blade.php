@extends('layouts.teacher')

@section('title','My Announcements')
@section('header','My Announcements')

@section('content')
<div class="container my-4">

  <!-- Flash messages -->
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <!-- Tabs -->
  <ul class="nav nav-tabs" id="announcementTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab">
        Create Announcement
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="my-tab" data-bs-toggle="tab" data-bs-target="#my" type="button" role="tab">
        My Announcements
      </button>
    </li>
  </ul>

  <div class="tab-content mt-3">

    <!-- Create Tab -->
    <div class="tab-pane fade show active" id="create" role="tabpanel">
      <div class="card">
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
              <select name="section_id" class="form-select">
                <option value="">All Sections</option>
                @foreach(\App\Models\Section::all() as $section)
                  <option value="{{ $section->id }}">{{ $section->name }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Post</button>
          </form>
        </div>
      </div>
    </div>

    <!-- My Announcements Tab -->
    <div class="tab-pane fade" id="my" role="tabpanel">
      @if($myAnnouncements->count() > 0)
        @foreach($myAnnouncements as $ann)
          <div class="card mb-3">
            <div class="card-body">
              <h6>{{ $ann->title }}</h6>
              <p>{{ $ann->content }}</p>
              <small class="text-muted">
                Posted on {{ $ann->created_at->format('M d, Y h:i A') }}
                @if($ann->section)
                  | Section: {{ $ann->section->name }}
                @else
                  | All Sections
                @endif
              </small>
              <div class="mt-2">
                <!-- Edit Button (Modal Trigger) -->
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $ann->id }}">
                  Edit
                </button>

                <!-- Delete -->
                <form action="{{ route('teachers.announcements.destroy',$ann) }}" method="POST" class="d-inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this announcement?')">Delete</button>
                </form>
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
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Announcement</h5>
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
                    <button type="submit" class="btn btn-success">Update</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        @endforeach

        <!-- Pagination -->
        <div class="mt-3">
          {{ $myAnnouncements->links('pagination::bootstrap-5') }}
        </div>

      @else
        <p>No announcements yet.</p>
      @endif
    </div>
  </div>
</div>
@endsection
