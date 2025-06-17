<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
        <!-- Google Fonts link -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
    }
    body {
        min-height: 100vh;
        background: #0f172a;
    }

    .sidebar{
        width: 270px;
        position: fixed;
        margin: 16px;
        border-radius: 16px;
        background: #151a2d;
        height: calc(100vh - 32px);
        transition: 0.4s ease;
    }

    .sidebar.collapsed{
        width: 85px;
    }


    .sidebar-nav .nav-list{
        list-style: none;
        display:flex;
        gap: 4px;
        padding: 0 15px;
        flex-direction: column;
        transform: translateY(15px);
        transition: 0.4s ease;
    }

    .sidebar-nav .nav-link {
        color: #fff;
        display: flex;
        gap: 12px;
        white-space: nowrap;
        border-radius: 8px;
        align-items: center;
        padding: 12px 15px;
        text-decoration: none;
        transition: 0.4s ease;
    }

    .sidebar-nav .nav-link:hover{
        color: #1dd3b0;
        background: #fff;
    }

    .sidebar-nav .nav-link .nav-label {
        transition: opacity 0.4s ease;
    }

    .sidebar.collapsed .sidebar-nav .nav-link .nav-label {
        opacity: 0;
        pointer-events: none;
    }

    .sidebar-nav .nav-item {
        position: relative;
    }

    .sidebar-nav .nav-tooltip{
        position: absolute;
        color: #1dd3b0;
        top: -10px;
        opacity: 0;
        display: none;
        pointer-events: none;
        left: calc(100% + 25px);
        padding: 6px 12px;
        border-radius: 8px;
        background: #fff;
        white-space: nowrap;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        transition: 0s;

    }

    .sidebar.collapsed .sidebar-nav .nav-tooltip {
        display: block;
    }

    .sidebar-nav .nav-item:hover .nav-tooltip{
        opacity: 1;
        pointer-events: auto;
        transform: translateY(50%);
        transition: 0.4s ease;
    }

    .sidebar-nav .secondary-nav {
        position: absolute;
        bottom: 30px;
        width: 100%;
    }

    .sidebar-header{
        position: relative;
        display:flex;
        padding: 25px 20px;
        align-items:center;
        justify-content: space-between;
    }

    .sidebar-header .toggler{
        height: 35px;
        width: 35px;
        border: none;
        color: #151a2d;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border-radius: 8px;
        background: #fff;
        transition: 0.4s ease;
    }

    .sidebar-header .sidebar-toggler{
        position: absolute;
        right: 20px;
    }

    .sidebar.collapsed .sidebar-header .sidebar-toggler {
        transform: translate(-4px, 65px);
    }

    .sidebar.collapsed .sidebar-nav .primary-nav {
        transform: translateY(65px);
    }

    .sidebar.collapsed .sidebar-header .sidebar-toggler span{
        transform: rotate(180deg);
    }

    .sidebar-header .toggler:hover{
        background: #1dd3b0;
    }

    .sidebar-header .menu-toggler{
        display: none;
    }

    .sidebar-header .sidebar-toggler span{
        font-size: 1.75rem;
        transition: 0.4s ease;
    }

    .sidebar-header .header-logo img {
        width: 46px;
        height: 46px;
        display: block;
        object-fit: contain;
        border-radius: 50%
    }

    @media (max-width: 1024px){
    .sidebar{
            height: 56px;
            overflow-y: hidden;
            margin: 13px;
            scrollable-width: none;
            width: calc(100% - 28px);
            max-height: calc(100vh - 28px)
    }

    .sidebar.menu-active{
        overflow-y: auto;
    }


    .sidebar-header {
        position: sticky;
        top: 0;
        z-index: 20;
        background: #151A2D;
        padding: 8px 10px;
        border-radius: 16px;
    }

    .sidebar-header .header-logo img{
        width: 40px;
        height: 40px;
    }

    .sidebar-header .sidebar-toggler, .sidebar-nav .nav-tooltip{
        display: none;
    }


    .sidebar-header .menu-toggler{
        display: flex;
        height: 30px;
        width: 30px;
    }

    .sidebar-header .menu-toggler span{
        font-size: 1.3rem;
    }

    .sidebar-nav .nav-list {
        padding: 0 10px;
    }

    .sidebar-nav .nav-link {
        gap: 10px;
        padding: 10px;
        font-size: 0.94rem;
    }

    .sidebar-nav .nav-link .nav-icon {
        font-size: 1.37rem;
    }

    .sidebar-nav .secondary-nav {
        position: relative;
        bottom: 0;
        margin: 40px 0 30px;
    }
}
</style>
<body>
    <aside class = "sidebar">
        <!-- side bar header -->
        <header class = "sidebar-header">
            <a href= "#" class= "header-logo">
                <img src="{{ asset('images/UBLOGO.png') }}" alt="ubnhsLogo">
            </a>
    <button id="sidebarToggle" class="toggler sidebar-toggler">
        <span class="material-symbols-rounded">chevron_left</span>
    </button>
            <button class = "toggler menu-toggler">
                <span class = "material-symbols-rounded">menu</span>

            </button>
        </header>
        <nav class = "sidebar-nav">
            <!-- Primary top nav -->
            <ul class="nav-list primary-nav">
                <li class="nav-item">
                    <a  href="{{ route('st.page') }}"  class="nav-link">
                        <span class="nav-icon material-symbols-rounded">dashboard</span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                    <span class="nav-tooltip">Dashboard</span>
                </li>

                <li class="nav-item">
                    <a href="{{ route('studentrequest.create') }}" class="nav-link">
                        <span class="nav-icon material-symbols-rounded">request_quote</span>
                        <span class="nav-label">Request Document</span>
                    </a>
                    <span class="nav-tooltip">Request Document</span>
                </li>

                <li class="nav-item">
                    <a href="{{ route('studentrequest.view') }}" class="nav-link">
                        <span class="nav-icon material-symbols-rounded">visibility</span>
                        <span class="nav-label">View Request</span>
                    </a>
                    <span class="nav-tooltip">View Request</span>
                </li>
            </ul>

            <ul class="nav-list secondary-nav">
                <li class="nav-item">
                    <a href="{{ url('logout') }}" class="nav-link">
                        <span class="nav-icon material-symbols-rounded">logout</span>
                        <span class="nav-label">Logout</span>
                    </a>
                    <span class="nav-tooltip">Logout</span>
                </li>
            </ul>


            </nav>

    </aside>

    <script>
        const sidebar = document.querySelector(".sidebar");
        const sidebarToggler = document.querySelector(".sidebar-toggler");
        const menuToggler = document.querySelector(".menu-toggler");

        const collapsedSidebarHeight = "56px";
        const fullSidebarHeight = "calc(100vh - 32px)";
        sidebarToggler.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
        });

        const toggleMenu = (isMenuActive) => {
        sidebar.style.height = isMenuActive ? `${sidebar.scrollHeight}px` : collapsedSidebarHeight;
        menuToggler.querySelector("span").innerText = isMenuActive ? "close" : "menu";

        }

        menuToggler.addEventListener("click", () => {
            toggleMenu(sidebar.classList.toggle("menu-active"));
        });

        window.addEventListener("resize", () => {
            if (window.innerWidth >= 1024) {
            sidebar.style.height = fullSidebarHeight;
        } else {
            sidebar.classList.remove("collapsed");
            sidebar.style.height = "auto";
            toggleMenu(sidebar.classList.contains("menu-active"));
        }
    });



    </script>
</body>
</html>
