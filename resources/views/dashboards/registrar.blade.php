<x-app-layout>
    <x-slot name="header">Registrar Dashboard</x-slot>

    <div class="space-y-6">

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="p-3 rounded-lg text-sm" style="background:var(--badge-bg);color:var(--accent);border:1px solid var(--border);">
                {{ session('info') }}
            </div>
        @endif

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

            <a href="{{ route('registrar.dashboard') }}" class="stat-card hover:opacity-90 transition">
                <div class="lbl"><i class="ti ti-users" style="font-size:14px;"></i> Total Students</div>
                <div class="val">{{ $stats['total_students'] }}</div>
            </a>

            <a href="{{ route('registrar.dashboard') }}" class="stat-card hover:opacity-90 transition">
                <div class="lbl"><i class="ti ti-files" style="font-size:14px;"></i> Total Applications</div>
                <div class="val">{{ $stats['total_applications'] }}</div>
            </a>

            <a href="{{ route('registrar.dashboard', ['status' => 'pending']) }}" class="stat-card hover:opacity-90 transition">
                <div class="lbl"><i class="ti ti-clock" style="font-size:14px;color:var(--warn-text);"></i> Pending</div>
                <div class="val" style="color:var(--warn-text);">{{ $stats['pending'] }}</div>
            </a>

            <a href="{{ route('registrar.dashboard', ['status' => 'approved']) }}" class="stat-card hover:opacity-90 transition">
                <div class="lbl"><i class="ti ti-circle-check" style="font-size:14px;color:var(--success-text);"></i> Approved</div>
                <div class="val" style="color:var(--success-text);">{{ $stats['approved'] }}</div>
            </a>

            <a href="{{ route('registrar.dashboard', ['status' => 'rejected']) }}" class="stat-card hover:opacity-90 transition">
                <div class="lbl"><i class="ti ti-circle-x" style="font-size:14px;color:var(--error-text);"></i> Rejected</div>
                <div class="val" style="color:var(--error-text);">{{ $stats['rejected'] }}</div>
            </a>
        </div>

        {{-- Applications Table Card --}}
        <div class="card p-6">

            {{-- Header row: title + search + filter --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                <div>
                    <p class="section-title" style="margin-bottom:2px;">All Applications</p>
                    <p class="text-xs" style="color:var(--text-muted);">
                        Showing {{ $applications->count() }} of {{ $applications->total() }} application(s)
                        @if(request('status'))
                            — filtered by <strong>{{ ucfirst(request('status')) }}</strong>
                            <a href="{{ route('registrar.dashboard') }}" class="underline ml-1" style="color:var(--accent);">clear</a>
                        @endif
                    </p>
                </div>

                <form method="GET" action="{{ route('registrar.dashboard') }}" class="flex items-center gap-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="relative">
                        <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2" style="font-size:14px;color:var(--text-muted);"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search name or reg number..."
                               class="form-input pl-9 text-sm" style="width:240px;">
                    </div>
                    <button type="submit" class="btn-secondary">Search</button>
                </form>
            </div>

            {{-- Table --}}
            @if ($applications->isEmpty())
                <div class="text-center py-12">
                    <i class="ti ti-inbox" style="font-size:40px;color:var(--text-muted);display:block;margin-bottom:10px;"></i>
                    <p class="text-sm" style="color:var(--text-muted);">No applications found.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="border-bottom:1px solid var(--border);">
                                <th class="text-left py-2 px-2 font-medium" style="color:var(--text-secondary);">Student</th>
                                <th class="text-left py-2 px-2 font-medium" style="color:var(--text-secondary);">Type</th>
                                <th class="text-left py-2 px-2 font-medium" style="color:var(--text-secondary);">Academic Year</th>
                                <th class="text-left py-2 px-2 font-medium" style="color:var(--text-secondary);">Progress</th>
                                <th class="text-left py-2 px-2 font-medium" style="color:var(--text-secondary);">Status</th>
                                <th class="text-left py-2 px-2 font-medium" style="color:var(--text-secondary);">Submitted</th>
                                <th class="text-right py-2 px-2 font-medium" style="color:var(--text-secondary);">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $app)
                                @php
                                    $statusValue = $app->status->value;
                                    $typeLabels = [
                                        'graduation' => '🎓 Graduation',
                                        'deferral'   => '📅 Deferral',
                                        'transfer'   => '🔄 Transfer',
                                        'withdrawal' => '🚪 Withdrawal',
                                        'other'      => '📝 Other',
                                    ];
                                @endphp
                                <tr style="border-bottom:1px solid var(--divider);" class="transition-colors"
                                    onmouseover="this.style.background='var(--hover-row)';"
                                    onmouseout="this.style.background='transparent';">

                                    <td class="py-3 px-2">
                                        <p class="font-medium" style="color:var(--text-primary);">
                                            {{ $app->student_full_name }}
                                        </p>
                                        <p class="text-xs" style="color:var(--text-muted);">
                                            {{ $app->student?->reg_number ?? '—' }}
                                        </p>
                                    </td>

                                    <td class="py-3 px-2" style="color:var(--text-secondary);">
                                        {{ $typeLabels[$app->application_type ?? 'graduation'] ?? 'Other' }}
                                    </td>

                                    <td class="py-3 px-2" style="color:var(--text-secondary);">
                                        {{ $app->academic_year }}
                                    </td>

                                    <td class="py-3 px-2" style="min-width:100px;">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 h-1.5 rounded-full" style="background:var(--border);">
                                                <div class="h-1.5 rounded-full" style="background:#2563eb;width:{{ $app->progressPercentage() }}%;"></div>
                                            </div>
                                            <span class="text-xs" style="color:var(--text-muted);">{{ $app->progressPercentage() }}%</span>
                                        </div>
                                    </td>

                                    <td class="py-3 px-2">
                                        <span class="badge badge-{{ $statusValue }}">
                                            {{ $app->status->label() }}
                                        </span>
                                    </td>

                                    <td class="py-3 px-2 text-xs" style="color:var(--text-muted);">
                                        {{ $app->submitted_at?->format('d M Y') ?? $app->created_at->format('d M Y') }}
                                    </td>

                                    <td class="py-3 px-2 text-right">
                                        <a href="{{ route('registrar.applications.show', $app) }}"
                                           class="text-xs font-medium inline-flex items-center gap-1"
                                           style="color:var(--accent);">
                                            View <i class="ti ti-chevron-right" style="font-size:12px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-5">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>