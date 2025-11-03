<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Enrollment Report</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 40px; color: #000; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background: #f2f2f2; }
    h2, h4 { margin: 0; }
    .header { display: flex; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
    .logo { width: 70px; height: 70px; margin-right: 15px; }
    .school-info { flex: 1; text-align: center; }
    .school-name { font-size: 16px; font-weight: bold; }
    .address { font-size: 12px; }
  </style>
</head>
<body>
  <div class="header">
    <img src="{{ public_path('Mindware.png') }}" alt="School Logo" class="logo">
    <div class="school-info">
      <div class="school-name">CHILDREN’S MINDWARE SCHOOL, INC.</div>
      <div class="address">027 St. Francis Subdivision Rd, Balagtas, Bulacan</div>
      <div><strong>Enrollment Report</strong> - School Year {{ $sy }}</div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Student No</th>
        <th>Full Name</th>
        <th>Section</th>
        <th>Grade Level</th>
        <th>Status</th>
        <th>Date Enrolled</th>
      </tr>
    </thead>
    <tbody>
      @forelse($data as $e)
        @php
          $profile = optional($e->student->user)->profile;
          $firstName = $profile->first_name ?? '';
          $middleName = $profile->middle_name ? ' ' . strtoupper($profile->middle_name[0]) . '.' : '';
          $lastName = $profile->last_name ?? '';
          $fullName = trim("{$firstName}{$middleName} {$lastName}");
        @endphp
        <tr>
          <td>{{ $e->student->student_number ?? '-' }}</td>
          <td>{{ $fullName ?: '-' }}</td>
          <td>{{ $e->section->name ?? '-' }}</td>
          <td>{{ $e->section->gradeLevel->name ?? '-' }}</td>
          <td>{{ ucfirst($e->status ?? '-') }}</td>
          <td>{{ $e->created_at ? $e->created_at->format('Y-m-d') : '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" align="center">No records found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div style="text-align:right; margin-top:40px;">
    <strong>Prepared by:</strong><br><br>
    ___________________________<br>
    <em>Registrar</em>
  </div>
</body>
</html>
