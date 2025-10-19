<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Student Reports</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      margin: 30px;
      color: #333;
    }
    .header {
      text-align: center;
      margin-bottom: 15px;
      position: relative;
    }
    .logo {
      position: absolute;
      left: 0;
      top: 0;
      width: 70px;
      height: 70px;
    }
    h2 {
      font-size: 20px;
      text-transform: uppercase;
      color: black;
      margin: 0;
    }
    h4 {
      font-size: 13px;
      color: #555;
      margin-top: 3px;
      font-weight: normal;
    }
    hr {
      border: none;
      height: 1px;
      background: #1a237e;
      margin: 10px 0 15px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      page-break-inside: auto;
    }
    th, td {
      border: 1px solid #444;
      padding: 6px 5px;
      text-align: center;
      vertical-align: middle;
    }
    th {
      background-color: #e3f2fd;
      font-weight: bold;
      font-size: 12px;
    }
    tr:nth-child(even) {
      background-color: #fafafa;
    }
    .footer {
      margin-top: 40px;
      font-size: 11px;
      text-align: right;
      color: #777;
    }
  </style>
</head>
<body>

  <div class="header">
    <img src="{{ public_path('Mindware.png') }}" class="logo" alt="School Logo">
    <h2>Children’s Mindware School Inc.</h2>
    <h4>Teacher’s Student Report</h4>
  </div>
  <hr>

  <table>
    <thead>
      <tr>
        <th>LRN</th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Middle Name</th>
        <th>Address</th>
        <th>Birthdate</th>
        <th>Age</th>
        <th>Contact</th>
        <th>Grade / Section</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($students as $student)
        @php
          $profile = $student->user->profile ?? null;
        @endphp
        <tr>
          <td>{{ $student->student_number ?? '—' }}</td>
          <td>{{ $profile->last_name ?? '—' }}</td>
          <td>{{ $profile->first_name ?? '—' }}</td>
          <td>{{ $profile->middle_name ?? '—' }}</td>
          <td>{{ $profile->address ?? '—' }}</td>
          <td>{{ $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->format('M d, Y') : '—' }}</td>
          <td>{{ $profile->birthdate ? \Carbon\Carbon::parse($profile->birthdate)->age : '—' }}</td>
          <td>{{ $profile->contact_number ?? '—' }}</td>
          @php
              $enrollment = $student->activeEnrollment;
              $gradeName = optional(optional($enrollment)->section)->gradeLevel->name ?? 'N/A';
              $sectionName = optional(optional($enrollment)->section)->name ?? 'N/A';
          @endphp
          <td>
              {{ $gradeName }} / 
              {{ $sectionName }}
          </td>
          <td>{{ $student->status ?? 'Enrolled' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="10">No students found</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    Generated on {{ \Carbon\Carbon::now()->format('F d, Y h:i A') }}
  </div>

</body>
</html>
