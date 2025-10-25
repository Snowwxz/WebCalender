   @php
       $isDashboard = (Auth::check() && request()->is('dashboard*')) || Auth::check();
   @endphp


   <aside class="sidebar">
       <div class="nav-tabs">
           @if ($isDashboard)
               <a href="/dashboard/hari" class="nav-tab {{ request()->is('dashboard/hari') ? 'active' : '' }}">Hari</a>
               <a href="/dashboard/bulan" class="nav-tab {{ request()->is('dashboard/bulan') ? 'active' : '' }}">Bulan</a>
               <a href="/dashboard/tahun" class="nav-tab {{ request()->is('dashboard/tahun') ? 'active' : '' }}">Tahun</a>
           @else
               <a href="/hari" class=" nav-tab {{ request()->is('hari') ? 'active' : '' }}">Hari</a>
               <a href="/bulan"
                   class="nav-tab {{ request()->is('/') || request()->is('bulan') ? 'active' : '' }}">Bulan</a>
               <a href="/tahun" class="nav-tab {{ request()->is('tahun') ? 'active' : '' }}">Tahun</a>
           @endif
       </div>

       <!-- Mini Calendar -->
       <div class="mini-calendar-section">
           <div class="section-title">
               <i class="fas fa-calendar"></i>
               <span>Kalender</span>
           </div>

           <div class="mini-calendar">
               <div class="mini-calendar-header" id="miniCalendarHeader">Okt 2025</div>
               <div class="mini-calendar-weekdays">
                   <div>Sen</div>
                   <div>Sel</div>
                   <div>Rab</div>
                   <div>Kam</div>
                   <div>Jum</div>
                   <div>Sab</div>
                   <div class="weekend">Min</div>
               </div>
               <div class="mini-calendar-days" id="miniCalendarDays"></div>
           </div>
       </div>

       <!-- Agenda Section -->
       <div class="agenda-section">
           <div class="section-title">
               <i class="fas fa-list"></i>
               <span>Kategori Agenda</span>
           </div>

           <div class="agenda-categories">
               <label class="category-item public {{ !$isDashboard ? 'disabled' : '' }}">
                   <input type="checkbox" checked {{ !$isDashboard ? 'disabled' : '' }}>
                   <span class="custom-checkbox"></span>
                   <span class="dot"></span>
                   <span class="text">Publik</span>
               </label>

               @auth
                   <label class="category-item private">
                       <input type="checkbox" checked>
                       <span class="custom-checkbox"></span>
                       <span class="dot"></span>
                       <span class="text">Privasi</span>
                   </label>
               @endauth
           </div>
       </div>
   </aside>
