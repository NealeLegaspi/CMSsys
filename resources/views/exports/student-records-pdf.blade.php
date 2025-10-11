<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Records</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; margin: 25px; }
    h2, h4, p { text-align: center; margin: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
    th, td { border: 1px solid #444; padding: 6px; text-align: left; }
    th { background-color: #f0f0f0; }
    .footer { margin-top: 30px; text-align: center; font-size: 12px; }
  </style>
</head>
<body>
  <div class="header">
    <img src="{{ public_path('Mindware.png') }}" alt="School Logo" style="width:70px;float:left;">
    <h2>{{ $schoolName }}</h2>
    <p>{{ $schoolAddress }}</p>
    <h4>STUDENT RECORDS</h4>
  </div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Student Name</th>
        <th>Grade Level</th>
        <th>Section</th>
        <th>School Year</th>
        <th>Status</th>
        <th>Email</th>
        <th>Contact</th>
      </tr>
    </thead>
    <tbody>
      @forelse($records as $index => $record)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ ($record->student->user->profile->last_name ?? '') . ', ' . ($record->student->user->profile->first_name ?? '') }}</td>
          <td>{{ $record->section->gradeLevel->name ?? 'N/A' }}</td>
          <td>{{ $record->section->name ?? 'N/A' }}</td>
          <td>{{ $record->schoolYear->name ?? 'N/A' }}</td>
          <td>{{ $record->status ?? 'N/A' }}</td>
          <td>{{ $record->student->user->email ?? 'N/A' }}</td>
          <td>{{ $record->student->user->profile->contact_number ?? 'N/A' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align:center;">No records found.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    <p>Generated on {{ $generatedAt }}</p>
  </div>
</body>
</html>
