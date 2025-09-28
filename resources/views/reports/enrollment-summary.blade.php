<!DOCTYPE html>
<html>
<head>
  <title>Enrollment Summary</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 5px; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  <h3>Enrollment Summary</h3>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Student</th>
        <th>Section</th>
        <th>School Year</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach($enrollments as $i => $e)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $e->student->user->profile->first_name }} {{ $e->student->user->profile->last_name }}</td>
        <td>{{ $e->section->name ?? 'N/A' }}</td>
        <td>{{ $e->schoolYear->name ?? 'N/A' }}</td>
        <td>{{ ucfirst($e->status) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
