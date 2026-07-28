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
        .sidebar-brand-name { font-size: 14px; font-weight: 700; color: var(--mu-white); letter-spacing: 0.3px; }
        .sidebar-brand-sub  { font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 2px; }
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
        .sidebar-section {
            padding: 8px 16px 4px;
            font-size: 10px; font-weight: 600;
            color: rgba(255,255,255,0.35);
            text-transform: uppercase; letter-spacing: 1px;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 16px; font-size: 13px; color: rgba(255,255,255,0.7);
            text-decoration: none; transition: all 0.15s;
            border-left: 3px solid transparent;
            min-height: 44px;
            position: relative;
        }
        .sidebar-link:hover  { background: rgba(255,255,255,0.07); color: white; }
        .sidebar-link.active { background: rgba(0,174,239,0.15); color: var(--mu-blue); border-left-color: var(--mu-blue); }

        /* ── Sidebar badge (for pending counts) ── */
        .sb-badge {
            margin-left: auto;
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 20px; height: 20px;
            padding: 0 6px;
            border-radius: 20px;
            font-size: 10px; font-weight: 700;
            background: #EF4444; color: white;
            flex-shrink: 0;
            animation: sbPop 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes sbPop {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }
        .sb-badge-blue { background: #2563EB; }

        .sidebar-footer {
            padding: 12px 16px; border-top: 1px solid rgba(255,255,255,0.08);
            font-size: 12px; color: rgba(255,255,255,0.4);
        }

        /* ── Overlay (mobile) ── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 200;
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
        .topbar-left  { display: flex; align-items: center; gap: 12px; }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-title { font-size: 15px; font-weight: 600; color: var(--mu-navy); }

        .hamburger {
            display: none; background: none; border: none; cursor: pointer;
            padding: 6px; border-radius: 6px;
            color: var(--mu-navy); font-size: 20px; line-height: 1;
        }

        /* ── Notification bell ── */
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
            width: 18px; height: 18px; border-radius: 50%;
            background: #EF4444; color: white; font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid white;
            animation: bellPop 0.4s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes bellPop {
            from { transform: scale(0); }
            to   { transform: scale(1); }
        }

        /* ── Notification panel ── */
        .notif-panel {
            display: none; position: absolute; top: 52px; right: 0;
            width: 320px; background: white; border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            border: 1px solid #E5E7EB; z-index: 200;
            max-height: 440px; flex-direction: column;
            animation: panelDrop 0.2s ease;
        }
        .notif-panel.open { display: flex; }
        @keyframes panelDrop {
            from { opacity: 0; transform: translateY(-8px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .notif-header {
            padding: 14px 16px;
            border-bottom: 1px solid #F3F4F6;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .notif-header span { font-size: 13px; font-weight: 700; color: var(--mu-navy); }
        .notif-unread { font-size: 11px; color: var(--mu-blue); font-weight: 700; }
        .notif-body { flex: 1; overflow-y: auto; }
        .notif-item {
            padding: 12px 16px; border-bottom: 1px solid #F9FAFB;
            transition: background 0.1s; cursor: default;
        }
        .notif-item:hover { background: #F9FAFB; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item-title { font-size: 13px; font-weight: 600; color: var(--mu-text); }
        .notif-item-body  { font-size: 12px; color: var(--mu-muted); margin-top: 2px; line-height: 1.4; }
        .notif-item-time  { font-size: 11px; color: #9CA3AF; margin-top: 4px; }
        .notif-item.unread { background: #F0F9FF; }
        .notif-item.unread .notif-item-title::before {
            content: ''; display: inline-block;
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--mu-blue); margin-right: 6px;
            vertical-align: middle; margin-top: -2px;
        }
        .notif-empty { padding: 32px; text-align: center; color: var(--mu-muted); font-size: 13px; }
        .notif-footer {
            padding: 10px 16px; border-top: 1px solid #F3F4F6;
            text-align: center; flex-shrink: 0;
        }
        .notif-footer a {
            font-size: 12px; color: var(--mu-blue); font-weight: 600; text-decoration: none;
        }

        /* ── Page content ── */
        .page-content { padding: 24px; flex: 1; }

        /* ── Cards & components ── */
        .card { background: white; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.07); }
        .card-p { padding: 24px; }
        .page-title   { font-size: 20px; font-weight: 700; color: var(--mu-navy); margin-bottom: 20px; }
        .section-title{ font-size: 15px; font-weight: 600; color: var(--mu-navy); margin-bottom: 14px; }
        .stat-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); margin-bottom: 24px; }
        .stat-card {
            background: white; border-radius: 10px; padding: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            border-left: 4px solid var(--mu-blue);
            text-decoration: none; display: block;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
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
        .badge-draft    { background: #F3F4F6; color: #374151; }
        .alert-success { background: #D1FAE5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-error   { background: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
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
            transition: border-color 0.15s; -webkit-appearance: none;
        }
        .form-input:focus { outline: none; border-color: var(--mu-blue); box-shadow: 0 0 0 3px rgba(0,174,239,0.1); }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--mu-text); margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 600; color: var(--mu-muted); text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #E5E7EB; }
        tbody td { padding: 12px 14px; border-bottom: 1px solid #F3F4F6; color: var(--mu-text); }
        tbody tr:hover td { background: #F9FAFB; }
        .progress-bar  { width: 100%; height: 6px; background: #E5E7EB; border-radius: 3px; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--mu-blue); border-radius: 3px; transition: width 0.3s; }
        .dept-row      { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #F3F4F6; gap: 8px; }
        .dept-row:last-child { border-bottom: none; }
        .dept-name     { font-size: 14px; font-weight: 500; color: var(--mu-text); display: flex; align-items: center; gap: 8px; flex: 1; min-width: 0; }
        .cert-box { background: linear-gradient(135deg, #F0F9FF 0%, #E8F7FD 100%); border: 2px solid var(--mu-blue); border-radius: 12px; padding: 24px; text-align: center; }
        .cert-box h3 { font-size: 18px; font-weight: 700; color: var(--mu-navy); margin-bottom: 6px; }
        .cert-box p  { font-size: 13px; color: var(--mu-muted); margin-bottom: 16px; }
        .cert-meta   { font-size: 12px; color: var(--mu-muted); margin-top: 10px; }
        .hover-row   { transition: background 0.1s; }
        .hover-row:hover { background: var(--mu-gray); }

        /* ══════════════════════════════════════
           TOAST NOTIFICATION SYSTEM
        ══════════════════════════════════════ */
        .toast-container {
            position: fixed;
            bottom: 24px; right: 24px;
            z-index: 9999;
            display: flex; flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }
        .toast {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 14px 18px;
            background: #111827;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2), 0 2px 8px rgba(0,0,0,0.1);
            min-width: 280px; max-width: 360px;
            pointer-events: all;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease;
        }
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .toast.hide {
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .toast-icon {
            font-size: 18px; flex-shrink: 0; margin-top: 1px;
        }
        .toast-content { flex: 1; min-width: 0; }
        .toast-title {
            font-size: 13px; font-weight: 700; color: white;
            margin-bottom: 2px;
        }
        .toast-msg {
            font-size: 12px; color: rgba(255,255,255,0.7);
            line-height: 1.4;
        }
        .toast-close {
            background: none; border: none; color: rgba(255,255,255,0.4);
            font-size: 16px; cursor: pointer; padding: 0;
            flex-shrink: 0; line-height: 1;
            transition: color 0.15s;
        }
        .toast-close:hover { color: white; }
        .toast-progress {
            position: absolute; bottom: 0; left: 0;
            height: 3px; border-radius: 0 0 12px 12px;
            transition: width linear;
        }
        .toast { position: relative; overflow: hidden; }

        /* Toast types */
        .toast-success { border-left: 4px solid #10B981; }
        .toast-success .toast-progress { background: #10B981; }
        .toast-error   { border-left: 4px solid #EF4444; }
        .toast-error   .toast-progress { background: #EF4444; }
        .toast-info    { border-left: 4px solid #00AEEF; }
        .toast-info    .toast-progress { background: #00AEEF; }
        .toast-warning { border-left: 4px solid #F59E0B; }
        .toast-warning .toast-progress { background: #F59E0B; }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); width: 260px; }
            .sidebar.open { transform: translateX(0); }
            .hamburger { display: flex; align-items: center; justify-content: center; }
            .main-wrap { margin-left: 0; }
            .topbar { padding: 0 16px; }
            .topbar-title { font-size: 14px; }
            .page-content { padding: 16px; }
            .card-p { padding: 16px; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .stat-card { padding: 14px; }
            .stat-card .val { font-size: 22px; }
            .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            table { min-width: 500px; }
            .notif-panel { width: calc(100vw - 32px); right: -60px; }
            .sidebar-link { padding: 13px 16px; font-size: 14px; }
            .hide-mobile { display: none !important; }
            .page-title { font-size: 17px; }
            .section-title { font-size: 14px; }
            .toast-container { bottom: 16px; right: 16px; left: 16px; }
            .toast { min-width: unset; max-width: 100%; }
        }
        @media (max-width: 380px) {
            .stat-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .topbar-title { font-size: 13px; max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        }
    </style>
</head>
<body>

{{-- Mobile overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ═══════════════════════════════
     TOAST CONTAINER
═══════════════════════════════ --}}
<div class="toast-container" id="toastContainer"></div>

{{-- ═══════════════════════════════
     SIDEBAR
═══════════════════════════════ --}}
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
            <a href="{{ route('student.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                ⊞ Dashboard
            </a>
            <a href="{{ route('student.clearance.index') }}"
               class="sidebar-link {{ request()->routeIs('student.clearance.index') ? 'active' : '' }}">
                ☑ My Application
            </a>
            <a href="{{ route('student.clearance.create') }}"
               class="sidebar-link {{ request()->routeIs('student.clearance.create') ? 'active' : '' }}">
                ＋ New Application
            </a>

        @elseif(auth()->user()->isOfficer())
            @php
                $pendingCount = 0;
                $deptId = auth()->user()->departmentOfficer?->department_id;
                if ($deptId) {
                    $pendingCount = \App\Models\DepartmentClearance::where('department_id', $deptId)
                        ->where('status', 'pending')->count();
                }
            @endphp
            <a href="{{ route('department.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('department.*') ? 'active' : '' }}">
                ⊞ Dashboard
                @if($pendingCount > 0)
                    <span class="sb-badge">{{ $pendingCount > 99 ? '99+' : $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('department.dashboard', ['tab' => 'pending']) }}"
               class="sidebar-link">
                ⏳ Pending Reviews
                @if($pendingCount > 0)
                    <span class="sb-badge">{{ $pendingCount > 99 ? '99+' : $pendingCount }}</span>
                @endif
            </a>

        @elseif(auth()->user()->isRegistrar())
            @php
                $awaitingCount = \App\Models\ClearanceApplication::where('status','awaiting_registrar')->count();
            @endphp
            <a href="{{ route('registrar.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('registrar.*') ? 'active' : '' }}">
                ⊞ Dashboard
                @if($awaitingCount > 0)
                    <span class="sb-badge sb-badge-blue">{{ $awaitingCount }}</span>
                @endif
            </a>
            <a href="{{ route('registrar.dashboard', ['status' => 'awaiting_registrar']) }}"
               class="sidebar-link">
                📬 Awaiting Approval
                @if($awaitingCount > 0)
                    <span class="sb-badge sb-badge-blue">{{ $awaitingCount }}</span>
                @endif
            </a>
            <a href="{{ route('registrar.dashboard') }}" class="sidebar-link">
                ☰ All Applications
            </a>

        @elseif(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                ⊞ Dashboard
            </a>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                ☺ Manage Officers
            </a>
        @endif

        <div class="sidebar-section">Account</div>
        <a href="{{ route('profile.edit') }}"
           class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            ☺ My Profile
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-link"
                    style="background:none;border:none;width:100%;text-align:left;cursor:pointer;">
                ⏻ Sign Out
            </button>
        </form>
    </nav>
    @endauth

    <div class="sidebar-footer">© {{ date('Y') }} Maseno University</div>
</aside>

{{-- ═══════════════════════════════
     MAIN AREA
═══════════════════════════════ --}}
<div class="main-wrap">
    <header class="topbar">
        <div class="topbar-left">
            <button class="hamburger" id="hamburgerBtn" aria-label="Open menu">&#9776;</button>
            <span class="topbar-title">
                @isset($header){{ $header }}@else Maseno University Clearance @endisset
            </span>
        </div>

        <div class="topbar-right">
            @auth
            @php
                $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                    ->where('is_read', false)->count();
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
                                <div class="notif-item-body">{{ Str::limit($n->message, 90) }}</div>
                                <div class="notif-item-time">{{ $n->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="notif-empty">
                                <div style="font-size:28px;margin-bottom:8px;">🔔</div>
                                No notifications yet.
                            </div>
                        @endforelse
                    </div>
                    @if($notifications->isNotEmpty())
                        <div class="notif-footer">
                            <a href="#">Mark all as read</a>
                        </div>
                    @endif
                </div>
            </div>
            @endauth
        </div>
    </header>

    <main class="page-content">
        {{ $slot }}
    </main>
</div>

{{-- ═══════════════════════════════
     JAVASCRIPT
═══════════════════════════════ --}}
<script>
// ──────────────────────────────────────
// TOAST SYSTEM
// ──────────────────────────────────────
const ToastManager = {
    container: null,

    init() {
        this.container = document.getElementById('toastContainer');
    },

    show(message, type = 'success', title = null, duration = 4000) {
        const icons = {
            success: '✅',
            error:   '❌',
            info:    'ℹ️',
            warning: '⚠️',
        };
        const titles = {
            success: 'Success',
            error:   'Error',
            info:    'Info',
            warning: 'Warning',
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">${icons[type] || '💬'}</div>
            <div class="toast-content">
                <div class="toast-title">${title || titles[type]}</div>
                <div class="toast-msg">${message}</div>
            </div>
            <button class="toast-close" onclick="ToastManager.dismiss(this.closest('.toast'))">×</button>
            <div class="toast-progress" style="width:100%;"></div>
        `;

        this.container.appendChild(toast);

        // Trigger enter animation
        requestAnimationFrame(() => {
            requestAnimationFrame(() => toast.classList.add('show'));
        });

        // Animate progress bar
        const progress = toast.querySelector('.toast-progress');
        if (progress) {
            progress.style.transition = `width ${duration}ms linear`;
            requestAnimationFrame(() => {
                requestAnimationFrame(() => { progress.style.width = '0%'; });
            });
        }

        // Auto dismiss
        setTimeout(() => this.dismiss(toast), duration);

        return toast;
    },

    dismiss(toast) {
        if (!toast || toast.classList.contains('hide')) return;
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 350);
    }
};

// ──────────────────────────────────────
// FIRE TOASTS FROM SESSION FLASH
// ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    ToastManager.init();

    @if(session('success'))
        ToastManager.show(@json(session('success')), 'success');
    @endif

    @if(session('error'))
        ToastManager.show(@json(session('error')), 'error');
    @endif

    @if(session('info'))
        ToastManager.show(@json(session('info')), 'info');
    @endif

    @if(session('warning'))
        ToastManager.show(@json(session('warning')), 'warning');
    @endif
});

// ──────────────────────────────────────
// SIDEBAR TOGGLE (MOBILE)
// ──────────────────────────────────────
const hamburgerBtn   = document.getElementById('hamburgerBtn');
const sidebar        = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebarOverlay');

function openSidebar() {
    sidebar.classList.add('open');
    sidebarOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('open');
    document.body.style.overflow = '';
}

if (hamburgerBtn) {
    hamburgerBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });
}
if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', closeSidebar);
}
document.querySelectorAll('.sidebar-link').forEach(function (link) {
    link.addEventListener('click', function () {
        if (window.innerWidth <= 768) closeSidebar();
    });
});

// ──────────────────────────────────────
// NOTIFICATION PANEL
// ──────────────────────────────────────
const notifBtn   = document.getElementById('notifBtn');
const notifPanel = document.getElementById('notifPanel');

if (notifBtn && notifPanel) {
    notifBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        notifPanel.classList.toggle('open');
    });
}
document.addEventListener('click', function (e) {
    if (notifPanel && !notifPanel.contains(e.target)) {
        notifPanel.classList.remove('open');
    }
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        if (notifPanel) notifPanel.classList.remove('open');
        closeSidebar();
    }
});
</script>
</body>
</html>