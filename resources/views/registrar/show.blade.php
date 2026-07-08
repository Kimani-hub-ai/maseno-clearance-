<x-app-layout>
    <x-slot name="header">Application Detail</x-slot>

    @php
        $sv = $application->status->value;
        $typeLabels = [
            'graduation' => '🎓 Graduation Clearance',
            'deferral'   => '📅 Deferral of Studies',
            'transfer'   => '🔄 Transfer to Another Institution',
            'withdrawal' => '🚪 Withdrawal from University',
            'other'      => '📝 Other',
        ];
    @endphp

    <div style="max-width:860px;margin:0 auto;">

        <a href="{{ route('registrar.dashboard') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:#6B7280;margin-bottom:20px;text-decoration:none;">
            ← Back to all applications
        </a>

        @if (session('success'))
            <div class="alert-success" style="margin-bottom:16px;">✅ {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-error" style="margin-bottom:16px;">❌ {{ session('error') }}</div>
        @endif

        {{-- ══════════════════════════════
             REGISTRAR ACTION BANNER
             Only shows when action needed
        ══════════════════════════════ --}}
        @if ($sv === 'awaiting_registrar')
            <div style="background:#EFF6FF;border:2px solid #2563EB;border-radius:12px;padding:20px 24px;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="font-size:22px;">📬</div>
                    <div>
                        <div style="font-size:15px;font-weight:700;color:#1E3A5F;">Final Approval Required</div>
                        <div style="font-size:13px;color:#3B82F6;margin-top:2px;">
                            All departments have cleared this application. Your sign-off will issue the certificate.
                        </div>
                    </div>
                </div>

                <div style="display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap;">
                    {{-- APPROVE BUTTON --}}
                    <form method="POST" action="{{ route('registrar.applications.approve', $application) }}">
                        @csrf
                        <button type="submit" class="btn-navy"
                                onclick="return confirm('Approve this application and issue the certificate?')">
                            ✅ Approve & Issue Certificate
                        </button>
                    </form>

                    {{-- REJECT FORM --}}
                    <div x-data="{ showReject: false }">
                        <button type="button" class="btn-secondary"
                                style="border-color:#DC2626;color:#DC2626;"
                                onclick="document.getElementById('rejectForm').style.display = document.getElementById('rejectForm').style.display === 'none' ? 'block' : 'none'">
                            ❌ Reject Application
                        </button>

                        <div id="rejectForm" style="display:none;margin-top:12px;background:white;border:1px solid #FECACA;border-radius:10px;padding:16px;min-width:340px;">
                            <form method="POST" action="{{ route('registrar.applications.reject', $application) }}">
                                @csrf
                                <label class="form-label" style="color:#991B1B;">
                                    Reason for rejection <span style="color:#DC2626;">*</span>
                                </label>
                                <textarea name="remarks" rows="3" required class="form-input"
                                          placeholder="State clearly why this application is being rejected..."></textarea>
                                @error('remarks')
                                    <p style="color:#DC2626;font-size:12px;margin-top:4px;">{{ $message }}</p>
                                @enderror
                                <div style="display:flex;gap:8px;margin-top:10px;">
                                    <button type="submit" class="btn-primary" style="background:#DC2626;"
                                            onclick="return confirm('Reject this application? The student will be notified.')">
                                        Confirm Rejection
                                    </button>
                                    <button type="button" class="btn-secondary"
                                            onclick="document.getElementById('rejectForm').style.display='none'">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Student Overview --}}
        <div class="card card-p" style="margin-bottom:16px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:16px;">
                    <div style="width:56px;height:56px;border-radius:50%;background:#1D4ED8;color:#BFDBFE;
                                display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($application->student_full_name, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:700;color:#003B5C;">{{ $application->student_full_name }}</div>
                        <div style="font-size:13px;color:#6B7280;margin-top:2px;">
                            {{ $application->student?->reg_number ?? '—' }} ·
                            {{ $application->student?->faculty ?? '—' }} ·
                            {{ $application->student?->programme ?? '—' }}
                        </div>
                        <div style="font-size:12px;color:#9CA3AF;margin-top:4px;">
                            {{ $typeLabels[$application->application_type ?? 'graduation'] ?? 'Other' }}
                            · Academic Year {{ $application->academic_year }}
                            · Submitted {{ $application->submitted_at?->format('d M Y') ?? $application->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>

                @if($sv === 'awaiting_registrar')
                    <span class="badge" style="background:#DBEAFE;color:#1D4ED8;font-size:12px;padding:6px 14px;">
                        📬 Awaiting Registrar
                    </span>
                @elseif($sv === 'approved')
                    <span class="badge badge-approved" style="font-size:12px;padding:6px 14px;">✅ Approved</span>
                @elseif($sv === 'rejected')
                    <span class="badge badge-rejected" style="font-size:12px;padding:6px 14px;">❌ Rejected</span>
                @else
                    <span class="badge badge-pending" style="font-size:12px;padding:6px 14px;">⏳ Pending</span>
                @endif
            </div>

            @if ($application->remarks)
                <div style="margin-top:16px;padding:12px;background:#F9FAFB;border-radius:8px;font-size:13px;color:#374151;">
                    <strong>Student notes:</strong> {{ $application->remarks }}
                </div>
            @endif

            {{-- Progress --}}
            <div style="margin-top:16px;">
                <div style="display:flex;justify-content:space-between;font-size:12px;color:#6B7280;margin-bottom:6px;">
                    <span>Department approvals</span>
                    <span>{{ $application->progressPercentage() }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width:{{ $application->progressPercentage() }}%;"></div>
                </div>
            </div>
        </div>

        {{-- Department Breakdown --}}
        <div class="card card-p" style="margin-bottom:16px;">
            <div class="section-title">Department Review Status</div>
            @foreach ($application->departmentClearances as $clearance)
                @php $ds = $clearance->status->value; @endphp
                <div class="dept-row">
                    <div class="dept-name">
                        @if($ds === 'approved') ✅
                        @elseif($ds === 'rejected') ❌
                        @else ⏳
                        @endif
                        <div>
                            <div>{{ $clearance->department->name }}</div>
                            @if($clearance->reviewer)
                                <div style="font-size:11px;color:#9CA3AF;">
                                    by {{ $clearance->reviewer->name }}
                                    @if($clearance->reviewed_at)
                                        on {{ $clearance->reviewed_at->format('d M Y') }}
                                    @endif
                                </div>
                            @endif
                            @if($clearance->remarks)
                                <div style="font-size:12px;color:#6B7280;margin-top:2px;">{{ $clearance->remarks }}</div>
                            @endif
                        </div>
                    </div>
                    <span class="badge badge-{{ $ds }}">{{ $clearance->status->label() }}</span>
                </div>
            @endforeach
        </div>

        {{-- Documents --}}
        <div class="card card-p" style="margin-bottom:16px;">
            <div class="section-title">Supporting Documents</div>
            @forelse ($application->documents as $doc)
                <div style="display:flex;align-items:center;justify-content:space-between;
                            padding:10px 0;border-bottom:1px solid #F3F4F6;">
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
                        📄 {{ $doc->original_name }}
                        @if($doc->department)
                            <span style="font-size:11px;color:#9CA3AF;">({{ $doc->department->name }})</span>
                        @endif
                    </div>
                </div>
            @empty
                <p style="font-size:13px;color:#9CA3AF;margin-top:8px;">No documents uploaded.</p>
            @endforelse
        </div>

        {{-- Certificate --}}
        <div class="card card-p">
            <div class="section-title">Certificate</div>

            @if ($application->certificate)
                <div class="cert-box">
                    <h3>🎓 Certificate Issued</h3>
                    <p>Certificate No: <strong>{{ $application->certificate->certificate_number }}</strong></p>
                    <p class="cert-meta">Issued on {{ $application->certificate->issued_at->format('d F Y, g:i A') }}</p>
                </div>
            @elseif($sv === 'awaiting_registrar')
                <p style="font-size:13px;color:#6B7280;margin-top:8px;">
                    Certificate will be generated automatically when you approve this application above.
                </p>
            @elseif($sv === 'rejected')
                <p style="font-size:13px;color:#9CA3AF;margin-top:8px;">
                    No certificate issued — application was rejected.
                </p>
            @else
                <p style="font-size:13px;color:#9CA3AF;margin-top:8px;">
                    Certificate will be available once all departments approve and the Registrar signs off.
                </p>
            @endif
        </div>
    </div>
</x-app-layout>