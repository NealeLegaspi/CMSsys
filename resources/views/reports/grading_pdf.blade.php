<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Grading Report</title>
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
      <div><strong>Grading Report</strong> - School Year {{ $sy }}</div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Subject</th>
        <th>Average Grade</th>
      </tr>
    </thead>
    <tbody>
      @forelse($data as $row)
        <tr>
          <td>{{ \App\Models\Subject::find($row->subject_id)->name ?? 'N/A' }}</td>
          <td>{{ number_format($row->avg, 2) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="2" align="center">No grading data available.</td>
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
