<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Walk-in Form with Sync</title>
    <style>
        .sync-panel {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-width: 250px;
            z-index: 1000;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            background: #dc3545;
        }

        .status-indicator.online {
            background: #28a745;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .sync-button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        .sync-button:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        .sync-button:hover:not(:disabled) {
            background: #0056b3;
        }

        .pending-count {
            background: #ffc107;
            color: #000;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-top: 8px;
        }

        .sync-details {
            margin-top: 10px;
            font-size: 13px;
            color: #666;
        }

        .sync-details div {
            margin: 4px 0;
        }
    </style>
</head>
<body>

<!-- Your existing form content here -->
<div class="container">
    <h1>Walk-in Document Request Form</h1>
    <!-- Your form fields -->
</div>

<!-- Sync Panel -->
<div class="sync-panel">
    <div>
        <span class="status-indicator" id="status-indicator"></span>
        <strong id="connection-status">Checking...</strong>
    </div>

    <div class="sync-details" id="sync-details">
        <div>Pending Students: <strong id="pending-students">0</strong></div>
        <div>Pending Accounts: <strong id="pending-accounts">0</strong></div>
        <div>Pending Requests: <strong id="pending-requests">0</strong></div>
    </div>

    <div>
        <span class="pending-count" id="total-pending">0 pending</span>
    </div>

    <button class="sync-button" id="sync-btn" onclick="syncData()" disabled>
        Sync Now
    </button>

    <div style="margin-top: 10px; font-size: 12px; color: #666;">
        Last checked: <span id="last-checked">Never</span>
    </div>
</div>

<script>
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Check connection every 30 seconds
    setInterval(checkConnection, 30000);

    // Initial check
    checkConnection();
    loadSyncStatus();

    /**
     * Check if online database is reachable
     */
    function checkConnection() {
        fetch('/sync/check-connection')
            .then(response => response.json())
            .then(data => {
                const indicator = document.getElementById('status-indicator');
                const statusText = document.getElementById('connection-status');
                const syncBtn = document.getElementById('sync-btn');
                const lastChecked = document.getElementById('last-checked');

                if (data.online) {
                    indicator.classList.add('online');
                    statusText.textContent = 'Online';
                    syncBtn.disabled = false;
                } else {
                    indicator.classList.remove('online');
                    statusText.textContent = 'Offline';
                    syncBtn.disabled = true;
                }

                lastChecked.textContent = new Date().toLocaleTimeString();
                loadSyncStatus();
            })
            .catch(error => {
                console.error('Connection check failed:', error);
                document.getElementById('connection-status').textContent = 'Error';
            });
    }

    /**
     * Load pending sync count
     */
    function loadSyncStatus() {
        fetch('/sync/status')
            .then(response => response.json())
            .then(data => {
                document.getElementById('pending-students').textContent = data.pending_students;
                document.getElementById('pending-accounts').textContent = data.pending_accounts;
                document.getElementById('pending-requests').textContent = data.pending_requests;
                document.getElementById('total-pending').textContent = data.total_pending + ' pending';
            })
            .catch(error => {
                console.error('Status load failed:', error);
            });
    }

    /**
     * Trigger sync
     */
    function syncData() {
        const syncBtn = document.getElementById('sync-btn');
        syncBtn.disabled = true;
        syncBtn.textContent = 'Syncing...';

        fetch('/sync/trigger', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            syncBtn.textContent = 'Sync Now';

            if (data.data.success) {
                alert(`✅ Sync Completed!\n\nStudents synced: ${data.data.students_synced}\nAccounts synced: ${data.data.accounts_synced}\nRequests synced: ${data.data.requests_synced}\nFailed: ${data.data.failed}`);
            } else {
                alert(`⚠️ Sync completed with errors:\n\n${data.data.errors.join('\n')}`);
            }

            loadSyncStatus();
            checkConnection();
        })
        .catch(error => {
            syncBtn.textContent = 'Sync Now';
            syncBtn.disabled = false;
            alert('❌ Sync failed: ' + error.message);
            console.error('Sync failed:', error);
        });
    }

    // Show notification when form is submitted offline
    @if(session('Success'))
        alert('{{ session('Success') }}');
    @endif

    @if(session('Error'))
        alert('{{ session('Error') }}');
    @endif
</script>

</body>
</html>
