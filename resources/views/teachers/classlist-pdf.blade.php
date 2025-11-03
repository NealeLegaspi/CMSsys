<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Class List - {{ $section->name ?? 'N/A' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h3 { text-align: center; margin-bottom: 10px; }
        h4 { margin-top: 20px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h3>
        Class List — {{ $section->gradeLevel->name ?? '' }} {{ $section->name ?? 'N/A' }}
    </h3>

    <h4>Male Students ({{ $studentsMale->count() }})</h4>
    <table>
        <thead>
            <tr>
                <th>LRN</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentsMale as $st)
            <tr>
                <td>{{ $st->student_number ?? 'N/A' }}</td>
                <td>{{ $st->user->profile->last_name ?? '' }}</td>
                <td>{{ $st->user->profile->first_name ?? '' }}</td>
                <td>{{ $st->user->profile->middle_name ?? '' }}</td>
                <td>{{ ucfirst($st->status ?? '') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" align="center">No male students</td></tr>
            @endforelse
        </tbody>
    </table>

    <h4>Female Students ({{ $studentsFemale->count() }})</h4>
    <table>
        <thead>
            <tr>
                <th>LRN</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($studentsFemale as $st)
            <tr>
                <td>{{ $st->student_number ?? 'N/A' }}</td>
                <td>{{ $st->user->profile->last_name ?? '' }}</td>
                <td>{{ $st->user->profile->first_name ?? '' }}</td>
                <td>{{ $st->user->profile->middle_name ?? '' }}</td>
                <td>{{ ucfirst($st->status ?? '') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" align="center">No female students</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
