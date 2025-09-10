<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UBNHS: Online Document Request and Management System</title>
    <link rel="icon" type="image/jpg" href="\images\APPLOGO.jpg">
    <meta name="theme-color" content="#6777ef">
    <link rel="apple-touch-icon" href="{{ asset('logo.PNG') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/sass/app.scss', 'resources/js/scripts.js', 'resources/js/datatables-simple-demo.js'])

    <style>
        :root {
            --sidebar-width: 270px;
            /* full size sidebar */
            --sidebar-collapsed-width: 85px;
            /* collapsed sidebar */
        }

        /* Default desktop layout */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            z-index: 1050;
            /* stays above content */
            transition: width 0.3s ease;
        }

        body.sidebar-shrink .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background: #0f172a;
            padding: 6rem 2rem 2rem;
        }

        body.sidebar-shrink .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Mobile behavior */
        @media (max-width: 991.98px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1100;
                /* ensure it's always above cards */
            }

            .main-content {
                margin-left: 0 !important;
                /* don’t push content */
                padding: 5rem 1rem 1rem;
                /* add top padding so cards don’t hide under nav */
            }
        }


        body,
        html {
            background-color: #0f172a;
            color: #e2e8f0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        footer,
        .footer {
            display: none !important;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.4s ease;
            padding: 6rem 2rem 2rem;
        }

        body.sidebar-shrink .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        .id-header h5 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1dd3b0;
            margin-bottom: 2rem;
        }


        .id-card {
            background-color: #1e293b;
            padding: 20px 25px;
            margin-bottom: 20px;
            border-radius: 12px;
            border: 2px solid #334155;
        }

        .id-card h5 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1dd3b0;
            margin-bottom: 20px;
            border-bottom: 2px solid #334155;
            padding-bottom: 10px;
        }

        .id-card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .id-card li {
            font-size: 1rem;
            line-height: 1.8;
            padding: 8px 0;
            border-bottom: 1px solid #475569;
            display: flex;
            justify-content: space-between;
        }

        .id-card li:last-child {
            border-bottom: none;
        }

        .id-card li strong {
            color: #f1f5f9;
            font-weight: 600;
            width: 50%;
        }

        .card-container {
            background-color: #1e293b;
            border: 2px solid #334155;
            border-radius: 1rem;
            padding: 2rem;
            margin-top: 3rem;
        }

        .card-header-custom {
            background-color: transparent;
            padding-bottom: 1rem;
            border-bottom: 1px solid #334155;
        }

        .custom-table th {
            background-color: #334155;
            color: #f1f5f9;
        }

        .custom-table td {
            background-color: #1e293b;
            color: #e2e8f0;
            vertical-align: middle;
        }

        .custom-table tr:hover td {
            background-color: #475569;
        }

        .badge {
            font-size: 0.75rem;
            padding: 5px 10px;
            border-radius: 8px;
        }

        .badge.bg-success {
            background-color: #16a34a;
            color: #fff;
        }

        .badge.bg-warning {
            background-color: #1dd3b0;
            color: #1f2937;
        }

        .badge.bg-secondary {
            background-color: #64748b;
            color: #f8fafc;
        }

        .pagination>.page-item>.page-link {
            background-color: #334155;
            color: #f8fafc;
            border: none;
        }

        .pagination>.page-item.active>.page-link {
            background-color: #1dd3b0;
            color: #1e293b;
            font-weight: bold;
        }

        .pagination>.page-item>.page-link:hover {
            background-color: #64748b;
        }

        .custom-table th,
        .custom-table td {
            border: 1px solid #64748b !important;
        }

        .custom-table {
            border: 1px solid #64748b;
            border-radius: 8px;
            overflow: hidden;
        }

        .text-accent {
            color: #1dd3b0 !important;
        }

        @media (max-width: 768px) {
            .main-content {
                padding-top: 7rem;
                margin-left: 0 !important;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
                margin-left: 0 !important;
            }

            .id-header h5 {
                font-size: 1.3rem;
                text-align: center;
            }

            .id-header {
                margin-top: 1.5rem;
                padding-top: 1rem;
            }

            .id-card {
                margin-left: 0 !important;
            }

            .card-container {
                padding: 1.2rem;
                margin-top: 2rem;
            }

            .id-card li {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .id-card li strong {
                width: 100%;
            }
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

        .form-container {
            width: 100%;
            max-width: 15000px;
            background-color: #1e293b;
            border: 2px solid #334155;
            border-radius: 1rem;
            padding: 3rem 4rem;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1dd3b0;
            margin-bottom: 2rem;
        }

        .form-section-title {
            font-size: 1.1rem;
            font-weight: bold;
            color: #1dd3b0;
            margin-bottom: 1rem;
        }

        .form-control,
        .form-select {
            background-color: #334155;
            border: none;
            color: #f1f5f9;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #475569;
            color: #fff;
            box-shadow: none;
        }
    </style>
</head>

<body>
    @include('layout.partials.navstud')

    <div id="layoutStudent">
        <div id="layoutStudent_content">
            <main>
                @yield('content')
            </main>
        </div>

        <div class="mb-5"></div>

        @include('layout.partials.normalfooter')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- PWA & OneSignal merged service worker -->
    <script src="{{ asset('/public/sw.js') }}"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/public/sw.js') // Register the merged service worker
                .then(function(registration) {
                    console.log('Service Worker registered with scope:', registration.scope);
                })
                .catch(function(error) {
                    console.error('Service Worker registration failed:', error);
                });
        }
    </script>

    <!-- OneSignal Script -->
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function(OneSignal) {
            await OneSignal.init({
                appId: "4177a306-5791-4b2c-ac5a-ae6b4bb937bf",
                serviceWorkerPath: '/public/push/OneSignal/OneSignalSDKWorker.js', // Ensure this is correct path
            });

            // Access the push subscription ID
            const pushSubscriptionId = await OneSignal.User.PushSubscription.id;
            console.log("Push Subscription ID:", pushSubscriptionId);

            // Get the current token from the server
            const existingToken = "{{ auth()->user()->fcm_token ?? '' }}";

            // Only submit the form if the token is not already saved or if it's different
            if (pushSubscriptionId && pushSubscriptionId !== existingToken) {
                document.getElementById('fcm-token').value = pushSubscriptionId;
                document.getElementById('fcm-token-form').submit();
            }
        });
    </script>

    <!-- Hidden Form to Submit Token -->
    <form id="fcm-token-form" action="{{ route('save.fcm.token') }}" method="POST">
        @csrf
        <input type="hidden" id="fcm-token" name="fcm_token" value="">
    </form>

    <!-- Service Worker for OneSignal Push Notifications -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/public/push/OneSignal/OneSignalSDKWorker.js', {
                    scope: '/public/push/OneSignal/'
                })
                .then(function(registration) {
                    console.log('OneSignal Service Worker registered with scope:', registration.scope);
                })
                .catch(function(error) {
                    console.error('Failed to register OneSignal Service Worker:', error);
                });
        } else {
            console.log('Service workers are not supported in this browser.');
        }
    </script>
</body>

</html>