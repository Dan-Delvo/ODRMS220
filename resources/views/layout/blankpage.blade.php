<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBNHS: Online Document Request and Management System</title>
    <link rel="icon" type="image/jpg" href="\images\APPLOGO.jpg">
    <link rel="icon" type="image/jpg" href="\images\APPLOGO.jpg">
    <!-- PWA -->
    <meta name="theme-color" content="#6777ef">
    <link rel="apple-touch-icon" href="{{ asset('logo.PNG') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/sass/app.scss', 'resources/js/scripts.js', 'resources/js/datatables-simple-demo.js', 'resources/js/app.js'])

    <!-- Customized Pagination Links-->
    <style>
        .zoomable.zoomed {
            max-height: none !important;
            max-width: 100% !important;
            height: auto !important;
            cursor: zoom-out;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 42px;
            height: 22px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: rgba(255, 255, 255, 0.3);
            transition: 0.4s;
            border-radius: 34px;
        }

        .slider:before {
            content: "";
            position: absolute;
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #1dd3b0;
        }

        input:checked+.slider:before {
            transform: translateX(20px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .page-item.active .page-link {
            z-index: 1;
            color: #fff;
            background-color: #1dd3b0;
            border-color: #1dd3b0;

        }


        .page-item:not(.active) .page-link {
            color: #1f2937;
        }


        .page-item:not(.active) .page-link:hover {
            background-color: #cbd5e1;
        }


        .page-item .page-link[aria-label="« Previous"],
        .page-item .page-link[aria-label="Next »"] {
            background-color: #1f2937;
            color: #fff;
        }

        .floating-attempt {
            position: absolute;
            top: 1.25rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1051;
            background-color: rgba(220, 53, 69, 0.95);
            /* Bootstrap red */
            color: #fff;
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            font-weight: 500;
            max-width: 90%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            animation: fadeInSlide 0.3s ease-out;
            text-align: center;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .floating-attempt i {
            font-size: 1.2rem;
        }

        .floating-attempt.hide {
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        /* Additional CSS for the new table filter functionality */

        /* Search container styling */
        .search-container {
            flex-wrap: nowrap;
        }

        @media (max-width: 768px) {
            .search-container {
                width: 100%;
                flex-wrap: wrap;
            }

            .search-container .input-group {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }

            .search-container .dropdown {
                margin-bottom: 0.5rem;
            }
        }

        /* Active filter option styling */
        .filter-option.active,
        .table-filter-option.active {
            background-color: #1dd3b0;
            color: white;
        }

        .filter-option.active:hover,
        .table-filter-option.active:hover {
            background-color: #17a085;
            color: white;
        }

        /* Dropdown menu styling */
        .dropdown-menu {
            max-height: 250px;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        /* Button icons */
        .btn i {
            font-size: 0.875rem;
        }

        /* Responsive adjustments for mobile */
        @media (max-width: 576px) {
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }

            .search-container .input-group,
            .search-container .dropdown {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }

            .dropdown-toggle {
                justify-content: space-between;
                text-align: left;
            }
        }

        /* Table badge hover effects */
        .table-hover tbody tr:hover {
            background-color: rgba(29, 211, 176, 0.1);
        }

        /* Loading states */
        .btn.loading {
            position: relative;
            pointer-events: none;
            opacity: 0.7;
        }

        .btn.loading::after {
            content: "";
            position: absolute;
            width: 14px;
            height: 14px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* Badge improvements */
        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* Custom scrollbar for dropdown menus */
        .dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }

        .dropdown-menu::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .btn-details {
            background-color: #1dd3b0;
            color: #fff;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-details:hover {
            background-color: #17a085;
            /* darker shade for hover */
            color: #fff;
        }

        /* Search container styling */
        .search-container {
            flex-wrap: nowrap;
        }

        @media (max-width: 768px) {
            .search-container {
                width: 100%;
                flex-wrap: wrap;
            }

            .search-container .input-group {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }
        }

        /* Search input focus styling */
        #searchInput:focus {
            border-color: #1dd3b0;
            box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
        }

        /* Filter dropdown styling */
        .dropdown-menu {
            max-height: 200px;
            overflow-y: auto;
        }

        .filter-option:hover {
            background-color: #f8f9fa;
        }

        .filter-option.active {
            background-color: #1dd3b0;
            color: white;
        }

        /* Modal styling */
        .modal-xl {
            max-width: 1200px;
        }

        .modal-lg {
            max-width: 900px;
        }

        /* Pre-formatted text styling */
        pre {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 1rem;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.85rem;
            line-height: 1.4;
            max-height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Card hover effects */

        .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn.loading {
            opacity: 0.7;
            pointer-events: none;
        }



        /* Badge styling */
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        /* Table row hover effect */
        .audit-row:hover {
            background-color: rgba(29, 211, 176, 0.1);
        }

        /* Spinner styling */
        .spinner-border-sm {
            width: 0.875rem;
            height: 0.875rem;
        }

        /* Button loading states */
        .btn:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            .modal-xl,
            .modal-lg {
                max-width: 95%;
                margin: 1rem auto;
            }

            .card-body {
                padding: 1rem;
            }

            pre {
                font-size: 0.75rem;
                max-height: 200px;
            }
        }

        /* Color scheme consistency */
        .text-primary {
            color: #1dd3b0 !important;
        }

        .bg-primary {
            background-color: #1dd3b0 !important;
        }

        .border-primary {
            border-color: #1dd3b0 !important;
        }

        /* Custom scrollbar for pre elements */
        pre::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        pre::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        pre::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        pre::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body class="sb-nav-fixed">

    @include('layout.partials.navbar')

    <div id="layoutSidenav">

        <div id="layoutSidenav_nav">
            @include('layout.partials.sidebar')
        </div>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4 ">
                    @yield('content')
                </div>
            </main>

            @include('layout.partials.footer')

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>

    <!-- PWA -->
    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        if (!navigator.serviceWorker.controller) {
            navigator.serviceWorker.register("/sw.js")
                .then(function(reg) {
                    console.log("Service worker has been registered for scope: " + reg.scope);
                });
        }
    </script>
    @include('layout.partials.swal-loading')
    @stack('scripts')
</body>

</html>