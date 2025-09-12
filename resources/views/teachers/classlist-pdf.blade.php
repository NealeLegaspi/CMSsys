<!DOCTYPE html>
<html>
<head>
    <title>Class List</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h3 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h3>Class List - {{ $sectionName ?? 'N/A' }}</h3>

    <h4>Male Students ({{ count($studentsMale ?? []) }})</h4>
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
            @foreach($studentsMale as $st)
            <tr>
                <td>{{ $st->lrn ?? 'N/A' }}</td>
                <td>{{ $st->last_name ?? '' }}</td>
                <td>{{ $st->first_name ?? '' }}</td>
                <td>{{ $st->middle_name ?? '' }}</td>
                <td>{{ $st->status ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Female Students ({{ count($studentsFemale ?? []) }})</h4>
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
            @foreach($studentsFemale as $st)
            <tr>
                <td>{{ $st->lrn ?? 'N/A' }}</td>
                <td>{{ $st->last_name ?? '' }}</td>
                <td>{{ $st->first_name ?? '' }}</td>
                <td>{{ $st->middle_name ?? '' }}</td>
                <td>{{ $st->status ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
