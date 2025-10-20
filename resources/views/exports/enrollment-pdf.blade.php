<!DOCTYPE html>
<html>
<head>
    <title>Enrollment List</title>
    <style>
          img {
            position: absolute;
            top: 0;
            left: 0;
            width: 70px;
        }
        table { width:100%; border-collapse: collapse; font-size:12px; }
        th, td { border:1px solid #000; padding:6px; text-align:left; }
        th { background:#eee; }
    </style>
</head>
<body>
    <img src="{{ public_path('Mindware.png') }}" alt="School Logo">
    <h3>Enrollment List</h3>
    <table>
        <thead>
            <tr>
                <th>LRN</th>
                <th>Name</th>
                <th>Section</th>
                <th>School Year</th>
            </tr>
        </thead>
        <tbody>
            @foreach($enrollments as $e)
                <tr>
                    <td>{{ $e->student->student_number }}</td>
                    <td>{{ $e->student->user->profile->first_name }} {{ $e->student->user->profile->last_name }}</td>
                    <td>{{ $e->section->name ?? 'N/A' }}</td>
                    <td>{{ $e->schoolYear->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
