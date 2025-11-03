<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Class Masterlist - {{ $section->name }}</title>
<style>
  body {
    font-family: DejaVu Sans, sans-serif;
    margin: 30px 40px;
    font-size: 13px;
    color: #000;
  }

  .header {
    text-align: center;
    margin-bottom: 20px;
    position: relative;
  }

  .header img {
    position: absolute;
    top: 0;
    left: 0;
    width: 70px;
  }

  .school-name {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 2px;
  }

  .school-address {
    font-size: 13px;
    margin-bottom: 5px;
  }

  .report-title {
    font-size: 16px;
    font-weight: bold;
    margin-top: 5px;
    text-decoration: underline;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  th, td {
    border: 1px solid #444;
    padding: 6px 8px;
  }

  th {
    background-color: #f0f0f0;
    font-weight: bold;
    text-align: center;
  }

  td {
    text-align: center;
  }

  .meta {
    margin: 10px 0;
    line-height: 1.6;
  }

  .meta strong {
    display: inline-block;
    width: 100px;
  }

  .footer {
    margin-top: 40px;
    text-align: center;
    font-size: 13px;
  }

  .signature {
    margin-top: 60px;
    text-align: center;
  }

  .signature-line {
    display: inline-block;
    width: 250px;
    border-top: 1px solid #000;
    margin-bottom: 5px;
  }

  .small-text {
    font-size: 12px;
    color: #555;
  }

  .address { 
    font-size: 12px;
  }
</style>
</head>
<body>

<div class="header">
  <img src="{{ public_path('Mindware.png') }}" alt="School Logo">
  <div class="school-name">{{ $schoolName }}</div>
  <div class="address">027 St Francis Subdivision Rd, Balagtas, 3016 Bulacan</div>
  <div class="report-title">CLASS MASTERLIST</div>
</div>

<div class="meta">
  <p>
    <strong>Section:</strong> {{ $section->name }} <br>
    <strong>Grade Level:</strong> {{ $section->gradeLevel->name ?? 'N/A' }} <br>
    <strong>Adviser:</strong> {{ optional($section->adviser->profile)->first_name }}
    {{ optional($section->adviser->profile)->last_name ?? '' }} <br>
    <strong>Capacity:</strong> {{ $section->capacity ?? '∞' }} |
    <strong>Enrolled:</strong> {{ $section->enrollments->count() }}
  </p>
</div>

<table>
  <thead>
    <tr>
      <th style="width: 5%;">#</th>
      <th style="width: 15%;">LRN</th>
      <th style="width: 40%;">Full Name</th>
      <th style="width: 10%;">Sex</th>
      <th style="width: 20%;">Contact</th>
    </tr>
  </thead>
  <tbody>
    @forelse($students as $index => $s)
      <tr>
        <td style="text-align: center;">{{ $index + 1 }}</td>
        <td style="text-align: center;">{{ $s->student->student_number ?? 'N/A' }}</td>
        <td>{{ $s->profile->last_name }}, {{ $s->profile->first_name }}</td>
        <td style="text-align: center;">{{ $s->profile->sex ?? 'N/A' }}</td>
        <td>{{ $s->profile->contact_number ?? 'N/A' }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="5" style="text-align:center; padding:12px;">No enrolled students.</td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  <p>Generated on {{ now()->format('F d, Y - h:i A') }}</p>
</div>

<div class="signature">
  <div class="signature-line"></div><br>
  <span>
    {{ optional($section->adviser->profile)->first_name }}
    {{ optional($section->adviser->profile)->last_name ?? '' }}
  </span><br>
  <span class="small-text">Adviser</span>
</div>

</body>
</html>
