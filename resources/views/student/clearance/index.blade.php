<x-app-layout>
    <x-slot name="header">My Application</x-slot>

    <style>
        /* ── Detailed Timeline ── */
        .dtl-timeline {
            position: relative;
            padding: 4px 0;
        }
        .dtl-timeline::before {
            content: '';
            position: absolute;
            left: 23px;
            top: 28px;
            bottom: 28px;
            width: 2px;
            background: #E5E7EB;
            z-index: 0;
        }
        .dtl-step {
            display: flex;
            align-items: flex-start;
            gap: 18px;
            position: relative;
            z-index: 1;
        }
        .dtl-step:not(:last-child) { margin-bottom: 2px; }

        .dtl-node {
            width: 48px; height: 48px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; font-weight: 700;
            flex-shrink: 0;
            border: 3px solid #E5E7EB;
            background: white;
            position: relative; z-index: 2;
            transition: all 0.3s ease;
        }
        .dtl-node.done     { background:#10B981;border-color:#10B981;color:white;box-shadow:0 0 0 5px rgba(16,185,129,0.12); }
        .dtl-node.active   { background:#003B5C;border-color:#003B5C;color:white;box-shadow:0 0 0 5px rgba(0,59,92,0.12);animation:dtlPulse 2s infinite; }
        .dtl-node.rejected { background:#EF4444;border-color:#EF4444;color:white;box-shadow:0 0 0 5px rgba(239,68,68,0.12); }
        .dtl-node.waiting  { background:#F9FAFB;border-color:#E5E7EB;color:#D1D5DB; }
        @keyframes dtlPulse {
            0%,100% { box-shadow:0 0 0 5px rgba(0,59,92,0.12); }
            50%      { box-shadow:0 0 0 10px rgba(0,59,92,0.05); }
        }

        .dtl-connector {
            position: absolute;
            left: 23px; top: 48px;
            width: 2px; height: 24px;
            z-index: 1;
        }
        .dtl-connector.done    { background: #10B981; }
        .dtl-connector.active  { background: linear-gradient(#10B981,#E5E7EB); }
        .dtl-connector.waiting { background: #E5E7EB; }
        .dtl-connector.rejected{ background: #EF4444; }

        .dtl-body { flex:1; padding: 10px 0 28px; min-width: 0; }
        .dtl-title {
            font-size: 14px; font-weight: 700; color: #111827;
            display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
        }
        .dtl-title.waiting { color: #9CA3AF; }
        .dtl-desc  { font-size: 13px; color: #6B7280; margin-top: 4px; line-height: 1.5; }
        .dtl-time  { font-size: 11px; font-weight: 600; margin-top: 5px; }
        .dtl-time.done     { color: #10B981; }
        .dtl-time.active   { color: #003B5C; }
        .dtl-time.rejected { color: #EF4444; }

        /* Step badge */
        .step-badge {
            display: inline-flex; align-items: center;
            padding: 2px 8px; border-radius: 20px;
            font-size: 10px; font-weight: 700;
            letter-spacing: 0.3px;
        }
        .step-badge.done     { background:#D1FAE5;color:#065F46; }
        .step-badge.active   { background:#DBEAFE;color:#1D4ED8; }
        .step-badge.rejected { background:#FEE2E2;color:#991B1B; }
        .step-badge.waiting  { background:#F3F4F6;color:#9CA3AF; }

        /* Department detail grid inside timeline */
        .dept-detail-grid { margin-top: 12px; display: flex; flex-direction: column; gap: 6px; }
        .dept-detail-row {
            display: flex; align-items: center; justify-content: space-between;
            background: #F9FAFB; border-radius: 8px; padding: 8px 12px;
            gap: 8px;
        }
        .dept-detail-name { font-size: 12px; font-weight: 600; color: #374151; flex:1; }
        .dept-detail-sub  { font-size: 11px; color: #9CA3AF; margin-top: 1px; }
        .dept-chip {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600; flex-shrink: 0;
        }
        .dept-chip.approved { background:#D1FAE5;color:#065F46; }
        .dept-chip.pending  { background:#FEF3C7;color:#92400E; }
        .dept-chip.rejected { background:#FEE2E2;color:#991B1B; }

        /* Certificate card */
        .cert-ready-card {
            background: linear-gradient(135deg,#F0FDF4,#DCFCE7);
            border: 2px solid #16A34A;
            border-radius: 14px; padding: 28px;
            text-align: center; margin-top: 4px;
        }

        /* Upload area */
        .upload-area {
            border: 2px dashed #D1D5DB;
            border-radius: 10px; padding: 16px;
            display: flex; align-items: center;
            gap: 12px; flex-wrap: wrap;
            transition: border-color 0.2s;
        }
        .upload-area:hover { border-color: #00AEEF; }
    </style>

    <div style="max-width:720px;margin:0 auto;">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert-success" style="margin-bottom:16px;">✅ {{ session('success') }}</div>
        @endif
        @if(session('info'))
            <div class="alert-success" style="margin-bottom:16px;">ℹ️ {{ session('info') }}</div>
        @endif
        @if(session('error'))
            <div class="alert-error" style="margin-bottom:16px;">❌ {{ session('error') }}</div>
        @endif

        @if(!$application)
            <div class="card card-p" style="text-align:center;padding:56px 24px;">
                <div style="font-size:52px;margin-bottom:14px;">📋</div>
                <div class="section-title" style="font-size:17px;">No Application Yet</div>
                <p style="font-size:13px;color:#6B7280;margin:8px 0 24px;">
                    Hello <strong>{{ $student->full_name ?? $student->user->name }}</strong>,
                    you haven't submitted an application yet.
                </p>
                <a href="{{ route('student.clearance.create') }}" class="btn-primary"
                   style="display:inline-flex;">
                    + Submit New Application
                </a>
            </div>

        @else
            @php
                $sv    = $application->status->value;
                $depts = $application->departmentClearances;
                $approvedDepts = $depts->filter(fn($d) => $d->status->value === 'approved');
                $rejectedDepts = $depts->filter(fn($d) => $d->status->value === 'rejected');
                $pendingDepts  = $depts->filter(fn($d) => $d->status->value === 'pending');
                $total    = $depts->count();
                $progress = $application->progressPercentage();

                $typeLabels = [
                    'graduation' => '🎓 Graduation Clearance',
                    'deferral'   => '📅 Deferral of Studies',
                    'transfer'   => '🔄 Transfer to Another Institution',
                    'withdrawal' => '🚪 Withdrawal from University',
                    'other'      => '📝 Other',
                ];

                $step2 = match(true) {
                    $sv === 'rejected' && $rejectedDepts->count() > 0 => 'rejected',
                    in_array($sv, ['awaiting_registrar','approved'])   => 'done',
                    $approvedDepts->count() > 0                        => 'active',
                    default                                            => 'active',
                };
                $step3 = match($sv) {
                    'awaiting_registrar' => 'active',
                    'approved'           => 'done',
                    default              => 'waiting',
                };
                $step4 = ($sv === 'approved' && $application->certificate) ? 'done' : 'waiting';
            @endphp

            {{-- Application header card --}}
            <div class="card card-p" style="margin-bottom:16px;
                 background:linear-gradient(135deg,#003B5C,#005a8e);border:none;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:16px;font-weight:700;color:white;">
                            {{ $typeLabels[$application->application_type ?? 'graduation'] ?? 'Application' }}
                        </div>
                        <div style="font-size:12px;color:rgba(255,255,255,0.7);margin-top:4px;">
                            {{ $application->student_full_name }} ·
                            Academic Year {{ $application->academic_year }}
                        </div>
                        <div style="font-size:11px;color:rgba(255,255,255,0.5);margin-top:2px;">
                            Submitted {{ $application->submitted_at?->format('d M Y, g:i A') ?? $application->created_at->format('d M Y') }}
                        </div>
                    </div>
                    @if($sv === 'awaiting_registrar')
                        <span style="background:rgba(255,255,255,0.2);color:white;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700;">📬 Awaiting Registrar</span>
                    @elseif($sv === 'approved')
                        <span style="background:#D1FAE5;color:#065F46;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700;">✅ Fully Approved</span>
                    @elseif($sv === 'rejected')
                        <span style="background:#FEE2E2;color:#991B1B;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700;">❌ Rejected</span>
                    @else
                        <span style="background:rgba(255,255,255,0.2);color:white;padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700;">⏳ In Progress</span>
                    @endif
                </div>

                {{-- Progress bar --}}
                <div style="margin-top:16px;">
                    <div style="display:flex;justify-content:space-between;font-size:11px;
                                color:rgba(255,255,255,0.6);margin-bottom:6px;">
                        <span>Department clearance progress</span>
                        <span>{{ $approvedDepts->count() }}/{{ $total }} departments</span>
                    </div>
                    <div style="background:rgba(255,255,255,0.2);height:6px;border-radius:3px;overflow:hidden;">
                        <div style="height:100%;border-radius:3px;background:white;
                                    width:{{ $progress }}%;transition:width 0.5s ease;"></div>
                    </div>
                </div>
            </div>

            {{-- ══ DETAILED TIMELINE ══ --}}
            <div class="card card-p" style="margin-bottom:16px;">
                <div class="section-title">Application Timeline</div>

                <div class="dtl-timeline">

                    {{-- STEP 1: Submitted --}}
                    <div class="dtl-step">
                        <div style="position:relative;flex-shrink:0;">
                            <div class="dtl-node done">✓</div>
                            <div class="dtl-connector done"></div>
                        </div>
                        <div class="dtl-body">
                            <div class="dtl-title">
                                Application Submitted
                                <span class="step-badge done">DONE</span>
                            </div>
                            <div class="dtl-desc">
                                Your application was submitted and sent to {{ $total }} department(s) for review.
                            </div>
                            <div class="dtl-time done">
                                {{ $application->submitted_at?->format('d M Y, g:i A') ?? $application->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: Department Reviews --}}
                    <div class="dtl-step">
                        <div style="position:relative;flex-shrink:0;">
                            <div class="dtl-node {{ $step2 }}">
                                @if($step2 === 'done') ✓
                                @elseif($step2 === 'rejected') ✕
                                @else ●
                                @endif
                            </div>
                            <div class="dtl-connector {{ in_array($step2,['done','active']) ? $step2 : 'waiting' }}"></div>
                        </div>
                        <div class="dtl-body">
                            <div class="dtl-title {{ $step2 === 'waiting' ? 'waiting' : '' }}">
                                Department Reviews
                                <span class="step-badge {{ $step2 }}">
                                    @if($step2 === 'done') CLEARED
                                    @elseif($step2 === 'rejected') REJECTED
                                    @else IN PROGRESS
                                    @endif
                                </span>
                            </div>
                            <div class="dtl-desc">
                                @if($step2 === 'done')
                                    All departments have cleared your application.
                                @elseif($step2 === 'rejected')
                                    One or more departments have rejected your application. Resolve the issue(s) below.
                                @else
                                    {{ $pendingDepts->count() }} department(s) still reviewing.
                                    {{ $approvedDepts->count() }} of {{ $total }} have approved.
                                @endif
                            </div>

                            {{-- Per-department breakdown --}}
                            <div class="dept-detail-grid">
                                @foreach($depts as $dept)
                                    @php $ds = $dept->status->value; @endphp
                                    <div class="dept-detail-row">
                                        <div style="flex:1;min-width:0;">
                                            <div class="dept-detail-name">{{ $dept->department->name }}</div>
                                            @if($dept->remarks)
                                                <div class="dept-detail-sub">{{ $dept->remarks }}</div>
                                            @endif
                                            @if($dept->reviewed_at)
                                                <div class="dept-detail-sub">
                                                    Reviewed {{ $dept->reviewed_at->format('d M Y') }}
                                                </div>
                                            @endif
                                        </div>
                                        <span class="dept-chip {{ $ds }}">
                                            @if($ds === 'approved') ✓ Approved
                                            @elseif($ds === 'rejected') ✕ Rejected
                                            @else ⏳ Pending
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if($step2 === 'done')
                                <div class="dtl-time done">All {{ $total }} departments cleared ✓</div>
                            @elseif($step2 === 'rejected')
                                <div class="dtl-time rejected">Action required — resolve issues and reapply</div>
                            @else
                                <div class="dtl-time active">{{ $progress }}% complete</div>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 3: Registrar --}}
                    <div class="dtl-step">
                        <div style="position:relative;flex-shrink:0;">
                            <div class="dtl-node {{ $step3 }}">
                                @if($step3 === 'done') ✓
                                @elseif($step3 === 'active') ●
                                @else ○
                                @endif
                            </div>
                            <div class="dtl-connector {{ $step3 === 'done' ? 'done' : ($step3 === 'active' ? 'active' : 'waiting') }}"></div>
                        </div>
                        <div class="dtl-body">
                            <div class="dtl-title {{ $step3 === 'waiting' ? 'waiting' : '' }}">
                                Academic Registrar Sign-off
                                <span class="step-badge {{ $step3 }}">
                                    @if($step3 === 'done') APPROVED
                                    @elseif($step3 === 'active') UNDER REVIEW
                                    @else WAITING
                                    @endif
                                </span>
                            </div>
                            <div class="dtl-desc">
                                @if($step3 === 'active')
                                    Your application is with the Academic Registrar for final sign-off.
                                    You will be notified by email once a decision is made.
                                @elseif($step3 === 'done')
                                    The Academic Registrar has approved your application.
                                    Your certificate is being prepared.
                                @else
                                    The Registrar will review your application after all departments clear it.
                                @endif
                            </div>
                            @if($step3 === 'active')
                                <div class="dtl-time active">📬 Awaiting registrar decision</div>
                            @elseif($step3 === 'done')
                                <div class="dtl-time done">Registrar approved ✓</div>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 4: Certificate --}}
                    <div class="dtl-step">
                        <div style="position:relative;flex-shrink:0;">
                            <div class="dtl-node {{ $step4 }}">
                                @if($step4 === 'done') 🎓
                                @else ○
                                @endif
                            </div>
                        </div>
                        <div class="dtl-body" style="padding-bottom:8px;">
                            <div class="dtl-title {{ $step4 === 'waiting' ? 'waiting' : '' }}">
                                Certificate Issued
                                <span class="step-badge {{ $step4 }}">
                                    @if($step4 === 'done') READY @else PENDING @endif
                                </span>
                            </div>
                            <div class="dtl-desc">
                                @if($step4 === 'done')
                                    Your clearance certificate has been issued and is ready for download.
                                @else
                                    Your certificate will be issued after the Registrar approves.
                                @endif
                            </div>
                            @if($step4 === 'done' && $application->certificate)
                                <div class="dtl-time done">
                                    Issued {{ $application->certificate->issued_at->format('d F Y') }}
                                </div>
                            @endif
                        </div>
                    </div>

                </div>{{-- end dtl-timeline --}}
            </div>

            {{-- ══ CERTIFICATE DOWNLOAD ══ --}}
            @if($application->certificate)
                <div class="cert-ready-card" style="margin-bottom:16px;">
                    <div style="font-size:44px;margin-bottom:10px;">🎓</div>
                    <div style="font-size:19px;font-weight:700;color:#14532D;margin-bottom:6px;">
                        Your Certificate is Ready!
                    </div>
                    <div style="font-size:13px;color:#166534;margin-bottom:4px;">
                        Certificate No: <strong>{{ $application->certificate->certificate_number }}</strong>
                    </div>
                    <div style="font-size:12px;color:#4B7C59;margin-bottom:22px;">
                        Issued {{ $application->certificate->issued_at->format('d F Y') }} · Approved by Academic Registrar
                    </div>
                    <a href="{{ route('student.certificate.download', $application->certificate) }}"
                       class="btn-primary"
                       style="background:#16A34A;font-size:14px;padding:13px 32px;display:inline-flex;">
                        ⬇ Download Certificate (PDF)
                    </a>
                    <div style="font-size:11px;color:#4B7C59;margin-top:12px;">
                        This certificate is digitally verifiable. Keep it safe.
                    </div>
                </div>

            @elseif($sv === 'rejected')
                <div class="card card-p" style="background:#FEF2F2;border:1px solid #FECACA;
                            text-align:center;margin-bottom:16px;">
                    <div style="font-size:32px;margin-bottom:10px;">❌</div>
                    <div style="font-size:15px;font-weight:700;color:#991B1B;margin-bottom:6px;">
                        Application Rejected
                    </div>
                    <div style="font-size:13px;color:#B91C1C;margin-bottom:16px;">
                        Please resolve the issues listed above and submit a new application.
                    </div>
                    <a href="{{ route('student.clearance.create') }}" class="btn-primary"
                       style="display:inline-flex;background:#DC2626;">
                        Submit New Application
                    </a>
                </div>
            @endif

            {{-- ══ SUPPORTING DOCUMENTS ══ --}}
            <div class="card card-p">
                <div class="section-title">Supporting Documents</div>

                <form method="POST" action="{{ route('student.clearance.documents.upload') }}"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="upload-area" style="margin-bottom:14px;">
                        <select name="department_id" class="form-input" style="width:auto;min-width:160px;flex-shrink:0;">
                            <option value="">General document</option>
                            @foreach($application->departmentClearances as $clearance)
                                <option value="{{ $clearance->department->id }}">
                                    {{ $clearance->department->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="file" name="document" required
                               style="font-size:13px;flex:1;min-width:0;">
                        <button type="submit" class="btn-primary" style="flex-shrink:0;">
                            Upload
                        </button>
                    </div>
                </form>

                @error('document')
                    <p style="color:#DC2626;font-size:12px;margin-bottom:10px;">{{ $message }}</p>
                @enderror

                @if($application->documents->isNotEmpty())
                    @foreach($application->documents as $doc)
                        <div style="display:flex;align-items:center;justify-content:space-between;
                                    padding:10px 0;border-bottom:1px solid #F3F4F6;">
                            <span style="font-size:13px;color:#374151;">
                                📄 {{ $doc->original_name }}
                                @if($doc->department)
                                    <span style="font-size:11px;color:#9CA3AF;">({{ $doc->department->name }})</span>
                                @endif
                            </span>
                            <a href="{{ route('student.clearance.documents.download', $doc) }}"
                               style="font-size:12px;color:#00AEEF;font-weight:600;">
                                Download
                            </a>
                        </div>
                    @endforeach
                @else
                    <p style="font-size:13px;color:#9CA3AF;">No documents uploaded yet.</p>
                @endif
            </div>

        @endif
    </div>
</x-app-layout>