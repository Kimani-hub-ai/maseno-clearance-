<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify a Certificate — Maseno University</title>
    <style>
        body {
            font-family: -apple-system, system-ui, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-width: 480px;
            width: 100%;
            padding: 32px;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header h1 {
            font-size: 18px;
            color: #1F4E5C;
            margin: 0 0 4px;
        }
        .header p {
            color: #6b7280;
            font-size: 13px;
            margin: 0;
        }
        label {
            display: block;
            font-size: 13px;
            color: #374151;
            margin-bottom: 6px;
            font-weight: 600;
        }
        input[type=text] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            margin-bottom: 16px;
        }
        button {
            width: 100%;
            background: #1F4E5C;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover {
            background: #163945;
        }
        .status-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 600;
        }
        .valid { background: #d1fae5; color: #065f46; }
        .invalid { background: #fee2e2; color: #991b1b; }
        .details { border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 16px; }
        .row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; }
        .row .label { color: #6b7280; }
        .row .value { font-weight: 600; color: #1f2937; text-align: right; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>MASENO UNIVERSITY</h1>
            <p>Verify a Clearance Certificate</p>
        </div>

        <form method="POST" action="{{ route('public.certificate.search') }}">
            @csrf
            <label for="certificate_number">Certificate Number</label>
            <input type="text" id="certificate_number" name="certificate_number"
                   placeholder="e.g. MAS-CLR-2026-00001"
                   value="{{ old('certificate_number') }}" required>
            <button type="submit">Verify Certificate</button>
        </form>

        @if (isset($searched) && $searched)
            <div style="margin-top: 24px;">
                @if ($certificate)
                    <div class="status-badge valid">✓ Certificate is Valid</div>
                    <div class="details">
                        <div class="row"><span class="label">Student Name</span><span class="value">{{ $certificate->application->student->full_name }}</span></div>
                        <div class="row"><span class="label">Registration No.</span><span class="value">{{ $certificate->application->student->reg_number }}</span></div>
                        <div class="row"><span class="label">Programme</span><span class="value">{{ $certificate->application->student->programme }}</span></div>
                        <div class="row"><span class="label">Academic Year</span><span class="value">{{ $certificate->application->academic_year }}</span></div>
                        <div class="row"><span class="label">Issued</span><span class="value">{{ $certificate->issued_at->format('jS F Y') }}</span></div>
                    </div>
                @else
                    <div class="status-badge invalid">✗ Certificate Not Found</div>
                    <p style="text-align:center; color:#6b7280; font-size:13px;">
                        No certificate matches that number. Double-check and try again.
                    </p>
                @endif
            </div>
        @endif
    </div>
</body>
</html>
