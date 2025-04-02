<!-- Sidebar Navigation Start -->
<nav class="sb-sidenav accordion sb-sidenav-dark bg-dark shadow" id="sidenavAccordion">
    <div class="sb-sidenav-menu" style="max-height: 85vh; overflow-y: auto;">
        <div class="nav">

            @php
            $PermissionDashboard = App\Models\PermissionRoleModel::getPermission('dashboard', Auth::user()->role_id);
            $PermissionPending = App\Models\PermissionRoleModel::getPermission('pending', Auth::user()->role_id);
            $PermissionOngoing = App\Models\PermissionRoleModel::getPermission('ongoing', Auth::user()->role_id);
            $PermissionCompleted = App\Models\PermissionRoleModel::getPermission('completed', Auth::user()->role_id);
            $PermissionRole = App\Models\PermissionRoleModel::getPermission('role', Auth::user()->role_id);
            $PermissionAcc = App\Models\PermissionRoleModel::getPermission('user', Auth::user()->role_id);
            $PermissionStud = App\Models\PermissionRoleModel::getPermission('student', Auth::user()->role_id);
            $PermissionDoc= App\Models\PermissionRoleModel::getPermission('doc', Auth::user()->role_id);
            $PermissionWalk= App\Models\PermissionRoleModel::getPermission('walkinRequest', Auth::user()->role_id);
            $PermissionGen= App\Models\PermissionRoleModel::getPermission('generateReports', Auth::user()->role_id);
            @endphp

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
                Ongoing Requests
            </a>
            @endif

            @if(!empty($PermissionCompleted))
            <a class="nav-link text-light sidebar-item" href="{{ route('tables.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-check-circle"></i></div>
                Completed Requests
            </a>
            @endif

            @if(!empty($PermissionWalk))
            <a class="nav-link text-light sidebar-item" href="{{route('walkin.form')}}">
                <div class="sb-nav-link-icon"><i class="fas fa-walking"></i></div>
                Walk In Requests
            </a>
            @endif

        </div>
    </div>

    <!-- Sidebar Footer -->
    <div class="sb-sidenav-footer bg-secondary text-light text-center py-3">
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
    }

    /* Active Link Highlight */
    .sidebar-item.active, .sidebar-item:active {
        background-color: #007bff;
        color: white !important;
    }
</style>
