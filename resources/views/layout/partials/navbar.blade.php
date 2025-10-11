<!-- Navbar Start -->
<nav class="sb-topnav navbar navbar-expand-lg shadow-sm" style="background-color: #1f2937;">
    <div class="container-fluid d-flex align-items-center flex-nowrap">

        <!-- Sidebar Toggle (for small screens) -->
        <button class="btn btn-link order-1 order-lg-0 me-2 text-light flex-shrink-0" id="sidebarToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>

        <!-- Navbar Brand -->
        <a class="navbar-brand d-flex align-items-center flex-shrink-0" href="{{ route('dashboard') }}" style="min-width: 0;">
            <img src="/images/UBLOGO.png" alt="Logo" width="40" height="40" class="me-2 flex-shrink-0">
            <span class="fw-bold text-uppercase text-truncate" style="color: #1dd3b0;">ODRMS</span>
        </a>

        <!-- User Dropdown -->
        <ul class="navbar-nav ms-auto flex-shrink-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white d-flex align-items-center" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="fw-bold text-white d-none d-sm-inline me-2">{{ Auth::user()->username }}</span>
                    <i class="fas fa-user fa-fw" style="color: #1dd3b0;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!"><i class="fas fa-cog me-2"></i> Settings</a></li>
                    <li><a class="dropdown-item" href="{{ url('activityLog') }}"><i class="fas fa-list-alt me-2"></i> Activity Log</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item text-danger" href="{{ url('logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
<!-- Navbar End -->

<!-- Add this style block to ensure navbar stays on one line -->
<style>
    .sb-topnav .container-fluid {
        flex-wrap: nowrap !important;
        overflow: visible !important;
    }

    .sb-topnav .navbar-brand {
        overflow: hidden;
    }

    .sb-topnav .navbar-brand span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sb-topnav .navbar-nav {
        position: relative;
        z-index: 1050;
    }

    /* Hide username text on very small screens */
    @media (max-width: 576px) {
        .sb-topnav .navbar-brand span {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 400px) {
        .sb-topnav .navbar-brand span {
            display: none;
        }
    }
</style>
