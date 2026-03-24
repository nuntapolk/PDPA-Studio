@extends('layouts.app')
@section('title', 'แก้ไข Privacy Notice — PDPA Studio')
@section('page-title', 'แก้ไข Privacy Notice')

@section('content')

<div class="mb-5">
    <a href="{{ route('privacy.show', $notice) }}" class="inline-flex items-center gap-1.5 text-sm font-medium" style="color:#15572e;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        กลับหน้ารายละเอียด
    </a>
</div>

{{-- Current status banner --}}
<div class="mb-5 px-4 py-3 rounded-xl flex items-center gap-3"
     style="background:{{ $notice->getStatus()==='published' ? '#fffbeb' : '#f8fafc' }}; border:1px solid {{ $notice->getStatus()==='published' ? '#fde68a' : '#e2e8f0' }};">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"
         style="color:{{ $notice->getStatus()==='published' ? '#b45309' : '#94a3b8' }};"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="text-xs" style="color:{{ $notice->getStatus()==='published' ? '#92400e' : '#64748b' }};">
        @if($notice->getStatus() === 'published')
        ⚠️ ประกาศนี้กำลังเผยแพร่อยู่ (v{{ $notice->version }}) — การแก้ไขจะมีผลทันที หรือ <a href="{{ route('privacy.new-version', $notice) }}" style="color:#b45309; font-weight:600;">สร้างเวอร์ชันใหม่</a> แทน
        @else
        กำลังแก้ไข {{ $notice->getTypeLabel() }} — v{{ $notice->version }} ({{ $notice->getStatusLabel() }})
        @endif
    </p>
</div>

<form method="POST" action="{{ route('privacy.update', $notice) }}">
@csrf @method('PUT')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Content --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Title & Meta --}}
        <div class="card p-5 space-y-4">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                     style="background:{{ \App\Models\PrivacyNotice::typeBg($notice->type) }};">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color:{{ \App\Models\PrivacyNotice::typeColor($notice->type) }};"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ \App\Models\PrivacyNotice::typeIcon($notice->type) }}"/></svg>
                </div>
                <h3 class="text-sm font-bold" style="color:#0f3020;">{{ $notice->getTypeLabel() }}</h3>
                <span class="text-xs px-2 py-0.5 rounded-md font-mono" style="background:#e8f0eb; color:#15572e;">v{{ $notice->version }}</span>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">ชื่อประกาศ <span style="color:#c0272d;">*</span></label>
                <input type="text" name="title" value="{{ old('title', $notice->title) }}" class="form-input w-full" required>
                @error('title')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">ภาษา</label>
                    <select name="language" class="form-input w-full">
                        <option value="th" {{ old('language',$notice->language)==='th' ? 'selected':'' }}>🇹🇭 ภาษาไทย</option>
                        <option value="en" {{ old('language',$notice->language)==='en' ? 'selected':'' }}>🇬🇧 English</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">เวอร์ชัน</label>
                    <input type="number" name="version" value="{{ old('version',$notice->version) }}" min="1" class="form-input w-full">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">วันที่มีผลบังคับใช้</label>
                    <input type="text" name="effective_date" value="{{ old('effective_date',$notice->effective_date) }}" class="form-input w-full" placeholder="เช่น 1 มกราคม 2567">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">วันหมดอายุ (ถ้ามี)</label>
                    <input type="date" name="expires_at"
                           value="{{ old('expires_at', $notice->expires_at ? $notice->expires_at->format('Y-m-d') : '') }}"
                           class="form-input w-full">
                </div>
            </div>
        </div>

        {{-- Content Editor --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold" style="color:#0f3020;">เนื้อหาประกาศ <span style="color:#c0272d;">*</span></h3>
                <div class="flex gap-2">
                    <button type="button" onclick="switchTab('editor')" id="tab-editor"
                            class="text-xs px-3 py-1.5 rounded-lg font-medium" style="background:#15572e; color:white;">แก้ไข</button>
                    <button type="button" onclick="switchTab('preview')" id="tab-preview"
                            class="text-xs px-3 py-1.5 rounded-lg font-medium" style="background:#f1f5f9; color:#64748b;">ตัวอย่าง</button>
                </div>
            </div>

            <div id="editor-panel">
                <div class="flex flex-wrap gap-1 mb-2 p-2 rounded-lg" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <button type="button" onclick="insertTag('h2')" class="editor-btn">H2</button>
                    <button type="button" onclick="insertTag('h3')" class="editor-btn">H3</button>
                    <button type="button" onclick="insertTag('p')" class="editor-btn">¶</button>
                    <button type="button" onclick="insertTag('strong')" class="editor-btn"><b>B</b></button>
                    <button type="button" onclick="insertTag('em')" class="editor-btn"><i>I</i></button>
                    <button type="button" onclick="insertTag('u')" class="editor-btn"><u>U</u></button>
                    <button type="button" onclick="insertList('ul')" class="editor-btn">• List</button>
                </div>
                <textarea id="content-editor" name="content" rows="20"
                          class="form-input w-full font-mono text-xs"
                          style="resize:vertical; line-height:1.6;"
                          required>{{ old('content', $notice->content) }}</textarea>
            </div>

            <div id="preview-panel" class="hidden">
                <div id="preview-content"
                     class="rounded-xl p-5 min-h-64 privacy-content"
                     style="border:1px solid #e2e8f0; background:#fafafa;">
                </div>
            </div>
            @error('content')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Right: Actions --}}
    <div class="space-y-5">
        <div class="card p-5 space-y-3">
            <p class="text-xs font-bold" style="color:#0f3020;">การดำเนินการ</p>
            <button type="submit" class="btn-primary w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                บันทึกการแก้ไข
            </button>
            <a href="{{ route('privacy.show', $notice) }}" class="btn-outline w-full justify-center">ยกเลิก</a>
        </div>

        <div class="card p-4">
            <p class="text-xs font-bold mb-3" style="color:#0f3020;">ตัวเลือกอื่น</p>
            <form method="POST" action="{{ route('privacy.new-version', $notice) }}">
                @csrf
                <button type="submit" class="btn-outline w-full justify-center mb-2" style="border-color:#7c3aed; color:#7c3aed; font-size:12px;">
                    สร้างเวอร์ชันใหม่ (v{{ $notice->version + 1 }})
                </button>
            </form>
            @if($notice->getStatus() !== 'published')
            <form method="POST" action="{{ route('privacy.publish', $notice) }}">
                @csrf
                <button type="submit" class="btn-outline w-full justify-center" style="font-size:12px;">
                    เผยแพร่ทันทีหลังบันทึก
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
</form>

