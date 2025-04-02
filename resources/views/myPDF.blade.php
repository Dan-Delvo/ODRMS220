<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
            word-wrap: break-word;
            word-break: break-word;
        }

        th {
            background-color: #f2f2f2;
        }

        h1, h4 {
            color: #333;
        }

        .total {
            text-align: right;
            font-weight: bold;
        }

        /* Add page-break to handle large tables */
        .page-break {
            page-break-before: always;
        }

        /* Ensure the content fits within a page */
        body {
            max-width: 100%;
            overflow-x: hidden;
        }

        table {
            table-layout: fixed;
            width: 100%;
            max-width: 100%;
        }

        /* Prevent table from overflowing */
        .table-container {
            overflow-x: auto;
            margin-bottom: 30px;
        }

        /* Style for the page to break gracefully */
        @media print {
            body {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            table {
                width: 100%;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <h4>Total Completed Requests: {{ $totalCount }}</h4>
    <p>Date: {{ $date }}</p>

    <!-- Table container to handle overflow -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Claimer</th>
                    <th>Student</th>
                    <th>Document</th>
                    <th>School</th>
                    <th>Requested Via</th>
                    <th>Release Mode</th>
                    <th>Remarks</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($DocRequests as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->claimer->full_name ?? 'N/A' }}</td>
                        <td>{{ $item->studentInformation->full_name ?? 'N/A' }}</td>
                        <td>{{ $item->documents->DocType ?? 'N/A' }}</td>
                        <td>{{ $item->request_schl_entity }}</td>
                        <td>{{ $item->request_mode }}</td>
                        <td>{{ $item->release_mode }}</td>
                        <td>{{ $item->remarks }}</td>
                        <td>{{ $item->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Page break if the content overflows -->
    <div class="page-break"></div>

</body>
</html>
