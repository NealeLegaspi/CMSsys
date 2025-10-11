@extends('layouts.admin')

@section('title', 'Announcements')
@section('header', 'Announcements')

@section('content')
<div class="container-fluid my-4">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <h6 class="fw-bold mb-0"><i class="bi bi-megaphone me-2"></i> Announcements</h6>
      <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
        <i class="bi bi-plus-circle me-1"></i> New Announcement
      </button>
    </div>

    <div class="card-body">
      @include('partials.alerts')

      {{-- 🔍 Search --}}
      <form method="GET" action="{{ route('admins.announcements') }}" class="row g-2 mb-4">
        <div class="col-md-6">
          <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by title or content...">
        </div>
        <div class="col-md-3 d-flex">
          <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i></button>
          <a href="{{ route('admins.announcements') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
        </div>
      </form>

      {{-- 📋 Announcements Table --}}
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-primary text-center">
            <tr>
              <th>Title</th>
              <th>Content</th>
              <th>Audience</th>
              <th>Posted By</th>
              <th>Created</th>
              <th>Expires</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($announcements as $ann)
              <tr>
                <td class="fw-semibold">{{ $ann->title }}</td>
                <td title="{{ $ann->content }}">{{ Str::limit($ann->content, 50) }}</td>
                <td>
                  @if($ann->target_type === 'Global')
                    <span class="badge bg-info text-dark"><i class="bi bi-globe me-1"></i> All Users</span>
                  @elseif($ann->target_type === 'Teacher')
                    <span class="badge bg-primary"><i class="bi bi-person-badge me-1"></i> Teacher</span>
                    @if($ann->target_id && optional($ann->target)->profile)
                      <div class="small text-muted">
                        {{ optional($ann->target->profile)->last_name }}, {{ optional($ann->target->profile)->first_name }}
                      </div>
                    @endif
                  @elseif($ann->target_type === 'Student')
                    <span class="badge bg-success"><i class="bi bi-person-fill me-1"></i> Student</span>
                    @if($ann->target_id && optional($ann->target)->profile)
                      <div class="small text-muted">
                        {{ optional($ann->target->profile)->last_name }}, {{ optional($ann->target->profile)->first_name }}
                      </div>
                    @endif
                  @endif
                </td>
                <td>{{ $ann->user?->name ?? 'Administrator' }}</td>
                <td>{{ $ann->created_at->format('M d, Y h:i A') }}</td>
                <td>{{ $ann->expires_at ? $ann->expires_at->format('M d, Y h:i A') : 'No Expiration' }}</td>
                <td>
                  @if($ann->is_expired)
                    <span class="badge bg-danger">Expired</span>
                  @else
                    <span class="badge bg-success">Active</span>
                  @endif
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal{{ $ann->id }}">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAnnouncementModal{{ $ann->id }}">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>

              {{-- ✏️ Edit Modal --}}
              <div class="modal fade" id="editAnnouncementModal{{ $ann->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                  <div class="modal-content border-0 shadow-sm">
                    <form action="{{ route('admins.announcements.update', $ann->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="modal-header bg-warning border-bottom">
                        <h5 class="modal-title fw-semibold text-dark">
                          <i class="bi bi-pencil-square me-2 text-dark"></i> Edit Announcement
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body bg-light">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <label class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" class="form-control" value="{{ $ann->title }}" required>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label fw-semibold">Expiration Date & Time</label>
                            <input type="datetime-local" name="expires_at" class="form-control"
                              value="{{ $ann->expires_at?->format('Y-m-d\TH:i') }}">
                            <small class="text-muted">Leave blank if no expiration</small>
                          </div>
                          <div class="col-12">
                            <label class="form-label fw-semibold">Content</label>
                            <textarea name="content" class="form-control" rows="3" required>{{ $ann->content }}</textarea>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label fw-semibold">Audience</label>
                            <select name="target_type" class="form-select audience-selector" data-target="editTargetSelect{{ $ann->id }}">
                              <option value="Global" {{ $ann->target_type === 'Global' ? 'selected' : '' }}>All Users</option>
                              <option value="Teacher" {{ $ann->target_type === 'Teacher' ? 'selected' : '' }}>Teachers</option>
                              <option value="Student" {{ $ann->target_type === 'Student' ? 'selected' : '' }}>Students</option>
                            </select>
                          </div>
                          <div class="col-md-6 {{ $ann->target_type === 'Global' ? 'd-none' : '' }}" id="editTargetSelect{{ $ann->id }}">
                            <label class="form-label fw-semibold">Specific User (Optional)</label>
                            <select name="target_id" class="form-select user-target-select">
                              <option value="">All in this group</option>
                              @foreach(($users ?? collect()) as $u)
                                @php
                                  $roleName = optional($u->role)->name ?? $u->role_name ?? '';
                                  $displayName = optional($u->profile)->last_name
                                      ? optional($u->profile)->last_name . ', ' . optional($u->profile)->first_name
                                      : $u->email;
                                @endphp
                                <option value="{{ $u->id }}" data-role="{{ $roleName }}" 
                                  {{ $ann->target_id == $u->id ? 'selected' : '' }}>
                                  {{ $displayName }}
                                </option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer bg-white border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                          <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-warning text-dark fw-semibold">
                          <i class="bi bi-save me-1"></i> Update
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              {{-- 🗑️ Delete Modal --}}
              <div class="modal fade" id="deleteAnnouncementModal{{ $ann->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content border-0 shadow-sm">
                    <div class="modal-header bg-danger text-white">
                      <h5 class="modal-title fw-semibold"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Delete</h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      Are you sure you want to delete <strong>{{ $ann->title }}</strong>?
                    </div>
                    <div class="modal-footer bg-white border-top">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                      <form action="{{ route('admins.announcements.destroy', $ann->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                          <i class="bi bi-trash me-1"></i> Delete
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-4">
                  <i class="bi bi-info-circle me-1"></i> No announcements found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($announcements->hasPages())
        <div class="mt-3 d-flex justify-content-center">
          {{ $announcements->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      @endif
    </div>
  </div>
</div>

{{-- 🆕 Create Modal --}}
<div class="modal fade" id="createAnnouncementModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form action="{{ route('admins.announcements.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-megaphone me-2"></i> New Announcement</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Expiration Date</label>
            <input type="datetime-local" name="expires_at" class="form-control">
            <small class="text-muted">Leave blank if no expiration</small>
          </div>
          <div class="col-12">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="3" required></textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">Target Audience</label>
            <select name="target_type" class="form-select audience-selector" data-target="createTargetSelect">
              <option value="Global">All Users</option>
              <option value="Teacher">Teachers</option>
              <option value="Student">Students</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Post</button>
      </div>
    </form>
  </div>
</div>

{{-- 🎯 JS: Handle Audience Filtering --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  function updateAudience(select) {
    const target = document.getElementById(select.dataset.target);
    const selected = select.value;
    if (!target) return;

    target.classList.toggle('d-none', selected === 'Global');

    const userSelect = target.querySelector('.user-target-select');
    if (userSelect) {
      [...userSelect.options].forEach(opt => {
        const role = opt.dataset.role?.toLowerCase() || '';
        opt.hidden = opt.value && ((selected === 'Teacher' && role !== 'teacher') || (selected === 'Student' && role !== 'student'));
      });
    }
  }

  document.querySelectorAll('.audience-selector').forEach(sel => {
    sel.addEventListener('change', () => updateAudience(sel));
    updateAudience(sel);
  });
});
</script>
@endsection
