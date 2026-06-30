<x-app-layout>
    <x-slot name="header">Application Detail</x-slot>

    @php
        $statusValue = $application->status->value;
        $typeLabels = [
            'graduation' => '🎓 Graduation Clearance',
            'deferral'   => '📅 Deferral of Studies',
            'transfer'   => '🔄 Transfer to Another Institution',
            'withdrawal' => '🚪 Withdrawal from University',
            'other'      => '📝 Other',
        ];
    @endphp

    <div class="max-w-5xl mx-auto space-y-5">

        {{-- Back link --}}
        <a href="{{ route('registrar.dashboard') }}"
           class="inline-flex items-center gap-1 text-sm font-medium" style="color:var(--text-secondary);">
            <i class="ti ti-arrow-left" style="font-size:15px;"></i> Back to all applications
        </a>

        {{-- Flash --}}
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if (session('info'))
            <div class="p-3 rounded-lg text-sm" style="background:var(--badge-bg);color:var(--accent);border:1px solid var(--border);">
                {{ session('info') }}
            </div>
        @endif

        {{-- Overview Card --}}
        <div class="card p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-14 h-14 rounded-full text-lg font-bold flex-shrink-0"
                         style="background:#1d4ed8;color:#bfdbfe;">
                        {{ strtoupper(substr($application->student_full_name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-base font-semibold" style="color:var(--text-primary);">
                            {{ $application->student_full_name }}
                        </p>
                        <p class="text-sm" style="color:var(--text-secondary);">
                            {{ $application->student?->reg_number ?? '—' }} ·
                            {{ $application->student?->faculty ?? '—' }} ·
                            {{ $application->student?->programme ?? '—' }}
                        </p>
                        <p class="text-xs mt-1" style="color:var(--text-muted);">
                            {{ $typeLabels[$application->application_type ?? 'graduation'] ?? 'Other' }}
                            &middot; Academic Year {{ $application->academic_year }}
                        </p>
                    </div>
                </div>
                <span class="badge badge-{{ $statusValue }} flex-shrink-0">
                    {{ $application->status->label() }}
                </span>
            </div>

            @if ($application->remarks)
                <div class="mt-4 p-3 rounded-lg text-sm" style="background:var(--hover-row);color:var(--text-secondary);">
                    <strong style="color:var(--text-primary);">Student notes:</strong> {{ $application->remarks }}
                </div>
            @endif

            {{-- Progress bar --}}
            <div class="mt-4">
                <div class="flex justify-between text-xs mb-1" style="color:var(--text-muted);">
                    <span>Department approvals</span>
                    <span>{{ $application->progressPercentage() }}%</span>
                </div>
                <div class="w-full h-2 rounded-full" style="background:var(--border);">
                    <div class="h-2 rounded-full transition-all" style="background:#2563eb;width:{{ $application->progressPercentage() }}%;"></div>
                </div>
            </div>
        </div>

        {{-- Department Breakdown --}}
        <div class="card p-6">
            <p class="section-title">Department Review Status</p>
            <div class="space-y-2 mt-3">
                @foreach ($application->departmentClearances as $clearance)
                    @php $deptStatus = $clearance->status->value; @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg" style="border:1px solid var(--border);">
                        <div>
                            <p class="font-medium text-sm" style="color:var(--text-primary);">
                                {{ $clearance->department->name }}
                            </p>
                            @if ($clearance->remarks)
                                <p class="text-xs mt-0.5" style="color:var(--text-muted);">{{ $clearance->remarks }}</p>
                            @endif
                            @if ($clearance->reviewer)
                                <p class="text-xs mt-0.5" style="color:var(--text-muted);">
                                    Reviewed by {{ $clearance->reviewer->name }}
                                    @if ($clearance->reviewed_at)
                                        on {{ $clearance->reviewed_at->format('d M Y, g:i A') }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <span class="badge badge-{{ $deptStatus }}">{{ $clearance->status->label() }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Documents --}}
        <div class="card p-6">
            <p class="section-title">Supporting Documents</p>
            @if ($application->documents->isEmpty())
                <p class="text-sm mt-2" style="color:var(--text-muted);">No documents uploaded.</p>
            @else
                <div class="space-y-2 mt-3">
                    @foreach ($application->documents as $doc)
                        <div class="flex items-center justify-between p-2.5 rounded-lg" style="border:1px solid var(--border);">
                            <div class="flex items-center gap-2">
                                <i class="ti ti-file-text" style="font-size:16px;color:var(--text-muted);"></i>
                                <span class="text-sm" style="color:var(--text-primary);">{{ $doc->original_name }}</span>
                                @if ($doc->department)
                                    <span class="text-xs" style="color:var(--text-muted);">({{ $doc->department->name }})</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Certificate / Override --}}
        <div class="card p-6">
            <p class="section-title">Certificate</p>

            @if ($application->certificate)
                <div class="mt-3 p-4 rounded-lg" style="background:var(--success-bg);border:1px solid var(--success-border);">
                    <p class="text-sm font-medium" style="color:var(--success-text);">
                        ✅ Certificate issued: {{ $application->certificate->certificate_number }}
                    </p>
                    <p class="text-xs mt-1" style="color:var(--text-muted);">
                        Issued on {{ $application->certificate->issued_at->format('d M Y, g:i A') }}
                    </p>
                </div>
            @elseif ($statusValue === 'approved')
                <p class="text-sm mt-2 mb-3" style="color:var(--text-secondary);">
                    All departments approved but no certificate has been generated yet.
                </p>
                <form method="POST" action="{{ route('registrar.applications.issue-certificate', $application) }}">
                    @csrf
                    <button type="submit" class="btn-primary">
                        <i class="ti ti-certificate" style="margin-right:4px;"></i> Issue Certificate
                    </button>
                </form>
            @else
                <p class="text-sm mt-2" style="color:var(--text-muted);">
                    Certificate will be issued automatically once all departments approve this application.
                </p>
            @endif
        </div>

    </div>
</x-app-layout>