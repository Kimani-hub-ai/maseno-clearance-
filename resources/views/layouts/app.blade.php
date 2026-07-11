<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Maseno University Clearance') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --mu-blue:   #00AEEF;
            --mu-navy:   #003B5C;
            --mu-gold:   #F5A623;
            --mu-light:  #E8F7FD;
            --mu-white:  #FFFFFF;
            --mu-gray:   #F3F4F6;
            --mu-text:   #1F2937;
            --mu-muted:  #6B7280;
            --sidebar-w: 220px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, system-ui, sans-serif; background: var(--mu-gray); color: var(--mu-text); }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--mu-navy);
            display: flex; flex-direction: column;
            z-index: 300;
            transition: transform 0.25s ease;
        }
        .sidebar-brand {
            padding: 20px 16px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand-name {
            font-size: 14px; font-weight: 700; color: var(--mu-white);
            letter-spacing: 0.3px;
        }
        .sidebar-brand-sub {
            font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 2px;
        }
        .sidebar-user {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--mu-blue);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: white;
            margin-bottom: 8px;
        }
        .sidebar-username { font-size: 13px; font-weight: 600; color: white; }
        .sidebar-role {
            display: inline-block; font-size: 10px; font-weight: 600;
            padding: 2px 8px; border-radius: 20px; margin-top: 3px;
            background: var(--mu-blue); color: white; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .sidebar-nav { padding: 12px 0; flex: 1; overflow-y: auto; }
        .sidebar-section { padding: 8px 16px 4px; font-size: 10px; font-weight: 600; color: rgba(255,255,255,0.35); text-transform: uppercase; letter-spacing: 1px; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 16px; font-size: 13px; color: rgba(255,255,255,0.7);
            text-decoration: none; transition: all 0.15s;
            border-left: 3px solid transparent;
            min-height: 44px; /* touch-friendly */
        }
        .sidebar-link:hover { background: rgba(255,255,255,0.07); color: white; }
        .sidebar-link.active { background: rgba(0,174,239,0.15); color: var(--mu-blue); border-left-color: var(--mu-blue); }
        .sidebar-footer {
            padding: 12px 16px; border-top: 1px solid rgba(255,255,255,0.08);
            font-size: 12px; color: rgba(255,255,255,0.4);
        }

        /* ── Overlay (mobile only) ── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 200;
        }
        .sidebar-overlay.open { display: block; }

        /* ── Main ── */
        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            background: white; border-bottom: 1px solid #E5E7EB;
            padding: 0 20px; height: 56px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .topbar-title { font-size: 15px; font-weight: 600; color: var(--mu-navy); }
        .topbar-right { display: flex; align-items: center; gap: 12px; }

        /* Hamburger — hidden on desktop */
        .hamburger {
            display: none;
            background: none; border: none; cursor: pointer;
            padding: 6px; border-radius: 6px;
            color: var(--mu-navy); font-size: 20px; line-height: 1;
        }

        .notif-btn {
            position: relative; background: none; border: none; cursor: pointer;
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: var(--mu-muted); transition: background 0.15s;
            font-size: 18px;
        }
        .notif-btn:hover { background: var(--mu-gray); }
        .notif-badge {
            position: absolute; top: 4px; right: 4px;
            width: 16px; height: 16px; border-radius: 50%;
            background: #EF4444; color: white; font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }

        /* ── Notification panel ── */
        .notif-panel {
            display: none;
            position: absolute; top: 52px; right: 0;
            width: 320px;
            background: white; border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            border: 1px solid #E5E7EB;
            z-index: 200;
            max-height: 420px;
            flex-direction: column;
        }
        .notif-panel.open { display: flex; }
        .notif-header {
            padding: 12px 16px;
            border-bottom: 1px solid #F3F4F6;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .notif-header span { font-size: 13px; font-weight: 600; color: var(--mu-navy); }
        .notif-unread { font-size: 11px; color: var(--mu-blue); font-weight: 600; }
        .notif-body { flex: 1; overflow-y: auto; }
        .notif-item { padding: 12px 16px; border-bottom: 1px solid #F9FAFB; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item-title { font-size: 13px; font-weight: 600; color: var(--mu-text); }
        .notif-item-body { font-size: 12px; color: var(--mu-muted); margin-top: 2px; line-height: 1.4; }
        .notif-item-time { font-size: 11px; color: #9CA3AF; margin-top: 4px; }
        .notif-item.unread { background: #F0F9FF; }
        .notif-empty { padding: 24px; text-align: center; color: var(--mu-muted); font-size: 13px; }

        /* ── Page content ── */
        .page-content { padding: 24px; flex: 1; }

        /* ── Cards & components ── */
        .card { background: white; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.07); }
        .card-p { padding: 24px; }
        .page-title { font-size: 20px; font-weight: 700; color: var(--mu-navy); margin-bottom: 20px; }
        .section-title { font-size: 15px; font-weight: 600; color: var(--mu-navy); margin-bottom: 14px; }
        .stat-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); margin-bottom: 24px; }
        .stat-card {
            background: white; border-radius: 10px; padding: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            border-left: 4px solid var(--mu-blue);
            text-decoration: none; display: block;
        }
        .stat-card .lbl { font-size: 12px; color: var(--mu-muted); margin-bottom: 6px; }
        .stat-card .val { font-size: 26px; font-weight: 700; color: var(--mu-navy); }
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
        }
        .badge-cleared, .badge-approved { background: #D1FAE5; color: #065F46; }
        .badge-awaiting_registrar { background: #DBEAFE; color: #1D4ED8; }
        .badge-in_progress, .badge-submitted, .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-rejected { background: #FEE2E2; color: #991B1B; }
        .badge-draft { background: #F3F4F6; color: #374151; }
        .alert-success { background: #D1FAE5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-error { background: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; background: var(--mu-blue); color: white;
            border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: background 0.15s;
            min-height: 44px;
        }
        .btn-primary:hover { background: #0099D6; }
        .btn-navy {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; background: var(--mu-navy); color: white;
            border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: background 0.15s;
            min-height: 44px;
        }
        .btn-navy:hover { background: #002a42; }
        .btn-secondary {
            padding: 8px 16px; background: var(--mu-gray); color: var(--mu-text);
            border: 1px solid #D1D5DB; border-radius: 8px; font-size: 13px;
            cursor: pointer; transition: background 0.15s; min-height: 44px;
        }
        .btn-secondary:hover { background: #E5E7EB; }
        .form-input {
            width: 100%; padding: 10px 12px; border: 1px solid #D1D5DB;
            border-radius: 8px; font-size: 15px; color: var(--mu-text);
            transition: border-color 0.15s;
            -webkit-appearance: none; /* remove iOS styling */
        }
        .form-input:focus { outline: none; border-color: var(--mu-blue); box-shadow: 0 0 0 3px rgba(0,174,239,0.1); }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--mu-text); margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 600; color: var(--mu-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #E5E7EB; }
        tbody td { padding: 12px 14px; border-bottom: 1px solid #F3F4F6; color: var(--mu-text); }
        tbody tr:hover td { background: #F9FAFB; }
        .progress-bar { width: 100%; height: 6px; background: #E5E7EB; border-radius: 3px; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--mu-blue); border-radius: 3px; transition: width 0.3s; }
        .dept-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #F3F4F6; gap: 8px; }
        .dept-row:last-child { border-bottom: none; }
        .dept-name { font-size: 14px; font-weight: 500; color: var(--mu-text); display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0; }
        .dept-status { font-size: 12px; font-weight: 600; }
        .dept-approved { color: #059669; }
        .dept-pending { color: #D97706; }
        .dept-rejected { color: #DC2626; }
        .cert-box {
            background: linear-gradient(135deg, #F0F9FF 0%, #E8F7FD 100%);
            border: 2px solid var(--mu-blue); border-radius: 12px;
            padding: 24px; text-align: center;
        }
        .cert-box h3 { font-size: 18px; font-weight: 700; color: var(--mu-navy); margin-bottom: 6px; }
        .cert-box p { font-size: 13px; color: var(--mu-muted); margin-bottom: 16px; }
        .cert-meta { font-size: 12px; color: var(--mu-muted); margin-top: 10px; }
        .hover-row { transition: background 0.1s; }
        .hover-row:hover { background: var(--mu-gray); }

        /* ════════════════════════════════════
           MOBILE STYLES (max-width: 768px)
        ════════════════════════════════════ */
        @media (max-width: 768px) {

            /* Sidebar hidden off-screen by default */
            .sidebar {
                transform: translateX(-100%);
                width: 260px; /* slightly wider for easier touch */
            }
            .sidebar.open {
                transform: translateX(0);
            }

            /* Show hamburger */
            .hamburger { display: flex; align-items: center; justify-content: center; }

            /* Main takes full width */
            .main-wrap { margin-left: 0; }

            /* Topbar adjustments */
            .topbar { padding: 0 16px; }
            .topbar-title { font-size: 14px; }

            /* Page content less padding */
            .page-content { padding: 16px; }

            /* Cards full width less padding */
            .card-p { padding: 16px; }

            /* Stat grid — 2 columns on mobile */
            .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .stat-card { padding: 14px; }
            .stat-card .val { font-size: 22px; }

            /* Tables — horizontally scrollable */
            .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            table { min-width: 500px; }

            /* Notification panel full width on mobile */
            .notif-panel {
                width: calc(100vw - 32px);
                right: -60px; /* shift left so it doesn't overflow */
            }

            /* Buttons full width on mobile forms */
            .btn-block-mobile {
                width: 100%; justify-content: center;
            }

            /* Department rows wrap on small screens */
            .dept-row { flex-wrap: wrap; gap: 6px; }

            /* Bigger touch targets for links */
            .sidebar-link { padding: 13px 16px; font-size: 14px; }

            /* Hide less important table columns on mobile */
            .hide-mobile { display: none !important; }

            /* Page title smaller */
            .page-title { font-size: 17px; }
            .section-title { font-size: 14px; }
        }

        /* Small phones */
        @media (max-width: 380px) {
            .stat-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .stat-card .val { font-size: 20px; }
            .topbar-title { font-size: 13px; max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        }
    </style>
</head>
<body>

{{-- Mobile overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- Sidebar --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-name">Maseno University</div>
        <div class="sidebar-brand-sub">Clearance System</div>
    </div>

    @auth
    <div class="sidebar-user">
        <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
        <div class="sidebar-username">{{ Str::limit(auth()->user()->name, 20) }}</div>
        <span class="sidebar-role">{{ ucfirst(auth()->user()->role->value) }}</span>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Main</div>

        @if(auth()->user()->isStudent())
            <a href="{{ route('student.dashboard') }}" class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                ⊞ Dashboard
            </a>
            <a href="{{ route('student.clearance.index') }}" class="sidebar-link {{ request()->routeIs('student.clearance.index') ? 'active' : '' }}">
                ☑ My Application
            </a>
            <a href="{{ route('student.clearance.create') }}" class="sidebar-link {{ request()->routeIs('student.clearance.create') ? 'active' : '' }}">
                ＋ New Application
            </a>
        @elseif(auth()->user()->isOfficer())
            <a href="{{ route('department.dashboard') }}" class="sidebar-link {{ request()->routeIs('department.*') ? 'active' : '' }}">
                ⊞ Dashboard
            </a>
        @elseif(auth()->user()->isRegistrar())
            <a href="{{ route('registrar.dashboard') }}" class="sidebar-link {{ request()->routeIs('registrar.*') ? 'active' : '' }}">
                ⊞ Dashboard
            </a>
            <a href="{{ route('registrar.dashboard') }}" class="sidebar-link">
                ☰ All Applications
            </a>
        @elseif(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                ⊞ Dashboard
            </a>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                ☺ Manage Officers
            </a>
        @endif

        <div class="sidebar-section">Account</div>
        <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            ☺ My Profile
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="background:none;border:none;width:100%;text-align:left;cursor:pointer;">
                ⏻ Sign Out
            </button>
        </form>
    </nav>
    @endauth

    <div class="sidebar-footer">© {{ date('Y') }} Maseno University</div>
</aside>

{{-- Main --}}
<div class="main-wrap">
    <header class="topbar">
        <div class="topbar-left">
            {{-- Hamburger menu button (mobile only) --}}
            <button class="hamburger" id="hamburgerBtn" aria-label="Open menu">
                &#9776;
            </button>
            <span class="topbar-title">
                @isset($header){{ $header }}@else Maseno University Clearance @endisset
            </span>
        </div>

        <div class="topbar-right">
            @auth
            @php
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
            @endphp
            <div class="notif-wrapper" style="position:relative;">
                <button class="notif-btn" id="notifBtn" aria-label="Notifications">
                    🔔
                    @if($unreadCount > 0)
                        <span class="notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>
                <div class="notif-panel" id="notifPanel">
                    <div class="notif-header">
                        <span>Notifications</span>
                        @if($unreadCount > 0)
                            <span class="notif-unread">{{ $unreadCount }} unread</span>
                        @endif
                    </div>
                    <div class="notif-body">
                        @php
                            $notifications = \App\Models\Notification::where('user_id', auth()->id())
                                ->latest()->take(20)->get();
                        @endphp
                        @forelse($notifications as $n)
                            <div class="notif-item {{ !$n->is_read ? 'unread' : '' }}">
                                <div class="notif-item-title">{{ $n->title }}</div>
                                <div class="notif-item-body">{{ Str::limit($n->message, 80) }}</div>
                                <div class="notif-item-time">{{ $n->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="notif-empty">No notifications yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </header>

    <main class="page-content">
        {{ $slot }}
    </main>
</div>

<script>
    // ── Sidebar toggle (mobile) ──
    const hamburgerBtn  = document.getElementById('hamburgerBtn');
    const sidebar       = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        sidebar.classList.add('open');
        sidebarOverlay.classList.add('open');
        document.body.style.overflow = 'hidden'; // prevent scroll behind overlay
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebarOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    // Close sidebar when overlay is tapped
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // Close sidebar when a nav link is tapped (mobile UX)
    document.querySelectorAll('.sidebar-link').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    });

    // ── Notification panel ──
    const notifBtn   = document.getElementById('notifBtn');
    const notifPanel = document.getElementById('notifPanel');

    if (notifBtn && notifPanel) {
        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifPanel.classList.toggle('open');
        });
    }

    document.addEventListener('click', function(e) {
        if (notifPanel && !notifPanel.contains(e.target)) {
            notifPanel.classList.remove('open');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (notifPanel) notifPanel.classList.remove('open');
            closeSidebar();
        }
    });
</script>
</body>
</html>