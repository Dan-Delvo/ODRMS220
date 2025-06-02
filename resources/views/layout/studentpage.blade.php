<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
            navigator.serviceWorker.register('/public/push/OneSignal/OneSignalSDKWorker.js', { scope: '/public/push/OneSignal/' })
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
