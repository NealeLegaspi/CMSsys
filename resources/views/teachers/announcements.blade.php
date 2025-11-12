@extends('layouts.teacher')

@section('title', 'Announcements')
@section('header')
    <i class="bi bi-megaphone me-2"></i> Announcements
@endsection

@section('content')
<div class="container-fluid my-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-3" id="announcementTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="create-tab" data-bs-toggle="tab" data-bs-target="#create" type="button" role="tab" aria-controls="create" aria-selected="true">
                <i class="bi bi-plus-circle text-primary me-1"></i> Create
            </button>
        </li>
        <li class="nav-item" role="presentation">
            {{-- Note: The active tab must be set based on the URL parameter if filtering was used, but we default to 'my' for simplicity here. --}}
            <button class="nav-link fw-semibold" id="my-tab" data-bs-toggle="tab" data-bs-target="#my" type="button" role="tab" aria-controls="my" aria-selected="false">
                <i class="bi bi-megaphone text-success me-1"></i> My Announcements
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="global-tab" data-bs-toggle="tab" data-bs-target="#global" type="button" role="tab" aria-controls="global" aria-selected="false">
                <i class="bi bi-broadcast text-danger me-1"></i> Global
            </button>
        </li>
    </ul>

    <div class="tab-content" id="announcementTabsContent">
        
        <div class="tab-pane fade show active" id="create" role="tabpanel" aria-labelledby="create-tab">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    {{-- Assuming $sections is available to the view --}}
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
                                        <optgroup label="{{ $gradeSections->first()->gradeLevel->name ?? 'Grade Level N/A' }}">
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
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="bi bi-send me-1"></i> Post Announcement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="my" role="tabpanel" aria-labelledby="my-tab">
            
            {{-- Client-side Filter Bar --}}
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-body d-flex flex-wrap gap-2 align-items-center">
                    <input type="text" id="mySearchInput" class="form-control" placeholder="Search by title or content..." style="max-width: 300px;">
                    <select id="mySectionFilter" class="form-select" style="max-width: 200px;">
                        <option value="">All Sections</option>
                        @foreach(\App\Models\Section::all() as $section)
                            {{-- We need the section name to be easily searchable in JS --}}
                            <option value="{{ $section->name }}">
                                {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <button id="mySearchButton" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i> Search</button>
                    <button id="myResetButton" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise me-1"></i> Reset</button>
                    
                </div>
            </div>

            {{-- Announcement List Container --}}
            <div id="myAnnouncementsList">
                @if($myAnnouncements->count())
                    @foreach($myAnnouncements as $ann)
                        {{-- Ann card with data attributes for filtering --}}
                        <div class="card shadow-sm border-0 rounded-3 mb-3 announcement-item" 
                             data-title="{{ strtolower($ann->title) }}"
                             data-content="{{ strtolower($ann->content) }}"
                             data-section="{{ strtolower($ann->section->name ?? '') }}">

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
                                    <button class="btn btn-sm btn-outline-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $ann->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $ann->id }}">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editModal{{ $ann->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $ann->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form method="POST" action="{{ route('teachers.announcements.update', $ann) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-semibold" id="editModalLabel{{ $ann->id }}"><i class="bi bi-pencil-square me-2"></i>Edit Announcement</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Section</label>
                                                <select name="section_id" class="form-select" required>
                                                    <option value="">-- Select Section --</option>
                                                    @foreach($sections->groupBy('gradelevel_id') as $gradeId => $gradeSections)
                                                        <optgroup label="{{ $gradeSections->first()->gradeLevel->name ?? 'N/A' }}">
                                                            @foreach($gradeSections as $sec)
                                                                <option value="{{ $sec->id }}" {{ $sec->id == $ann->section_id ? 'selected' : '' }}>
                                                                    {{ $sec->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success shadow-sm"><i class="bi bi-save me-1"></i> Update</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="modal fade" id="deleteModal{{ $ann->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $ann->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteModalLabel{{ $ann->id }}"><i class="bi bi-exclamation-triangle me-1"></i> Confirm Delete</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this announcement?
                                    </div>
                                    <div class="modal-footer">
                                        <form method="POST" action="{{ route('teachers.announcements.destroy', $ann) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger shadow-sm"><i class="bi bi-trash me-1"></i> Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card text-center border-0 shadow-sm p-4">
                        <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i>No announcements found.</p>
                    </div>
                @endif
            </div>

            {{-- Removed Pagination, as client-side filtering assumes all data is loaded --}}
            
        </div>

        <div class="tab-pane fade" id="global" role="tabpanel" aria-labelledby="global-tab">
             {{-- Note: Global announcements still use server-side pagination/filtering if needed --}}
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
                                        {{ e($ann->user->role->name ?? 'System') }}: {{ e($a->user->profile->first_name ?? 'Unknown') }} {{ e($a->user->profile->last_name ?? 'Unknown') }}
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
                    {{-- Pagination for Global Tab remains server-side --}}
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

{{-- Client-Side Filter Script for My Announcements --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- My Announcements Filter ---
    const mySearchInput = document.getElementById("mySearchInput");
    const mySectionFilter = document.getElementById("mySectionFilter");
    const mySearchButton = document.getElementById("mySearchButton");
    const myResetButton = document.getElementById("myResetButton");
    const announcementItems = document.querySelectorAll(".announcement-item");

    function filterAnnouncements() {
        const searchText = mySearchInput.value.toLowerCase().trim();
        const filterSection = mySectionFilter.value.toLowerCase();

        announcementItems.forEach(item => {
            const title = item.getAttribute('data-title') || "";
            const content = item.getAttribute('data-content') || "";
            const section = item.getAttribute('data-section') || "";

            // Check if title or content contains the search text
            const matchesSearch = !searchText || title.includes(searchText) || content.includes(searchText);
            
            // Check if section matches the filter
            const matchesSection = !filterSection || section === filterSection;
            
            item.style.display = (matchesSearch && matchesSection) ? "" : "none";
        });
    }

    function resetAnnouncementsFilter() {
        mySearchInput.value = '';
        mySectionFilter.value = '';
        filterAnnouncements(); 
    }

    // Attach event listeners to buttons
    mySearchButton.addEventListener("click", filterAnnouncements);
    myResetButton.addEventListener("click", resetAnnouncementsFilter);
    
    // Optional: Also filter when input changes (real-time filtering)
    mySearchInput.addEventListener("input", filterAnnouncements);
    mySectionFilter.addEventListener("change", filterAnnouncements);

    // Initial filter run (important if the tab is initially active)
    filterAnnouncements();
});
</script>

{{-- You can remove the unused script block from the Class List example that was mistakenly appended here --}}
@endsection