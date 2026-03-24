<!DOCTYPE html>
<html lang="{{ $notice->language }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notice->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Sarabun','Noto Sans Thai',sans-serif; }
        .content h2 { font-size:1.25em; font-weight:700; color:#0f3020; margin:1.4em 0 .5em; padding-bottom:.4em; border-bottom:2px solid #e8f0eb; }
        .content h3 { font-size:1.05em; font-weight:700; color:#15572e; margin:1.1em 0 .4em; }
        .content h4 { font-size:1em; font-weight:600; color:#475569; margin:.9em 0 .3em; }
        .content p  { margin-bottom:.8em; line-height:1.9; }
        .content ul,.content ol { padding-left:1.6em; margin-bottom:.8em; }
        .content li { margin-bottom:.35em; line-height:1.8; }
        .content strong { color:#1e293b; }
        .content a { color:#15572e; text-decoration:underline; }
        .content table { width:100%; border-collapse:collapse; margin-bottom:1em; font-size:.92em; }
        .content td,.content th { border:1px solid #e2e8f0; padding:.5em .8em; }
        .content th { background:#f8fafc; font-weight:600; }
    </style>
</head>
<body class="min-h-screen" style="background:#f8fafc;">

    {{-- Top bar --}}
    <div class="sticky top-0 z-10 shadow-sm" style="background:#15572e;">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:rgba(255,255,255,0.7);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ \App\Models\PrivacyNotice::typeIcon($notice->type) }}"/></svg>
                <span class="text-sm font-semibold text-white">{{ $notice->getTypeLabel() }}</span>
            </div>
            <div class="flex items-center gap-2 text-xs" style="color:rgba(255,255,255,0.6);">
                <span class="px-2 py-0.5 rounded" style="background:rgba(255,255,255,0.15); color:white;">v{{ $notice->version }}</span>
                <span>PDPA Studio</span>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border:1px solid #e8f0eb;">

            {{-- Header --}}
            <div class="px-8 py-6" style="background:linear-gradient(135deg,{{ \App\Models\PrivacyNotice::typeBg($notice->type) }},white); border-bottom:1px solid #f1f5f9;">
                <h1 class="text-xl font-bold mb-2" style="color:#0f3020;">{{ $notice->title }}</h1>
                <div class="flex flex-wrap gap-3 text-xs" style="color:#64748b;">
                    @if($notice->effective_date)
                    <span>📅 มีผลบังคับใช้: <strong>{{ $notice->effective_date }}</strong></span>
                    @endif
                    <span>📄 เวอร์ชัน {{ $notice->version }}</span>
                    <span>🌐 {{ $notice->language === 'th' ? 'ภาษาไทย' : 'English' }}</span>
                    @if($notice->published_at)
                    <span>📢 เผยแพร่: {{ $notice->published_at->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>

            {{-- Body --}}
            <div class="px-8 py-7 content" style="color:#1e293b; font-size:15px; line-height:1.85;">
                {!! $notice->content !!}
            </div>

            {{-- Footer --}}
            <div class="px-8 py-4" style="background:#f8fafc; border-top:1px solid #f1f5f9;">
                <p class="text-xs" style="color:#94a3b8;">
                    เอกสารนี้จัดทำโดยระบบ PDPA Studio
                    @if($notice->expires_at)
                    · หมดอายุ {{ $notice->expires_at->format('d/m/Y') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</body>
</html>
