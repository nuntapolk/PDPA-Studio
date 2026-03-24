@extends('layouts.app')
@section('title', 'สร้าง Privacy Notice — PDPA Studio')
@section('page-title', 'สร้าง Privacy Notice ใหม่')

@section('content')

<div class="mb-5">
    <a href="{{ route('privacy.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium" style="color:#15572e;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        กลับหน้ารายการ
    </a>
</div>

<form method="POST" action="{{ route('privacy.store') }}" id="notice-form">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Content --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Type Selector --}}
        <div class="card p-5">
            <h3 class="text-sm font-bold mb-4" style="color:#0f3020;">ประเภทประกาศ</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach([
                    ['privacy_policy',     'นโยบายความเป็นส่วนตัว', '#15572e','#e8f0eb','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['cookie_policy',      'นโยบาย Cookie',          '#0369a1','#e0f2fe','M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['employee_notice',    'ประกาศพนักงาน',          '#7c3aed','#ede9fe','M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0'],
                    ['cctv_notice',        'ประกาศกล้องวงจรปิด',     '#b45309','#fef3c7','M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                    ['marketing_notice',   'ประกาศการตลาด',           '#db2777','#fce7f3','M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
                    ['third_party_notice', 'ประกาศบุคคลที่สาม',      '#475569','#f1f5f9','M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'],
                ] as [$val, $label, $color, $bg, $icon])
                @php $selected = old('type', 'privacy_policy') === $val; @endphp
                <label class="type-card cursor-pointer rounded-xl p-3 flex flex-col items-center gap-2 transition-all"
                       style="border:2px solid {{ $selected ? $color : '#e2e8f0' }}; background:{{ $selected ? $bg : 'white' }};"
                       data-color="{{ $color }}" data-bg="{{ $bg }}" data-val="{{ $val }}">
                    <input type="radio" name="type" value="{{ $val }}" class="hidden" {{ $selected ? 'checked' : '' }}>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:{{ $bg }};">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:{{ $color }};"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-center leading-tight" style="color:{{ $color }};">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Title & Meta --}}
        <div class="card p-5 space-y-4">
            <h3 class="text-sm font-bold" style="color:#0f3020;">ข้อมูลทั่วไป</h3>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">ชื่อประกาศ <span style="color:#c0272d;">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-input w-full" placeholder="เช่น นโยบายความเป็นส่วนตัว บริษัท XYZ จำกัด" required>
                @error('title')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">ภาษา</label>
                    <select name="language" class="form-input w-full">
                        <option value="th" {{ old('language','th')==='th' ? 'selected':'' }}>🇹🇭 ภาษาไทย</option>
                        <option value="en" {{ old('language')==='en' ? 'selected':'' }}>🇬🇧 English</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">เวอร์ชัน</label>
                    <input type="number" name="version" value="{{ old('version',1) }}" min="1" class="form-input w-full">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">วันที่มีผลบังคับใช้</label>
                    <input type="text" name="effective_date" value="{{ old('effective_date') }}" class="form-input w-full" placeholder="เช่น 1 มกราคม 2567">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">วันหมดอายุ (ถ้ามี)</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at') }}" class="form-input w-full">
                </div>
            </div>
        </div>

        {{-- Content Editor --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold" style="color:#0f3020;">เนื้อหาประกาศ <span style="color:#c0272d;">*</span></h3>
                <div class="flex gap-2">
                    <button type="button" onclick="switchTab('editor')" id="tab-editor"
                            class="text-xs px-3 py-1.5 rounded-lg font-medium transition"
                            style="background:#15572e; color:white;">แก้ไข</button>
                    <button type="button" onclick="switchTab('preview')" id="tab-preview"
                            class="text-xs px-3 py-1.5 rounded-lg font-medium transition"
                            style="background:#f1f5f9; color:#64748b;">ตัวอย่าง</button>
                </div>
            </div>

            {{-- Editor toolbar --}}
            <div id="editor-panel">
                <div class="flex flex-wrap gap-1 mb-2 p-2 rounded-lg" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <button type="button" onclick="fmt('bold')" class="editor-btn" title="ตัวหนา"><b>B</b></button>
                    <button type="button" onclick="fmt('italic')" class="editor-btn" title="ตัวเอียง"><i>I</i></button>
                    <button type="button" onclick="fmt('underline')" class="editor-btn" title="ขีดเส้นใต้"><u>U</u></button>
                    <span style="width:1px; background:#e2e8f0; margin:0 4px;"></span>
                    <button type="button" onclick="insertTag('h2')" class="editor-btn">H2</button>
                    <button type="button" onclick="insertTag('h3')" class="editor-btn">H3</button>
                    <button type="button" onclick="insertTag('p')" class="editor-btn">¶</button>
                    <span style="width:1px; background:#e2e8f0; margin:0 4px;"></span>
                    <button type="button" onclick="insertTemplate()" class="editor-btn" style="font-size:10px; padding:3px 8px; background:#e8f0eb; color:#15572e; border-color:#c5d9cb;">แทรก Template</button>
                </div>
                <textarea id="content-editor" name="content" rows="18"
                          class="form-input w-full font-mono text-xs"
                          style="resize:vertical; line-height:1.6;"
                          placeholder="<h2>หัวข้อหลัก</h2>&#10;<p>เนื้อหา...</p>"
                          required>{{ old('content') }}</textarea>
            </div>

            {{-- Preview panel --}}
            <div id="preview-panel" class="hidden">
                <div id="preview-content"
                     class="rounded-xl p-5 min-h-64"
                     style="border:1px solid #e2e8f0; background:#fafafa; font-family:'Sarabun','Noto Sans Thai',sans-serif; font-size:14px; line-height:1.8; color:#1e293b;">
                    <p style="color:#94a3b8; text-align:center; padding: 40px 0;">ยังไม่มีเนื้อหา</p>
                </div>
            </div>

            @error('content')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
        </div>
    </div>

    {{-- Right: Tips + Submit --}}
    <div class="space-y-5">

        {{-- Tips by type --}}
        <div class="card p-4" id="type-tips" style="border-left:3px solid #15572e;">
            <p class="text-xs font-bold mb-2" style="color:#15572e;">💡 สิ่งที่ต้องระบุ</p>
            <ul id="tips-list" class="text-xs space-y-1.5" style="color:#475569;">
                <li>• วัตถุประสงค์การเก็บข้อมูล</li>
                <li>• ประเภทข้อมูลที่เก็บรวบรวม</li>
                <li>• ระยะเวลาการเก็บรักษา</li>
                <li>• สิทธิ์ของเจ้าของข้อมูล</li>
                <li>• ช่องทางติดต่อ DPO</li>
                <li>• ฐานทางกฎหมาย (Legal Basis)</li>
            </ul>
        </div>

        {{-- PDPA Checklist --}}
        <div class="card p-4">
            <p class="text-xs font-bold mb-3" style="color:#0f3020;">✅ Checklist PDPA ม.23</p>
            @foreach(['ชื่อและที่อยู่ผู้ควบคุม','วัตถุประสงค์การประมวลผล','ประเภทข้อมูลที่เก็บ','ระยะเวลาเก็บรักษา','สิทธิ์เจ้าของข้อมูล','ผู้รับข้อมูล (ถ้ามี)','ช่องทางติดต่อ'] as $item)
            <label class="flex items-center gap-2 py-1.5 cursor-pointer" style="border-bottom:1px solid #f1f5f9;">
                <input type="checkbox" class="w-3.5 h-3.5 rounded" style="accent-color:#15572e;">
                <span class="text-xs" style="color:#475569;">{{ $item }}</span>
            </label>
            @endforeach
        </div>

        {{-- Actions --}}
        <div class="card p-4 space-y-3">
            <button type="submit" class="btn-primary w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                บันทึกเป็นร่าง
            </button>
            <a href="{{ route('privacy.index') }}" class="btn-outline w-full justify-center">ยกเลิก</a>
        </div>

    </div>
</div>
</form>

@push('scripts')
<style>
    .editor-btn {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        cursor: pointer;
        font-weight: 600;
        transition: all .15s;
    }
    .editor-btn:hover { background: #f1f5f9; }
    .type-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); transform: translateY(-1px); }
    #preview-content h1,#preview-content h2 { font-size:1.15em; font-weight:700; color:#0f3020; margin: 1em 0 .4em; }
    #preview-content h3 { font-size:1em; font-weight:700; color:#15572e; margin: .9em 0 .3em; }
    #preview-content p { margin-bottom: .6em; }
    #preview-content ul,#preview-content ol { padding-left:1.4em; margin-bottom:.6em; }
    #preview-content li { margin-bottom:.2em; }
    #preview-content strong { color:#1e293b; }
</style>
<script>
// Type card selector
document.querySelectorAll('.type-card').forEach(card => {
    card.addEventListener('click', () => {
        const val = card.dataset.val, color = card.dataset.color, bg = card.dataset.bg;
        document.querySelectorAll('.type-card').forEach(c => {
            c.style.borderColor = '#e2e8f0';
            c.style.background  = 'white';
        });
        card.style.borderColor = color;
        card.style.background  = bg;
        card.querySelector('input[type=radio]').checked = true;
    });
});

// Tab switch
function switchTab(tab) {
    if (tab === 'preview') {
        document.getElementById('preview-content').innerHTML =
            document.getElementById('content-editor').value || '<p style="color:#94a3b8;text-align:center;padding:40px 0">ยังไม่มีเนื้อหา</p>';
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

// Simple formatting
function fmt(cmd) { document.execCommand(cmd); }
function insertTag(tag) {
    const ta = document.getElementById('content-editor');
    const start = ta.selectionStart, end = ta.selectionEnd, sel = ta.value.substring(start, end);
    const replacement = `<${tag}>${sel || 'ข้อความ'}</${tag}>`;
    ta.value = ta.value.substring(0, start) + replacement + ta.value.substring(end);
    ta.focus();
}

// Template insert
const templates = {
    privacy_policy: `<h2>นโยบายความเป็นส่วนตัว</h2>\n<p>บริษัท [ชื่อบริษัท] ("บริษัท") ให้ความสำคัญกับการคุ้มครองข้อมูลส่วนบุคคลของท่าน นโยบายนี้อธิบายถึงวิธีที่เราเก็บรวบรวม ใช้ และเปิดเผยข้อมูลของท่าน</p>\n\n<h3>1. ข้อมูลที่เราเก็บรวบรวม</h3>\n<p>เราเก็บรวบรวมข้อมูลต่อไปนี้...</p>\n\n<h3>2. วัตถุประสงค์การใช้ข้อมูล</h3>\n<p>เราใช้ข้อมูลของท่านเพื่อ...</p>\n\n<h3>3. ระยะเวลาการเก็บรักษาข้อมูล</h3>\n<p>เราจะเก็บรักษาข้อมูลของท่านไว้เป็นระยะเวลา...</p>\n\n<h3>4. สิทธิ์ของเจ้าของข้อมูล</h3>\n<p>ท่านมีสิทธิ์ในการเข้าถึง แก้ไข ลบ และโอนย้ายข้อมูลส่วนบุคคลของท่าน</p>\n\n<h3>5. ช่องทางติดต่อ</h3>\n<p>หากมีข้อสงสัย กรุณาติดต่อ DPO: [อีเมล DPO]</p>`,
    cookie_policy: `<h2>นโยบาย Cookie</h2>\n<p>เว็บไซต์ของเราใช้ Cookie เพื่อปรับปรุงประสบการณ์การใช้งาน</p>\n\n<h3>1. Cookie คืออะไร?</h3>\n<p>Cookie คือไฟล์ข้อความขนาดเล็กที่จัดเก็บในอุปกรณ์ของท่าน</p>\n\n<h3>2. ประเภท Cookie ที่เราใช้</h3>\n<p><strong>Cookie จำเป็น:</strong> ใช้สำหรับการทำงานพื้นฐานของเว็บไซต์</p>\n<p><strong>Cookie วิเคราะห์:</strong> ใช้เพื่อวิเคราะห์การใช้งานเว็บไซต์</p>\n<p><strong>Cookie การตลาด:</strong> ใช้สำหรับแสดงโฆษณาที่เกี่ยวข้อง</p>\n\n<h3>3. การจัดการ Cookie</h3>\n<p>ท่านสามารถปฏิเสธหรือลบ Cookie ได้ผ่านการตั้งค่าเบราว์เซอร์</p>`,
    employee_notice: `<h2>ประกาศความเป็นส่วนตัวสำหรับพนักงาน</h2>\n<p>บริษัท [ชื่อบริษัท] เก็บรวบรวมและใช้ข้อมูลส่วนบุคคลของพนักงานเพื่อวัตถุประสงค์ดังนี้</p>\n\n<h3>1. ข้อมูลที่เก็บรวบรวม</h3>\n<p>ข้อมูลส่วนตัว ข้อมูลการจ้างงาน ข้อมูลเงินเดือน ข้อมูลสุขภาพ (เฉพาะที่จำเป็น)</p>\n\n<h3>2. วัตถุประสงค์</h3>\n<p>การบริหารงานบุคคล การจ่ายเงินเดือน การปฏิบัติตามกฎหมายแรงงาน</p>\n\n<h3>3. สิทธิ์ของพนักงาน</h3>\n<p>พนักงานมีสิทธิ์เข้าถึงและแก้ไขข้อมูลส่วนตัวของตนเองได้</p>`,
    cctv_notice: `<h2>ประกาศการใช้กล้องวงจรปิด (CCTV)</h2>\n<p>บริษัทได้ติดตั้งกล้องวงจรปิดในพื้นที่ [ระบุพื้นที่] เพื่อวัตถุประสงค์ด้านความปลอดภัย</p>\n\n<h3>1. วัตถุประสงค์</h3>\n<p>เพื่อป้องกันและระงับเหตุอาชญากรรม และเพื่อความปลอดภัยของบุคคลและทรัพย์สิน</p>\n\n<h3>2. ระยะเวลาการเก็บภาพ</h3>\n<p>ภาพวิดีโอจะถูกเก็บรักษาไว้เป็นระยะเวลา 30 วัน หลังจากนั้นจะถูกลบออกโดยอัตโนมัติ</p>\n\n<h3>3. การเข้าถึงภาพ</h3>\n<p>ภาพจาก CCTV จะถูกเข้าถึงได้เฉพาะเจ้าหน้าที่ที่ได้รับอนุญาตเท่านั้น</p>`,
};

function insertTemplate() {
    const type = document.querySelector('input[name="type"]:checked')?.value || 'privacy_policy';
    const tpl = templates[type] || templates.privacy_policy;
    const ta = document.getElementById('content-editor');
    if (!ta.value || confirm('แทรก Template จะแทนที่เนื้อหาปัจจุบัน ต้องการดำเนินการหรือไม่?')) {
        ta.value = tpl;
    }
}
</script>
@endpush

@endsection
