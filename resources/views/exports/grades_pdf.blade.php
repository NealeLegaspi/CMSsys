<!DOCTYPE html>
<html>
<head>
  <title>Grade Submissions Report</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    h2 { text-align: center; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    th { background-color: #f2f2f2; }
  </style>
</head>
<body>
  <h2>Grade Submissions Report</h2>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Teacher</th>
        <th>Subject</th>
        <th>Section</th>
        <th>Quarter</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($assignments as $index => $a)
      <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $a->teacher_name }}</td>
        <td>{{ $a->subject_name }}</td>
        <td>{{ $a->section_name }}</td>
        <td>{{ $a->quarter ?? 'All' }}</td>
        <td>{{ ucfirst($a->grade_status ?? 'Draft') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
