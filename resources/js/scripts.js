/*!
 * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
 * Copyright 2013-2023 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
 */
//
// Scripts
//

window.addEventListener("DOMContentLoaded", (event) => {
    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector("#sidebarToggle");
    if (sidebarToggle) {
        // Persist sidebar toggle between refreshes
        if (localStorage.getItem("sb|sidebar-toggle") === "true") {
            document.body.classList.toggle("sb-sidenav-toggled");
        }
        sidebarToggle.addEventListener("click", (event) => {
            event.preventDefault();
            document.body.classList.toggle("sb-sidenav-toggled");
            localStorage.setItem(
                "sb|sidebar-toggle",
                document.body.classList.contains("sb-sidenav-toggled")
            );
        });
    }

    // Persist sidebar scroll position
    const sidebarMenu = document.querySelector(".sb-sidenav-menu");
    if (sidebarMenu) {
        // Restore scroll position on page load
        const savedScrollPosition = localStorage.getItem("sb|sidebar-scroll");
        if (savedScrollPosition !== null) {
            sidebarMenu.scrollTop = parseInt(savedScrollPosition, 10);
        }

        // Save scroll position when scrolling
        let scrollTimeout;
        sidebarMenu.addEventListener("scroll", function () {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(function () {
                localStorage.setItem(
                    "sb|sidebar-scroll",
                    sidebarMenu.scrollTop
                );
            }, 100); // Debounce to avoid excessive writes
        });

        // Also save scroll position before page unload
        window.addEventListener("beforeunload", function () {
            localStorage.setItem("sb|sidebar-scroll", sidebarMenu.scrollTop);
        });
    }
});
