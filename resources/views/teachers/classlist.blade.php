@extends('layouts.teacher')

@section('title', 'Class List')

@section('header')
    <i class="bi bi-people-fill me-2"></i> Class List
@endsection

@section('content')

@php
    $syClosed = $syClosed ?? false;
@endphp

@if($syClosed)
    <div class="alert alert-warning shadow-sm mb-4">
        <i class="bi bi-lock-fill me-2"></i>
        The School Year is closed. Class lists is temporarily disabled.
    </div>
@endif

<div class="container-fluid my-4">

    {{-- Export Error Message --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="tab-content" id="classlistTabContent">

        {{-- Advisory Class Tab Content --}}
        <div class="tab-pane fade show active" id="advisory" role="tabpanel" aria-labelledby="advisory-tab">
            
            @if($section) 
                {{-- Summary Card --}}
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="fw-bold text-primary mb-1">
                                <i class="bi bi-mortarboard me-2"></i> Class: 
                                {{ $section->gradeLevel->name ?? 'N/A' }} - {{ $section->name ?? 'No Advisory Section Assigned' }}
                            </h5>
                            <p class="mb-0 text-muted">
                                Total Students: 
                                <span class="fw-semibold">{{ $studentsMale->count() + $studentsFemale->count() }}</span>
                                (Male: {{ $studentsMale->count() }}, Female: {{ $studentsFemale->count() }})
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Filters Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body d-flex flex-wrap gap-3 justify-content-start align-items-center">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by LRN, Student ID, or Name..." style="max-width: 300px;">
                        <select id="statusFilter" class="form-select" style="max-width: 180px;">
                            <option value="">All Status</option>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        
                        <button id="searchButton" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i> Search</button>
                        <button id="resetButton" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise me-1"></i> Reset</button>
                      </div>
                </div>

                {{-- Nested Tabs for Male/Female Students --}}
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header p-0 bg-white border-bottom-0">
                        <ul class="nav nav-tabs card-header-tabs" id="genderTab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active fw-semibold" id="male-tab" data-bs-toggle="tab" data-bs-target="#maleStudents" type="button" role="tab" aria-controls="maleStudents" aria-selected="true">
                                    <i class="bi bi-gender-male me-1 text-primary"></i> Male ({{ $studentsMale->count() }})
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-semibold" id="female-tab" data-bs-toggle="tab" data-bs-target="#femaleStudents" type="button" role="tab" aria-controls="femaleStudents" aria-selected="false">
                                    <i class="bi bi-gender-female me-1" style="color: #d63384;"></i> Female ({{ $studentsFemale->count() }})
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body pt-3">
                        <div class="tab-content" id="genderTabContent">

                            {{-- Male Students Tab Content --}}
                            <div class="tab-pane fade show active" id="maleStudents" role="tabpanel" aria-labelledby="male-tab">
                                @if($studentsMale->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle student-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">LRN</th>
                                                    <th>Last Name</th>
                                                    <th>First Name</th>
                                                    <th class="text-center">Middle Name</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($studentsMale as $st)
                                                <tr>
                                                    <td class="text-center">{{ $st->student_number ?? 'N/A' }}</td> 
                                                    <td>{{ $st->user->profile->last_name ?? '' }}</td>
                                                    <td>{{ $st->user->profile->first_name ?? '' }}</td>
                                                    <td class="text-center">{{ $st->user->profile->middle_name ?? '' }}</td>
                                                    <td class="text-center">
                                                        @php $status = strtolower($st->status ?? ''); @endphp
                                                        @if($status === 'active' || $status === 'enrolled')
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-light text-center mb-0">No male students enrolled in this section.</div>
                                @endif
                            </div>
                            
                            {{-- Female Students Tab Content --}}
                            <div class="tab-pane fade" id="femaleStudents" role="tabpanel" aria-labelledby="female-tab">
                                @if($studentsFemale->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle student-table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">LRN</th>
                                                    <th>Last Name</th>
                                                    <th>First Name</th>
                                                    <th class="text-center">Middle Name</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($studentsFemale as $st)
                                                <tr>
                                                    <td class="text-center">{{ $st->student_number ?? 'N/A' }}</td>
                                                    <td>{{ $st->user->profile->last_name ?? '' }}</td>
                                                    <td>{{ $st->user->profile->first_name ?? '' }}</td>
                                                    <td class="text-center">{{ $st->user->profile->middle_name ?? '' }}</td>
                                                    <td class="text-center">
                                                        @php $status = strtolower($st->status ?? ''); @endphp
                                                        @if($status === 'active' || $status === 'enrolled')
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-light text-center mb-0">No female students enrolled in this section.</div>
                                @endif
                            </div>

                        </div> {{-- End of genderTabContent --}}
                    </div> {{-- End of card-body --}}
                </div> {{-- End of card --}}
                
            @else
                <div class="alert alert-warning shadow-sm border-0">
                    <i class="bi bi-info-circle me-1"></i> **No Advisory Section Assigned.** Please contact the Administrator or Registrar to assign an advisory class.
                </div>
            @endif
        </div>

        {{-- My Subjects Tab Content --}}
        <div class="tab-pane fade" id="subjects" role="tabpanel" aria-labelledby="subjects-tab">
            @if($mySubjects->count() > 0)
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-light fw-semibold">
                        <i class="bi bi-book text-success me-2"></i> Subjects You Handle
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-primary text-center">
                                <tr>
                                    <th>Grade Level</th>
                                    <th>Section</th>
                                    <th>Subject</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mySubjects as $sub)
                                <tr class="text-center">
                                    <td>{{ $sub->gradelevel ?? 'N/A' }}</td>
                                    <td>{{ $sub->section_name ?? 'N/A' }}</td>
                                    <td>{{ $sub->subject_name ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="alert alert-info shadow-sm border-0">
                    <i class="bi bi-info-circle me-1"></i> **No Subjects Assigned.** You currently do not have any subjects linked to your Teacher Profile.
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Search & Filter Script --}}
<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const statusFilter = document.getElementById("statusFilter");
    const searchButton = document.getElementById("searchButton");
    const resetButton = document.getElementById("resetButton");

    const advisoryTab = document.getElementById("advisory");
    const tables = advisoryTab.querySelectorAll(".student-table tbody"); 

    function filterTable() {
        const searchText = searchInput.value.toLowerCase().trim();
        const filterStatus = statusFilter.value.toLowerCase();

        tables.forEach(tbody => {
            Array.from(tbody.querySelectorAll("tr")).forEach(row => {
                // Get relevant columns for search (LRN/ID, Names)
                const searchableText = Array.from(row.querySelectorAll("td:nth-child(-n+4)"))
                                        .map(td => td.innerText.toLowerCase())
                                        .join(" ");

                // Status is in the last column
                const status = row.querySelector("td:last-child")?.innerText.toLowerCase() || "";
                
                const matchesSearch = !searchText || searchableText.includes(searchText);
                const matchesStatus = !filterStatus || status.includes(filterStatus);
                
                row.style.display = (matchesSearch && matchesStatus) ? "" : "none";
            });
        });
    }

    function resetFilters() {
        searchInput.value = '';
        statusFilter.value = '';
        filterTable(); // Apply the filter after reset
    }

    // Attach event listeners to buttons
    searchButton.addEventListener("click", filterTable);
    resetButton.addEventListener("click", resetFilters);

    // Initial filter application (to make sure table is correct on load)
    filterTable(); 
});
</script>
@endsection