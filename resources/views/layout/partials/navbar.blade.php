<!-- Navbar Start -->
<nav class="sb-topnav navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <!-- Navbar Brand -->
        <a class="navbar-brand d-flex align-items-center ps-3" href="#">
            <img src="/images/UBLOGO.png" alt="Logo" width="40" height="40" class="me-2">
            <span class="fw-bold text-uppercase">ODRMS</span>
        </a>

        <!-- Sidebar Toggle (for small screens) -->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-3 me-lg-0 text-light" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Navbar Search (Optional) -->
        <!-- Uncomment if search is needed
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search..." aria-label="Search" />
                <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        -->

        <!-- User Dropdown -->
        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-light" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!"><i class="fas fa-cog me-2"></i> Settings</a></li>
                    <li><a class="dropdown-item" href="#!"><i class="fas fa-list-alt me-2"></i> Activity Log</a></li>
                    <li><hr class="dropdown-divider" /></li>
                    <li><a class="dropdown-item text-danger" href="{{ url('logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
<!-- Navbar End -->
