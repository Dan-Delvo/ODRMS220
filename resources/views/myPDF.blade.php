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
    padding: 0;
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
    margin-bottom: 3px;
    margin-top: 0;
}

.school-header table {
    width: 100vw;
    max-width: 100vw;
    border-collapse: collapse;
}

.school-header td {
    border: none;
    vertical-align: middle;
    padding-left: 0;
    padding-right: 0;
}

.school-header img {
    width: 70px;
    height: 70px;
}

.school-header .republic-info,
.school-header .deped-info,
.school-header .school-name,
.school-header .school-address {
    margin: 1px 0;
}

.school-header .school-name {
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
}

/* Report Title */
.header {
    text-align: center;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #dee2e6;
    width: 100vw;
    max-width: 100vw;
}

.header h1 {
    margin: 0;
    font-size: 22px;
    color: #1f2937;
}

.header .info {
    margin-top: 5px;
    color: #666;
}

/* Filters */
.filters {
    padding: 8px 0;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    background-color: #f8f9fa;
    margin-bottom: 8px;
    width: 100vw;
    max-width: 100vw;

    display: flex;          /* flex container */
    flex-wrap: wrap;        /* wrap if not enough space */
    align-items: center;    /* vertically center items */
    gap: 20px;              /* space between filter items */
}

.filter-item {
    font-weight: bold;
    font-size: 11px;
}

/* Table with clean borders */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11px;
}

thead th {
    font-weight: bold;
    font-size: 11px;
    text-align: center;
    border: 1px solid #444;
    padding: 6px;
    background-color: #f8f8f8;
}

tbody td {
    font-size: 11px;
    padding: 6px;
    vertical-align: middle;
    border: 1px solid #444;
    text-align: center;
    word-wrap: break-word;
}

tbody tr:hover {
    background-color: #f1f1f1;
}

/* Uppercase text */
.uppercase {
    text-transform: uppercase;
}

/* Status badges */
.status-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 8px;
    font-weight: bold;
    color: #fff;
}

.status-pending { background-color: #ffc107; color: #000; }
.status-processing { background-color: #17a2b8; }
.status-for-release { background-color: #007bff; }
.status-claimed { background-color: #28a745; }
.status-default { background-color: #6c757d; }

/* Footer */
.footer {
    margin-top: 10px;
    text-align: center;
    font-size: 10px;
    color: #666;
    border-top: 1px solid #dee2e6;
    padding-top: 5px;
    width: 100vw;
    max-width: 100vw;
}

/* Pagination */
@page {
    margin: 10mm 15mm 15mm 15mm;
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
            <td style="width: 20%; text-align: left; border: none; padding-left: 70px;">
                <img src="{{ $ubnhsLogo }}" alt="UBNHS Logo">
            </td>
            <td style="width: 60%; text-align: center; border: none;">
                <div class="republic-info" style="font-size: 11px; font-weight: 600;">Republic of the Philippines</div>
                <div class="deped-info" style="font-size: 11px; font-weight: 500;">DepEd - National Capital Region</div>
                <div class="deped-info" style="font-size: 11px; font-weight: 500;">Division of Taguig City and Pateros</div>
                <div class="deped-info" style="font-size: 11px; font-weight: 500;">City of Taguig</div>
                <div class="school-name">Upper Bicutan National High School</div>
                <div class="school-address" style="font-size: 11px;">General Santos Avenue, Central Bicutan, Taguig City</div>
            </td>
            <td style="width: 20%; text-align: right; border: none; padding-right: 70px;">
                <img src="{{ $depedLogo }}" alt="DepEd Logo">
            </td>
        </tr>
    </table>
</div>

<!-- Report Header -->
<div class="header">
    <h1>{{ $title }}</h1>
    <div class="info">Generated on: {{ $date }}</div>
</div>

<div class="filters">
    <div class="filter-item">Date Range: {{ $start_date }} to {{ $end_date }}</div>
    <div class="filter-item">Status: {{ $status_filter == 'all' ? 'All Status' : $status_filter }}</div>
    <div class="filter-item">Total Records: {{ $totalCount }}</div>
</div>


<!-- Table -->
@if($totalCount > 0)
<table class="table table-bordered table-striped text-center align-middle">
    <thead>
        <tr>
            <th>Req #</th>
            <th>Student</th>
            <th>Doc</th>
            <th>School/Entity</th>
            <th>Via</th>
            <th>Rel Mode</th>
            <th>Remarks</th>
            <th>Status</th>
            <th>Req Date</th>
            <th>App Date</th>
            <th>Rel Date</th>
            <th>Clm Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($DocRequests as $item)
        <tr>
            <td>{{ $item->req_no ?? 'N/A' }}</td>
            <td class="uppercase">{{ $item->full_name ?? 'N/A' }}</td>
            <td class="uppercase">{{ $item->DocType ?? 'N/A' }}</td>
            <td class="uppercase">{{ $item->request_schl_entity ?? 'N/A' }}</td>
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

<!-- Footer -->
<div class="footer">
    <p>Document Request Management System | Report generated automatically</p>
    <p>This report contains {{ $totalCount }} record(s) based on the selected filters</p>
</div>

</body>
</html>
