<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        .certificate {
            border: 12px solid #1F4E5C;
            padding: 50px 60px;
            margin: 20px;
            text-align: center;
            position: relative;
        }
        .inner-border {
            border: 2px solid #c9a227;
            padding: 40px;
        }
        .university-name {
            font-size: 26px;
            font-weight: bold;
            color: #1F4E5C;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 13px;
            color: #595959;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .title {
            font-size: 32px;
            font-weight: bold;
            color: #c9a227;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .body-text {
            font-size: 14px;
            line-height: 1.8;
            margin: 20px 0;
            color: #374151;
        }
        .student-name {
            font-size: 24px;
            font-weight: bold;
            color: #1F4E5C;
            margin: 15px 0;
            border-bottom: 1px solid #c9a227;
            display: inline-block;
            padding-bottom: 4px;
        }
        .details-table {
            width: 100%;
            margin: 30px 0;
            font-size: 12px;
        }
        .details-table td {
            padding: 6px 10px;
            text-align: left;
        }
        .details-table .label {
            color: #595959;
            width: 40%;
        }
        .details-table .value {
            font-weight: bold;
            color: #1f2937;
        }
        .footer-row {
            margin-top: 40px;
            width: 100%;
        }
        .footer-row table {
            width: 100%;
        }
        .footer-row td {
            vertical-align: bottom;
            text-align: center;
        }
        .qr-code {
            width: 90px;
            height: 90px;
        }
        .cert-number {
            font-size: 10px;
            color: #595959;
            margin-top: 8px;
        }
        .signature-line {
            border-top: 1px solid #1f2937;
            width: 200px;
            margin: 0 auto;
            padding-top: 6px;
            font-size: 11px;
            color: #595959;
        }
        .issued-date {
            font-size: 11px;
            color: #595959;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="inner-border">
            <div class="university-name">MASENO UNIVERSITY</div>
            <div class="subtitle">Republic of Kenya</div>

            <div class="title">Certificate of Clearance</div>

            <div class="body-text">This is to certify that</div>

            <div class="student-name">{{ $student->full_name }}</div>

            <div class="body-text">
                Registration Number <strong>{{ $student->reg_number }}</strong>,
                a student of the <strong>{{ $student->programme }}</strong> programme
                in the {{ $student->faculty }}, has successfully completed the
                university clearance process for the academic year
                <strong>{{ $application->academic_year }}</strong> and has been found
                to have no outstanding obligations to any university department.
            </div>

            <table class="details-table">
                <tr>
                    <td class="label">Faculty:</td>
                    <td class="value">{{ $student->faculty }}</td>
                </tr>
                <tr>
                    <td class="label">Department:</td>
                    <td class="value">{{ $student->department }}</td>
                </tr>
                <tr>
                    <td class="label">Graduation Year:</td>
                    <td class="value">{{ $student->graduation_year }}</td>
                </tr>
                <tr>
                    <td class="label">Certificate Number:</td>
                    <td class="value">{{ $certificate->certificate_number }}</td>
                </tr>
            </table>

            <div class="footer-row">
                <table>
                    <tr>
                        <td style="width: 33%;">
                            <div class="signature-line">Academic Registrar</div>
                        </td>
                        <td style="width: 34%;">
                            <img src="{{ $qrDataUri }}" class="qr-code" alt="QR Code">
                            <div class="cert-number">Scan to verify authenticity</div>
                        </td>
                        <td style="width: 33%;">
                            <div class="signature-line">Date Issued</div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="issued-date">
                Issued on {{ $certificate->issued_at->format('jS F Y') }}
            </div>
        </div>
    </div>
</body>
</html>
