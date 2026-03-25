@extends('layouts.app')
@section('title', $notice->title.' — PDPA Studio')
@section('page-title', 'Privacy Notice')

@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15572e;">
    ✅ {{ session('success') }}
</div>
@endif

<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('privacy.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium" style="color:#15572e;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        รายการ Privacy Notice
    </a>
    <span style="color:#e2e8f0;">/</span>
    <span class="text-sm" style="color:#64748b;">{{ Str::limit($notice->title, 40) }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Notice Content --}}
    <div class="lg:col-span-2">
        <div class="card overflow-hidden">
            {{-- Header banner --}}
            <div class="px-6 py-4 flex items-center gap-3" style="background:linear-gradient(135deg,{{ \App\Models\PrivacyNotice::typeBg($notice->type) }},white); border-bottom:1px solid #f1f5f9;">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:{{ \App\Models\PrivacyNotice::typeBg($notice->type) }}; border:1px solid {{ \App\Models\PrivacyNotice::typeColor($notice->type) }}20;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color:{{ \App\Models\PrivacyNotice::typeColor($notice->type) }};">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ \App\Models\PrivacyNotice::typeIcon($notice->type) }}"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base font-bold" style="color:#0f3020;">{{ $notice->title }}</h2>
                        <span class="text-xs font-mono px-2 py-0.5 rounded-md" style="background:#e8f0eb; color:#15572e;">v{{ $notice->version }}</span>
                        <span class="badge {{ $notice->getStatusBadge() }}">{{ $notice->getStatusLabel() }}</span>
                    </div>
                    <p class="text-xs mt-0.5" style="color:{{ \App\Models\PrivacyNotice::typeColor($notice->type) }};">{{ $notice->getTypeLabel() }} · {{ $notice->language === 'th' ? '🇹🇭 ภาษาไทย' : '🇬🇧 English' }}</p>
                </div>
            </div>

            {{-- Rendered content --}}
            <div class="px-6 py-5 privacy-content" style="font-family:'Sarabun','Noto Sans Thai',sans-serif; font-size:14px; line-height:1.9; color:#1e293b; min-height:300px;">
                {!! $notice->content !!}
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3 flex items-center gap-4 text-xs" style="background:#fafafa; border-top:1px solid #f1f5f9; color:#94a3b8;">
                @if($notice->effective_date)
                <span>📅 มีผล: <strong style="color:#475569;">{{ $notice->effective_date }}</strong></span>
                @endif
                @if($notice->expires_at)
                <span>⏱ หมดอายุ: <strong style="color:{{ $notice->expires_at->isPast() ? '#c0272d':'#475569' }};">{{ $notice->expires_at->format('d/m/Y') }}</strong></span>
                @endif
                @if($notice->published_at)
                <span>📢 เผยแพร่: <strong style="color:#475569;">{{ $notice->published_at->format('d/m/Y') }}</strong></span>
                @endif
            </div>
        </div>

        {{-- Version History --}}
        @if($history->count() > 1)
        <div class="card p-5 mt-5">
            <h3 class="text-sm font-bold mb-4" style="color:#0f3020;">ประวัติเวอร์ชัน</h3>
            <div class="space-y-2">
                @foreach($history as $h)
                <div class="flex items-center justify-between py-2.5 px-3 rounded-xl {{ $h->id === $notice->id ? '' : '' }}"
                     style="background:{{ $h->id === $notice->id ? '#e8f0eb' : '#f8fafc' }}; border:1px solid {{ $h->id === $notice->id ? '#c5d9cb' : '#f1f5f9' }};">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded"
                              style="background:{{ $h->id === $notice->id ? '#15572e':'#e2e8f0' }}; color:{{ $h->id === $notice->id ? 'white':'#64748b' }};">
                            v{{ $h->version }}
                        </span>
                        <span class="badge {{ $h->getStatusBadge() }}">{{ $h->getStatusLabel() }}</span>
                        @if($h->id === $notice->id)
                        <span class="text-xs font-medium" style="color:#15572e;">← ดูอยู่</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs" style="color:#94a3b8;">{{ $h->created_at->format('d/m/Y') }}</span>
                        @if($h->id !== $notice->id)
                        <a href="{{ route('privacy.show', $h) }}" style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:20px;background:#f0fdf4;color:#15572e;border:1px solid #bbf7d0;font-size:12px;font-weight:500;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg></a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-5">

        {{-- Status Card --}}
        <div class="card p-5">
            <p class="text-xs font-bold mb-4" style="color:#64748b; text-transform:uppercase; letter-spacing:.06em;">สถานะและการจัดการ</p>

            <div class="flex items-center gap-2 mb-4">
                <span class="badge {{ $notice->getStatusBadge() }} text-sm">{{ $notice->getStatusLabel() }}</span>
                @if($notice->getStatus() === 'published')
                <span class="text-xs font-medium" style="color:#15572e;">✓ Active</span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="space-y-2.5">
                @if($notice->getStatus() === 'draft' || $notice->getStatus() === 'inactive')
                <form method="POST" action="{{ route('privacy.publish', $notice) }}">
                    @csrf
                    <button type="submit" class="btn-primary w-full justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        เผยแพร่ประกาศ
                    </button>
                </form>
                @endif
                @if($notice->getStatus() === 'published')
                <form method="POST" action="{{ route('privacy.unpublish', $notice) }}">
                    @csrf
                    <button type="submit" class="btn-outline w-full justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        ปิดการเผยแพร่
                    </button>
                </form>
                @endif

                <a href="{{ route('privacy.edit', $notice) }}" class="btn-outline w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    แก้ไขเนื้อหา
                </a>

                <form method="POST" action="{{ route('privacy.new-version', $notice) }}">
                    @csrf
                    <button type="submit" class="btn-outline w-full justify-center" style="border-color:#7c3aed; color:#7c3aed;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        สร้างเวอร์ชันใหม่ (v{{ $notice->version + 1 }})
                    </button>
                </form>

                @if($notice->public_url && $notice->getStatus() === 'published')
                <a href="{{ route('privacy.public', $notice->public_url) }}" target="_blank"
                   class="btn-outline w-full justify-center" style="border-color:#0369a1; color:#0369a1;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    ดูหน้า Public
                </a>
                @endif

                <form method="POST" action="{{ route('privacy.destroy', $notice) }}"
                      onsubmit="return confirm('ยืนยันการลบ? ข้อมูลจะถูกลบถาวร')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full text-xs font-medium py-2 rounded-lg transition"
                            style="color:#c0272d; border:1px solid #fecaca; background:#fff5f5;">
                        🗑 ลบประกาศนี้
                    </button>
                </form>
            </div>
        </div>

        {{-- Meta Info --}}
        <div class="card p-5">
            <p class="text-xs font-bold mb-4" style="color:#64748b; text-transform:uppercase; letter-spacing:.06em;">ข้อมูล</p>
            <dl class="space-y-3 text-xs">
                <div class="flex justify-between">
                    <dt style="color:#94a3b8;">ประเภท</dt>
                    <dd class="font-medium text-right" style="color:{{ \App\Models\PrivacyNotice::typeColor($notice->type) }};">{{ $notice->getTypeLabel() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt style="color:#94a3b8;">ภาษา</dt>
                    <dd class="font-medium" style="color:#475569;">{{ $notice->language === 'th' ? '🇹🇭 ภาษาไทย' : '🇬🇧 English' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt style="color:#94a3b8;">เวอร์ชัน</dt>
                    <dd class="font-mono font-bold" style="color:#475569;">v{{ $notice->version }}</dd>
                </div>
                @if($notice->effective_date)
                <div class="flex justify-between">
                    <dt style="color:#94a3b8;">วันมีผล</dt>
                    <dd class="font-medium" style="color:#475569;">{{ $notice->effective_date }}</dd>
                </div>
                @endif
                @if($notice->expires_at)
                <div class="flex justify-between">
                    <dt style="color:#94a3b8;">หมดอายุ</dt>
                    <dd class="font-medium" style="color:{{ $notice->expires_at->isPast() ? '#c0272d':'#475569' }};">{{ $notice->expires_at->format('d/m/Y') }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt style="color:#94a3b8;">สร้างเมื่อ</dt>
                    <dd style="color:#475569;">{{ $notice->created_at->format('d/m/Y') }}</dd>
                </div>
                @if($notice->published_at)
                <div class="flex justify-between">
                    <dt style="color:#94a3b8;">เผยแพร่</dt>
                    <dd style="color:#15572e; font-weight:600;">{{ $notice->published_at->format('d/m/Y') }}</dd>
                </div>
                @endif
                @if($notice->approvedBy)
                <div class="flex justify-between">
                    <dt style="color:#94a3b8;">อนุมัติโดย</dt>
                    <dd style="color:#475569;">{{ $notice->approvedBy->name }}</dd>
                </div>
                @endif
            </dl>
        </div>

        {{-- Public URL --}}
        @if($notice->public_url && $notice->getStatus() === 'published')
        <div class="card p-4">
            <p class="text-xs font-bold mb-2" style="color:#0f3020;">🔗 Public URL</p>
            <div class="flex items-center gap-2">
                <input type="text" readonly
                       value="{{ route('privacy.public', $notice->public_url) }}"
                       class="form-input flex-1 text-xs" style="color:#475569; background:#f8fafc;"
                       onclick="this.select()">
                <button onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.textContent='✓'"
                        class="btn-outline" style="padding:6px 10px; font-size:11px; flex-shrink:0;">คัดลอก</button>
            </div>
            <p class="text-xs mt-1.5" style="color:#94a3b8;">ลิงก์สาธารณะ — ไม่ต้อง Login</p>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<style>
    .privacy-content h1,.privacy-content h2 { font-size:1.2em; font-weight:700; color:#0f3020; margin: 1.2em 0 .5em; padding-bottom:.3em; border-bottom:2px solid #e8f0eb; }
    .privacy-content h3 { font-size:1em; font-weight:700; color:#15572e; margin: 1em 0 .4em; }
    .privacy-content h4 { font-size:.95em; font-weight:600; color:#475569; margin: .8em 0 .3em; }
    .privacy-content p { margin-bottom:.7em; }
    .privacy-content ul,.privacy-content ol { padding-left:1.5em; margin-bottom:.7em; }
    .privacy-content li { margin-bottom:.3em; line-height:1.7; }
    .privacy-content strong { color:#1e293b; }
    .privacy-content a { color:#15572e; text-decoration:underline; }
    .privacy-content table { width:100%; border-collapse:collapse; margin-bottom:1em; }
    .privacy-content td,.privacy-content th { border:1px solid #e2e8f0; padding:.4em .7em; font-size:.9em; }
    .privacy-content th { background:#f8fafc; font-weight:600; }
</style>
@endpush

@endsection
