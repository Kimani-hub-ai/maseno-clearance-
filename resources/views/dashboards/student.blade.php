<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <style>
        /* ── Timeline ── */
        .timeline-wrap {
            position: relative;
            padding: 8px 0;
        }

        /* Vertical connector line */
        .timeline-wrap::before {
            content: '';
            position: absolute;
            left: 19px;
            top: 24px;
            bottom: 24px;
            width: 2px;
            background: #E5E7EB;
            z-index: 0;
        }

        .timeline-step {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            position: relative;
            z-index: 1;
            margin-bottom: 0;
        }
        .timeline-step:not(:last-child) {
            margin-bottom: 4px;
        }

        /* Circle node */
        .tl-node {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            border: 3px solid #E5E7EB;
            background: white;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }
        .tl-node.done {
            background: #10B981;
            border-color: #10B981;
            color: white;
            box-shadow: 0 0 0 4px rgba(16,185,129,0.15);
        }
        .tl-node.active {
            background: #003B5C;
            border-color: #003B5C;
            color: white;
            box-shadow: 0 0 0 4px rgba(0,59,92,0.15);
            animation: pulse 2s infinite;
        }
        .tl-node.rejected {
            background: #EF4444;
            border-color: #EF4444;
            color: white;
            box-shadow: 0 0 0 4px rgba(239,68,68,0.15);
        }
        .tl-node.waiting {
            background: #F9FAFB;
            border-color: #E5E7EB;
            color: #D1D5DB;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 4px rgba(0,59,92,0.15); }
            50%       { box-shadow: 0 0 0 8px rgba(0,59,92,0.08); }
        }

        /* Connector line between nodes */
        .tl-connector {
            position: absolute;
            left: 19px;
            top: 40px;
            width: 2px;
            height: 20px;
            z-index: 1;
        }
        .tl-connector.done    { background: #10B981; }
        .tl-connector.active  { background: linear-gradient(#10B981, #E5E7EB); }
        .tl-connector.waiting { background: #E5E7EB; }
        .tl-connector.rejected{ background: #EF4444; }

        .tl-content {
            flex: 1;
            padding: 8px 0 20px;
        }
        .tl-title {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
        }
        .tl-title.waiting { color: #9CA3AF; }
        .tl-desc {
            font-size: 12px;
            color: #6B7280;
            line-height: 1.4;
        }
        .tl-date {
            font-size: 11px;
            color: #10B981;
            font-weight: 600;
            margin-top: 3px;
        }
        .tl-date.rejected { color: #EF4444; }
        .tl-date.active   { color: #003B5C; }

        /* Department mini-progress inside timeline */
        .tl-dept-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 8px;
        }
        .tl-dept-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }
        .tl-dept-pill.approved { background: #D1FAE5; color: #065F46; }
        .tl-dept-pill.pending  { background: #F3F4F6; color: #6B7280; }
        .tl-dept-pill.rejected { background: #FEE2E2; color: #991B1B; }

        /* Identity card */
        .identity-card {
            background: linear-gradient(135deg, #003B5C 0%, #005a8e 100%);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }
        .id-avatar {
            width: 60px; height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            border: 3px solid rgba(255,255,255,0.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; font-weight: 700; color: white;
            flex-shrink: 0;
        }
        .id-name  { font-size: 18px; font-weight: 700; color: white; }
        .id-reg   { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 3px; }
        .id-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
        .id-pill  {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px; font-weight: 600;
            background: rgba(255,255,255,0.15);
            color: white;
        }

        /* Quick stats row */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .qs-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            text-decoration: none;
            display: block;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .qs-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .qs-icon  { font-size: 22px; margin-bottom: 6px; }
        .qs-val   { font-size: 20px; font-weight: 700; color: #003B5C; }
        .qs-lbl   { font-size: 11px; color: #6B7280; margin-top: 2px; }

        @media (max-width: 768px) {
            .identity-card { padding: 18px; gap: 14px; }
            .id-name { font-size: 16px; }
            .quick-stats { grid-template-columns: repeat(3, 1fr); gap: 8px; }
            .qs-card { padding: 12px 8px; }
            .qs-val { font-size: 18px; }
        }
    </style>

    <div style="max-width:700px;margin:0 auto;">

        {{-- ══════════════════════════════════
             IDENTITY CARD
        ══════════════════════════════════ --}}
        <div class="identity-card">
            <div class="id-avatar">{{ strtoupper(substr($student->full_name ?? $student->user->name, 0, 2)) }}</div>
            <div style="flex:1;min-width:0;">
                <div class="id-name">{{ $student->full_name ?? $student->user->name }}</div>
                <div class="id-reg">{{ $student->reg_number }}</div>
                <div class="id-pills">
                    <span class="id-pill">{{ $student->faculty }}</span>
                    <span class="id-pill">{{ $student->programme }}</span>
                    <span class="id-pill">{{ $student->graduation_year }}</span>
                    @if($student->phone)
                        <span class="id-pill">📱 {{ $student->phone }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════
             QUICK STATS
        ══════════════════════════════════ --}}
        @php
            $totalApps = $student->clearanceApplications()->count();
            $approved  = $student->clearanceApplications()->where('status','approved')->count();
            $pending   = $student->clearanceApplications()->whereIn('status',['pending','awaiting_registrar'])->count();
        @endphp
        <div class="quick-stats">
            <div class="qs-card">
                <div class="qs-icon">📋</div>
                <div class="qs-val">{{ $totalApps }}</div>
                <div class="qs-lbl">Total Applications</div>
            </div>
            <div class="qs-card">
                <div class="qs-icon">✅</div>
                <div class="qs-val" style="color:#10B981;">{{ $approved }}</div>
                <div class="qs-lbl">Approved</div>
            </div>
            <div class="qs-card">
                <div class="qs-icon">⏳</div>
                <div class="qs-val" style="color:#F59E0B;">{{ $pending }}</div>
                <div class="qs-lbl">In Progress</div>
            </div>
        </div>

        {{-- ══════════════════════════════════
             APPLICATION TIMELINE
        ══════════════════════════════════ --}}
        <div class="card card-p" style="margin-bottom:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:8px;">
                <div class="section-title" style="margin-bottom:0;">Application Progress</div>
                <a href="{{ route('student.clearance.index') }}"
                   style="font-size:12px;font-weight:600;color:#00AEEF;text-decoration:none;">
                    Full details →
                </a>
            </div>

            @if(!$latestApplication)
                {{-- No application --}}
                <div style="text-align:center;padding:32px 0;">
                    <div style="font-size:40px;margin-bottom:12px;">📋</div>
                    <div style="font-size:14px;font-weight:600;color:#374151;margin-bottom:6px;">No application yet</div>
                    <div style="font-size:13px;color:#6B7280;margin-bottom:20px;">
                        Submit your first application to get started.
                    </div>
                    <a href="{{ route('student.clearance.create') }}" class="btn-primary"
                       style="display:inline-flex;">
                        + New Application
                    </a>
                </div>
            @else
                @php
                    $sv       = $latestApplication->status->value;
                    $depts    = $latestApplication->departmentClearances;
                    $approved = $depts->filter(fn($d) => $d->status->value === 'approved')->count();
                    $rejected = $depts->filter(fn($d) => $d->status->value === 'rejected')->count();
                    $total    = $depts->count();
                    $progress = $latestApplication->progressPercentage();

                    // Determine which step is active
                    $step1 = 'done'; // always done if application exists
                    $step2 = match(true) {
                        $sv === 'rejected' && $rejected > 0 => 'rejected',
                        $sv === 'awaiting_registrar'        => 'done',
                        $sv === 'approved'                  => 'done',
                        $approved > 0                       => 'active',
                        default                             => 'active',
                    };
                    $step3 = match(true) {
                        $sv === 'awaiting_registrar' => 'active',
                        $sv === 'approved'           => 'done',
                        default                      => 'waiting',
                    };
                    $step4 = match(true) {
                        $sv === 'approved' && $latestApplication->certificate => 'done',
                        default => 'waiting',
                    };
                @endphp

                {{-- Type & year --}}
                @php
                    $typeLabels = [
                        'graduation' => '🎓 Graduation Clearance',
                        'deferral'   => '📅 Deferral of Studies',
                        'transfer'   => '🔄 Transfer',
                        'withdrawal' => '🚪 Withdrawal',
                        'other'      => '📝 Other',
                    ];
                @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;
                            background:#F9FAFB;border-radius:10px;padding:12px 14px;
                            margin-bottom:24px;flex-wrap:wrap;gap:8px;">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#111827;">
                            {{ $typeLabels[$latestApplication->application_type ?? 'graduation'] ?? 'Application' }}
                        </div>
                        <div style="font-size:11px;color:#6B7280;margin-top:2px;">
                            Academic Year {{ $latestApplication->academic_year }}
                            · Submitted {{ $latestApplication->submitted_at?->format('d M Y') ?? $latestApplication->created_at->format('d M Y') }}
                        </div>
                    </div>
                    @if($sv === 'awaiting_registrar')
                        <span style="background:#DBEAFE;color:#1D4ED8;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;">📬 Awaiting Registrar</span>
                    @elseif($sv === 'approved')
                        <span style="background:#D1FAE5;color:#065F46;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;">✅ Approved</span>
                    @elseif($sv === 'rejected')
                        <span style="background:#FEE2E2;color:#991B1B;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;">❌ Rejected</span>
                    @else
                        <span style="background:#FEF3C7;color:#92400E;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;">⏳ In Progress</span>
                    @endif
                </div>

                {{-- TIMELINE --}}
                <div class="timeline-wrap">

                    {{-- STEP 1: Application Submitted --}}
                    <div class="timeline-step">
                        <div style="position:relative;flex-shrink:0;">
                            <div class="tl-node done">✓</div>
                            <div class="tl-connector done"></div>
                        </div>
                        <div class="tl-content">
                            <div class="tl-title">Application Submitted</div>
                            <div class="tl-desc">Your application was received and forwarded to all departments.</div>
                            <div class="tl-date">
                                {{ $latestApplication->submitted_at?->format('d M Y, g:i A') ?? $latestApplication->created_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: Department Reviews --}}
                    <div class="timeline-step">
                        <div style="position:relative;flex-shrink:0;">
                            <div class="tl-node {{ $step2 }}">
                                @if($step2 === 'done') ✓
                                @elseif($step2 === 'rejected') ✕
                                @elseif($step2 === 'active') ●
                                @else ○
                                @endif
                            </div>
                            <div class="tl-connector {{ $step2 === 'done' ? 'done' : ($step2 === 'active' ? 'active' : 'waiting') }}"></div>
                        </div>
                        <div class="tl-content">
                            <div class="tl-title {{ $step2 === 'waiting' ? 'waiting' : '' }}">
                                Department Reviews
                                <span style="font-size:11px;font-weight:400;color:#6B7280;margin-left:4px;">
                                    ({{ $approved }}/{{ $total }} cleared)
                                </span>
                            </div>
                            <div class="tl-desc">
                                @if($step2 === 'done')
                                    All {{ $total }} departments have approved your application.
                                @elseif($step2 === 'rejected')
                                    One or more departments have rejected your application.
                                @else
                                    {{ $total - $approved - $rejected }} department(s) still reviewing.
                                @endif
                            </div>

                            {{-- Department pills --}}
                            @if($depts->isNotEmpty())
                                <div class="tl-dept-grid">
                                    @foreach($depts as $dept)
                                        <span class="tl-dept-pill {{ $dept->status->value }}">
                                            @if($dept->status->value === 'approved') ✓
                                            @elseif($dept->status->value === 'rejected') ✕
                                            @else ●
                                            @endif
                                            {{ $dept->department->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if($step2 === 'done')
                                <div class="tl-date">All departments cleared ✓</div>
                            @elseif($step2 === 'rejected')
                                <div class="tl-date rejected">Rejected — please resolve issues and reapply</div>
                            @elseif($step2 === 'active')
                                <div class="tl-date active">In progress — {{ $progress }}% complete</div>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 3: Registrar Review --}}
                    <div class="timeline-step">
                        <div style="position:relative;flex-shrink:0;">
                            <div class="tl-node {{ $step3 }}">
                                @if($step3 === 'done') ✓
                                @elseif($step3 === 'active') ●
                                @else ○
                                @endif
                            </div>
                            <div class="tl-connector {{ $step3 === 'done' ? 'done' : ($step3 === 'active' ? 'active' : 'waiting') }}"></div>
                        </div>
                        <div class="tl-content">
                            <div class="tl-title {{ $step3 === 'waiting' ? 'waiting' : '' }}">
                                Academic Registrar Review
                            </div>
                            <div class="tl-desc">
                                @if($step3 === 'active')
                                    Your application is with the Academic Registrar for final sign-off.
                                @elseif($step3 === 'done')
                                    The Academic Registrar has approved your application.
                                @else
                                    Pending completion of all department reviews.
                                @endif
                            </div>
                            @if($step3 === 'active')
                                <div class="tl-date active">📬 Under registrar review</div>
                            @elseif($step3 === 'done')
                                <div class="tl-date">Registrar approved ✓</div>
                            @endif
                        </div>
                    </div>

                    {{-- STEP 4: Certificate Issued --}}
                    <div class="timeline-step">
                        <div style="position:relative;flex-shrink:0;">
                            <div class="tl-node {{ $step4 }}">
                                @if($step4 === 'done') 🎓
                                @else ○
                                @endif
                            </div>
                        </div>
                        <div class="tl-content" style="padding-bottom:8px;">
                            <div class="tl-title {{ $step4 === 'waiting' ? 'waiting' : '' }}">
                                Certificate Issued
                            </div>
                            <div class="tl-desc">
                                @if($step4 === 'done')
                                    Your clearance certificate is ready to download!
                                @else
                                    Your certificate will be issued after registrar approval.
                                @endif
                            </div>
                            @if($step4 === 'done' && $latestApplication->certificate)
                                <div class="tl-date">
                                    Issued {{ $latestApplication->certificate->issued_at->format('d M Y') }}
                                </div>
                                <a href="{{ route('student.certificate.download', $latestApplication->certificate) }}"
                                   class="btn-primary"
                                   style="display:inline-flex;margin-top:10px;background:#10B981;font-size:12px;padding:8px 16px;">
                                    ⬇ Download Certificate
                                </a>
                            @endif
                        </div>
                    </div>

                </div>{{-- end timeline --}}

                @if($sv === 'rejected')
                    <div style="margin-top:16px;padding:14px 16px;background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;">
                        <div style="font-size:13px;font-weight:600;color:#991B1B;margin-bottom:4px;">Application Rejected</div>
                        <div style="font-size:12px;color:#B91C1C;">
                            Please resolve any outstanding issues with the relevant departments, then submit a new application.
                        </div>
                        <a href="{{ route('student.clearance.create') }}" class="btn-primary"
                           style="display:inline-flex;margin-top:12px;background:#DC2626;font-size:12px;padding:8px 18px;">
                            Submit New Application
                        </a>
                    </div>
                @endif
            @endif
        </div>

        {{-- ══════════════════════════════════
             QUICK ACTIONS
        ══════════════════════════════════ --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <a href="{{ route('student.clearance.create') }}" class="card card-p"
               style="display:flex;align-items:center;gap:12px;text-decoration:none;transition:transform 0.15s,box-shadow 0.15s;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';"
               onmouseout="this.style.transform='';this.style.boxShadow='';">
                <div style="width:40px;height:40px;border-radius:10px;background:#EFF6FF;
                            display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                    ➕
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:#111827;">New Application</div>
                    <div style="font-size:11px;color:#6B7280;margin-top:1px;">Start a new request</div>
                </div>
            </a>

            <a href="{{ route('profile.edit') }}" class="card card-p"
               style="display:flex;align-items:center;gap:12px;text-decoration:none;transition:transform 0.15s,box-shadow 0.15s;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';"
               onmouseout="this.style.transform='';this.style.boxShadow='';">
                <div style="width:40px;height:40px;border-radius:10px;background:#F0FDF4;
                            display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                    👤
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:#111827;">My Profile</div>
                    <div style="font-size:11px;color:#6B7280;margin-top:1px;">Update your details</div>
                </div>
            </a>
        </div>

    </div>
</x-app-layout>