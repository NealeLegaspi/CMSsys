<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Student Record</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
    .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 15px; }
    .header img { float: left; width: 70px; height: 70px; margin-right: 10px; }
    .school-name { font-size: 16px; font-weight: bold; }
    .address { font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    th, td { border: 1px solid #333; padding: 6px; }
    th { background-color: #f0f0f0; }
    .section-title { font-weight: bold; font-size: 13px; margin-top: 10px; margin-bottom: 5px; }
    .signature { margin-top: 50px; text-align: right; }
    .signature-line { display: inline-block; border-top: 1px solid #000; width: 200px; margin-top: 30px; }
  </style>
</head>
<body>
  <div class="header">
    <img src="{{ public_path('Mindware.png') }}" alt="Logo">
    <div class="school-info">
      <div class="school-name">CHILDREN’S MINDWARE SCHOOL, INC.</div>
      <div class="address">123 Example Street, City, Philippines</div>
      <div class="address">Tel: (02) 123-4567 | Email: info@mindware.edu.ph</div>
    </div>
  </div>

  <h3 style="text-align:center; margin-bottom:20px;">STUDENT RECORD</h3>

  <table>
    <tr><th width="25%">LRN</th><td>{{ $student->student_number ?? 'N/A' }}</td></tr>
    <tr><th>Full Name</th><td>{{ $student->user->profile->last_name }}, {{ $student->user->profile->first_name }} {{ $student->user->profile->middle_name }}</td></tr>
    <tr><th>Email</th><td>{{ $student->user->email }}</td></tr>
    <tr><th>Gender</th><td>{{ $student->user->profile->sex ?? 'N/A' }}</td></tr>
    <tr><th>Contact</th><td>{{ $student->user->profile->contact_number ?? 'N/A' }}</td></tr>
    <tr><th>Address</th><td>{{ $student->user->profile->address ?? 'N/A' }}</td></tr>
  </table>

  <div class="section-title">Enrollment History</div>
  <table>
    <thead>
      <tr>
        <th>School Year</th>
        <th>Grade Level</th>
        <th>Section</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($student->enrollments as $e)
        <tr>
          <td>{{ $e->schoolYear->name ?? 'N/A' }}</td>
          <td>{{ $e->section->gradeLevel->name ?? 'N/A' }}</td>
          <td>{{ $e->section->name ?? 'N/A' }}</td>
          <td>{{ ucfirst($e->status ?? 'N/A') }}</td>
        </tr>
      @empty
        <tr><td colspan="4" align="center">No enrollment records found.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="section-title">Academic Records</div>
  <table>
    <thead>
      <tr>
        <th>Subject</th>
        <th>Grade Level</th>
        <th>Section</th>
        <th>Grade</th>
      </tr>
    </thead>
    <tbody>
      @forelse($grades as $grade)
        <tr>
          <td>{{ $grade->subject->name ?? 'N/A' }}</td>
          <td>{{ $grade->enrollment->section->gradeLevel->name ?? 'N/A' }}</td>
          <td>{{ $grade->enrollment->section->name ?? 'N/A' }}</td>
          <td>{{ $grade->grade ?? 'N/A' }}</td>
        </tr>
      @empty
        <tr><td colspan="4" align="center">No grades recorded yet.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="section-title">Submitted Documents</div>
  <table>
    <thead>
      <tr>
        <th>Type</th>
        <th>Status</th>
        <th>Date Uploaded</th>
      </tr>
    </thead>
    <tbody>
      @forelse($documents as $doc)
        <tr>
          <td>{{ $doc->document_type ?? 'N/A' }}</td>
          <td>{{ $doc->status ?? 'N/A' }}</td>
          <td>{{ $doc->created_at?->format('M d, Y') ?? 'N/A' }}</td>
        </tr>
      @empty
        <tr><td colspan="3" align="center">No documents uploaded.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="signature">
    <div class="signature-line"></div>
    <div><strong>Registrar’s Signature</strong></div>
  </div>
</body>
</html>
