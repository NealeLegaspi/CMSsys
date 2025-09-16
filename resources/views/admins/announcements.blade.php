@extends('layouts.admin')
@section('title','Announcements')
@section('header','Announcements')

@section('content')
@include('partials.alerts')

<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center bg-light">
    <h6 class="fw-bold mb-0">📢 Announcements</h6>
    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAnnModal">
      <i class="bi bi-plus-circle"></i> New Announcement
    </button>
  </div>
  <div class="card-body">
    <table class="table table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>Title</th>
          <th>Posted By</th>
          <th>Date</th>
          <th width="120">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($announcements as $a)
        <tr>
            <td>{{ $a->title }}</td>
            <td>{{ $a->user->profile->first_name ?? '' }} {{ $a->user->profile->last_name ?? '' }}</td>
            <td>{{ $a->created_at->format('M d, Y h:i A') }}</td>
          <td>
            <form action="{{ route('admins.announcements.destroy',$a->id) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this announcement?')">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted">No announcements yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="mt-3">{{ $announcements->links('pagination::bootstrap-5') }}</div>
  </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addAnnModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admins.announcements.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">New Announcement</h5>
          <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control mb-2" name="title" placeholder="Title" required>
          <textarea name="content" class="form-control" rows="4" placeholder="Write announcement..." required></textarea>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Post</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
