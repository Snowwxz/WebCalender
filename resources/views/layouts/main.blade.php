<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiKota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Base CSS -->
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <!-- Component CSS -->
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

    @stack('styles')
</head>
<body>
    <div class="app-container">
        {{-- Header (dinamis sesuai role) --}}
        @auth
            @if (Auth::user()->role === 'superadmin')
                @include('layouts.header')
            @elseif (Auth::user()->role === 'admin')
                @include('layouts.header')
            @else
                @include('layouts.header')
            @endif
        @else
            @include('layouts.header')
        @endauth

        <div class="main-wrapper">
            {{-- Sidebar --}}
            @include('layouts.aside')

            {{-- Main Content --}}
            <main class="main-content">
                @yield('content')
            </main>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleBtn = document.querySelector('.sidebar-toggle');

            // Check if mobile view
            if (window.innerWidth <= 480) {
                sidebar.classList.toggle('show');
                // persist mobile/tablet sidebar open state
                localStorage.setItem('sidebarShow', sidebar.classList.contains('show') ? '1' : '0');
                // Add overlay on mobile
                if (sidebar.classList.contains('show')) {
                    createOverlay();
                } else {
                    removeOverlay();
                }
                return;
            }

            // Check if tablet view
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                // persist mobile/tablet sidebar open state
                localStorage.setItem('sidebarShow', sidebar.classList.contains('show') ? '1' : '0');
                // Also add overlay on tablet
                if (sidebar.classList.contains('show')) {
                    createOverlay();
                } else {
                    removeOverlay();
                }
                return;
            }

            // Desktop view - toggle expanded state (overlay content, no push)
            sidebar.classList.toggle('expanded');
            const isExpanded = sidebar.classList.contains('expanded');
            localStorage.setItem('sidebarExpanded', isExpanded ? '1' : '0');
            if (isExpanded) {
                createOverlay();
            } else {
                removeOverlay();
            }
        }

        // Close sidebar on mobile/tablet
        function closeSidebarOnMobile() {
            const sidebar = document.querySelector('.sidebar');
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('show');
                localStorage.setItem('sidebarShow', '0');
                removeOverlay();
            }
        }

        // Create overlay for mobile sidebar
        function createOverlay() {
            if (document.getElementById('sidebar-overlay')) return;

            const overlay = document.createElement('div');
            overlay.id = 'sidebar-overlay';
            overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9998;';
            overlay.onclick = function() {
                const sidebar = document.querySelector('.sidebar');
                if (!sidebar) return;

                // Close mobile/tablet sidebar
                if (sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    localStorage.setItem('sidebarShow', '0');
                }

                // Close desktop expanded sidebar
                if (sidebar.classList.contains('expanded')) {
                    sidebar.classList.remove('expanded');
                    localStorage.setItem('sidebarExpanded', '0');
                }

                removeOverlay();
            };
            document.body.appendChild(overlay);
            // prevent background scrolling when sidebar is open
            document.body.style.overflow = 'hidden';
        }

        // Remove overlay
        function removeOverlay() {
            const overlay = document.getElementById('sidebar-overlay');
            if (overlay) {
                overlay.remove();
            }
            // restore background scroll
            document.body.style.overflow = '';
        }

        // Switch view
        function switchView(view) {
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Global variables for mini calendar
        window.currentDate = new Date(); // Use current date as default
        window.miniCalendarDate = new Date(); // Separate date for mini calendar

        // Initialize mini calendar date from URL parameters
        function initializeMiniCalendarDate() {
            const urlParams = new URLSearchParams(window.location.search);
            const month = urlParams.get('bulan');
            const year = urlParams.get('tahun');
            
            if (month && year) {
                window.currentDate = new Date(year, month - 1, 1);
                window.miniCalendarDate = new Date(year, month - 1, 1);
            } else {
                window.currentDate = new Date();
                window.miniCalendarDate = new Date();
            }
        }

        // Generate mini calendar (global function)
        window.generateMiniCalendar = function() {
            const miniCalendar = document.getElementById('miniCalendarDays');
            const miniHeader = document.getElementById('miniCalendarHeader');

            if (!miniCalendar || !miniHeader) return;

            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            miniHeader.textContent = `${monthNames[window.miniCalendarDate.getMonth()]} ${window.miniCalendarDate.getFullYear()}`;

            const firstDay = new Date(window.miniCalendarDate.getFullYear(), window.miniCalendarDate.getMonth(), 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1)); // Mulai dari hari Senin

            miniCalendar.innerHTML = '';

            for (let i = 0; i < 35; i++) { // 5 minggu x 7 hari = 35 hari
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const dayElement = document.createElement('div');
                dayElement.className = 'mini-day';
                dayElement.textContent = date.getDate();

                if (date.getMonth() !== window.miniCalendarDate.getMonth()) {
                    dayElement.classList.add('other-month');
                }

                if (date.toDateString() === new Date().toDateString()) {
                    dayElement.classList.add('today');
                }

                if (date.getDay() === 0) {
                    dayElement.classList.add('weekend');
                } else if (date.getDay() === 6) {
                    dayElement.classList.add('saturday');
                }

                // Add click functionality to navigate to day view
                dayElement.addEventListener('click', function() {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    
                    // Check if we're in dashboard or landing
                    const isDashboard = window.location.pathname.includes('/dashboard');
                    const baseUrl = isDashboard ? '/dashboard/hari' : '/hari';
                    window.location.href = `${baseUrl}?tanggal=${year}-${month}-${day}`;
                });

                miniCalendar.appendChild(dayElement);
            }
        }

        // Function to update mini calendar when main calendar changes
        window.updateMiniCalendar = function(newDate) {
            window.currentDate = new Date(newDate);
            window.miniCalendarDate = new Date(newDate); // Sync mini calendar with main calendar
            window.generateMiniCalendar();
        }

        // Function to change mini calendar month independently
        window.changeMiniMonth = function(direction) {
            window.miniCalendarDate.setMonth(window.miniCalendarDate.getMonth() + direction);
            window.generateMiniCalendar();
            
            // Don't update main calendar - only mini calendar should change
        }

        // Apply saved sidebar state based on viewport
        function applySavedSidebarState() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const savedExpanded = localStorage.getItem('sidebarExpanded'); // '1' or '0'
            const savedShow = localStorage.getItem('sidebarShow'); // '1' or '0'

            if (window.innerWidth > 1024) {
                // Desktop: use expanded state persistence, overlay content
                sidebar.classList.remove('show');
                if (savedExpanded === '1') {
                    sidebar.classList.add('expanded');
                    createOverlay();
                } else {
                    sidebar.classList.remove('expanded');
                    removeOverlay();
                }
            } else if (window.innerWidth <= 768) {
                // Mobile/Tablet: use show state, default closed if none
                if (savedShow === '1') {
                    sidebar.classList.add('show');
                    createOverlay();
                } else {
                    sidebar.classList.remove('show');
                    removeOverlay();
                }
            } else {
                // Large tablet (769-1024) keep closed unless explicitly saved open
                if (savedShow === '1') {
                    sidebar.classList.add('show');
                    createOverlay();
                } else {
                    sidebar.classList.remove('show');
                    removeOverlay();
                }
            }
        }

        // Ensure sidebar top aligns with actual header height on mobile/tablet
        function updateSidebarTopHeight() {
            const sidebar = document.querySelector('.sidebar');
            const headerEl = document.querySelector('.header');
            if (!sidebar || !headerEl) return;

            const headerHeight = headerEl.offsetHeight || 72;

            if (window.innerWidth <= 1024) {
                sidebar.style.top = headerHeight + 'px';
                sidebar.style.height = `calc(100vh - ${headerHeight}px)`;
            } else {
                // Desktop sticks to design defaults
                sidebar.style.top = '72px';
                sidebar.style.height = 'calc(100vh - 72px)';
            }
        }

        // Initialize mini calendar when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent sidebar animation during initial restore
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) sidebar.classList.add('no-animate');

            initializeMiniCalendarDate();
            window.generateMiniCalendar();

            // Initialize responsive behavior
            handleResize();
            // Re-apply saved state after initial sizing
            applySavedSidebarState();
            // Align sidebar with header height
            updateSidebarTopHeight();
            // Ensure overlay visible if sidebar is shown after restore
            if (sidebar.classList.contains('show') && window.innerWidth <= 1024) {
                createOverlay();
            }

            // Re-enable animations after first frame
            requestAnimationFrame(() => {
                if (sidebar) sidebar.classList.remove('no-animate');
            });
        });

        // Handle window resize
        function handleResize() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            if (window.innerWidth <= 480) {
                // Mobile: hide sidebar by default
                sidebar.classList.remove('show');
                sidebar.classList.remove('expanded');
                removeOverlay();
            } else if (window.innerWidth <= 768) {
                // Tablet: hide sidebar by default
                sidebar.classList.remove('expanded');
                sidebar.classList.remove('show');
                removeOverlay();
            } else if (window.innerWidth <= 1024) {
                // Large tablet: hide sidebar by default
                sidebar.classList.remove('show');
                removeOverlay();
            } else {
                // Desktop: default to icon-only unless expanded is saved; overlay when expanded
                sidebar.classList.remove('show');
                const savedExpanded = localStorage.getItem('sidebarExpanded');
                const isExpanded = savedExpanded === '1';
                sidebar.classList.toggle('expanded', isExpanded);
                if (isExpanded) {
                    createOverlay();
                } else {
                    removeOverlay();
                }
                removeOverlay();
            }

            // After base adjustments, re-apply saved state
            applySavedSidebarState();
            updateSidebarTopHeight();
        }

        // Listen for window resize
        window.addEventListener('resize', handleResize);

        // User dropdown functionality
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            const profile = document.querySelector('.user-profile');

            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                profile.classList.remove('active');
            } else {
                dropdown.classList.add('show');
                profile.classList.add('active');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const profileSection = document.querySelector('.user-profile-section');
            const dropdown = document.getElementById('userDropdown');
            const profile = document.querySelector('.user-profile');

            if (profileSection && !profileSection.contains(event.target)) {
                dropdown.classList.remove('show');
                profile.classList.remove('active');
            }
        });

        function getel(id) {
            return document.getElementById(id);
        }
    </script>
</body>
</html>
