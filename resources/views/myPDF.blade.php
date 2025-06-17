<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .school-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1f2937;
        }
        .school-header .logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
        }
        .school-header .logo-placeholder {
            width: 60px;
            height: 60px;
            background-color: #f0f0f0;
            border: 2px solid #ccc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #666;
        }
        .school-header .republic-info {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .school-header .deped-info {
            font-size: 12px;
            color: #333;
            margin-bottom: 5px;
        }
        .school-header .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
        }
        .school-header .school-address {
            font-size: 12px;
            color: #333;
            margin-bottom: 5px;
        }
        .school-header .department {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            text-decoration: underline;
            margin-top: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #1f2937;
            margin: 10px 0 0 0;
            font-size: 24px;
        }
        .header .info {
            margin-top: 10px;
            color: #666;
        }
        .filters {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .filters h3 {
            margin: 0 0 10px 0;
            color: #1f2937;
            font-size: 14px;
        }
        .filter-item {
            display: inline-block;
            margin-right: 20px;
            font-weight: bold;
        }
        .summary {
            text-align: right;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #1f2937;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            border: 1px solid #000;
        }
        td {
            padding: 4px 6px;
            border: 1px solid #ddd;
            font-size: 9px;
            vertical-align: top;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
        .status-processing {
            background-color: #17a2b8;
            color: #fff;
        }
        .status-for-release {
            background-color: #007bff;
            color: #fff;
        }
        .status-claimed {
            background-color: #28a745;
            color: #fff;
        }
        .status-default {
            background-color: #6c757d;
            color: #fff;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            position: relative;
        }
        @page {
            margin: 15mm;
            @bottom-right {
                content: "Page " counter(page) " of " counter(pages);
                font-size: 10px;
                color: #666;
            }
        }
        .page-break {
            page-break-before: always;
        }
        @page {
            margin: 15mm;
        }
    </style>
</head>
<body>
    <div class="school-header">
        <div class="logos">
            <img src="{{ public_path('images/LOGO1.png') }}" alt="Logo 1" style="width: 60px; height: 60px;">
            <img src="{{ public_path('images/LOGO2.png') }}" alt="Logo 2" style="width: 60px; height: 60px;">
        </div>
        <div class="republic-info">Republic of the Philippines</div>
        <div class="deped-info">DepEd – National Capital Region</div>
        <div class="deped-info">Division of Taguig City and Pateros</div>
        <div class="deped-info">City of Taguig</div>
        <div class="school-name">Upper Bicutan National High School</div>
        <div class="school-address">General Santos Avenue, Central Bicutan, Taguig City</div>
        <div class="department">Senior High School Department</div>
    </div>

    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="info">
            Generated on: {{ $date }}
        </div>
    </div>

    <div class="filters">
        <h3>Report Filters</h3>
        <div class="filter-item">Date Range: {{ $start_date }} to {{ $end_date }}</div>
        <div class="filter-item">
            Status:
            @if($status_filter == 'all')
                All Status
            @else
                {{ $status_filter }}
            @endif
        </div>
    </div>

    <div class="summary">
        Total Records: {{ $totalCount }}
    </div>

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
                <td>{{ $item->request_date ? \Carbon\Carbon::parse($item->request_date)->format('m/d/Y') : 'N/A' }}</td>
                <td>{{ $item->approve_date ? \Carbon\Carbon::parse($item->approve_date)->format('m/d/Y') : 'N/A' }}</td>
                <td>{{ $item->forRelease_date ? \Carbon\Carbon::parse($item->forRelease_date)->format('m/d/Y') : 'N/A' }}</td>
                <td>{{ $item->claimed_date ? \Carbon\Carbon::parse($item->claimed_date)->format('m/d/Y') : 'N/A' }}</td>
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

    <div class="footer">
        <p>Document Request Management System | Report generated automatically</p>
        <p>This report contains {{ $totalCount }} record(s) based on the selected filters</p>
    </div>
</body>
</html>
