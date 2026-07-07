<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — Maseno University Clearance</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            background: #003B5C;
        }
        .left-panel {
            width: 45%; background: #003B5C;
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            padding: 48px; color: white;
        }
        .left-panel .crest {
            width: 90px; height: 90px; border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 40px; margin-bottom: 24px;
        }
        .left-panel h1 { font-size: 26px; font-weight: 700; margin-bottom: 8px; text-align: center; }
        .left-panel p { font-size: 14px; color: rgba(255,255,255,0.6); text-align: center; line-height: 1.6; max-width: 280px; }
        .gold-line { width: 48px; height: 3px; background: #F5A623; border-radius: 2px; margin: 20px auto; }
        .feature { display: flex; align-items: center; gap: 10px; margin-top: 14px; font-size: 13px; color: rgba(255,255,255,0.7); }
        .right-panel {
            flex: 1; background: #F3F4F6;
            display: flex; align-items: center; justify-content: center;
            padding: 48px;
        }
        .login-card {
            background: white; border-radius: 16px;
            padding: 40px; width: 100%; max-width: 400px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .login-card h2 { font-size: 22px; font-weight: 700; color: #003B5C; margin-bottom: 6px; }
        .login-card .sub { font-size: 14px; color: #6B7280; margin-bottom: 28px; }
        .field { margin-bottom: 18px; }
        .field label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .field input {
            width: 100%; padding: 11px 14px; border: 1.5px solid #E5E7EB;
            border-radius: 8px; font-size: 14px; color: #111827;
            transition: border-color 0.15s;
        }
        .field input:focus { outline: none; border-color: #00AEEF; box-shadow: 0 0 0 3px rgba(0,174,239,0.1); }
        .error-msg { background: #FEE2E2; color: #991B1B; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 18px; }
        .btn-login {
            width: 100%; padding: 12px; background: #003B5C; color: white;
            border: none; border-radius: 8px; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: background 0.15s;
        }
        .btn-login:hover { background: #002a42; }
        .login-footer { text-align: center; margin-top: 20px; font-size: 13px; color: #6B7280; }
        .gold-accent { width: 40px; height: 3px; background: #F5A623; border-radius: 2px; margin-bottom: 20px; }
        @media (max-width: 700px) {
            body { flex-direction: column; }
            .left-panel { width: 100%; padding: 32px 24px; }
            .right-panel { padding: 24px; }
        }
    </style>
</head>
<body>
    <div class="left-panel">
        <div class="crest">🎓</div>
        <h1>Maseno University</h1>
        <div class="gold-line"></div>
        <p>Automated Student Clearance System — streamlining your path to graduation.</p>
        <div style="margin-top: 32px;">
            <div class="feature">✓ &nbsp;Apply from anywhere, anytime</div>
            <div class="feature">✓ &nbsp;Real-time department tracking</div>
            <div class="feature">✓ &nbsp;Instant digital certificate</div>
            <div class="feature">✓ &nbsp;QR-verified authenticity</div>
        </div>
    </div>

    <div class="right-panel">
        <div class="login-card">
            <div class="gold-accent"></div>
            <h2>Welcome back</h2>
            <p class="sub">Sign in to your clearance portal account</p>

            @if ($errors->any())
                <div class="error-msg">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@maseno.ac.ke">
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn-login">Sign In →</button>
            </form>

            <div class="login-footer">
                Don't have an account? <a href="{{ route('register') }}" style="color:#00AEEF;font-weight:600;">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>
