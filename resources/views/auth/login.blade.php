<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Sarabun', sans-serif; margin: 0; padding: 0; }

        .login-bg {
            min-height: 100vh;
            display: flex;
            background: #0a2918;
        }

        /* Left panel */
        .left-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(192,39,45,0.15) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 70%, rgba(42,107,77,0.25) 0%, transparent 60%);
        }
        .left-panel::after {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(192,39,45,0.08) 0%, transparent 70%);
        }

        /* Right panel */
        .right-panel {
            width: 480px;
            flex-shrink: 0;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 52px 48px;
            box-shadow: -20px 0 60px rgba(0,0,0,0.15);
        }

        /* Grid decorations */
        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* Form inputs */
        .field-group { margin-bottom: 18px; }
        .field-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .field-input {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px; font-family: 'Sarabun', sans-serif;
            color: #111827; background: #fafafa;
            outline: none; transition: all 0.15s;
        }
        .field-input:focus { border-color: #15572e; box-shadow: 0 0 0 3px rgba(21,87,46,0.1); background: white; }
        .field-input::placeholder { color: #9ca3af; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #0f3020 0%, #15572e 50%, #2a6b4d 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Sarabun', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 15px rgba(15,48,32,0.35);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #15572e 0%, #2a6b4d 100%);
            box-shadow: 0 6px 20px rgba(15,48,32,0.45);
            transform: translateY(-1px);
        }

        .demo-btn {
            width: 100%;
            text-align: left;
            padding: 10px 14px;
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-radius: 9px;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'Sarabun', sans-serif;
            margin-bottom: 7px;
        }
        .demo-btn:hover { background: #f0fdf4; border-color: #86efac; }
        .demo-btn:last-child { margin-bottom: 0; }

        /* Floating badge dots */
        .dot { position: absolute; border-radius: 50%; filter: blur(1px); }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; }
        }
    </style>
</head>
<body>
<div class="login-bg">

    <!-- Left Panel — Brand ───────────────────────────────────────────────── -->
    <div class="left-panel">
        <div class="grid-pattern"></div>

        <!-- Decorative dots -->
        <div class="dot" style="width:180px;height:180px;background:rgba(192,39,45,0.12);top:10%;left:5%;"></div>
        <div class="dot" style="width:120px;height:120px;background:rgba(42,107,77,0.18);bottom:15%;right:10%;"></div>
        <div class="dot" style="width:60px;height:60px;background:rgba(192,39,45,0.2);bottom:30%;left:15%;"></div>

        <div class="relative z-10 text-center max-w-md">

            {{-- ── MPX Logo บน left panel ── --}}
            @if(file_exists(public_path('images/logo.png')))
                <div class="inline-block mb-6">
                    <img src="{{ asset('images/logo.png') }}"
                         alt="{{ config('app.company') }}"
                         class="object-contain block"
                         style="height:72px;max-width:220px;filter:drop-shadow(0 4px 12px rgba(0,0,0,0.25));">
                </div>
            @else
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6"
                     style="background:linear-gradient(135deg,#c0272d,#e53e3e);box-shadow:0 8px 32px rgba(192,39,45,0.4);">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            @endif

            <h1 class="text-4xl font-extrabold text-white mb-2 tracking-tight">{{ config('app.name') }}</h1>
            <p class="text-sm mb-1" style="color:#6baa86;">{{ config('app.company') }}</p>
            <p class="mb-10"></p>

            <!-- Feature pills -->
            <div class="flex flex-col gap-3 text-left">
                @foreach([
                    ['ความยินยอม (Consent Management)', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                    ['สิทธิ์เจ้าของข้อมูล (Data Rights)', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['Data Breach & 72h Notification', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    ['ROPA — บันทึกกิจกรรมการประมวลผล', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ] as [$label, $icon])
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl"
                     style="background: rgba(255,255,255,0.05); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.08);">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(42,107,77,0.3);">
                        <svg class="w-3.5 h-3.5" style="color:#6bda96;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium" style="color:#c8e6d4;">{{ $label }}</span>
                </div>
                @endforeach
            </div>

        </div>
    </div>

    <!-- Right Panel — Form ───────────────────────────────────────────────── -->
    <div class="right-panel">
        <div>
            <div class="mb-6">
                <p class="font-bold text-base" style="color:#0f3020;">Incognito Lab &amp; MPX</p>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-extrabold mb-1" style="color: #0f3020;">ยินดีต้อนรับ</h2>
                <p class="text-sm" style="color: #6b7280;">กรุณาเข้าสู่ระบบเพื่อดำเนินการต่อ</p>
            </div>

            @if ($errors->any())
            <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
                 style="background: #fff1f2; border: 1.5px solid #fca5a5; color: #991b1b;">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ $errors->first() }}
            </div>
            @endif

            @if (session('success'))
            <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl text-sm font-medium"
                 style="background: #f0fdf4; border: 1.5px solid #86efac; color: #166534;">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="field-group">
                    <label class="field-label">ชื่อผู้ใช้</label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                           class="field-input" placeholder="admin"
                           autocomplete="username" autocapitalize="none">
                    @error('username')
                        <p class="mt-1 text-xs" style="color:#c0272d;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">รหัสผ่าน</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password-input" required
                               class="field-input" placeholder="••••••••" style="padding-right: 44px;">
                        <button type="button" onclick="togglePwd()"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px;">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center mb-6">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded"
                               style="accent-color:#15572e;width:15px;height:15px;">
                        <span class="text-sm" style="color: #6b7280;">จดจำการเข้าสู่ระบบ</span>
                    </label>
                </div>

                <button type="submit" class="btn-login">เข้าสู่ระบบ</button>
            </form>

            <p class="mt-6 text-center text-xs" style="color:#94a3b8;">Powered by Incognito Lab.</p>

        </div>
    </div>
</div>

<script>
function fillDemo(username, pw) {
    document.querySelector('input[name=username]').value = username;
    document.querySelector('input[name=password]').value = pw;
}
function togglePwd() {
    const input = document.getElementById('password-input');
    input.type  = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
