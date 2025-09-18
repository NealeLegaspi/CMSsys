<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Enrollment Report</title>
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
    <h4>Enrollment Report - School Year {{ $sy }}</h4>
  </div>

  <table>
    <thead>
      <tr>
        <th>Student No</th>
        <th>Name</th>
        <th>Section</th>
        <th>Grade Level</th>
        <th>Status</th>
        <th>Enrolled At</th>
      </tr>
    </thead>
    <tbody>
      @forelse($data as $e)
        <tr>
          <td>{{ $e->student->student_number }}</td>
          <td>{{ optional($e->student->user->profile)->first_name }} {{ optional($e->student->user->profile)->last_name }}</td>
          <td>{{ $e->section->name ?? '-' }}</td>
          <td>{{ $e->section->gradeLevel->name ?? '-' }}</td>
          <td>{{ ucfirst($e->status) }}</td>
          <td>{{ $e->created_at->format('Y-m-d') }}</td>
        </tr>
      @empty
        <tr><td colspan="6" align="center">No records found.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
