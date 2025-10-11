@extends('layouts.teacher')

@section('title','Class List')
@section('header','Class List')

@section('content')
<div class="container-fluid my-4">

  <!-- Tabs -->
  <ul class="nav nav-tabs mb-3" id="classlistTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active fw-semibold" id="advisory-tab" data-bs-toggle="tab" data-bs-target="#advisory" type="button" role="tab">
        <i class="bi bi-people-fill text-primary me-1"></i> Advisory Class
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link fw-semibold" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">
        <i class="bi bi-book-half text-success me-1"></i> My Subjects
      </button>
    </li>
  </ul>

  <div class="tab-content" id="classlistTabContent">

    <!-- Advisory Class -->
    <div class="tab-pane fade show active" id="advisory" role="tabpanel">
      @if(!empty($sectionId))
        <!-- Summary -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="fw-bold text-primary mb-1">
                <i class="bi bi-mortarboard me-2"></i> Class: {{ $sectionName ?? 'No Advisory Section Assigned' }}
              </h5>
              <p class="mb-0 text-muted">
                Total Students: 
                <span class="fw-semibold">{{ count($studentsMale ?? []) + count($studentsFemale ?? []) }}</span>
              </p>
            </div>
            <a href="{{ route('teachers.classlist.export') }}" class="btn btn-outline-primary">
              <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </a>
          </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <div class="d-flex flex-wrap gap-2">
              <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search by LRN or Name..." style="min-width: 280px;">
              <select id="statusFilter" class="form-select" style="min-width: 180px;">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
            <span class="text-muted small"><i class="bi bi-info-circle me-1"></i> Filter updates automatically</span>
          </div>
        </div>

        <!-- Male Students -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-primary text-white fw-semibold">
            <i class="bi bi-gender-male me-1"></i> Male Students ({{ count($studentsMale ?? []) }})
          </div>
          <div class="card-body table-responsive">
            @if(!empty($studentsMale) && count($studentsMale) > 0)
              <table class="table table-hover align-middle student-table">
                <thead class="table-light">
                  <tr>
                    <th>LRN</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($studentsMale as $st)
                  <tr>
                    <td>{{ $st->lrn ?? 'N/A' }}</td>
                    <td>{{ $st->last_name ?? '' }}</td>
                    <td>{{ $st->first_name ?? '' }}</td>
                    <td>{{ $st->middle_name ?? '' }}</td>
                    <td>
                      @if(($st->status ?? '') === 'active')
                        <span class="badge bg-success">Active</span>
                      @else
                        <span class="badge bg-danger">Inactive</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <p class="text-muted mb-0">No male students enrolled.</p>
            @endif
          </div>
        </div>

        <!-- Female Students -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header text-white fw-semibold" style="background-color: #d63384;">
            <i class="bi bi-gender-female me-1"></i> Female Students ({{ count($studentsFemale ?? []) }})
          </div>
          <div class="card-body table-responsive">
            @if(!empty($studentsFemale) && count($studentsFemale) > 0)
              <table class="table table-hover align-middle student-table">
                <thead class="table-light">
                  <tr>
                    <th>LRN</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($studentsFemale as $st)
                  <tr>
                    <td>{{ $st->lrn ?? 'N/A' }}</td>
                    <td>{{ $st->last_name ?? '' }}</td>
                    <td>{{ $st->first_name ?? '' }}</td>
                    <td>{{ $st->middle_name ?? '' }}</td>
                    <td>
                      @if(($st->status ?? '') === 'active')
                        <span class="badge bg-success">Active</span>
                      @else
                        <span class="badge bg-danger">Inactive</span>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <p class="text-muted mb-0">No female students enrolled.</p>
            @endif
          </div>
        </div>
      @else
        <div class="alert alert-warning shadow-sm border-0">
          <i class="bi bi-info-circle me-1"></i> No advisory section assigned to you yet.
        </div>
      @endif
    </div>

    <!-- My Subjects -->
    <div class="tab-pane fade" id="subjects" role="tabpanel">
      @if(!empty($mySubjects) && count($mySubjects) > 0)
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
          <i class="bi bi-info-circle me-1"></i> No subjects assigned to you yet.
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Search & Filter Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("searchInput");
  const statusFilter = document.getElementById("statusFilter");
  const tables = document.querySelectorAll(".student-table tbody");

  function filterTable() {
    const searchText = searchInput.value.toLowerCase();
    const filterStatus = statusFilter.value.toLowerCase();

    tables.forEach(tbody => {
      Array.from(tbody.querySelectorAll("tr")).forEach(row => {
        const rowText = row.innerText.toLowerCase();
        const status = row.querySelector("td:last-child")?.innerText.toLowerCase() || "";
        const matchesSearch = rowText.includes(searchText);
        const matchesStatus = !filterStatus || status.includes(filterStatus);
        row.style.display = (matchesSearch && matchesStatus) ? "" : "none";
      });
    });
  }

  searchInput.addEventListener("input", filterTable);
  statusFilter.addEventListener("change", filterTable);
});
</script>
@endsection
