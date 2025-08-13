<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBNHS: Online Document Request and Management System</title>
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

    @vite(['resources/css/app.css', 'resources/sass/app.scss', 'resources/js/scripts.js', 'resources/js/datatables-simple-demo.js'])

    <!-- Customized Pagination Links-->
    <style>
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
    @stack('scripts')
</body>

</html>