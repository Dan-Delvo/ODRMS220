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
    font-size: 11px;
    margin: 0;
    padding: 0;
    position: relative;
    z-index: 1;
}

/* Watermark */
body::before {
    content: "";
    background-image: url("{{ $ubnhsLogo }}");
    background-size: 400px;
    background-position: center;
    background-repeat: no-repeat;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0.04;
    z-index: 0;
}

/* Header - Only on first page */
.school-header {
    margin-bottom: 2px;
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
    width: 55px;
    height: 55px;
}

.school-header .republic-info,
.school-header .deped-info,
.school-header .school-name,
.school-header .school-address {
    margin: 0.5px 0;
    line-height: 1.1;
}

.school-header .school-name {
    font-size: 15px;
    font-weight: bold;
    text-transform: uppercase;
}

.school-header .republic-info {
    font-size: 10px;
    font-weight: 600;
}

.school-header .deped-info {
    font-size: 10px;
    font-weight: 500;
}

.school-header .school-address {
    font-size: 10px;
}

/* Report Title */
.header {
    text-align: center;
    margin-bottom: 4px;
    padding-bottom: 4px;
    border-bottom: 1px solid #dee2e6;
    width: 100vw;
    max-width: 100vw;
}

.header h1 {
    margin: 0;
    font-size: 18px;
    color: #1f2937;
}

.header .info {
    margin-top: 2px;
    color: #666;
    font-size: 10px;
}

/* Filters */
.filters {
    padding: 4px 8px;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    background-color: #f8f9fa;
    margin-bottom: 4px;
    width: 100vw;
    max-width: 100vw;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 15px;
}

.filter-item {
    font-weight: bold;
    font-size: 10px;
}

/* Table with clean borders */
table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10px;
}

thead th {
    font-weight: bold;
    font-size: 10px;
    text-align: center;
    border: 1px solid #444;
    padding: 4px 3px;
    background-color: #f8f8f8;
    line-height: 1.2;
}

tbody td {
    font-size: 10px;
    padding: 4px 3px;
    vertical-align: middle;
    border: 1px solid #444;
    text-align: center;
    word-wrap: break-word;
    line-height: 1.2;
    white-space: normal;
    overflow-wrap: break-word;
    word-break: break-word;
    max-width: 120px;
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
    padding: 1px 4px;
    border-radius: 2px;
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
    margin-top: 6px;
    text-align: center;
    font-size: 9px;
    color: #666;
    border-top: 1px solid #dee2e6;
    padding-top: 3px;
    width: 100vw;
    max-width: 100vw;
}

.footer p {
    margin: 2px 0;
}

/* Pagination */
@page {
    margin: 8mm 12mm 12mm 12mm;
    @bottom-right {
        content: "Page " counter(page) " of " counter(pages);
        font-size: 9px;
        color: #666;
    }
}

/* Page breaks - first page has header, subsequent pages don't */
@page :first {
    margin-top: 8mm;
}

@page :not(:first) {
    margin-top: 8mm;
}

/* Repeat table header on each page */
thead {
    display: table-header-group;
}

tbody {
    display: table-row-group;
}

/* Avoid breaking rows across pages */
tr {
    page-break-inside: avoid;
}
</style>
</head>
<body>

<!-- Logos + School Info -->
<div class="school-header">
    <table style="width: 100%; border: none; border-collapse: collapse;">
        <tr>
            <td style="width: 20%; text-align: left; border: none; padding-left: 60px;">
                <img src="{{ $ubnhsLogo }}" alt="UBNHS Logo">
            </td>
            <td style="width: 60%; text-align: center; border: none;">
                <div class="republic-info">Republic of the Philippines</div>
                <div class="deped-info">DepEd - National Capital Region</div>
                <div class="deped-info">Division of Taguig City and Pateros</div>
                <div class="deped-info">City of Taguig</div>
                <div class="school-name">Upper Bicutan National High School</div>
                <div class="school-address">General Santos Avenue, Central Bicutan, Taguig City</div>
            </td>
            <td style="width: 20%; text-align: right; border: none; padding-right: 60px;">
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
    <div class="filter-item">
        Date Range: {{ $start_date }} to {{ $end_date }} |
        Status: {{ $status_filter == 'all' ? 'All Status' : $status_filter }} |
        Total Records: {{ $totalCount }}
    </div>
</div>


<!-- Table -->
@if($totalCount > 0)
<table class="table table-bordered table-striped text-center align-middle">
    <thead>
        <tr>
            <th style="width: 6%;">Req #</th>
            <th style="width: 14%;">Student</th>
            <th style="width: 8%;">Doc</th>
            <th style="width: 17%;">School/Entity</th>
            <th style="width: 15%;">Claimer</th>
            <th style="width: 6%;">Req Via</th>
            <th style="width: 7%;">Rel Mode</th>
            <th style="width: 7%;">Status</th>
            <th style="width: 7%;">Req Date</th>
            <th style="width: 7%;">App Date</th>
            <th style="width: 7%;">Rel Date</th>
            <th style="width: 7%;">Clm Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($DocRequests as $item)
        <tr>
            <td>{{ $item->req_no ?? 'N/A' }}</td>
            <td class="uppercase">{{ $item->full_name ?? 'N/A' }}</td>
            <td class="uppercase">{{ $item->DocType ?? 'N/A' }}</td>
            <td class="uppercase">{{ $item->request_schl_entity ?? 'N/A' }}</td>
            <td>
                {{ (empty($item->claimer) || $item->claimer === 'Blank Blank') ? 'N/A' : $item->claimer }}
            </td>
            <td>{{ $item->request_mode ?? 'N/A' }}</td>
            <td>{{ $item->release_mode ?? 'N/A' }}</td>
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
    <p>Generated by: {{Auth::user()->studentInformation->full_name}} </p>
</div>

</body>
</html>
