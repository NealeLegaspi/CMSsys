<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Form 137 - {{ $student->user->name }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .header { text-align: center; margin-bottom: 20px; }
    .school-name { font-size: 16px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #333; padding: 5px; text-align: center; }
    .footer { margin-top: 40px; text-align: center; font-size: 11px; }
  </style>
</head>
<body>
  <div class="header">
    <img src="{{ public_path('images/deped_logo.png') }}" width="60" style="position:absolute; left:50px;">
    <img src="{{ public_path('Mindware.png') }}" width="60" style="position:absolute; right:50px;">
    <p>Republic of the Philippines</p>
    <p class="school-name">Children’s Mindware School Inc.</p>
    <p><strong>Form 137 — Permanent Record</strong></p>
  </div>

  <p><strong>Learner’s Name:</strong> {{ $student->user->profile->full_name }}</p>
  <p><strong>Grade Level:</strong> {{ $student->section->gradeLevel->name ?? 'N/A' }}</p>

  @foreach($grades as $level => $records)
  <h4>{{ $level }}</h4>
  <table>
    <thead>
      <tr>
        <th>Subject</th>
        <th>1st</th>
        <th>2nd</th>
        <th>3rd</th>
        <th>4th</th>
        <th>Final</th>
      </tr>
    </thead>
    <tbody>
      @foreach($records as $grade)
      <tr>
        <td>{{ $grade->subject->name }}</td>
        <td>{{ $grade->q1 ?? '-' }}</td>
        <td>{{ $grade->q2 ?? '-' }}</td>
        <td>{{ $grade->q3 ?? '-' }}</td>
        <td>{{ $grade->q4 ?? '-' }}</td>
        <td>{{ $grade->final ?? '-' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endforeach

  <div class="footer">
    <p>__________________________<br>Registrar’s Signature</p>
  </div>
</body>
</html>
