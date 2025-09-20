@extends('layouts.teacher')

@section('title','Reports')
@section('header','Reports')

@section('content')
<div class="container my-4">

  <!-- Filters Card -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <form class="row g-3" method="POST" action="{{ route('teachers.filterReports') }}">
        @csrf
        <div class="col-md-3">
          <label class="form-label">Grade Level</label>
          <select name="gradelevel_id" class="form-select">
            <option value="">All</option>
            @foreach($gradeLevels as $gl)
              <option value="{{ $gl->id }}">{{ $gl->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Section</label>
          <select name="section_id" class="form-select">
            <option value="">All</option>
            @foreach($sections as $section)
              <option value="{{ $section->id }}">{{ $section->gradeLevel->name }} - {{ $section->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">School Year</label>
          <select name="school_year" class="form-select">
            <option value="">All</option>
            @foreach($schoolYears as $sy)
              <option value="{{ $sy }}">{{ $sy }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 d-flex align-items-end justify-content-end gap-2">
          <button type="submit" class="btn btn-primary">Submit</button>
          <button type="button" class="btn btn-secondary" onclick="window.print()">Print</button>
          <button type="button" class="btn btn-success" onclick="exportTableToCSV('students.csv')">CSV</button>
          <button type="submit" formaction="{{ route('teachers.reports.export.pdf') }}" class="btn btn-danger">PDF</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Student List -->
  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="mb-3 fw-bold">📋 List of Students</h5>
      @if($students->isEmpty())
        <div class="text-center py-5 text-muted">
          <i class="bi bi-people" style="font-size: 3rem;"></i>
          <p class="mt-3">No students found. Try adjusting the filters above.</p>
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-striped table-bordered align-middle" id="studentsTable">
            <thead class="table-light sticky-top">
              <tr>
                <th>LRN</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Address</th>
                <th>Birthdate</th>
                <th>Age</th>
                <th>Contact</th>
                <th>Grade/Section</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($students as $student)
              <tr>
                <td>{{ $student->student_number }}</td>
                <td>{{ $student->user->profile->last_name }}</td>
                <td>{{ $student->user->profile->first_name }}</td>
                <td>{{ $student->user->profile->middle_name }}</td>
                <td>{{ $student->user->profile->address }}</td>
                <td>{{ $student->user->profile->birthdate }}</td>
                <td>{{ \Carbon\Carbon::parse($student->user->profile->birthdate)->age }}</td>
                <td>{{ $student->user->profile->contact }}</td>
                <td>{{ $student->section?->gradeLevel?->name ?? 'N/A' }} - {{ $student->section?->name ?? '' }}</td>
                <td>{{ $student->status ?? 'Enrolled' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
  function downloadCSV(csv, filename) {
    var csvFile = new Blob([csv], { type: "text/csv" });
    var downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
  }

  function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll("#studentsTable tr");

    for (var i = 0; i < rows.length; i++) {
      var row = [], cols = rows[i].querySelectorAll("td, th");
      for (var j = 0; j < cols.length; j++) 
        row.push('"' + cols[j].innerText + '"');
      csv.push(row.join(","));
    }
    downloadCSV(csv.join("\n"), filename);
  }
</script>
@endsection
