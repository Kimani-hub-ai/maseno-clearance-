<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Clearance Certificate — {{ $certificate->certificate_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "Times New Roman", Times, serif;
            background: #ffffff;
            color: #1a1a2e;
            width: 297mm;
            min-height: 210mm;
            padding: 0;
        }

        /* ── Outer border frame ── */
        .outer-border {
            position: fixed;
            top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
            border: 3px solid #003B5C;
        }
        .inner-border {
            position: fixed;
            top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
            border: 1px solid #00AEEF;
        }

        /* ── Page content ── */
        .page {
            padding: 18mm 20mm;
            min-height: 210mm;
            position: relative;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            border-bottom: 2px solid #003B5C;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .university-name {
            font-size: 24pt;
            font-weight: bold;
            color: #003B5C;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .university-sub {
            font-size: 11pt;
            color: #00AEEF;
            margin-top: 2px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 18pt;
            font-weight: bold;
            color: #003B5C;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .doc-subtitle {
            font-size: 10pt;
            color: #555;
            margin-top: 4px;
        }

        /* ── Certificate body ── */
        .body-text {
            font-size: 12pt;
            line-height: 1.8;
            text-align: center;
            margin: 14px 20px;
            color: #1a1a2e;
        }
        .student-name {
            font-size: 20pt;
            font-weight: bold;
            color: #003B5C;
            text-decoration: underline;
            text-underline-offset: 4px;
            display: block;
            margin: 6px 0;
        }
        .highlight {
            font-weight: bold;
            color: #003B5C;
        }

        /* ── Details grid ── */
        .details-box {
            background: #f0f9ff;
            border: 1px solid #00AEEF;
            border-radius: 4px;
            padding: 10px 20px;
            margin: 12px 0;
        }
        .details-grid {
            display: table;
            width: 100%;
        }
        .detail-row {
            display: table-row;
        }
        .detail-label {
            display: table-cell;
            font-size: 9pt;
            color: #555;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 3px 16px 3px 0;
            width: 35%;
        }
        .detail-value {
            display: table-cell;
            font-size: 10pt;
            font-weight: bold;
            color: #003B5C;
            padding: 3px 0;
        }

        /* ── Departments cleared ── */
        .dept-section {
            margin: 10px 0;
        }
        .dept-title {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
            margin-bottom: 6px;
            text-align: center;
        }
        .dept-grid {
            display: table;
            width: 100%;
        }
        .dept-cell {
            display: table-cell;
            text-align: center;
            padding: 4px 6px;
            font-size: 8.5pt;
            width: 14.28%;
        }
        .dept-check {
            color: #059669;
            font-size: 11pt;
            font-weight: bold;
            display: block;
        }
        .dept-name-cell {
            color: #333;
            font-size: 7.5pt;
            display: block;
            margin-top: 2px;
        }

        /* ── Signature row ── */
        .signature-section {
            margin-top: 14px;
            display: table;
            width: 100%;
        }
        .sig-col {
            display: table-cell;
            text-align: center;
            width: 33.33%;
            padding: 0 10px;
            vertical-align: bottom;
        }
        .sig-line {
            border-top: 1px solid #003B5C;
            padding-top: 5px;
            margin-top: 30px;
        }
        .sig-name {
            font-size: 9.5pt;
            font-weight: bold;
            color: #003B5C;
        }
        .sig-title {
            font-size: 8.5pt;
            color: #555;
            margin-top: 2px;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 12px;
            border-top: 1px solid #e0e0e0;
            padding-top: 8px;
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            font-size: 7.5pt;
            color: #888;
            vertical-align: middle;
        }
        .footer-right {
            display: table-cell;
            text-align: right;
            font-size: 7.5pt;
            color: #888;
            vertical-align: middle;
        }
        .cert-number-badge {
            display: inline-block;
            background: #003B5C;
            color: white;
            font-size: 8pt;
            font-weight: bold;
            padding: 2px 10px;
            border-radius: 3px;
            letter-spacing: 1px;
        }

        /* ── Watermark ── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72pt;
            font-weight: bold;
            color: rgba(0, 59, 92, 0.04);
            text-transform: uppercase;
            letter-spacing: 10px;
            white-space: nowrap;
            z-index: -1;
        }
    </style>
</head>
<body>

    <div class="outer-border"></div>
    <div class="inner-border"></div>
    <div class="watermark">MASENO UNIVERSITY</div>

    <div class="page">

        {{-- Header --}}
        <div class="header">
            <div class="university-name">Maseno University</div>
            <div class="university-sub">Office of the Academic Registrar</div>
            <div class="doc-title">Certificate of Clearance</div>
            <div class="doc-subtitle">This is to certify that the following student has been duly cleared by all university departments</div>
        </div>

        {{-- Body text --}}
        <div class="body-text">
            This is to certify that
            <span class="student-name">{{ strtoupper($student->full_name) }}</span>
            of
            <span class="highlight">{{ $student->faculty }}</span>,
            pursuing a programme in
            <span class="highlight">{{ $student->programme }}</span>
            with Registration Number
            <span class="highlight">{{ $student->reg_number }}</span>
            has been fully cleared by all university departments
            for the academic year <span class="highlight">{{ $application->academic_year }}</span>.
        </div>

        {{-- Details box --}}
        <div class="details-box">
            <div class="details-grid">
                <div class="detail-row">
                    <div class="detail-label">Application Type</div>
                    <div class="detail-value">
                        @php
                            $typeLabels = [
                                'graduation' => 'Graduation Clearance',
                                'deferral'   => 'Deferral of Studies',
                                'transfer'   => 'Transfer to Another Institution',
                                'withdrawal' => 'Withdrawal from University',
                                'other'      => 'Other',
                            ];
                        @endphp
                        {{ $typeLabels[$application->application_type ?? 'graduation'] ?? 'Graduation Clearance' }}
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Date of Issue</div>
                    <div class="detail-value">{{ $certificate->issued_at->format('d F Y') }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Certificate No.</div>
                    <div class="detail-value">{{ $certificate->certificate_number }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Verification Token</div>
                    <div class="detail-value" style="font-size:8pt;color:#555;">{{ $certificate->verification_token }}</div>
                </div>
            </div>
        </div>

        {{-- Departments cleared --}}
        <div class="dept-section">
            <div class="dept-title">Cleared by the following departments</div>
            <div class="dept-grid">
                @foreach ($application->departmentClearances as $clearance)
                    <div class="dept-cell">
                        <span class="dept-check">✓</span>
                        <span class="dept-name-cell">{{ $clearance->department->name }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Signatures --}}
        <div class="signature-section">
            <div class="sig-col">
                <div class="sig-line">
                    <div class="sig-name">Academic Registrar</div>
                    <div class="sig-title">Maseno University</div>
                </div>
            </div>
            <div class="sig-col">
                <div class="sig-line">
                    <div class="sig-name">{{ $student->full_name }}</div>
                    <div class="sig-title">Student Signature</div>
                </div>
            </div>
            <div class="sig-col">
                <div class="sig-line">
                    <div class="sig-name">Vice Chancellor</div>
                    <div class="sig-title">Maseno University</div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div class="footer-left">
                Maseno University, Private Bag, Maseno, Kenya &bull;
                Tel: +254 (057) 351622 &bull;
                www.maseno.ac.ke
            </div>
            <div class="footer-right">
                <span class="cert-number-badge">{{ $certificate->certificate_number }}</span>
                &nbsp; Verify at: {{ url("/verify/certificate/" . $certificate->verification_token) }} · Issued: {{ $certificate->issued_at->format('d M Y') }}
            </div>
        </div>

    </div>
</body>
</html>