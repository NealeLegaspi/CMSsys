<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  img {
    position: absolute;
    top: 0;
    left: 0;
    width: 70px;
  }
body { font-family: DejaVu Sans, sans-serif; text-align:center; margin:60px; }
h2 { margin-bottom:0; }
h4 { margin-top:0; }
p { font-size:14px; }
.signature { margin-top:60px; text-align:right; margin-right:100px; }
</style>
</head>
<body>
  <img src="{{ public_path('Mindware.png') }}" alt="School Logo">
  <h2>{{ $schoolName }}</h2>
  <h4>{{ $schoolAddress }}</h4>
  <h3 style="margin-top:30px;">CERTIFICATE OF ENROLLMENT</h3>
  <p>This is to certify that <strong>{{ $student->user->profile->first_name }} {{ optional($student->user->profile)->middle_name ? ' ' . strtoupper(optional($student->user->profile)->middle_name[0]) . '.' : '' }} {{ $student->user->profile->last_name }}</strong>
     is officially enrolled at <strong>{{ $schoolName }}</strong> for the current school year.</p>
  @if($certificate->purpose)
  <p><em>Purpose: {{ $certificate->purpose }}</em></p>
  @endif
  <div class="signature">
    <p>__________________________<br>{{ $registrarName }}<br><em>Registrar</em></p>
  </div>
  <p style="margin-top:40px;font-size:12px;">Generated on {{ now()->format('F d, Y') }}</p>
</body>
</html>