@push('scripts')
<style>
    .editor-btn { font-size:11px; padding:3px 8px; border-radius:6px; border:1px solid #e2e8f0; background:white; color:#475569; cursor:pointer; font-weight:600; }
    .editor-btn:hover { background:#f1f5f9; }
    .privacy-content h1,.privacy-content h2 { font-size:1.15em; font-weight:700; color:#0f3020; margin: 1em 0 .4em; }
    .privacy-content h3 { font-size:1em; font-weight:700; color:#15572e; margin: .9em 0 .3em; }
    .privacy-content p { margin-bottom:.6em; }
    .privacy-content ul,.privacy-content ol { padding-left:1.4em; margin-bottom:.6em; }
</style>
<script>
function switchTab(tab) {
    if (tab === 'preview') {
        document.getElementById('preview-content').innerHTML = document.getElementById('content-editor').value || '<p style="color:#94a3b8;text-align:center;padding:40px 0">ยังไม่มีเนื้อหา</p>';
        document.getElementById('editor-panel').classList.add('hidden');
        document.getElementById('preview-panel').classList.remove('hidden');
        document.getElementById('tab-preview').style.cssText = 'background:#15572e;color:white;';
        document.getElementById('tab-editor').style.cssText  = 'background:#f1f5f9;color:#64748b;';
    } else {
        document.getElementById('preview-panel').classList.add('hidden');
        document.getElementById('editor-panel').classList.remove('hidden');
        document.getElementById('tab-editor').style.cssText  = 'background:#15572e;color:white;';
        document.getElementById('tab-preview').style.cssText = 'background:#f1f5f9;color:#64748b;';
    }
}
function insertTag(tag) {
    const ta = document.getElementById('content-editor');
    const s = ta.selectionStart, e = ta.selectionEnd, sel = ta.value.substring(s,e);
    ta.value = ta.value.substring(0,s) + `<${tag}>${sel||'ข้อความ'}</${tag}>` + ta.value.substring(e);
    ta.focus();
}
function insertList(type) {
    const ta = document.getElementById('content-editor');
    const s = ta.selectionStart;
    ta.value = ta.value.substring(0,s) + `\n<${type}>\n  <li>รายการที่ 1</li>\n  <li>รายการที่ 2</li>\n</${type}>\n` + ta.value.substring(s);
    ta.focus();
}
</script>
@endpush

@endsection
