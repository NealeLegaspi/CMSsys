@extends('layouts.teacher')

@section('title','Reports')
@section('header','Reports')

@section('content')
<div class="container my-4">
  
  <!-- Filter Form -->
  <form class="row g-3 mb-3">
    <div class="col-md-3">
      <label class="form-label">Grade Level</label>
      <select class="form-select">
        <option value="">All</option>
        <option value="1">Grade 7</option>
        <option value="2">Grade 8</option>
        <option value="3">Grade 9</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Section</label>
      <select class="form-select">
        <option value="">All</option>
        <option value="1">Section A</option>
        <option value="2">Section B</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">School Year</label>
      <select class="form-select">
        <option value="">All</option>
        <option value="2022-2023">2022-2023</option>
        <option value="2023-2024">2023-2024</option>
        <option value="2024-2025">2024-2025</option>
      </select>
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button type="button" class="btn btn-primary me-2">Submit</button>
      <button type="button" class="btn btn-secondary me-2" onclick="window.print()">Print</button>
      <button type="button" class="btn btn-success" onclick="exportTableToCSV('students.csv')">Export to CSV</button>
    </div>
  </form>

  <!-- Student List -->
  <div class="card">
    <div class="card-body">
      <h5 class="mb-3">List of Students</h5>
      <table class="table table-bordered" id="studentsTable">
        <thead class="table-light">
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
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <!-- Dummy Row 1 -->
          <tr>
            <td>1234567890</td>
            <td>Cruz</td>
            <td>Juan</td>
            <td>Santos</td>
            <td>Manila City</td>
            <td>2008-03-15</td>
            <td>17</td>
            <td>09123456789</td>
            <td>Grade 10 - A</td>
            <td>Enrolled</td>
            <td>
              <button class="btn btn-sm btn-warning"><i class="bx bx-edit"></i></button>
              <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
            </td>
          </tr>
          <!-- Dummy Row 2 -->
          <tr>
            <td>9876543210</td>
            <td>Dela Cruz</td>
            <td>Maria</td>
            <td>Lopez</td>
            <td>Quezon City</td>
            <td>2007-11-22</td>
            <td>18</td>
            <td>09998887777</td>
            <td>Grade 10 - B</td>
            <td>Enrolled</td>
            <td>
              <button class="btn btn-sm btn-warning"><i class="bx bx-edit"></i></button>
              <button class="btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  function downloadCSV(csv, filename) {
    var csvFile = new Blob([csv], {type: "text/csv"});
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
        row.push(cols[j].innerText);
      csv.push(row.join(","));
    }
    downloadCSV(csv.join("\n"), filename);
  }
</script>
@endsection
