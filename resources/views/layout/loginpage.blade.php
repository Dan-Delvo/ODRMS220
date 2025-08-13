<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <link rel="icon" type="image/jpg" href="\images\APPLOGO.jpg">
    <!-- PWA -->
    <meta name="theme-color" content="#6777ef">
    <link rel="apple-touch-icon" href="{{ asset('/images/UBLOGO.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/sass/app.scss', 'resources/js/scripts.js', 'resources/js/datatables-simple-demo.js'])

    <style>
        .hide {
            opacity: 0;
            transition: opacity 0.5s ease-out;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        @media (max-width: 991.98px) {
            .col-lg-3 {
                max-width: 100% !important;
                box-shadow: none !important;
                padding: 2rem 1.5rem;
            }

            .btn.btn-warning {
                width: 100%;
            }

            h2.font-weight-bold {
                font-size: 1.75rem;
            }
        }

        @media (max-width: 576px) {
            .col-lg-3 {
                padding: 1.5rem 1rem;
            }

            .form-floating label {
                font-size: 0.85rem;
            }

            .custom-teal-link {
                font-size: 0.85rem;
            }
        }

        .text-warning {
            color: #1dd3b0 !important;
        }

        .btn-warning {
            background-color: #1dd3b0 !important;
            border-color: #1dd3b0 !important;
        }

        .btn-warning:hover,
        .btn-warning:focus {
            background-color: #14b59c !important;
            border-color: #14b59c !important;
        }

        .custom-teal-link {
            color: #1dd3b0;
        }

        .custom-teal-link:hover {
            color: #14b1a2;
            text-decoration: underline;
        }

        #installButton:hover {
            background-color: #1dd3b0 !important;
            color: #1f2937 !important;
            border-color: #1dd3b0 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgb(29 211 176 / 0.4);
        }

        #installButton:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(29, 211, 176, 0.25);
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

        .btn-outline-custom {
            border: 1px solid #1dd3b0;
            color: #1dd3b0;
            background-color: transparent;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-outline-custom:hover,
        .btn-outline-custom:focus {
            background-color: #1dd3b0;
            color: white;
        }
    </style>


</head>

<body >

    <div id="layoutAuthentication" class="w-100 h-100">
        <div id="layoutAuthentication_content" class="w-100 h-100">
            <main>
                @yield('content')
            </main>
        </div>
        {{-- Optional footer if needed --}}
        {{-- @include('layout.partials.normalfooter') --}}
    </div>


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