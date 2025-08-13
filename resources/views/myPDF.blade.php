@php
    function embedBase64Image($path) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    $ubnhsLogo = embedBase64Image(public_path('images/UBNHSLOGO.png'));
    $depedLogo = embedBase64Image(public_path('images/DOLOGO.png'));
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Bookman Old Style', serif;
            font-size: 12px;
            margin: 0;
            padding: 0 20px 20px 20px;
            position: relative;
            z-index: 1;
        }

        /* Watermark */
        body::before {
            content: "";
            background-image: url("{{ $ubnhsLogo }}");
            background-size: 450px;
            background-position: center;
            background-repeat: no-repeat;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.05;
            z-index: 0;
        }

        /* Header */
        .school-header {
            margin-bottom: 30px;
        }

        .school-header table {
            width: 100%;
            border-collapse: collapse;
        }

        .school-header td {
            border: none;
            vertical-align: middle;
        }

        .school-header img {
            width: 75px;
            height: 75px;
        }

        .school-header .republic-info,
        .school-header .deped-info,
        .school-header .school-name,
        .school-header .school-address {
            margin: 2px 0;
        }

        .school-header .school-name {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Title */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1f2937;
        }

        .header .info {
            margin-top: 10px;
            color: #666;
        }

        /* Filters */
        .filters {
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }

        .filters h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #1f2937;
        }

        .filter-item {
            display: inline-block;
            margin-right: 20px;
            font-weight: bold;
        }

        /* Summary */
        .summary {
            text-align: right;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            font-size: 9px;
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        th {
            background-color: #1f2937;
            color: white;
            font-size: 10px;
            text-align: left;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .status-pending { background-color: #ffc107; color: #000; }
        .status-processing { background-color: #17a2b8; color: #fff; }
        .status-for-release { background-color: #007bff; color: #fff; }
        .status-claimed { background-color: #28a745; color: #fff; }
        .status-default { background-color: #6c757d; color: #fff; }

        /* Footer */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }

        /* Pagination */
        @page {
            margin: 15mm;
            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 10px;
                color: #666;
            }
        }
    </style>
</head>
<body>
    <!-- Logos + School Info -->
    <div class="school-header">
    <table style="width: 100%; border: none; border-collapse: collapse;">
        <tr>
            <!-- Left Logo -->
            <td style="width: 20%; text-align: left; border: none; padding-left: 70px;">
                <img src="{{ $ubnhsLogo }}" alt="UBNHS Logo" style="width: 94px; height: 94px;">
            </td>

            <!-- Center Info -->
            <td style="width: 60%; text-align: center; border: none;">
                <div class="republic-info">Republic of the Philippines</div>
                <div class="deped-info">DepEd - National Capital Region</div>
                <div class="deped-info">Division of Taguig City and Pateros</div>
                <div class="deped-info">City of Taguig</div>
                <div class="school-name">Upper Bicutan National High School</div>
                <div class="school-address">General Santos Avenue, Central Bicutan, Taguig City</div>
            </td>

            <!-- Right Logo -->
            <td style="width: 20%; text-align: right; border: none; padding-right: 70px;">
                <img src="{{ $depedLogo }}" alt="DepEd Logo" style="width: 94px; height: 94px;">
            </td>
        </tr>
    </table>
</div>


    <!-- Report Header -->
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="info">Generated on: {{ $date }}</div>
    </div>

    <!-- Filters -->
    <div class="filters">
        <h3>Report Filters</h3>
        <div class="filter-item">Date Range: {{ $start_date }} to {{ $end_date }}</div>
        <div class="filter-item">
            Status: {{ $status_filter == 'all' ? 'All Status' : $status_filter }}
        </div>
    </div>

    <!-- Summary -->
    <div class="summary">Total Records: {{ $totalCount }}</div>

    <!-- Table -->
    @if($totalCount > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">Req #</th>
                <th style="width: 12%;">Student</th>
                <th style="width: 10%;">Doc</th>
                <th style="width: 10%;">School</th>
                <th style="width: 6%;">Via</th>
                <th style="width: 6%;">Rel Mode</th>
                <th style="width: 12%;">Remarks</th>
                <th style="width: 6%;">Status</th>
                <th style="width: 6%;">Req Date</th>
                <th style="width: 6%;">App Date</th>
                <th style="width: 6%;">Rel Date</th>
                <th style="width: 6%;">Clm Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($DocRequests as $item)
            <tr>
                <td>{{ $item->req_no ?? 'N/A' }}</td>
                <td>{{ $item->studentInformation->full_name ?? 'N/A' }}</td>
                <td>{{ $item->documents->DocType ?? 'N/A' }}</td>
                <td>{{ $item->request_schl_entity ?? 'N/A' }}</td>
                <td>{{ $item->request_mode ?? 'N/A' }}</td>
                <td>{{ $item->release_mode ?? 'N/A' }}</td>
                <td>{{ $item->remarks ?? 'N/A' }}</td>
                <td>
                    @php
                        $status = $item->status ?? 'Unknown';
                        $statusClass = match($status) {
                            'Pending' => 'status-pending',
                            'Processing' => 'status-processing',
                            'For Release' => 'status-for-release',
                            'Claimed' => 'status-claimed',
                            default => 'status-default'
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $status }}</span>
                </td>
                <td>{{ optional($item->request_date)->format('m/d/Y') ?? 'N/A' }}</td>
                <td>{{ optional($item->approve_date)->format('m/d/Y') ?? 'N/A' }}</td>
                <td>{{ optional($item->forRelease_date)->format('m/d/Y') ?? 'N/A' }}</td>
                <td>{{ optional($item->claimed_date)->format('m/d/Y') ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 50px; color: #666;">
        <h3>No Records Found</h3>
        <p>No document requests found for the selected criteria.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Document Request Management System | Report generated automatically</p>
        <p>This report contains {{ $totalCount }} record(s) based on the selected filters</p>
    </div>
</body>
</html>
