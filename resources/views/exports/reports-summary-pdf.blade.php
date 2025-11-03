<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Enrollment Report</title>
  <style>
    @page { margin: 40px 50px; }
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      color: #333;
    }
    .header {
      text-align: center;
      border-bottom: 2px solid #000;
      padding-bottom: 10px;
      margin-bottom: 15px;
      position: relative;
    }
    .header img {
      position: absolute;
      top: 0;
      left: 0;
      width: 70px;
      height: 70px;
    }
    .school-name {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 2px;
    }
    .address {
      font-size: 12px;
      margin-bottom: 5px;
    }
    .report-title {
      font-weight: bold;
      margin-top: 3px;
      font-size: 13px;
      text-transform: uppercase;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 12px;
    }
    th, td {
      border: 1px solid #333;
      padding: 6px;
      text-align: left;
    }
    th {
      background: #f2f2f2;
      text-align: center;
      font-weight: bold;
    }
    .summary-box {
      width: 50%;
      margin-bottom: 20px;
    }
    .section-title {
      margin-top: 20px;
      font-weight: bold;
      font-size: 13px;
    }
    .footer {
      text-align: right;
      margin-top: 50px;
      font-size: 12px;
    }
    .prepared-line {
      margin-top: 40px;
      text-align: right;
    }
  </style>
</head>
<body>

  <!-- Header Section -->
  <div class="header">
    <img src="{{ public_path('Mindware.png') }}" alt="Logo">
    <div class="school-name">CHILDREN’S MINDWARE SCHOOL, INC.</div>
    <div class="address">027 St. Francis Subdivision Rd, Balagtas, Bulacan 3016</div>
    <div class="report-title">Enrollment Report for School Year {{ $schoolYear->name ?? 'N/A' }}</div>
  </div>

  <!-- Summary Section -->
  <div class="summary-box">
    <table>
      <tr>
        <th style="width: 60%;">Total Enrolled</th>
        <td style="text-align: center;">{{ $totalEnrolled }}</td>
      </tr>
      <tr>
        <th>Male Students</th>
        <td style="text-align: center;">{{ $maleCount }}</td>
      </tr>
      <tr>
        <th>Female Students</th>
        <td style="text-align: center;">{{ $femaleCount }}</td>
      </tr>
    </table>
  </div>

  <!-- Enrollment by Grade Level -->
  <h4 class="section-title">Enrollment by Grade Level</h4>
  <table>
    <thead>
      <tr>
        <th style="width: 70%;">Grade Level</th>
        <th style="width: 30%;">Total Students</th>
      </tr>
    </thead>
    <tbody>
      @forelse($byGradeLevel as $item)
        <tr>
          <td>{{ $item->grade }}</td>
          <td style="text-align: center;">{{ $item->total }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="2" style="text-align: center;">No data available.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <!-- Footer -->
  <div class="prepared-line">
    <strong>Prepared by:</strong><br><br>
    ___________________________<br>
    <em>Registrar</em>
  </div>

</body>
</html>
