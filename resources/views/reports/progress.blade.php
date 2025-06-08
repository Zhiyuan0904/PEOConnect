<!DOCTYPE html>
<html>
<head>
    <title>Survey Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f1f5f9; }
    </style>
</head>
<body>
    <h2>Survey Report</h2>
    <table>
        <thead>
            <tr>
                <th>Survey Title</th>
                <th>Total Students</th>
                <th>Responded</th>
                <th>Completion (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $item)
                <tr>
                    <td>{{ $item['survey_title'] }}</td>
                    <td>{{ $item['total'] }}</td>
                    <td>{{ $item['responded'] }}</td>
                    <td>{{ $item['percentage'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
