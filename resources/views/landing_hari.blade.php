@extends('layouts.main')

@section('content')
<div class="day-view-page">
    <div class="day-view-main">
        <!-- Day Header -->
        <div class="day-header">
            <div class="day-navigation">
                <button class="nav-btn" onclick="changeDay(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="current-day" id="currentDay">Senin, 21 Oktober 2025</h2>
                <button class="nav-btn" onclick="changeDay(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <!-- Time Grid -->
        <div class="day-grid">
            <div class="time-column">
                <div class="time-slot">00:00</div>
                <div class="time-slot">01:00</div>
                <div class="time-slot">02:00</div>
                <div class="time-slot">03:00</div>
                <div class="time-slot">04:00</div>
                <div class="time-slot">05:00</div>
                <div class="time-slot">06:00</div>
                <div class="time-slot">07:00</div>
                <div class="time-slot">08:00</div>
                <div class="time-slot">09:00</div>
                <div class="time-slot">10:00</div>
                <div class="time-slot">11:00</div>
                <div class="time-slot">12:00</div>
                <div class="time-slot">13:00</div>
                <div class="time-slot">14:00</div>
                <div class="time-slot">15:00</div>
                <div class="time-slot">16:00</div>
                <div class="time-slot">17:00</div>
                <div class="time-slot">18:00</div>
                <div class="time-slot">19:00</div>
                <div class="time-slot">20:00</div>
                <div class="time-slot">21:00</div>
                <div class="time-slot">22:00</div>
                <div class="time-slot">23:00</div>
            </div>
            
            <div class="day-column">
                <div class="hour-slot" data-hour="0"></div>
                <div class="hour-slot" data-hour="1"></div>
                <div class="hour-slot" data-hour="2"></div>
                <div class="hour-slot" data-hour="3"></div>
                <div class="hour-slot" data-hour="4"></div>
                <div class="hour-slot" data-hour="5"></div>
                <div class="hour-slot" data-hour="6"></div>
                <div class="hour-slot" data-hour="7"></div>
                <div class="hour-slot" data-hour="8"></div>
                <div class="hour-slot" data-hour="9"></div>
                <div class="hour-slot" data-hour="10"></div>
                <div class="hour-slot" data-hour="11"></div>
                <div class="hour-slot" data-hour="12"></div>
                <div class="hour-slot" data-hour="13"></div>
                <div class="hour-slot" data-hour="14"></div>
                <div class="hour-slot" data-hour="15"></div>
                <div class="hour-slot" data-hour="16"></div>
                <div class="hour-slot" data-hour="17"></div>
                <div class="hour-slot" data-hour="18"></div>
                <div class="hour-slot" data-hour="19"></div>
                <div class="hour-slot" data-hour="20"></div>
                <div class="hour-slot" data-hour="21"></div>
                <div class="hour-slot" data-hour="22"></div>
                <div class="hour-slot" data-hour="23"></div>
            </div>
        </div>
    </div>
</div>

<script>
let currentDate = new Date(2025, 9, 21); // October 21, 2025

// Tidak ada agenda untuk sementara
const dayEvents = {};

// Update day display
function updateDayDisplay() {
    const dayElement = document.getElementById('currentDay');
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    dayElement.textContent = currentDate.toLocaleDateString('id-ID', options);
}

// Change day
function changeDay(direction) {
    currentDate.setDate(currentDate.getDate() + direction);
    updateDayDisplay();
    renderDayEvents();
}

// Render day events
function renderDayEvents() {
    const dayColumn = document.querySelector('.day-column');
    const dateString = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
    
    // Clear existing events
    dayColumn.querySelectorAll('.event-item').forEach(item => item.remove());
    
    // Tidak ada agenda untuk ditampilkan saat ini
}

// Initialize day view
document.addEventListener('DOMContentLoaded', function() {
    updateDayDisplay();
    renderDayEvents();
});
</script>
@endsection
