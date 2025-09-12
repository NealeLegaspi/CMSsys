<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Student Reports</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 5px; text-align: center; }
    th { background: #f2f2f2; }
    h2 { text-align: center; }
  </style>
</head>
<body>
  <h2>Student Reports</h2>
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
        <th>Grade/Section</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($students as $student)
      <tr>
        <td>{{ $student->lrn }}</td>
        <td>{{ $student->user->profile->last_name }}</td>
        <td>{{ $student->user->profile->first_name }}</td>
        <td>{{ $student->user->profile->middle_name }}</td>
        <td>{{ $student->user->profile->address }}</td>
        <td>{{ $student->user->profile->birthdate }}</td>
        <td>{{ \Carbon\Carbon::parse($student->user->profile->birthdate)->age }}</td>
        <td>{{ $student->user->profile->contact }}</td>
        <td>{{ $student->section->gradeLevel->name }} - {{ $student->section->name }}</td>
        <td>{{ $student->status ?? 'Enrolled' }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="10">No students found</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
