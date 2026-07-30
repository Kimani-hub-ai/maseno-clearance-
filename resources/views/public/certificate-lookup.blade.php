<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Certificate — Maseno University</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, system-ui, sans-serif;
            background: linear-gradient(135deg, #003B5C 0%, #005a8e 50%, #00AEEF 100%);
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 24px;
        }

        .verify-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 24px 64px rgba(0,0,0,0.2);
            text-align: center;
        }

        .logo-wrap {
            width: 72px; height: 72px;
            border-radius: 20px;
            background: linear-gradient(135deg, #003B5C, #00AEEF);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(0,59,92,0.25);
        }

        .card-title {
            font-size: 22px; font-weight: 800;
            color: #003B5C; margin-bottom: 6px;
        }
        .card-sub {
            font-size: 14px; color: #6B7280;
            margin-bottom: 32px; line-height: 1.5;
        }

        .search-wrap { position: relative; margin-bottom: 8px; }
        .search-input {
            width: 100%; padding: 14px 50px 14px 18px;
            border: 2px solid #E5E7EB; border-radius: 12px;
            font-size: 15px; color: #111827;
            transition: border-color 0.2s, box-shadow 0.2s;
            -webkit-appearance: none;
        }
        .search-input:focus {
            outline: none; border-color: #00AEEF;
            box-shadow: 0 0 0 4px rgba(0,174,239,0.12);
        }
        .search-input.error { border-color: #EF4444; }
        .search-btn {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #003B5C, #005a8e);
            color: white; border: none; border-radius: 12px;
            font-size: 15px; font-weight: 700; cursor: pointer;
            transition: opacity 0.15s, transform 0.15s;
            margin-top: 10px;
        }
        .search-btn:hover   { opacity: 0.92; transform: translateY(-1px); }
        .search-btn:active  { transform: translateY(0); }

        .error-msg {
            background: #FEF2F2; border: 1px solid #FECACA;
            border-radius: 10px; padding: 12px 16px;
            font-size: 13px; color: #991B1B;
            text-align: left; margin-bottom: 16px;
            display: flex; align-items: flex-start; gap: 8px;
        }

        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 24px 0; color: #9CA3AF; font-size: 12px;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: #E5E7EB;
        }

        .qr-hint {
            background: #F0F9FF; border: 1px solid #BAE6FD;
            border-radius: 12px; padding: 16px;
            font-size: 13px; color: #0369A1;
            display: flex; align-items: center; gap: 12px;
            text-align: left;
        }
        .qr-hint-icon { font-size: 24px; flex-shrink: 0; }

        .trust-badges {
            display: flex; justify-content: center; gap: 20px;
            margin-top: 28px; flex-wrap: wrap;
        }
        .trust-badge {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: rgba(255,255,255,0.8);
        }

        .back-link {
            display: inline-block; margin-top: 28px;
            font-size: 13px; color: #6B7280; text-decoration: none;
        }
        .back-link:hover { color: #003B5C; }

        @media (max-width: 520px) {
            .verify-card { padding: 28px 20px; border-radius: 16px; }
            .card-title  { font-size: 18px; }
        }
    </style>
</head>
<body>

    <div class="verify-card">
        <div class="logo-wrap">🎓</div>
        <div class="card-title">Certificate Verification</div>
        <div class="card-sub">
            Verify the authenticity of a Maseno University<br>
            clearance certificate instantly.
        </div>

        {{-- Error message --}}
        @if($errors->has('query'))
            <div class="error-msg">
                <span>⚠️</span>
                {{ $errors->first('query') }}
            </div>
        @endif

        {{-- Search form --}}
        <form method="POST" action="{{ route('public.certificate.search') }}">
            @csrf
            <div class="search-wrap">
                <input
                    type="text"
                    name="query"
                    value="{{ old('query') }}"
                    class="search-input {{ $errors->has('query') ? 'error' : '' }}"
                    placeholder="Enter certificate number e.g. MAS-CLR-2026-00001"
                    autofocus
                    autocomplete="off"
                />
            </div>
            <button type="submit" class="search-btn">
                🔍 Verify Certificate
            </button>
        </form>

        <div class="divider">or</div>

        <div class="qr-hint">
            <div class="qr-hint-icon">📱</div>
            <div>
                <strong style="display:block;margin-bottom:2px;">Scan the QR code</strong>
                Every Maseno clearance certificate has a QR code.
                Scan it with your phone camera to verify instantly.
            </div>
        </div>

        <a href="{{ route('home') }}" class="back-link">← Back to home</a>
    </div>

    {{-- Trust badges below card --}}
    <div class="trust-badges">
        <div class="trust-badge">🔒 Secure verification</div>
        <div class="trust-badge">⚡ Instant results</div>
        <div class="trust-badge">🎓 Maseno University</div>
    </div>

</body>
</html>