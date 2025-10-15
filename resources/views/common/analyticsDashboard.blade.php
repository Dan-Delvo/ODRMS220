@extends('layout.blankpage')

@section('content')
@include('layout.partials.message')
     <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            background: #f8f9fa;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .page-header {
            background: #1f2937;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            margin: 0;
        }

        .breadcrumb {
            margin: 0.5rem 0 0 0;
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
        }

        .modern-card {
            background: white;
            border-radius: 16px;
            border: none;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            height: 100%;
        }

        .modern-card:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-2px);
        }

        .card-header-modern {
            background: transparent;
            border: none;
            padding: 1.5rem 1.5rem 1rem 1.5rem;
        }

        .card-header-modern h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #1a202c;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .card-header-modern .icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-gradient);
            color: white;
            font-size: 0.875rem;
        }

        .card-body-modern {
            padding: 1.5rem;
            padding-top: 0.5rem;
        }

        .filter-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .filter-section input[type="date"] {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .filter-section input[type="date"]:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .btn-filter {
            background: var(--primary-gradient);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: white;
            transition: all 0.2s;
        }

        .btn-filter:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .toggle-switch {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #f7fafc;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e0;
            transition: 0.3s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background: var(--primary-gradient);
        }

        input:checked + .slider:before {
            transform: translateX(20px);
        }

        .stats-text {
            color: #4a5568;
            font-size: 0.875rem;
        }

        .stats-text strong {
            color: #1a202c;
        }

        .chart-container {
            min-height: 300px;
            position: relative;
        }

        @media (max-width: 768px) {
            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }

            .toggle-switch {
                justify-content: center;
            }
        }
    </style>

    <div class="container-fluid px-4 py-4">
        <!-- Page Header -->
        <div class="page-header">
            <h1>📊 Analytics Dashboard</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item active text-white">Analytics</li>
            </ol>
        </div>

        <!-- Main Chart - Full Width -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <h5>
                                <span class="icon">📈</span>
                                <span id="mainChartTitle">Monthly Document Requests</span>
                            </h5>
                            <div class="filter-section">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="date" id="mainStartDate" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm" style="width: 140px;">
                                    <input type="date" id="mainEndDate" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm" style="width: 140px;">
                                    <input type="number" id="mainStartYear" placeholder="Start Year" class="form-control form-control-sm" style="width: 120px; display: none;" min="2000" max="2100">
                                    <input type="number" id="mainEndYear" placeholder="End Year" class="form-control form-control-sm" style="width: 120px; display: none;" min="2000" max="2100">
                                    <button type="button" id="mainFilterBtn" class="btn btn-filter btn-sm">Filter</button>
                                    <button type="button" id="mainResetBtn" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">Reset</button>
                                </div>
                                <div class="toggle-switch">
                                    <span class="small text-muted">Monthly</span>
                                    <label class="switch mb-0">
                                        <input type="checkbox" id="toggleYearly">
                                        <span class="slider"></span>
                                    </label>
                                    <span class="small text-muted">Yearly</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <div class="chart-container" id="monthlyRequestsChart"></div>
                        <div class="text-center mt-3 stats-text">
                            <strong>Total Requests:</strong> <span id="totalRequests">{{ $totalRequestsInInterval }}</span><br>
                            <small id="dateRangeText">
                                From <strong>{{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }}</strong>
                                to <strong>{{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</strong>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Grid -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5>
                                <span class="icon">📄</span>
                                Request by Document Type
                            </h5>
                            <select id="docTypeFilter" class="form-select form-select-sm" style="width: auto; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <option value="all">All Types</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <div class="chart-container" id="docTypeChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5>
                                <span class="icon">🌐</span>
                                Request Mode
                            </h5>
                            <select id="requestModeFilter" class="form-select form-select-sm" style="width: auto; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <option value="all">All Types</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <div class="chart-container" id="modeChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Three Column Grid -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5>
                                <span class="icon">🎓</span>
                                Grade Level Distribution
                            </h5>
                            <select id="gradeLevelFilter" class="form-select form-select-sm" style="width: auto; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <option value="all">All Types</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <div class="chart-container" id="gradeLevelChart" style="min-height: 250px;"></div>
                        <div class="text-center mt-3 stats-text">
                            <strong>Total:</strong> <span id="gradeLevelTotal">856</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="col-12 col-lg-4">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <h5>
                            <span class="icon">💰</span>
                            Monthly Revenue
                        </h5>
                    </div>
                    <div class="card-body-modern">
                        <div class="chart-container" id="revenueChart" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div> -->
            <div class="col-12 col-lg-6">
                <div class="modern-card">
                    <div class="card-header-modern">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <h5>
                                <span class="icon">📦</span>
                                Unclaimed Documents
                            </h5>
                            <div class="filter-section">
                                <input type="date" id="unclaimedStartDate" class="form-control form-control-sm" style="width: 140px;">
                                <input type="date" id="unclaimedEndDate" class="form-control form-control-sm" style="width: 140px;">
                                <button type="button" id="unclaimedFilterBtn" class="btn btn-filter btn-sm">Filter</button>
                                <button type="button" id="unclaimedResetBtn" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">Reset</button>
                                <div class="toggle-switch">
                                    <span class="small text-muted">Monthly</span>
                                    <label class="switch mb-0">
                                        <input type="checkbox" id="toggleUnclaimedYearly">
                                        <span class="slider"></span>
                                    </label>
                                    <span class="small text-muted">Yearly</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body-modern">
                        <div class="chart-container" id="unclaimedChart" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const revenueData = @json($revenueData);
    const monthlyRequests = @json($monthlyRequestsData);
    const yearlyRequests = @json($yearlyRequestsData);

    const allMonths = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    const mapDataToAllMonths = data => allMonths.map(month => data[month] ?? 0);

    let monthlyRequestsChart;
    let currentViewMode = 'monthly';

    // Function to render/update main chart
    function renderMainChart(startDate = null, endDate = null, viewMode = 'monthly') {
        let categories, chartData, chartTitle, chartColor;
        
        if (viewMode === 'yearly') {
            // Yearly view
            let yearlyData = yearlyRequests;
            
            // Filter by year range if provided
            if (startDate && endDate) {
                const startYear = parseInt(startDate);
                const endYear = parseInt(endDate);
                
                yearlyData = Object.keys(yearlyRequests)
                    .filter(year => Number(year) >= startYear && Number(year) <= endYear)
                    .reduce((obj, year) => {
                        obj[year] = yearlyRequests[year];
                        return obj;
                    }, {});
            }
            
            categories = Object.keys(yearlyData);
            chartData = Object.values(yearlyData);
            chartTitle = 'Requests Per Year';
            chartColor = '#1dd3b0';
        } else {
            // Monthly view
            let filteredData = monthlyRequests;
            
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const startMonth = start.getMonth();
                const endMonth = end.getMonth();
                
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                                'July', 'August', 'September', 'October', 'November', 'December'];
                
                filteredData = {};
                monthNames.forEach((month, index) => {
                    if (index >= startMonth && index <= endMonth && monthlyRequests[month]) {
                        filteredData[month] = monthlyRequests[month];
                    }
                });
            }
            
            categories = allMonths;
            chartData = mapDataToAllMonths(filteredData);
            chartTitle = 'Requests Per Month';
            chartColor = '#36A2EB';
        }
        
        // Calculate total
        const total = chartData.reduce((a, b) => a + b, 0);
        document.getElementById('totalRequests').textContent = total;
        
        const chartOptions = {
            chart: { type: 'bar', height: 300, toolbar: { show: true } },
            series: [{ name: chartTitle, data: chartData }],
            xaxis: { 
                categories: categories,
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            colors: [chartColor],
            plotOptions: { bar: { borderRadius: 4 } },
            title: { text: chartTitle, align: 'center' },
            tooltip: { y: { formatter: val => val + ' requests' } },
            dataLabels: { 
                enabled: true,
                formatter: function(val) {
                    return Math.round(val);
                }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                },
                tickAmount: undefined,
                forceNiceScale: true,
                decimalsInFloat: 0
            }
        };
        
        if (monthlyRequestsChart) {
            monthlyRequestsChart.destroy();
        }
        monthlyRequestsChart = new ApexCharts(document.querySelector("#monthlyRequestsChart"), chartOptions);
        monthlyRequestsChart.render();
    }

    // Notification function
    function showNotification(message, type = 'info') {
        const existingNotif = document.querySelector('.custom-notification');
        if (existingNotif) {
            existingNotif.remove();
        }
        
        const notification = document.createElement('div');
        notification.className = 'custom-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'warning' ? '#ffc107' : type === 'success' ? '#28a745' : '#17a2b8'};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            font-size: 14px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        `;
        notification.textContent = message;
        
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        if (!document.querySelector('style[data-notification-style]')) {
            style.setAttribute('data-notification-style', 'true');
            document.head.appendChild(style);
        }
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Initial render
    renderMainChart();

    // Toggle between monthly/yearly
    document.getElementById('toggleYearly').addEventListener('change', function() {
        currentViewMode = this.checked ? 'yearly' : 'monthly';
        
        // Toggle input visibility
        const dateInputs = document.querySelectorAll('#mainStartDate, #mainEndDate');
        const yearInputs = document.querySelectorAll('#mainStartYear, #mainEndYear');
        
        if (this.checked) {
            // Show year inputs, hide date inputs
            dateInputs.forEach(input => input.style.display = 'none');
            yearInputs.forEach(input => input.style.display = 'block');
            document.getElementById('mainChartTitle').textContent = 'Yearly Document Requests';
        } else {
            // Show date inputs, hide year inputs
            dateInputs.forEach(input => input.style.display = 'block');
            yearInputs.forEach(input => input.style.display = 'none');
            document.getElementById('mainChartTitle').textContent = 'Monthly Document Requests';
        }
        
        renderMainChart(null, null, currentViewMode);
    });

    // Filter button
    document.getElementById('mainFilterBtn').addEventListener('click', function() {
        let startValue, endValue;
        
        if (currentViewMode === 'yearly') {
            startValue = document.getElementById('mainStartYear').value;
            endValue = document.getElementById('mainEndYear').value;
        } else {
            startValue = document.getElementById('mainStartDate').value;
            endValue = document.getElementById('mainEndDate').value;
        }
        
        if (startValue && endValue) {
            renderMainChart(startValue, endValue, currentViewMode);
        } else {
            showNotification('Please select both start and end ' + (currentViewMode === 'yearly' ? 'years' : 'dates'), 'warning');
        }
    });

    // Reset button
    document.getElementById('mainResetBtn').addEventListener('click', function() {
        document.getElementById('mainStartDate').value = '';
        document.getElementById('mainEndDate').value = '';
        document.getElementById('mainStartYear').value = '';
        document.getElementById('mainEndYear').value = '';
        document.getElementById('toggleYearly').checked = false;
        currentViewMode = 'monthly';
        
        // Reset input visibility
        document.querySelectorAll('#mainStartDate, #mainEndDate').forEach(input => input.style.display = 'block');
        document.querySelectorAll('#mainStartYear, #mainEndYear').forEach(input => input.style.display = 'none');
        document.getElementById('mainChartTitle').textContent = 'Monthly Document Requests';
        
        renderMainChart();
    });

    // Date input changes
    document.getElementById('mainStartDate').addEventListener('change', function() {
        const startDate = this.value;
        const endDate = document.getElementById('mainEndDate').value;
        if (startDate && endDate) {
            renderMainChart(startDate, endDate, currentViewMode);
        }
    });

    document.getElementById('mainEndDate').addEventListener('change', function() {
        const startDate = document.getElementById('mainStartDate').value;
        const endDate = this.value;
        if (startDate && endDate) {
            renderMainChart(startDate, endDate, currentViewMode);
        }
    });

    // Year input changes
    document.getElementById('mainStartYear').addEventListener('change', function() {
        const startYear = this.value;
        const endYear = document.getElementById('mainEndYear').value;
        if (startYear && endYear) {
            renderMainChart(startYear, endYear, currentViewMode);
        }
    });

    document.getElementById('mainEndYear').addEventListener('change', function() {
        const startYear = document.getElementById('mainStartYear').value;
        const endYear = this.value;
        if (startYear && endYear) {
            renderMainChart(startYear, endYear, currentViewMode);
        }
    });

    // ✅ Document Type (Donut)
    const docTypeData = @json($docTypeData);
    let docTypeChart; // Store chart instance

    // Define color palette
    const docTypeColorPalette = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];

    // Create dynamic color mapping based on actual data keys from database
    const docTypeColors = {};
    Object.keys(docTypeData).forEach((type, index) => {
        docTypeColors[type] = docTypeColorPalette[index % docTypeColorPalette.length];
    });

    // Populate filter dropdown
    const docTypeFilter = document.getElementById('docTypeFilter');
    Object.keys(docTypeData).forEach(type => {
        const option = document.createElement('option');
        option.value = type;
        option.textContent = type;
        docTypeFilter.appendChild(option);
    });

    // Function to render/update chart
    function renderDocTypeChart(filterValue = 'all') {
        let filteredData = {};
        let filteredColors = [];
        
        if (filterValue === 'all') {
            filteredData = docTypeData;
            filteredColors = Object.keys(docTypeData).map(type => docTypeColors[type]);
        } else {
            filteredData[filterValue] = docTypeData[filterValue];
            filteredColors = [docTypeColors[filterValue]];
        }
        
        const chartOptions = {
            chart: { type: 'donut', height: 300, toolbar: { show: true } },
            series: Object.values(filteredData),
            labels: Object.keys(filteredData),
            colors: filteredColors,
            legend: { position: 'bottom' },
            title: { text: 'Document Types', align: 'center' },
            dataLabels: {
                formatter: function (val, opts) {
                    const actualValue = opts.w.config.series[opts.seriesIndex];
                    return actualValue;
                },
                style: {
                    fontSize: '14px',
                    colors: ['#fff']
                }
            }
        };
        
        // Destroy existing chart and create new one
        if (docTypeChart) {
            docTypeChart.destroy();
        }
        docTypeChart = new ApexCharts(document.querySelector("#docTypeChart"), chartOptions);
        docTypeChart.render();
    }

    // Initial render
    renderDocTypeChart();

    // Add event listener for filter
    docTypeFilter.addEventListener('change', function() {
        renderDocTypeChart(this.value);
    });

    const modeData = @json($modeData);
    let modeChart; // Store chart instance

    // Define color palette
    const colorPalette = ['#1dd3b0', '#1f2937', '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'];

    // Create dynamic color mapping based on actual data keys from database
    const modeColors = {};
    Object.keys(modeData).forEach((mode, index) => {
        modeColors[mode] = colorPalette[index % colorPalette.length];
    });

    // Populate filter dropdown
    const requestModeFilter = document.getElementById('requestModeFilter');
    Object.keys(modeData).forEach(mode => {
        const option = document.createElement('option');
        option.value = mode;
        option.textContent = mode;
        requestModeFilter.appendChild(option);
    });

    // Function to render/update chart
    function renderModeChart(filterValue = 'all') {
        let filteredData = {};
        let filteredColors = [];
        
        if (filterValue === 'all') {
            filteredData = modeData;
            filteredColors = Object.keys(modeData).map(mode => modeColors[mode]);
        } else {
            filteredData[filterValue] = modeData[filterValue];
            filteredColors = [modeColors[filterValue]];
        }
        
        const chartOptions = {
            chart: { type: 'pie', height: 300, toolbar: { show: true } },
            series: Object.values(filteredData),
            labels: Object.keys(filteredData),
            colors: filteredColors,
            legend: { position: 'bottom' },
            title: { text: 'Request Mode', align: 'center' },
            dataLabels: {
                formatter: function (val, opts) {
                    const actualValue = opts.w.config.series[opts.seriesIndex];
                    return actualValue;
                },
                style: {
                    fontSize: '14px',
                    colors: ['#fff']
                }
            }
        };
        
        // Destroy existing chart and create new one
        if (modeChart) {
            modeChart.destroy();
        }
        modeChart = new ApexCharts(document.querySelector("#modeChart"), chartOptions);
        modeChart.render();
    }

    // Initial render
    renderModeChart();

    // Add event listener for filter
    requestModeFilter.addEventListener('change', function() {
        renderModeChart(this.value);
    });

    // ✅ Grade Level
    const gradeLevelData = @json($gradeLevelData);
    let gradeLevelChart; // Store chart instance

    // Populate filter dropdown
    const gradeLevelFilter = document.getElementById('gradeLevelFilter');
    Object.keys(gradeLevelData).forEach(grade => {
        const option = document.createElement('option');
        option.value = grade;
        option.textContent = `Grade ${grade}`;
        gradeLevelFilter.appendChild(option);
    });

    // Function to render/update chart
    function renderGradeLevelChart(filterValue = 'all') {
        let filteredData = {};
        
        if (filterValue === 'all') {
            filteredData = gradeLevelData;
        } else {
            filteredData[filterValue] = gradeLevelData[filterValue];
        }
        
        const gradeLabels = Object.keys(filteredData).map(l => `Grade ${l}`);
        const gradeValues = Object.values(filteredData);
        document.getElementById('gradeLevelTotal').textContent = gradeValues.reduce((a,b)=>a+b,0);
        
        const chartOptions = {
            chart: { type: 'bar', height: 300 },
            series: [{ name: 'Requests', data: gradeValues }],
            xaxis: { categories: gradeLabels },
            colors: ['#1dd3b0'],
            dataLabels: { 
                enabled: true,
                formatter: function(val) {
                    return Math.round(val);
                }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            title: { text: 'Requests by Grade Level', align: 'center' }
        };
        
        // Destroy existing chart and create new one
        if (gradeLevelChart) {
            gradeLevelChart.destroy();
        }
        gradeLevelChart = new ApexCharts(document.querySelector("#gradeLevelChart"), chartOptions);
        gradeLevelChart.render();
    }

    // Initial render
    renderGradeLevelChart();

    // Add event listener for filter
    gradeLevelFilter.addEventListener('change', function() {
        renderGradeLevelChart(this.value);
    });

    // ✅ Revenue (Area)
    new ApexCharts(document.querySelector("#revenueChart"), {
        chart: { type: 'area', height: 300 },
        series: [{ name: 'Revenue (₱)', data: mapDataToAllMonths(revenueData) }],
        xaxis: { categories: allMonths },
        colors: ['#4BC0C0'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        yaxis: { labels: { formatter: val => '₱' + val.toLocaleString() } },
        title: { text: 'Monthly Revenue', align: 'center' }
    }).render();

   // ✅ Unclaimed Documents (Bar)
const unclaimedData = @json($unclaimedData);
const unclaimedYearlyData = @json($unclaimedYearlyData);
let unclaimedChart; // Store chart instance

// Function to render/update chart
function renderUnclaimedChart(startDate = null, endDate = null, viewMode = 'monthly') {
    let categories, chartData;
    
    if (viewMode === 'yearly') {
        // Use yearly data from backend
        let yearlyData = unclaimedYearlyData;
        
        // Filter by date range if provided
        if (startDate && endDate) {
            const startYear = new Date(startDate).getFullYear();
            const endYear = new Date(endDate).getFullYear();
            
            yearlyData = Object.keys(unclaimedYearlyData)
                .filter(year => Number(year) >= startYear && Number(year) <= endYear)
                .reduce((obj, year) => {
                    obj[year] = unclaimedYearlyData[year];
                    return obj;
                }, {});
        }
        
        categories = Object.keys(yearlyData);
        chartData = Object.values(yearlyData);
    } else {
        // Monthly view
        let filteredData = unclaimedData;
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const startMonth = start.getMonth(); // 0-11
            const endMonth = end.getMonth(); // 0-11
            
            // Filter months based on the date range
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December'];
            
            filteredData = {};
            monthNames.forEach((month, index) => {
                if (index >= startMonth && index <= endMonth && unclaimedData[month]) {
                    filteredData[month] = unclaimedData[month];
                }
            });
        }
        
        categories = allMonths;
        chartData = mapDataToAllMonths(filteredData);
    }
    
    const chartOptions = {
        chart: { type: 'bar', height: 300 },
        series: [{ name: 'Unclaimed Documents', data: chartData }],
        xaxis: { 
            categories: categories,
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        colors: ['#FF6384'],
        dataLabels: { 
            enabled: true,
            formatter: function(val) {
                return Math.round(val);
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return Math.round(val);
                }
            },
            tickAmount: undefined,
            forceNiceScale: true,
            decimalsInFloat: 0
        },
        title: { text: 'Unclaimed Documents', align: 'center' }
    };
    
    // Destroy existing chart and create new one
    if (unclaimedChart) {
        unclaimedChart.destroy();
    }
    unclaimedChart = new ApexCharts(document.querySelector("#unclaimedChart"), chartOptions);
    unclaimedChart.render();
}

// Initial render
renderUnclaimedChart();

// Add event listeners
document.getElementById('unclaimedStartDate').addEventListener('change', function() {
    const startDate = this.value;
    const endDate = document.getElementById('unclaimedEndDate').value;
    const viewMode = document.getElementById('toggleUnclaimedYearly').checked ? 'yearly' : 'monthly';
    if (startDate && endDate) {
        renderUnclaimedChart(startDate, endDate, viewMode);
    }
});

document.getElementById('unclaimedEndDate').addEventListener('change', function() {
    const startDate = document.getElementById('unclaimedStartDate').value;
    const endDate = this.value;
    const viewMode = document.getElementById('toggleUnclaimedYearly').checked ? 'yearly' : 'monthly';
    if (startDate && endDate) {
        renderUnclaimedChart(startDate, endDate, viewMode);
    }
});

document.getElementById('toggleUnclaimedYearly').addEventListener('change', function() {
    const startDate = document.getElementById('unclaimedStartDate').value;
    const endDate = document.getElementById('unclaimedEndDate').value;
    const viewMode = this.checked ? 'yearly' : 'monthly';
    renderUnclaimedChart(startDate, endDate, viewMode);
});

document.getElementById('unclaimedFilterBtn').addEventListener('click', function() {
    const startDate = document.getElementById('unclaimedStartDate').value;
    const endDate = document.getElementById('unclaimedEndDate').value;
    const viewMode = document.getElementById('toggleUnclaimedYearly').checked ? 'yearly' : 'monthly';
    
    if (startDate && endDate) {
        renderUnclaimedChart(startDate, endDate, viewMode);
    } else {
        alert('Please select both start and end dates');
    }
});

document.getElementById('unclaimedResetBtn').addEventListener('click', function() {
    document.getElementById('unclaimedStartDate').value = '';
    document.getElementById('unclaimedEndDate').value = '';
    document.getElementById('toggleUnclaimedYearly').checked = false;
    renderUnclaimedChart();
});
</script>
@endsection
