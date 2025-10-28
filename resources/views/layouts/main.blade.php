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
        {{-- Header --}}
        @include('layouts.header')

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
                // Also add overlay on tablet
                if (sidebar.classList.contains('show')) {
                    createOverlay();
                } else {
                    removeOverlay();
                }
                return;
            }
            
            // Desktop view - toggle collapsed state
            sidebar.classList.toggle('collapsed');
            
            // Adjust main content margin based on sidebar state
            if (sidebar.classList.contains('collapsed')) {
                mainContent.style.marginLeft = '70px';
            } else {
                mainContent.style.marginLeft = '280px';
            }
        }

        // Close sidebar on mobile/tablet
        function closeSidebarOnMobile() {
            const sidebar = document.querySelector('.sidebar');
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('show');
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
                document.querySelector('.sidebar').classList.remove('show');
                removeOverlay();
            };
            document.body.appendChild(overlay);
        }

        // Remove overlay
        function removeOverlay() {
            const overlay = document.getElementById('sidebar-overlay');
            if (overlay) {
                overlay.remove();
            }
        }

        // Switch view
        function switchView(view) {
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Global variables for mini calendar
        window.currentDate = new Date(2025, 9, 21); // October 21, 2025

        // Generate mini calendar (global function)
        window.generateMiniCalendar = function() {
            const miniCalendar = document.getElementById('miniCalendarDays');
            const miniHeader = document.getElementById('miniCalendarHeader');

            if (!miniCalendar || !miniHeader) return;

            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            miniHeader.textContent = `${monthNames[window.currentDate.getMonth()]} ${window.currentDate.getFullYear()}`;

            const firstDay = new Date(window.currentDate.getFullYear(), window.currentDate.getMonth(), 1);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - (firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1)); // Mulai dari hari Senin

            miniCalendar.innerHTML = '';

            for (let i = 0; i < 35; i++) { // 5 minggu x 7 hari = 35 hari
                const date = new Date(startDate);
                date.setDate(startDate.getDate() + i);

                const dayElement = document.createElement('div');
                dayElement.className = 'mini-day';
                dayElement.textContent = date.getDate();

                if (date.getMonth() !== window.currentDate.getMonth()) {
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

                miniCalendar.appendChild(dayElement);
            }
        }

        // Initialize mini calendar when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.generateMiniCalendar();
            
            // Initialize responsive behavior
            handleResize();
        });
        
        // Handle window resize
        function handleResize() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (window.innerWidth <= 480) {
                // Mobile: hide sidebar by default
                sidebar.classList.remove('show');
                sidebar.classList.remove('collapsed');
                mainContent.style.marginLeft = '0';
                removeOverlay();
            } else if (window.innerWidth <= 768) {
                // Tablet: hide sidebar by default
                sidebar.classList.remove('collapsed');
                sidebar.classList.remove('show');
                mainContent.style.marginLeft = '0';
                removeOverlay();
            } else if (window.innerWidth <= 1024) {
                // Large tablet: hide sidebar by default
                sidebar.classList.remove('show');
                mainContent.style.marginLeft = '0';
                removeOverlay();
            } else {
                // Desktop: check if collapsed or not
                const isCollapsed = sidebar.classList.contains('collapsed');
                sidebar.classList.remove('show');
                if (isCollapsed) {
                    mainContent.style.marginLeft = '70px';
                } else {
                    mainContent.style.marginLeft = '280px';
                }
                removeOverlay();
            }
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