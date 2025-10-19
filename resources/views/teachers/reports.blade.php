@extends('layouts.teacher')

@section('title','Reports')
@section('header')
    <i class="bi bi-people-fill me-2"></i> Reports
@endsection

@section('content')
<div class="container my-4">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filters Card --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form class="row g-3" method="POST" action="{{ route('teachers.filterReports') }}">
                @csrf
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Grade Level</label>
                    <select name="gradelevel_id" class="form-select">
                        <option value="">All</option>
                        @foreach($gradeLevels as $gl)
                            <option value="{{ $gl->id }}" {{ request('gradelevel_id') == $gl->id ? 'selected' : '' }}>
                                {{ $gl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Section</label>
                    <select name="section_id" class="form-select">
                        <option value="">All</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->gradeLevel->name }} - {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">School Year</label>
                    <select name="school_year" class="form-select">
                        <option value="">All</option>
                        @foreach($schoolYears as $sy)
                            <option value="{{ $sy }}" {{ request('school_year') == $sy ? 'selected' : '' }}>
                                {{ $sy }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end justify-content-end gap-2">
                    <button type="submit" class="btn btn-outline-primary shadow-sm"><i class="bi bi-search me-1"></i> Search</button>
                    <a href="{{ route('teachers.reports') }}" class="btn btn-outline-secondary shadow-sm"> 
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Student Table Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-table me-1"></i> Student Reports
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success shadow-sm" onclick="exportTableToCSV('students_report.csv')">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV
                    </button>
                    <form method="POST" action="{{ route('teachers.reports.export.pdf') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger shadow-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                        </button>
                    </form>
                </div>
            </div>

            @if($students->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-person-lines-fill" style="font-size: 3rem;"></i>
                    <p class="mt-3">No students found. Try adjusting the filters above.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="studentsTable">
                        <thead class="table-light text-center">
                            <tr>
                                <th>LRN</th>
                                <th class="text-start">Last Name</th>
                                <th class="text-start">First Name</th>
                                <th>Middle Name</th>
                                <th class="text-start">Address</th>
                                <th>Birthdate</th>
                                <th>Age</th>
                                <th>Contact</th>
                                <th>Grade / Section</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                @php
                                    $profile = $student->user->profile ?? null;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $student->student_number ?? 'N/A' }}</td>
                                    <td>{{ $profile->last_name ?? '—' }}</td>
                                    <td>{{ $profile->first_name ?? '—' }}</td>
                                    <td class="text-center">{{ $profile->middle_name ?? '—' }}</td>
                                    <td>{{ $profile->address ?? '—' }}</td>
                                    <td class="text-center">{{ $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('M d, Y') : '—' }}</td>
                                    <td class="text-center">{{ $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->age : '—' }}</td>
                                    <td class="text-center">{{ $profile->contact_number ?? '—' }}</td>
                                    @php
                                        $enrollment = $student->activeEnrollment;
                                        $gradeName = optional(optional($enrollment)->section)->gradeLevel->name ?? 'N/A';
                                        $sectionName = optional(optional($enrollment)->section)->name ?? 'N/A';
                                    @endphp
                                    <td class="text-center">
                                        {{ $gradeName }} / 
                                        {{ $sectionName }}
                                    </td>
                                    <td class="text-center" data-status="{{ $student->status ?? 'Enrolled' }}">
                                        @if(($student->status ?? 'Enrolled') === 'Enrolled')
                                            <span class="badge bg-success">Enrolled</span>
                                        @else
                                            <span class="badge bg-danger">{{ $student->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- CSV Export Script (Improved to handle badges) --}}
<script>
    function downloadCSV(csv, filename) {
        const csvFile = new Blob([csv], { type: "text/csv;charset=utf-8;" });
        const downloadLink = document.createElement("a");
        
        // Fix for IE (if needed, but modern browsers prefer the simpler approach)
        if (window.navigator.msSaveOrOpenBlob) { 
            window.navigator.msSaveBlob(csvFile, filename);
        } else {
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    }

    function exportTableToCSV(filename) {
        const table = document.getElementById("studentsTable");
        const rows = table.querySelectorAll("tr");
        
        const csv = Array.from(rows).map(row => {
            const cols = row.querySelectorAll("td, th");
            return Array.from(cols).map(col => {
                let text;
                // For the Status column, we use the data-status attribute for clean data
                if (col.hasAttribute('data-status')) {
                    text = col.getAttribute('data-status');
                } else {
                    // For all other columns, use innerText
                    text = col.innerText.trim();
                }
                // Wrap text in quotes and escape internal quotes for CSV compatibility
                return `"${text.replace(/"/g, '""').replace(/\n/g, ' ')}"`;
            }).join(",");
        }).join("\n");
        
        downloadCSV(csv, filename);
    }
</script>
@endsection