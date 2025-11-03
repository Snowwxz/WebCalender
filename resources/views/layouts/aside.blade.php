@php
       $isDashboard = (Auth::check() && request()->is('dashboard*')) || Auth::check();
   @endphp

  <aside class="sidebar">

      <!-- Navigation Icons -->
      <div class="sidebar-nav">
          @php
              $adminBadge = 0;
              $userBadge = 0;
              if (Auth::check()) {
                  if (Auth::user()->role === 'admin') {
                      $lastSeen = session('approve_seen_at');
                      $adminBadge = \App\Models\Agenda::where('status', 'pending')
                          ->when($lastSeen, function ($q) use ($lastSeen) {
                              $q->where('created_at', '>', $lastSeen);
                          })
                          ->count();
                  } elseif (Auth::user()->role !== 'superadmin') {
                      $lastSeenU = session('user_notifications_seen_at');
                      $userId = Auth::user()->id_user;
                      $userBadge = \App\Models\Agenda::where('id_user', $userId)
                          ->whereIn('status', ['approved', 'rejected'])
                          ->when($lastSeenU, function ($q) use ($lastSeenU) {
                              $q->where('updated_at', '>', $lastSeenU);
                          })
                          ->count();
                  }
              }
          @endphp
          @if ($isDashboard)
              <a href="/dashboard/bulan" class="nav-icon {{ request()->is('dashboard/bulan') || request()->is('dashboard/hari') || request()->is('dashboard/tahun') ? 'active' : '' }}" title="Kalender">
                  <i class="fas fa-calendar"></i>
                  <span class="nav-label">Kalender</span>
              </a>
              @if (Auth::check() && Auth::user()->role === 'superadmin')
                  <a href="{{ route('superadmin.dashboard') }}" class="nav-icon {{ request()->is('superadmin') ? 'active' : '' }}" title="Super Admin">
                      <i class="fas fa-users"></i>
                      <span class="nav-label">Super Admin</span>
                  </a>
              @endif
              @if (Auth::user()->role !== 'superadmin')
                  <a href="{{ route('agenda.create') }}" class="nav-icon {{ request()->routeIs('agenda.create') ? 'active' : '' }}" title="Tambah Agenda">
                      <i class="fas fa-plus"></i>
                      <span class="nav-label">Tambah Agenda</span>
                  </a>
                  <a href="{{ Auth::user()->role === 'admin' ? route('approve') : route('agenda.notification') }}" class="nav-icon {{ request()->routeIs('approve') || request()->routeIs('agenda.notification') ? 'active' : '' }}" title="Notifikasi" style="position: relative;">
                      <i class="fas fa-bell"></i>
                      @if (Auth::user()->role === 'admin' && $adminBadge > 0)
                          <span class="notif-badge" style="position:absolute; top:6px; right:6px; min-width:18px; height:18px; padding:0 5px; border-radius:9px; background:#e63946; color:#fff; font-size:11px; line-height:18px; text-align:center; font-weight:600;">{{ $adminBadge }}</span>
                      @elseif (Auth::user()->role !== 'admin' && Auth::user()->role !== 'superadmin' && $userBadge > 0)
                          <span class="notif-badge" style="position:absolute; top:6px; right:6px; min-width:18px; height:18px; padding:0 5px; border-radius:9px; background:#e63946; color:#fff; font-size:11px; line-height:18px; text-align:center; font-weight:600;">{{ $userBadge }}</span>
                      @endif
                      <span class="nav-label">Notifikasi</span>
                  </a>
              @endif
          @else
              <a href="/bulan" class="nav-icon {{ request()->is('/') || request()->is('bulan') || request()->is('hari') || request()->is('tahun') ? 'active' : '' }}" title="Kalender">
                  <i class="fas fa-calendar"></i>
                  <span class="nav-label">Kalender</span>
              </a>
              @auth
                  @if (Auth::check() && Auth::user()->role === 'superadmin')
                      <a href="{{ route('superadmin.dashboard') }}" class="nav-icon {{ request()->is('superadmin') ? 'active' : '' }}" title="Super Admin">
                          <i class="fas fa-users"></i>
                          <span class="nav-label">Super Admin</span>
                      </a>
                  @endif
                  @if (Auth::user()->role !== 'superadmin')
                      <a href="{{ route('agenda.create') }}" class="nav-icon {{ request()->routeIs('agenda.create') ? 'active' : '' }}" title="Tambah Agenda">
                          <i class="fas fa-plus"></i>
                          <span class="nav-label">Tambah Agenda</span>
                      </a>
                      <a href="{{ route('agenda.notification') }}" class="nav-icon {{ request()->routeIs('agenda.notification') ? 'active' : '' }}" title="Notifikasi" style="position: relative;">
                          <i class="fas fa-bell"></i>
                          @if ($userBadge > 0)
                              <span class="notif-badge" style="position:absolute; top:6px; right:6px; min-width:18px; height:18px; padding:0 5px; border-radius:9px; background:#e63946; color:#fff; font-size:11px; line-height:18px; text-align:center; font-weight:600;">{{ $userBadge }}</span>
                          @endif
                          <span class="nav-label">Notifikasi</span>
                      </a>
                  @endif
             @else
                 {{-- Login icon removed for landing sidebar --}}
             @endauth
          @endif
      </div>
  </aside>
