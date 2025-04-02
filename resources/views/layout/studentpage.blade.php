<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- PWA -->
    <meta name="theme-color" content="#6777ef">
    <link rel="apple-touch-icon" href="{{ asset('logo.PNG') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/sass/app.scss', 'resources/js/scripts.js', 'resources/js/datatables-simple-demo.js'])
</head>

<body>
    @include('layout.partials.navstud')

    <div id="layoutStudent">
        <div id="layoutStudent_content">
            <main>
                @yield('content')
            </main>
        </div>

        <!-- Added margin-bottom to create space -->
        <div class="mb-5"></div>

        @include('layout.partials.normalfooter')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- PWA & OneSignal merged service worker -->
    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js') // Register the merged service worker
                .then(function(registration) {
                    console.log('Service Worker registered with scope:', registration.scope);
                })
                .catch(function(error) {
                    console.error('Service Worker registration failed:', error);
                });
        }
    </script>
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(async function(OneSignal) {
            await OneSignal.init({
                appId: "4177a306-5791-4b2c-ac5a-ae6b4bb937bf",
            });

            // Get the push subscription ID
            const pushSubscriptionId = await OneSignal.User.PushSubscription.id;

            console.log("Push Subscription ID:", pushSubscriptionId);

            // Check if the token is already saved (to avoid submitting multiple times)
            const existingToken = "{{ auth()->user()->fcm_token ?? '' }}";  // You can use Blade to pass the current token from the server

            // Only submit the form if the token doesn't exist or if it's different
            if (pushSubscriptionId && pushSubscriptionId !== existingToken) {
                // Set the hidden input field's value with the pushSubscriptionId
                document.getElementById('fcm-token').value = pushSubscriptionId;

                // Automatically submit the form
                document.getElementById('fcm-token-form').submit();
            }
        });
    </script>

    <!-- Hidden Form -->
    <form id="fcm-token-form" action="{{ route('save.fcm.token') }}" method="POST">
        @csrf
        <input type="hidden" id="fcm-token" name="fcm_token" value="">
    </form>

</body>

</html>
