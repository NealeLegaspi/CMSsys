<!DOCTYPE html>
<html>
<head>
  <title>Grade Validation Logs</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 5px; }
    th { background: #f2f2f2; }
  </style>
</head>
<body>
  <h3>Grade Validation Logs</h3>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Action</th>
        <th>User</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      @foreach($logs as $i => $log)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $log->action }}</td>
        <td>{{ $log->user->name ?? 'System' }}</td>
        <td>{{ $log->created_at->format('M d, Y h:i A') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
