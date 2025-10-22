<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiKota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
            document.querySelector('.sidebar').classList.toggle('collapsed');
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

                if (date.getDay() === 0 || date.getDay() === 6) {
                    dayElement.classList.add('weekend');
                }

                miniCalendar.appendChild(dayElement);
            }
        }

        // Initialize mini calendar when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            window.generateMiniCalendar();
        });

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