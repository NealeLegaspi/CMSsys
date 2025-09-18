<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Grading Report</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background: #f2f2f2; }
    h2, h4 { margin: 0; text-align: center; }
    .header { margin-bottom: 20px; }
  </style>
</head>
<body>
  <div class="header">
    <h2>Children's Mindware School, Inc.</h2>
    <h4>Grading Report - School Year {{ $sy }}</h4>
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
        <tr><td colspan="2" align="center">No grading data available.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
