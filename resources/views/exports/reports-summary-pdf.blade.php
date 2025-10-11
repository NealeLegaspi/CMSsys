<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Enrollment Report</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
    .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 15px; }
    .header img { width: 70px; height: 70px; float: left; }
    .school-name { font-size: 16px; font-weight: bold; }
    .section-title { margin-top: 10px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #333; padding: 6px; }
    th { background: #f0f0f0; }
    .summary-box { margin-top: 20px; }
  </style>
</head>
<body>
  <div class="header">
    <img src="{{ public_path('Mindware.png') }}" alt="Logo">
    <div class="school-name">CHILDREN’S MINDWARE SCHOOL, INC.</div>
    <div>Enrollment Report for School Year {{ $schoolYear->name ?? 'N/A' }}</div>
  </div>

  <div class="summary-box">
    <table>
      <tr><th>Total Enrolled</th><td>{{ $totalEnrolled }}</td></tr>
      <tr><th>Male Students</th><td>{{ $maleCount }}</td></tr>
      <tr><th>Female Students</th><td>{{ $femaleCount }}</td></tr>
    </table>
  </div>

  <h4 class="section-title">Enrollment by Grade Level</h4>
  <table>
    <thead>
      <tr>
        <th>Grade Level</th>
        <th>Total Students</th>
      </tr>
    </thead>
    <tbody>
      @foreach($byGradeLevel as $item)
        <tr>
          <td>{{ $item->grade }}</td>
          <td>{{ $item->total }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div style="text-align:right; margin-top:40px;">
    <strong>Prepared by:</strong><br><br>
    ___________________________<br>
    <em>Registrar</em>
  </div>
</body>
</html>
