<!-- Sidebar Navigation Start -->
<nav class="sb-sidenav accordion sb-sidenav-dark  shadow" style="background-color: #1f2937;" id="sidenavAccordion">
    <div class="sb-sidenav-menu" style="max-height: 85vh; overflow-y: auto;">
        <div class="nav">

            @php
            $roleId = Auth::user()->role_id;
            $PermissionDashboard = App\Models\PermissionRoleModel::getPermission('dashboard', $roleId);
            $PermissionPending = App\Models\PermissionRoleModel::getPermission('pending', $roleId);
            $PermissionOngoing = App\Models\PermissionRoleModel::getPermission('ongoing', $roleId);
            $PermissionCompleted = App\Models\PermissionRoleModel::getPermission('completed', $roleId);
            $PermissionRole = App\Models\PermissionRoleModel::getPermission('role', $roleId);
            $PermissionAcc = App\Models\PermissionRoleModel::getPermission('user', $roleId);
            $PermissionStud = App\Models\PermissionRoleModel::getPermission('student', $roleId);
            $PermissionDoc = App\Models\PermissionRoleModel::getPermission('doc', $roleId);
            $PermissionWalk = App\Models\PermissionRoleModel::getPermission('walkinRequest', $roleId);
            $PermissionGen= App\Models\PermissionRoleModel::getPermission('generateReports', $roleId);
            $PermissionAnalytics = App\Models\PermissionRoleModel::getPermission('analytics', $roleId);
            $PermissionClaimed = App\Models\PermissionRoleModel::getPermission('claimed', $roleId);
            $PermissionAudit = App\Models\PermissionRoleModel::getPermission('auditTrail', $roleId);
            $PermissionDeclined = App\Models\PermissionRoleModel::getPermission('declined', $roleId);
            $PermissionBulkRequest = App\Models\PermissionRoleModel::getPermission('bulkRequest', $roleId);
            $PermissionAddBulkRequest = App\Models\PermissionRoleModel::getPermission('addBulkRequest', $roleId);
            @endphp

            {{-- Admin Group --}}
            @if(!empty($PermissionDashboard) || !empty($PermissionGen) || !empty($PermissionAnalytics)) {{-- true = always show Analytics --}}
            <div class="sb-sidenav-menu-heading text-uppercase text-light fw-bold mt-3">Admin</div>

            @if(!empty($PermissionDashboard))
            <a class="nav-link text-light sidebar-item" href="{{ route('dashboard') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                Dashboard
            </a>
            @endif

            @if(!empty($PermissionGen))
            <a class="nav-link text-light sidebar-item" href="{{ route('generate') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                Generate Report
            </a>
            @endif

            @if(!empty($PermissionAnalytics))
            <a class="nav-link text-light sidebar-item" href="{{ route('analytics') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                Analytics
            </a>
            @endif
            @endif

            {{-- Maintenance Group --}}
            @if(!empty($PermissionRole) || !empty($PermissionAcc) || !empty($PermissionStud) || !empty($PermissionDoc || !empty($PermissionAudit)))
            {{-- true = always show Audit Trail --}}
            <div class="sb-sidenav-menu-heading text-uppercase text-light fw-bold mt-3">Maintenance</div>

            @if(!empty($PermissionRole))
            <a class="nav-link text-light sidebar-item" href="{{ route('role') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
                Role Management
            </a>
            @endif

            @if(!empty($PermissionAcc))
            <a class="nav-link text-light sidebar-item" href="{{ route('user') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                Account Management
            </a>
            @endif

            @if(!empty($PermissionStud))
            <a class="nav-link text-light sidebar-item" href="{{ route('student') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-user-graduate"></i></div>
                Student Management
            </a>
            @endif

            @if(!empty($PermissionDoc))
            <a class="nav-link text-light sidebar-item" href="{{ route('doc') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                Document Management
            </a>
            @endif

            @if(!empty($PermissionAudit))
            <a class="nav-link text-light sidebar-item" href="{{ route('audit.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                Audit Trail
            </a>
            @endif

            @endif

            {{-- Requests Group --}}
            @if(!empty($PermissionPending) || !empty($PermissionOngoing) || !empty($PermissionCompleted) || !empty($PermissionClaimed) || !empty($PermissionDeclined) || !empty($PermissionWalk))
            <div class="sb-sidenav-menu-heading text-uppercase text-light fw-bold mt-3">Requests</div>

            @if(!empty($PermissionPending))
            <a class="nav-link text-light sidebar-item" href="{{ route('pending.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-clock"></i></div>
                Pending Requests
            </a>
            @endif

            @if(!empty($PermissionOngoing))
            <a class="nav-link text-light sidebar-item" href="{{ route('ongoing.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-spinner"></i></div>
                Processing Requests
            </a>
            @endif

            @if(!empty($PermissionCompleted))
            <a class="nav-link text-light sidebar-item" href="{{ route('tables.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                For Release Requests
            </a>
            @endif

            @if(!empty($PermissionClaimed))
            <a class="nav-link text-light sidebar-item" href="{{ route('claimed-documents.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                Claimed Requests
            </a>
            @endif

            @if(!empty($PermissionDeclined))
            <a class="nav-link text-light sidebar-item" href="{{ route('declined-documents.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                Declined Requests
            </a>
            @endif

            @if(!empty($PermissionWalk))
            <a class="nav-link text-light sidebar-item" href="{{ route('walkin.form') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-walking"></i></div>
                Walk In Requests
            </a>
            @endif
            @endif

            @if(!empty($PermissionBulkRequest) || !empty($PermissionAddBulkRequest))

            @if(!empty($PermissionBulkRequest))
            <div class="sb-sidenav-menu-heading text-uppercase text-light fw-bold mt-3">Bulk Requests</div>
            <a class="nav-link text-light sidebar-item" href="{{ route('bulk_request.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                Bulk Requests
            </a>
            @endif

            @if(!empty($PermissionAddBulkRequest))
            <a class="nav-link text-light sidebar-item" href="{{ route('bulk_request_add.show') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                Add Bulk Requests
            </a>
            @endif

            @endif


        </div>
    </div>

    <!-- Sidebar Footer -->
    <div class="sb-sidenav-footer bg-secondary text-light text-center py-3 ">
        <div class="small">Logged in as:</div>
        <span class="fw-bold">{{ Auth::user()->username }}</span>
    </div>
</nav>
<!-- Sidebar Navigation End -->

<!-- Custom Sidebar Styles -->
<style>
    /* Sidebar Scrollbar */
    .sb-sidenav-menu {
        scrollbar-width: thin;
        scrollbar-color: #6c757d #343a40;
    }

    /* WebKit Scrollbar (Chrome, Edge, Safari) */
    .sb-sidenav-menu::-webkit-scrollbar {
        width: 8px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-thumb {
        background-color: #6c757d;
        border-radius: 5px;
    }

    .sb-sidenav-menu::-webkit-scrollbar-track {
        background: #343a40;
    }

    /* Sidebar Hover Effect */
    .sidebar-item {
        transition: all 0.3s ease-in-out;
        padding: 10px 15px;
        border-radius: 5px;
    }

    .sidebar-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
        padding-left: 18px;
        color: #1dd3b0 !important;
    }

    /* Active Link Highlight */
    .sidebar-item.active,
    .sidebar-item:active {
        background-color: #007bff;
        color: #ffc107 !important;
    }
</style>
