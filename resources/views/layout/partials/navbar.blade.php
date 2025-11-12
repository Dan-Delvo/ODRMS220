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
        <div class="ms-auto flex-shrink-0 user-dropdown-wrapper">
            <a class="nav-link dropdown-toggle text-white d-flex align-items-center" id="navbarDropdown" href="#" role="button" aria-expanded="false" style="cursor: pointer;">
                <span class="fw-bold text-white d-none d-sm-inline me-2">{{ Auth::user()->username }}</span>
                <i class="fas fa-user fa-fw" style="color: #1dd3b0;"></i>
            </a>
            <div class="dropdown-menu-custom" id="userDropdownMenu">
                <a class="dropdown-item-custom text-success" href="{{ route('st.page') }}"><i class="fa-solid fa-repeat"></i> Switch To Student Side</a>
                <a class="dropdown-item-custom text-danger" href="{{ url('logout') }}"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>
<!-- Navbar End -->

<!-- Styles -->
<style>
    .sb-topnav {
        position: relative;
        z-index: 1030;
    }

    .sb-topnav .container-fluid {
        flex-wrap: nowrap !important;
        min-height: 56px;
        height: 56px;
        max-height: 56px;
    }

    .sb-topnav .navbar-brand {
        overflow: hidden;
    }

    .sb-topnav .navbar-brand span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* User dropdown wrapper */
    .user-dropdown-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        height: 56px;
    }

    .user-dropdown-wrapper .nav-link {
        padding: 0.5rem 0.75rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        height: 100%;
    }

    /* Custom dropdown menu */
    .dropdown-menu-custom {
        display: none;
        position: fixed;
        background-color: #fff;
        min-width: 180px;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0,0,0,.15);
        padding: 0.5rem 0;
        z-index: 2000;
    }

    .dropdown-menu-custom.show {
        display: block;
    }

    .dropdown-item-custom {
        display: block;
        width: 100%;
        padding: 0.5rem 1rem;
        color: #212529;
        text-decoration: none;
        background-color: transparent;
        border: 0;
        transition: background-color 0.15s ease-in-out;
        white-space: nowrap;
    }

    .dropdown-item-custom:hover {
        background-color: #e9ecef;
        color: #1e2125;
    }

    .dropdown-item-custom.text-danger:hover {
        color: #dc3545 !important;
        background-color: #f8d7da;
    }

    .dropdown-divider-custom {
        height: 0;
        margin: 0.5rem 0;
        overflow: hidden;
        border-top: 1px solid #dee2e6;
    }

    /* Hide username text on very small screens */
    @media (max-width: 576px) {
        .sb-topnav .navbar-brand span {
            font-size: 0.9rem;
        }

        .dropdown-menu-custom {
            min-width: 160px;
        }

        .user-dropdown-wrapper .nav-link {
            padding: 0.5rem;
        }
    }

    @media (max-width: 400px) {
        .sb-topnav .navbar-brand span {
            display: none;
        }

        .dropdown-menu-custom {
            min-width: 150px;
            font-size: 0.9rem;
        }

        .dropdown-item-custom {
            padding: 0.4rem 0.8rem;
        }
    }
</style>

<!-- Manual Dropdown Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggle = document.getElementById('navbarDropdown');
        const dropdownMenu = document.getElementById('userDropdownMenu');

        if (!dropdownToggle || !dropdownMenu) return;

        // Function to position dropdown
        function positionDropdown() {
            const toggleRect = dropdownToggle.getBoundingClientRect();

            // Position dropdown below the toggle button, aligned to the right
            dropdownMenu.style.top = (toggleRect.bottom + 2) + 'px';
            dropdownMenu.style.left = 'auto';
            dropdownMenu.style.right = (window.innerWidth - toggleRect.right) + 'px';

            // Make sure dropdown doesn't go off-screen on the left
            const menuWidth = dropdownMenu.offsetWidth || 180;
            if (toggleRect.right - menuWidth < 0) {
                dropdownMenu.style.left = '10px';
                dropdownMenu.style.right = 'auto';
            }
        }

        // Toggle dropdown on click
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const isShown = dropdownMenu.classList.contains('show');

            if (!isShown) {
                // Show dropdown
                positionDropdown();
                dropdownMenu.classList.add('show');
                dropdownToggle.setAttribute('aria-expanded', 'true');
            } else {
                // Hide dropdown
                dropdownMenu.classList.remove('show');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Reposition on scroll
        window.addEventListener('scroll', function() {
            if (dropdownMenu.classList.contains('show')) {
                positionDropdown();
            }
        });

        // Close and reposition on resize
        window.addEventListener('resize', function() {
            if (dropdownMenu.classList.contains('show')) {
                positionDropdown();
            }
        });

        // Close dropdown when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
                dropdownToggle.setAttribute('aria-expanded', 'false');
                dropdownToggle.focus();
            }
        });
    });
</script>
