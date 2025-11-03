   @php
       $isDashboard = (Auth::check() && request()->is('dashboard*')) || Auth::check();
   @endphp

  <aside class="sidebar">

      <!-- Navigation Icons -->
      <div class="sidebar-nav">
          @if ($isDashboard)
              <a href="/dashboard/bulan" class="nav-icon {{ request()->is('dashboard/bulan') || request()->is('dashboard/hari') || request()->is('dashboard/tahun') ? 'active' : '' }}" title="Kalender">
                  <i class="fas fa-calendar"></i>
                  <span class="nav-label">Kalender</span>
              </a>
              <a href="{{ route('agenda.create') }}" class="nav-icon {{ request()->routeIs('agenda.create') ? 'active' : '' }}" title="Tambah Agenda">
                  <i class="fas fa-plus"></i>
                  <span class="nav-label">Tambah Agenda</span>
              </a>
              <a href="{{ Auth::user()->role === 'admin' ? route('approve') : route('agenda.notification') }}" class="nav-icon {{ request()->routeIs('approve') || request()->routeIs('agenda.notification') ? 'active' : '' }}" title="Notifikasi">
                  <i class="fas fa-bell"></i>
                  <span class="nav-label">Notifikasi</span>
              </a>
          @else
              <a href="/bulan" class="nav-icon {{ request()->is('/') || request()->is('bulan') || request()->is('hari') || request()->is('tahun') ? 'active' : '' }}" title="Kalender">
                  <i class="fas fa-calendar"></i>
                  <span class="nav-label">Kalender</span>
              </a>
              @auth
                  <a href="{{ route('agenda.create') }}" class="nav-icon {{ request()->routeIs('agenda.create') ? 'active' : '' }}" title="Tambah Agenda">
                      <i class="fas fa-plus"></i>
                      <span class="nav-label">Tambah Agenda</span>
                  </a>
                  <a href="{{ route('agenda.notification') }}" class="nav-icon {{ request()->routeIs('agenda.notification') ? 'active' : '' }}" title="Notifikasi">
                      <i class="fas fa-bell"></i>
                      <span class="nav-label">Notifikasi</span>
                  </a>
              @else
                  <a href="/login" class="nav-icon" title="Login">
                      <i class="fas fa-sign-in-alt"></i>
                      <span class="nav-label">Login</span>
                  </a>
              @endauth
          @endif
      </div>
  </aside>
