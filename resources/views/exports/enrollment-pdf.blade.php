<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Enrollment List</title>
    <style>
        @page { margin: 50px 40px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        .logo {
            position: absolute;
            top: -10px;
            left: 0;
            width: 70px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .report-title {
            font-size: 14px;
            text-decoration: underline;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        td {
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('Mindware.png') }}" class="logo" alt="School Logo">
        <div class="school-name">Children’s Mindware School Inc.</div>
        <div class="report-title">Enrollment List</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">LRN</th>
                <th style="width: 35%;">Student Name</th>
                <th style="width: 25%;">Section</th>
                <th style="width: 20%;">School Year</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $e)
                <tr>
                    <td class="text-center">{{ $e->student->student_number ?? 'N/A' }}</td>
                    <td>
                        {{ $e->student->user->profile->last_name ?? '' }},
                        {{ $e->student->user->profile->first_name ?? '' }}
                        {{ $e->student->user->profile->middle_name ? $e->student->user->profile->middle_name[0] . '.' : '' }}
                    </td>
                    <td>{{ $e->section->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $e->schoolYear->name ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No enrolled students found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
