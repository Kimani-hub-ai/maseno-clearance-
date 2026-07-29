<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        {{ $valid ? 'Certificate Verified ✓' : 'Certificate Not Found' }}
        — Maseno University
    </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, system-ui, sans-serif;
            min-height: 100vh;
            background: {{ $valid ? 'linear-gradient(135deg,#003B5C 0%,#005a8e 50%,#00AEEF 100%)' : 'linear-gradient(135deg,#1F2937,#374151)' }};
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 24px;
        }

        .result-card {
            background: white; border-radius: 20px;
            padding: 0; width: 100%; max-width: 540px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        /* ── Result header ── */
        .result-header {
            padding: 32px 32px 24px;
            text-align: center;
            background: {{ $valid ? 'linear-gradient(135deg,#F0FDF4,#DCFCE7)' : 'linear-gradient(135deg,#FEF2F2,#FEE2E2)' }};
            border-bottom: 1px solid {{ $valid ? '#BBF7D0' : '#FECACA' }};
        }
        .result-icon {
            font-size: 56px; display: block; margin-bottom: 14px;
            animation: iconPop 0.5s cubic-bezier(0.34,1.56,0.64,1);
        }
        @keyframes iconPop {
            from { transform: scale(0) rotate(-10deg); opacity: 0; }
            to   { transform: scale(1) rotate(0); opacity: 1; }
        }
        .result-title {
            font-size: 22px; font-weight: 800;
            color: {{ $valid ? '#14532D' : '#991B1B' }};
            margin-bottom: 6px;
        }
        .result-sub {
            font-size: 14px;
            color: {{ $valid ? '#166534' : '#B91C1C' }};
            line-height: 1.5;
        }

        /* Valid badge */
        .valid-badge {
            display: inline-flex; align-items: center; gap: 6px;
            margin-top: 14px; padding: 6px 18px;
            border-radius: 20px; font-size: 12px; font-weight: 700;
            letter-spacing: 0.5px;
            background: #16A34A; color: white;
            animation: badgeSlide 0.4s ease 0.3s both;
        }
        @keyframes badgeSlide {
            from { transform: translateY(10px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }

        /* ── Details body ── */
        .result-body { padding: 28px 32px; }

        .detail-section { margin-bottom: 24px; }
        .detail-section-title {
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
            color: #9CA3AF; margin-bottom: 12px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .detail-item {
            background: #F9FAFB; border-radius: 10px;
            padding: 12px 14px;
        }
        .detail-item.full { grid-column: 1 / -1; }
        .detail-label {
            font-size: 11px; font-weight: 600;
            color: #6B7280; text-transform: uppercase;
            letter-spacing: 0.3px; margin-bottom: 4px;
        }
        .detail-value {
            font-size: 14px; font-weight: 700; color: #111827;
        }
        .detail-value.cert-no {
            font-size: 13px; color: #003B5C;
            font-family: monospace; letter-spacing: 0.5px;
        }

        /* Departments cleared */
        .dept-grid {
            display: flex; flex-wrap: wrap; gap: 6px;
            margin-top: 4px;
        }
        .dept-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
            background: #D1FAE5; color: #065F46;
        }

        /* ── Footer actions ── */
        .result-footer {
            padding: 20px 32px 28px;
            display: flex; flex-direction: column; gap: 10px;
        }
        .btn-verify-another {
            display: flex; align-items: center; justify-content: center;
            gap: 8px; padding: 13px;
            background: #003B5C; color: white;
            border: none; border-radius: 12px;
            font-size: 14px; font-weight: 700;
            cursor: pointer; text-decoration: none;
            transition: background 0.15s;
        }
        .btn-verify-another:hover { background: #002a42; }
        .btn-home {
            display: flex; align-items: center; justify-content: center;
            gap: 8px; padding: 13px;
            background: #F3F4F6; color: #374151;
            border: none; border-radius: 12px;
            font-size: 14px; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: background 0.15s;
        }
        .btn-home:hover { background: #E5E7EB; }

        /* ── Invalid state ── */
        .invalid-tips {
            background: #FEF3C7; border: 1px solid #FDE68A;
            border-radius: 12px; padding: 16px 18px;
            margin-bottom: 20px;
        }
        .invalid-tips-title {
            font-size: 13px; font-weight: 700;
            color: #92400E; margin-bottom: 8px;
        }
        .invalid-tips ul {
            font-size: 13px; color: #78350F;
            padding-left: 16px; line-height: 2;
        }

        /* Maseno footer */
        .maseno-footer {
            text-align: center; margin-top: 24px;
            font-size: 12px; color: rgba(255,255,255,0.6);
        }

        @media (max-width: 520px) {
            .result-card { border-radius: 16px; }
            .result-header { padding: 24px 20px 18px; }
            .result-body { padding: 20px; }
            .result-footer { padding: 16px 20px 24px; }
            .detail-grid { grid-template-columns: 1fr; }
            .detail-item.full { grid-column: 1; }
            .result-title { font-size: 18px; }
        }
    </style>
</head>
<body>

    <div class="result-card">

        @if($valid)
        {{-- ════════════════════════
             VALID CERTIFICATE
        ════════════════════════ --}}
        <div class="result-header">
            <span class="result-icon">✅</span>
            <div class="result-title">Certificate Verified</div>
            <div class="result-sub">
                This is a genuine Maseno University clearance certificate.
            </div>
            <div class="valid-badge">
                🔒 AUTHENTIC · VERIFIED BY MASENO UNIVERSITY
            </div>
        </div>

        <div class="result-body">

            {{-- Certificate details --}}
            <div class="detail-section">
                <div class="detail-section-title">Certificate Details</div>
                <div class="detail-grid">
                    <div class="detail-item full">
                        <div class="detail-label">Certificate Number</div>
                        <div class="detail-value cert-no">{{ $certificate->certificate_number }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Date Issued</div>
                        <div class="detail-value">{{ $certificate->issued_at->format('d F Y') }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Academic Year</div>
                        <div class="detail-value">{{ $application->academic_year }}</div>
                    </div>
                </div>
            </div>

            {{-- Student details --}}
            <div class="detail-section">
                <div class="detail-section-title">Student Information</div>
                <div class="detail-grid">
                    <div class="detail-item full">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value" style="font-size:16px;">
                            {{ strtoupper($student->full_name) }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Registration No.</div>
                        <div class="detail-value cert-no">{{ $student->reg_number }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Application Type</div>
                        <div class="detail-value">
                            @php
                                $typeLabels = [
                                    'graduation' => 'Graduation Clearance',
                                    'deferral'   => 'Deferral of Studies',
                                    'transfer'   => 'Transfer',
                                    'withdrawal' => 'Withdrawal',
                                    'other'      => 'Other',
                                ];
                            @endphp
                            {{ $typeLabels[$application->application_type ?? 'graduation'] ?? 'Clearance' }}
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Faculty</div>
                        <div class="detail-value">{{ $student->faculty }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Programme</div>
                        <div class="detail-value">{{ $student->programme }}</div>
                    </div>
                </div>
            </div>

            {{-- Departments cleared --}}
            <div class="detail-section" style="margin-bottom:0;">
                <div class="detail-section-title">Cleared by Departments</div>
                <div class="dept-grid">
                    @foreach($application->departmentClearances as $clearance)
                        <span class="dept-pill">
                            ✓ {{ $clearance->department->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        @else
        {{-- ════════════════════════
             INVALID / NOT FOUND
        ════════════════════════ --}}
        <div class="result-header">
            <span class="result-icon">❌</span>
            <div class="result-title">Certificate Not Found</div>
            <div class="result-sub">
                We could not find a certificate matching that number or token
                in our records.
            </div>
        </div>

        <div class="result-body">
            <div class="invalid-tips">
                <div class="invalid-tips-title">⚠️ Please check the following:</div>
                <ul>
                    <li>The certificate number is entered correctly</li>
                    <li>There are no extra spaces before or after</li>
                    <li>The format is MAS-CLR-YYYY-NNNNN</li>
                    <li>The certificate was issued by Maseno University</li>
                </ul>
            </div>
            <p style="font-size:13px;color:#6B7280;line-height:1.6;">
                If you believe this is an error, please contact the
                <strong>Office of the Academic Registrar</strong> at
                Maseno University for assistance.
            </p>
        </div>
        @endif

        {{-- Footer actions --}}
        <div class="result-footer">
            <a href="{{ route('public.certificate.lookup') }}" class="btn-verify-another">
                🔍 Verify Another Certificate
            </a>
            <a href="{{ route('home') }}" class="btn-home">
                ← Back to Home
            </a>
        </div>
    </div>

    <div class="maseno-footer">
        Maseno University · Office of the Academic Registrar<br>
        Certificate Verification System · © {{ date('Y') }}
    </div>

</body>
</html>