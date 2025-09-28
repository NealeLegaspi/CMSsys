<!DOCTYPE html>
<html>
<head>
  <title>Masterlist</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 5px; text-align: left; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  <h3>Official Masterlist</h3>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>LRN</th>
        <th>Full Name</th>
      </tr>
    </thead>
    <tbody>
      @foreach($students as $i => $s)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $s->student->student_number ?? 'N/A' }}</td>
        <td>{{ $s->profile->first_name }} {{ $s->profile->last_name }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
