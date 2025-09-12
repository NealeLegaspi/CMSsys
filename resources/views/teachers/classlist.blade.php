@extends('layouts.teacher')

@section('title','Class List')
@section('header','Class List')

@section('content')
<div class="container my-4">
  <!-- Tabs -->
  <ul class="nav nav-tabs" id="classlistTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="advisory-tab" data-bs-toggle="tab" data-bs-target="#advisory" type="button" role="tab">
        <i class="bi bi-people-fill"></i> Advisory Class
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">
        <i class="bi bi-book-half"></i> My Subjects
      </button>
    </li>
  </ul>

  <div class="tab-content mt-4">
    <!-- Advisory -->
    <div class="tab-pane fade show active" id="advisory" role="tabpanel">
      @if(!empty($sectionId))
        <!-- Summary Card -->
        <div class="card shadow-sm mb-4">
          <div class="card-body d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-1 fw-bold text-primary">
                Class: {{ $sectionName ?? 'No Advisory Section Assigned' }}
              </h5>
              <p class="mb-0 text-muted">
                Total Students: <span class="fw-semibold">{{ count($studentsMale ?? []) + count($studentsFemale ?? []) }}</span>
              </p>
            </div>
            <a href="{{ route('teacher.classlist.export') }}" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-file-earmark-pdf"></i> Export to PDF
            </a>
          </div>
        </div>

        <!-- 🔎 Search + Filter -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <input type="text" id="searchInput" class="form-control w-50" placeholder="Search by LRN or Name...">
          <select id="statusFilter" class="form-select w-25 ms-2">
            <option value="">All Status</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>

        <!-- Male Students -->
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-gender-male"></i> Male Students ({{ count($studentsMale ?? []) }})
          </div>
          <div class="card-body table-responsive">
            @if(!empty($studentsMale) && count($studentsMale) > 0)
              <table class="table table-striped table-hover align-middle student-table">
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
              <p class="text-muted">No male students enrolled.</p>
            @endif
          </div>
        </div>

        <!-- Female Students -->
        <div class="card shadow-sm mb-4">
          <div class="card-header text-white fw-bold" style="background-color:#d63384">
            <i class="bi bi-gender-female"></i> Female Students ({{ count($studentsFemale ?? []) }})
          </div>
          <div class="card-body table-responsive">
            @if(!empty($studentsFemale) && count($studentsFemale) > 0)
              <table class="table table-striped table-hover align-middle student-table">
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
              <p class="text-muted">No female students enrolled.</p>
            @endif
          </div>
        </div>
      @else
        <div class="alert alert-warning">
          <i class="bi bi-info-circle"></i> No advisory section assigned to you yet.
        </div>
      @endif
    </div>

    <!-- Subjects -->
    <div class="tab-pane fade" id="subjects" role="tabpanel">
      @if(!empty($mySubjects) && count($mySubjects) > 0)
        <div class="card shadow-sm">
          <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Grade Level</th>
                  <th>Section</th>
                  <th>Subject</th>
                </tr>
              </thead>
              <tbody>
                @foreach($mySubjects as $sub)
                <tr>
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
        <div class="alert alert-info">
          <i class="bi bi-info-circle"></i> No subjects assigned to you yet.
        </div>
      @endif
    </div>
  </div>
</div>

<!-- 🔎 Search & Filter Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("searchInput");
  const statusFilter = document.getElementById("statusFilter");
  const tables = document.querySelectorAll(".student-table tbody");

  function filterTable() {
    const searchText = searchInput.value.toLowerCase();
    const filterStatus = statusFilter.value;

    tables.forEach(tbody => {
      Array.from(tbody.querySelectorAll("tr")).forEach(row => {
        const rowText = row.innerText.toLowerCase();
        const status = row.querySelector("td:last-child")?.innerText.trim();

        const matchesSearch = rowText.includes(searchText);
        const matchesStatus = !filterStatus || status === filterStatus;

        row.style.display = (matchesSearch && matchesStatus) ? "" : "none";
      });
    });
  }

  searchInput.addEventListener("input", filterTable);
  statusFilter.addEventListener("change", filterTable);
});
</script>
@endsection
