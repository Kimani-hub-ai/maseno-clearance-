<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Maseno Clearance') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { background: #f4f5f7; }
        .page-header-bar {
            background: #ffffff;
            border-bottom: 2px solid #e2e8f0;
            padding: 13px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .page-header-bar h2 {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
            line-height: 1.4;
        }
        .nav-section {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #475569;
            padding: 12px 12px 4px;
        }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
    </style>
</head>
<body class="font-sans antialiased" x-data="{ sidebarOpen: false }">

<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
     class="fixed inset-0 z-20 bg-black bg-opacity-60 lg:hidden"
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
</div>

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 flex flex-col w-64 flex-shrink-0
                  transform transition-transform duration-200 ease-in-out
                  lg:static lg:translate-x-0"
           style="background:#0f172a;">

        {{-- Brand --}}
        <div class="flex items-center gap-3 px-5 py-4 flex-shrink-0"
             style="border-bottom:1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg flex-shrink-0"
                 style="background:#2563eb;">
                <i class="ti ti-certificate" style="color:#fff;font-size:18px;"></i>
            </div>
            <div>
                <p class="text-sm font-semibold leading-tight" style="color:#f1f5f9;">Maseno University</p>
                <p class="text-xs" style="color:#64748b;">Clearance System</p>
            </div>
        </div>

        {{-- User block --}}
        <div class="flex items-center gap-3 px-5 py-3 flex-shrink-0"
             style="border-bottom:1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center justify-center w-9 h-9 rounded-full flex-shrink-0 text-xs font-semibold"
                 style="background:#1d4ed8;color:#bfdbfe;">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium truncate" style="color:#e2e8f0;">{{ auth()->user()->name }}</p>
                <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-xs font-medium"
                      style="background:#1e3a5f;color:#93c5fd;">
                    {{ auth()->user()->role->label() }}
                </span>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav flex-1 overflow-y-auto px-3 py-3">
            @php $role = auth()->user()->role->value; @endphp

            @if($role === 'student')
                <div class="nav-section">Main</div>
                <x-sidebar-link route="student.dashboard"        icon="layout-dashboard" label="Dashboard" />
                <x-sidebar-link route="student.clearance.index"  icon="file-text"        label="My Application" />
                <x-sidebar-link route="student.clearance.create" icon="circle-plus"      label="New Application" />
                <div class="nav-section">Account</div>
                <x-sidebar-link route="profile.edit"             icon="user-circle"      label="My Profile" />
            @endif

            @if($role === 'officer')
                <div class="nav-section">Main</div>
                <x-sidebar-link route="department.dashboard" icon="layout-dashboard" label="Dashboard" />
                <x-sidebar-link route="department.dashboard" icon="clipboard-check"  label="Pending Reviews" />
                <div class="nav-section">Account</div>
                <x-sidebar-link route="profile.edit"         icon="user-circle"      label="My Profile" />
            @endif

            @if($role === 'registrar')
                <div class="nav-section">Main</div>
                <x-sidebar-link route="registrar.dashboard" icon="layout-dashboard" label="Dashboard" />
                <x-sidebar-link route="registrar.dashboard" icon="files"            label="All Applications" />
                <x-sidebar-link route="registrar.dashboard" icon="certificate"      label="Certificates" />
                <div class="nav-section">Account</div>
                <x-sidebar-link route="profile.edit"        icon="user-circle"      label="My Profile" />
            @endif

            @if($role === 'admin')
                <div class="nav-section">Main</div>
                <x-sidebar-link route="admin.dashboard" icon="layout-dashboard" label="Dashboard" />
                <x-sidebar-link route="admin.dashboard" icon="users"            label="Manage Officers" />
                <x-sidebar-link route="admin.dashboard" icon="building"         label="Departments" />
                <div class="nav-section">Account</div>
                <x-sidebar-link route="profile.edit"    icon="user-circle"      label="My Profile" />
            @endif
        </nav>

        {{-- Sign out --}}
        <div class="px-3 py-3 flex-shrink-0" style="border-top:1px solid rgba(255,255,255,0.07);">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors duration-150"
                        style="color:#64748b;background:transparent;"
                        onmouseover="this.style.background='rgba(239,68,68,0.1)';this.style.color='#fca5a5';"
                        onmouseout="this.style.background='transparent';this.style.color='#64748b';">
                    <i class="ti ti-logout flex-shrink-0" style="font-size:17px;width:18px;"></i>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN AREA --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        {{-- TOP BAR --}}
        <header class="flex items-center justify-between flex-shrink-0 px-4 sm:px-6 h-14"
                style="background:#ffffff;border-bottom:1px solid #e2e8f0;">

            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2 rounded-lg transition-colors"
                    style="color:#64748b;"
                    onmouseover="this.style.background='#f4f5f7';"
                    onmouseout="this.style.background='transparent';">
                <i class="ti ti-menu-2" style="font-size:20px;"></i>
            </button>

            {{-- Breadcrumb --}}
            <div class="hidden lg:flex items-center gap-2 text-xs" style="color:#94a3b8;">
                <i class="ti ti-home" style="font-size:13px;"></i>
                <span style="color:#cbd5e1;">/</span>
                <span style="color:#64748b;">
                    @php
                        $routeName = request()->route()?->getName() ?? '';
                        echo match(true) {
                            str_contains($routeName, 'clearance.create') => 'New Application',
                            str_contains($routeName, 'clearance.index')  => 'My Application',
                            str_contains($routeName, 'dashboard')        => 'Dashboard',
                            str_contains($routeName, 'profile')          => 'Profile',
                            str_contains($routeName, 'officers')         => 'Manage Officers',
                            str_contains($routeName, 'applications')     => 'Applications',
                            default                                      => 'Overview',
                        };
                    @endphp
                </span>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-2 ml-auto">

                @php
                    $unreadCount = auth()->user()->appNotifications()->where('is_read', false)->count();
                @endphp

                {{-- Bell --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="relative flex items-center justify-center w-9 h-9 rounded-lg transition-colors"
                            style="background:#f4f5f7;"
                            onmouseover="this.style.background='#e9ebee';"
                            onmouseout="this.style.background='#f4f5f7';"
                            aria-label="Notifications">
                        <i class="ti ti-bell" style="font-size:18px;color:#64748b;"></i>
                        @if($unreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 flex items-center justify-center
                                         w-4 h-4 rounded-full font-bold"
                                  style="background:#ef4444;color:#fff;font-size:9px;
                                         border:1.5px solid #fff;">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                         class="absolute right-0 mt-2 w-80 rounded-xl overflow-hidden"
                         style="background:#fff;border:0.5px solid #e2e8f0;
                                box-shadow:0 8px 24px rgba(0,0,0,0.08);z-index:50;"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="flex items-center justify-between px-4 py-3"
                             style="border-bottom:1px solid #f1f5f9;">
                            <p class="text-sm font-semibold" style="color:#0f172a;">Notifications</p>
                            @if($unreadCount > 0)
                                <span class="text-xs font-medium" style="color:#2563eb;">{{ $unreadCount }} unread</span>
                            @endif
                        </div>

                        <div class="overflow-y-auto" style="max-height:300px;">
                            @forelse(auth()->user()->appNotifications()->latest()->take(8)->get() as $notif)
                                <div class="px-4 py-3 transition-colors"
                                     style="{{ !$notif->is_read ? 'background:#eff6ff;' : 'background:#fff;' }}
                                            border-bottom:1px solid #f8fafc;"
                                     onmouseover="this.style.background='#f8fafc';"
                                     onmouseout="this.style.background='{{ !$notif->is_read ? '#eff6ff' : '#fff' }}';">
                                    <div class="flex items-start gap-3">
                                        <div class="flex items-center justify-center w-7 h-7 rounded-full flex-shrink-0 mt-0.5"
                                             style="background:#dbeafe;">
                                            <i class="ti ti-bell" style="font-size:13px;color:#2563eb;"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold" style="color:#0f172a;">{{ $notif->title }}</p>
                                            <p class="text-xs mt-0.5 leading-relaxed" style="color:#64748b;">
                                                {{ Str::limit($notif->message, 80) }}
                                            </p>
                                            <p class="text-xs mt-1" style="color:#94a3b8;">{{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(!$notif->is_read)
                                            <div class="w-2 h-2 rounded-full flex-shrink-0 mt-1.5"
                                                 style="background:#2563eb;"></div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-10 text-center">
                                    <i class="ti ti-bell-off" style="font-size:32px;color:#cbd5e1;display:block;margin-bottom:8px;"></i>
                                    <p class="text-sm" style="color:#94a3b8;">No notifications yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- User pill --}}
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg cursor-pointer"
                     style="background:#f4f5f7;border:0.5px solid #e2e8f0;">
                    <div class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold flex-shrink-0"
                         style="background:#2563eb;color:#fff;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <span class="text-xs font-medium hidden md:block" style="color:#374151;">
                        {{ auth()->user()->name }}
                    </span>
                    <i class="ti ti-chevron-down hidden md:block" style="font-size:12px;color:#94a3b8;"></i>
                </div>
            </div>
        </header>

        {{-- PAGE HEADER — white strip with blue left border, always visible --}}
        @isset($header)
            <div class="page-header-bar">
                <div class="w-1 h-6 rounded-full flex-shrink-0" style="background:#2563eb;"></div>
                <h2>{{ $header }}</h2>
            </div>
        @endisset

        {{-- PAGE CONTENT --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6" style="background:#f4f5f7;">
            {{ $slot }}
        </main>

        {{-- FOOTER --}}
        <footer class="flex-shrink-0 px-6 py-2.5 text-xs text-center"
                style="background:#fff;border-top:1px solid #e2e8f0;color:#94a3b8;">
            © {{ date('Y') }} Maseno University — Clearance Management System
        </footer>
    </div>
</div>

</body>
</html>