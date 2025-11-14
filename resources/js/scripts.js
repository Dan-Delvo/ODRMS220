/*!
 * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
 * Copyright 2013-2023 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
 */
//
// Scripts
//

window.addEventListener("DOMContentLoaded", (event) => {
    // Lightweight/fast tasks first: attach event handlers so UI is responsive
    const body = document.body;
    const sidebarToggle = body.querySelector("#sidebarToggle");
    // Feature-detect localStorage once to avoid per-call try/catch overhead
    const supportsLocalStorage = (function () {
        try {
            const testKey = "__ls_test__";
            localStorage.setItem(testKey, "1");
            localStorage.removeItem(testKey);
            return true;
        } catch (e) {
            return false;
        }
    })();
    if (sidebarToggle) {
        // Attach click handler immediately (cheap)
        sidebarToggle.addEventListener("click", (event) => {
            event.preventDefault();
            body.classList.toggle("sb-sidenav-toggled");
            // Save choice (this write is cheap and happens on user action)
            if (supportsLocalStorage) {
                try {
                    localStorage.setItem(
                        "sb|sidebar-toggle",
                        body.classList.contains("sb-sidenav-toggled")
                    );
                } catch (e) {
                    // ignore write errors
                }
            }
        });
    }

    // Defer non-critical, potentially blocking work (reading localStorage, restoring scroll)
    const scheduleIdle =
        window.requestIdleCallback ||
        function (cb) {
            return setTimeout(cb, 50);
        };

    scheduleIdle(() => {
        try {
            // Restore sidebar toggle state (non-critical)
            scheduleIdle(() => {
                try {
                    // Restore sidebar toggle state (non-critical)
                    if (sidebarToggle && supportsLocalStorage) {
                        try {
                            if (
                                localStorage.getItem("sb|sidebar-toggle") ===
                                "true"
                            ) {
                                body.classList.toggle("sb-sidenav-toggled");
                            }
                        } catch (e) {
                            // ignore read errors
                        }
                    }

                    // Persist sidebar scroll position
                    const sidebarMenu =
                        document.querySelector(".sb-sidenav-menu");
                    if (sidebarMenu) {
                        // Restore scroll position on page load
                        if (supportsLocalStorage) {
                            try {
                                const savedScrollPosition =
                                    localStorage.getItem("sb|sidebar-scroll");
                                if (savedScrollPosition !== null) {
                                    sidebarMenu.scrollTop = parseInt(
                                        savedScrollPosition,
                                        10
                                    );
                                }
                            } catch (e) {
                                // ignore read errors
                            }
                        }

                        // Save scroll position when scrolling (debounced). Use passive listener to avoid blocking.
                        let scrollTimeout;
                        const onSidebarScroll = function () {
                            clearTimeout(scrollTimeout);
                            scrollTimeout = setTimeout(function () {
                                if (!supportsLocalStorage) return;
                                try {
                                    localStorage.setItem(
                                        "sb|sidebar-scroll",
                                        sidebarMenu.scrollTop
                                    );
                                } catch (e) {
                                    // ignore write errors
                                }
                            }, 100); // Debounce to avoid excessive writes
                        };
                        // Use passive:true for better scroll performance when supported
                        try {
                            sidebarMenu.addEventListener(
                                "scroll",
                                onSidebarScroll,
                                { passive: true }
                            );
                        } catch (e) {
                            // Fallback for older browsers
                            sidebarMenu.addEventListener(
                                "scroll",
                                onSidebarScroll
                            );
                        }

                        // Also save scroll position before page unload
                        if (supportsLocalStorage) {
                            window.addEventListener(
                                "beforeunload",
                                function () {
                                    try {
                                        localStorage.setItem(
                                            "sb|sidebar-scroll",
                                            sidebarMenu.scrollTop
                                        );
                                    } catch (e) {
                                        // ignore
                                    }
                                }
                            );
                        }
                    }
                } catch (e) {
                    // Defensive: if something throws, don't block page
                    // Keep this minimal to avoid expensive logging
                }
            });
        } catch (e) {
            // Defensive: if something throws, don't block page
        }
    });
});
