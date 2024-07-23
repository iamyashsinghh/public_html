<!DOCTYPE html>
<html>
<head>
    <title>Leads Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 75%;
        }
        h2, p {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .lead-status {
            padding: 5px 10px;
            border-radius: 4px;
            color: #fff;
        }
        .done {
            background-color: #5cb85c;
        }
        .active {
            background-color: #5bc0de;
        }
    </style>
</head>
<body>
    <h2>Leads Report</h2>
    <p>Vendor: {{ $vendorName }} ({{ $businessName }})</p>
    <p>From: {{ $fromDate }} To: {{ $toDate }}</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Lead Datetime</th>
                <th>Name</th>
                <th>Mobile</th>
                <th>Event Date</th>
                <th>Pax</th>
                <th>Lead Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leads as $lead)
                <tr>
                    <td>{{ $lead->lead_id }}</td>
                    <td>{{ \Carbon\Carbon::parse($lead->lead_date)->format('d-M-Y h:i A') }}</td>
                    <td>{{ $lead->name }}</td>
                    <td>{{ $lead->mobile }}</td>
                    <td>{{ \Carbon\Carbon::parse($lead->event_date)->format('d-M-Y') }}</td>
                    <td>{{ $lead->pax }}</td>
                    <td>
                        <span class="lead-status {{ strtolower($lead->lead_status) }}">
                            {{ ucfirst($lead->lead_status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
