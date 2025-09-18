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
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
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
                            <td>{{ Str::limit($ann->content, 50) }}</td>
                            <td>{{ $ann->user?->profile?->first_name ?? 'N/A' }} {{ $ann->user?->profile?->last_name ?? '' }}</td>
                            <td>{{ $ann->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                {{ $ann->expires_at ? $ann->expires_at->format('M d, Y') : 'No Expiration' }}
                            </td>
                            <td>
                                @if($ann->is_expired)
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal{{ $ann->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <!-- Delete -->
                                <form action="{{ route('admins.announcements.destroy', $ann->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
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
                                                <label class="form-label">Expiration Date</label>
                                                <input type="date" name="expires_at" class="form-control"
                                                       value="{{ $ann->expires_at?->format('Y-m-d') }}">
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
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No announcements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $announcements->links() }}
        </div>
    </div>
</div>

<!-- Create Modal -->
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
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiration Date</label>
                        <input type="date" name="expires_at" class="form-control">
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
@endsection
