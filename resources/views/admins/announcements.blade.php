@extends('layouts.admin')

@section('title', 'Announcements')
@section('header', 'Announcements')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Announcements</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
            <i class="bi bi-plus-circle me-1"></i> New Announcement
        </button>
    </div>

    <div class="card-body">
        {{-- Alerts --}}
        @include('partials.alerts')

        {{-- Make $userList safe if controller didn't pass $users --}}
        @php
            $userList = $users ?? collect();
        @endphp

        {{-- Search --}}
        <form method="GET" action="{{ route('admins.announcements') }}" class="row g-2 mb-3">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Search by title or content">
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ route('admins.announcements') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>

        {{-- Announcements Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Audience</th>
                        <th>Posted By</th>
                        <th>Created</th>
                        <th>Expires</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $ann)
                        <tr>
                            <td>{{ $ann->title }}</td>
                            <td title="{{ $ann->content }}">{{ \Illuminate\Support\Str::limit($ann->content, 50) }}</td>
                            <td>
                                @if($ann->target_type === 'Global')
                                    <span class="badge bg-info text-dark">All Users</span>
                                @elseif($ann->target_type === 'Teacher')
                                    <span class="badge bg-primary">Teacher</span>
                                    @if($ann->target_id && optional($ann->target)->profile)
                                        <div class="small text-muted">
                                            {{ optional($ann->target->profile)->last_name }}, {{ optional($ann->target->profile)->first_name }}
                                        </div>
                                    @endif
                                @elseif($ann->target_type === 'Student')
                                    <span class="badge bg-success">Student</span>
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
                                @if(optional($ann)->is_expired ?? false)
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <!-- Edit -->
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#editAnnouncementModal{{ $ann->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Delete -->
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteAnnouncementModal{{ $ann->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editAnnouncementModal{{ $ann->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admins.announcements.update', $ann->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
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

                                            <div class="mb-3">
                                                <label class="form-label">Audience</label>
                                                <select name="target_type"
                                                        class="form-select audience-selector"
                                                        data-target="editTargetSelect{{ $ann->id }}">
                                                    <option value="Global" {{ $ann->target_type === 'Global' ? 'selected' : '' }}>All Users</option>
                                                    <option value="Teacher" {{ $ann->target_type === 'Teacher' ? 'selected' : '' }}>Teachers</option>
                                                    <option value="Student" {{ $ann->target_type === 'Student' ? 'selected' : '' }}>Students</option>
                                                </select>
                                            </div>

                                            <div class="mb-3 {{ $ann->target_type === 'Global' ? 'd-none' : '' }}" id="editTargetSelect{{ $ann->id }}">
                                                <label class="form-label">Select Specific User (optional)</label>

                                                @if($userList->isEmpty())
                                                    <div class="alert alert-warning small">No users loaded. Please make sure the controller passes <code>$users</code> or load users first.</div>
                                                @else
                                                    <select name="target_id" class="form-select user-target-select">
                                                        <option value="">All in this group</option>
                                                        @foreach($userList as $u)
                                                            @php
                                                                // role may be relation or attribute depending on controller
                                                                $roleName = optional($u->role)->name ?? ($u->role_name ?? null);
                                                                $displayName = optional($u->profile)->last_name
                                                                    ? (optional($u->profile)->last_name . ', ' . optional($u->profile)->first_name)
                                                                    : ($u->email ?? 'User #' . ($u->id ?? ''));
                                                            @endphp
                                                            <option value="{{ $u->id }}"
                                                                    data-role="{{ $roleName }}"
                                                                    {{ $ann->target_id == ($u->id ?? null) ? 'selected' : '' }}>
                                                                {{ $displayName }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Expiration Date & Time</label>
                                                <input type="datetime-local" name="expires_at" class="form-control"
                                                    value="{{ $ann->expires_at?->format('Y-m-d\TH:i') }}">
                                                <small class="text-muted">Leave blank if no expiration</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Delete Modal --}}
                        <div class="modal fade" id="deleteAnnouncementModal{{ $ann->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Delete</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete <strong>{{ $ann->title }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <form action="{{ route('admins.announcements.destroy', $ann->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No announcements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {{ $announcements->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createAnnouncementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admins.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">New Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control" rows="3" required>{{ old('content') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Targer User</label>
                        <select name="target_type" class="form-select audience-selector" data-target="createTargetSelect">
                            <option value="Global" {{ old('target_type') === 'Global' ? 'selected' : '' }}>All Users</option>
                            <option value="Teacher" {{ old('target_type') === 'Teacher' ? 'selected' : '' }}>Teachers</option>
                            <option value="Student" {{ old('target_type') === 'Student' ? 'selected' : '' }}>Students</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expiration Date & Time</label>
                        <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                        <small class="text-muted">Leave blank if no expiration</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Post</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JS to toggle target user select + filter by role --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Helper: update visibility & options for a given audience selector
    function handleAudienceChange(selectEl) {
        const targetId = selectEl.dataset.target;
        const targetDiv = document.getElementById(targetId);
        const selected = selectEl.value;

        // show/hide the user selector container
        if (targetDiv) {
            if (selected === 'Global') {
                targetDiv.classList.add('d-none');
            } else {
                targetDiv.classList.remove('d-none');
            }

            // filter options inside the user-target-select (if any) based on role
            const userSelect = targetDiv.querySelector('.user-target-select');
            if (userSelect) {
                Array.from(userSelect.options).forEach(opt => {
                    const role = opt.dataset.role || '';
                    // keep the empty option always
                    if (!opt.value) {
                        opt.hidden = false;
                        opt.style.display = '';
                        return;
                    }
                    // if role not set on option, do not hide it (failsafe)
                    if (!role) {
                        opt.hidden = false;
                        opt.style.display = '';
                        return;
                    }
                    if (selected === 'Teacher' && role.toLowerCase() === 'teacher') {
                        opt.hidden = false;
                        opt.style.display = '';
                    } else if (selected === 'Student' && role.toLowerCase() === 'student') {
                        opt.hidden = false;
                        opt.style.display = '';
                    } else {
                        // hide options that don't match
                        opt.hidden = true;
                        opt.style.display = 'none';
                    }
                });

                // if currently selected option is hidden, reset to empty
                if (userSelect.selectedOptions.length > 0 && userSelect.selectedOptions[0].hidden) {
                    userSelect.value = '';
                }
            }
        }
    }

    // bind change events to all audience selectors
    document.querySelectorAll('.audience-selector').forEach(sel => {
        // initialize visibility on load
        handleAudienceChange(sel);
        sel.addEventListener('change', function () {
            handleAudienceChange(this);
        });
    });

    // Also initialize for create modal based on old() if applicable
    const createSel = document.querySelector('#createAnnouncementModal .audience-selector');
    if (createSel) {
        handleAudienceChange(createSel);
    }
});
</script>
@endsection
