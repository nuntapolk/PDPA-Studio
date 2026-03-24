@extends('layouts.app')
@section('title', 'แก้ไข External Party — '.$party->name)
@section('content')
<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('parties.show', $party) }}" class="btn-outline text-sm">← กลับ</a>
    <h1 class="text-2xl font-bold" style="color:#15572e;">แก้ไข External Party</h1>
    @if($party->code)
        <span class="text-sm font-mono px-2 py-0.5 rounded" style="background:#f1f5f9;color:#64748b;">{{ $party->code }}</span>
    @endif
</div>

<form method="POST" action="{{ route('parties.update', $party) }}" x-data="partyForm()">
@csrf @method('PUT')
<div class="grid gap-6" style="grid-template-columns:1fr 300px;">
<div class="space-y-6">

{{-- Relationship Type ────────────────────────────────────────────────────── --}}
<div class="card">
    <h3 class="font-semibold mb-4" style="color:#15572e;">🔗 Relationship กับองค์กรเรา</h3>
    <div class="grid grid-cols-2 gap-3">
        @foreach([
            ['data_processor',       'Data Processor',       '⚙️', 'เราจ้างเขาเป็น DP — Cloud, Vendor, SaaS','#dbeafe','#1d4ed8'],
            ['data_controller',      'Data Controller',      '🏢', 'เขาจ้างเราเป็น DP — ลูกค้าที่เราให้บริการ','#dcfce7','#15572e'],
            ['joint_controller',     'Joint Controller',     '🤝', 'ควบคุมข้อมูลร่วมกัน — บริษัทในเครือ','#ede9fe','#7c3aed'],
            ['sub_processor',        'Sub-Processor',        '🔗', 'ประมวลผลต่อจาก Processor ของเรา','#e0f2fe','#0369a1'],
            ['recipient',            'Recipient',            '📤', 'รับข้อมูลที่เราเปิดเผย — หน่วยงานรัฐ, Partner','#fef3c7','#d97706'],
            ['third_party',          'Third Party',          '👥', 'บุคคลที่สามทั่วไป','#f1f5f9','#64748b'],
            ['supervisory_authority','Supervisory Authority','⚖️', 'หน่วยงานกำกับดูแล — PDPC, กสทช.','#fde8d8','#c0272d'],
        ] as [$val,$label,$icon,$desc,$bg,$color])
        <label class="cursor-pointer rounded-xl p-3 border-2 transition" :class="relType==='{{ $val }}' ? 'border-current' : 'border-transparent'"
               :style="relType==='{{ $val }}' ? 'background:{{ $bg }};border-color:{{ $color }};' : 'background:#f8fafc;'"
               @click="relType='{{ $val }}'">
            <input type="radio" name="relationship_type" value="{{ $val }}"
                   x-model="relType" class="sr-only"
                   {{ $party->relationship_type === $val ? 'checked' : '' }} required>
            <div class="font-medium text-sm" style="color:{{ $color }};">{{ $icon }} {{ $label }}</div>
            <div class="text-xs mt-0.5" style="color:#64748b;">{{ $desc }}</div>
        </label>
        @endforeach
    </div>
</div>

{{-- Basic Info ───────────────────────────────────────────────────────────── --}}
<div class="card">
    <h3 class="font-semibold mb-4" style="color:#15572e;">📋 ข้อมูลองค์กร</h3>
    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">รหัส (Code)</label>
                <input type="text" name="code" class="form-input" value="{{ old('code',$party->code) }}" placeholder="EP-0001" maxlength="30">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อ (ไทย) <span style="color:#c0272d;">*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name',$party->name) }}" required>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อ (อังกฤษ)</label>
                <input type="text" name="name_en" class="form-input" value="{{ old('name_en',$party->name_en) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ประเภทองค์กร</label>
                <select name="type" class="form-input">
                    @foreach(['company'=>'บริษัท','individual'=>'บุคคล','government'=>'หน่วยงานรัฐ','ngo'=>'NGO','academic'=>'สถาบันการศึกษา','other'=>'อื่นๆ'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('type',$party->type)===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ประเทศ</label>
                <input type="text" name="country" class="form-input" value="{{ old('country',$party->country) }}" maxlength="2" style="text-transform:uppercase;">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">อุตสาหกรรม</label>
                <input type="text" name="industry" class="form-input" value="{{ old('industry',$party->industry) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">เลขนิติบุคคล/Tax ID</label>
                <input type="text" name="tax_id" class="form-input" value="{{ old('tax_id',$party->tax_id) }}">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">รายละเอียดบริการ/ความสัมพันธ์</label>
            <textarea name="services_description" class="form-input" rows="2">{{ old('services_description',$party->services_description) }}</textarea>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">Risk Level</label>
                <select name="risk_level" class="form-input" required>
                    @foreach(['low','medium','high','critical'] as $r)
                        <option value="{{ $r }}" {{ old('risk_level',$party->risk_level)===$r?'selected':'' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">Status</label>
                <select name="status" class="form-input" required>
                    @foreach(['active','inactive','under_review','suspended','terminated'] as $s)
                        <option value="{{ $s }}" {{ old('status',$party->status)===$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">รอบตรวจสอบ (เดือน)</label>
                <input type="number" name="review_frequency_months" class="form-input" value="{{ old('review_frequency_months',$party->review_frequency_months) }}" min="1" max="60">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">วันเริ่มสัมพันธ์</label>
                <input type="date" name="relationship_started_at" class="form-input"
                       value="{{ old('relationship_started_at', $party->relationship_started_at?->format('Y-m-d')) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">วันสิ้นสุดสัมพันธ์</label>
                <input type="date" name="relationship_ended_at" class="form-input"
                       value="{{ old('relationship_ended_at', $party->relationship_ended_at?->format('Y-m-d')) }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">วันตรวจสอบครั้งถัดไป</label>
                <input type="date" name="next_review_date" class="form-input"
                       value="{{ old('next_review_date', $party->next_review_date?->format('Y-m-d')) }}">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">หมายเหตุ</label>
            <textarea name="notes" class="form-input" rows="2">{{ old('notes',$party->notes) }}</textarea>
        </div>
    </div>
</div>

{{-- Cross-border ─────────────────────────────────────────────────────────── --}}
<div class="card" x-data="{ cross: {{ $party->is_cross_border ? 'true' : 'false' }} }">
    <div class="flex items-center gap-3 mb-4">
        <h3 class="font-semibold" style="color:#15572e;">🌐 Cross-border Transfer</h3>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_cross_border" value="1" x-model="cross"
                   {{ $party->is_cross_border ? 'checked' : '' }} class="accent-orange-500">
            <span class="text-sm">มีการส่งข้อมูลข้ามพรมแดน</span>
        </label>
    </div>
    <div x-show="cross" class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">กลไกการโอน</label>
                <select name="transfer_mechanism" class="form-input">
                    @foreach(['adequacy_decision'=>'Adequacy Decision','scc'=>'Standard Contractual Clauses (SCC)','bcr'=>'Binding Corporate Rules (BCR)','explicit_consent'=>'Explicit Consent','vital_interest'=>'Vital Interest','public_interest'=>'Public Interest','derogation'=>'Derogation','other'=>'อื่นๆ'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('transfer_mechanism',$party->transfer_mechanism)===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ประเทศปลายทาง (คั่นด้วย ,)</label>
                <input type="text" name="transfer_countries_raw" class="form-input"
                       value="{{ old('transfer_countries_raw', is_array($party->transfer_countries) ? implode(', ',$party->transfer_countries) : '') }}"
                       placeholder="SG, US, IE">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="tia_required" value="1" {{ $party->tia_required ? 'checked' : '' }} class="accent-orange-500">
                <span class="text-sm">ต้องทำ Transfer Impact Assessment (TIA)</span>
            </label>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">วันที่ทำ TIA เสร็จ</label>
                <input type="date" name="tia_completed_at" class="form-input"
                       value="{{ old('tia_completed_at', $party->tia_completed_at?->format('Y-m-d')) }}">
            </div>
        </div>
    </div>
</div>

{{-- Services & Data ──────────────────────────────────────────────────────── --}}
<div class="card">
    <h3 class="font-semibold mb-4" style="color:#15572e;">📦 บริการ & ข้อมูลที่แชร์</h3>
    <div class="space-y-3">
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">ประเภทข้อมูลที่แชร์ (แต่ละบรรทัด)</label>
            <textarea name="data_types_raw" class="form-input font-mono text-xs" rows="3"
                      placeholder="ชื่อ-นามสกุล&#10;อีเมล&#10;เลขบัตรประชาชน">{{ old('data_types_raw', is_array($party->data_types_shared) ? implode("\n",$party->data_types_shared) : '') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">วัตถุประสงค์การประมวลผล (แต่ละบรรทัด)</label>
            <textarea name="purposes_raw" class="form-input font-mono text-xs" rows="3"
                      placeholder="ส่งอีเมล Marketing&#10;ประมวลผลชำระเงิน">{{ old('purposes_raw', is_array($party->processing_purposes) ? implode("\n",$party->processing_purposes) : '') }}</textarea>
        </div>
    </div>
</div>

{{-- Contact ──────────────────────────────────────────────────────────────── --}}
<div class="card">
    <h3 class="font-semibold mb-4" style="color:#15572e;">📞 ผู้ติดต่อ</h3>
    <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อผู้ติดต่อ</label><input type="text" name="contact_name" class="form-input" value="{{ old('contact_name',$party->contact_name) }}"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">อีเมลผู้ติดต่อ</label><input type="email" name="contact_email" class="form-input" value="{{ old('contact_email',$party->contact_email) }}"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">โทรศัพท์ผู้ติดต่อ</label><input type="text" name="contact_phone" class="form-input" value="{{ old('contact_phone',$party->contact_phone) }}"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">เว็บไซต์</label><input type="url" name="website" class="form-input" value="{{ old('website',$party->website) }}" placeholder="https://"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">ที่อยู่</label><textarea name="address" class="form-input" rows="2">{{ old('address',$party->address) }}</textarea></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">อีเมลองค์กร</label><input type="email" name="email" class="form-input" value="{{ old('email',$party->email) }}"></div>
    </div>
</div>

{{-- DPO Contact ───────────────────────────────────────────────────────────── --}}
<div class="card">
    <h3 class="font-semibold mb-4" style="color:#15572e;">🛡️ DPO ของ External Party</h3>
    <div class="grid grid-cols-3 gap-3">
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อ DPO</label><input type="text" name="dpo_name" class="form-input" value="{{ old('dpo_name',$party->dpo_name) }}"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">อีเมล DPO</label><input type="email" name="dpo_email" class="form-input" value="{{ old('dpo_email',$party->dpo_email) }}"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">โทรศัพท์ DPO</label><input type="text" name="dpo_phone" class="form-input" value="{{ old('dpo_phone',$party->dpo_phone) }}"></div>
    </div>
</div>

</div>
{{-- Sidebar ─────────────────────────────────────────────────────────────── --}}
<div>
    <div class="card sticky top-6 space-y-3">
        <h3 class="font-semibold mb-2" style="color:#15572e;">⚡ บันทึกการแก้ไข</h3>
        <button type="submit" class="btn-primary w-full">💾 บันทึกการแก้ไข</button>
        <a href="{{ route('parties.show', $party) }}" class="btn-outline w-full text-center block">ยกเลิก</a>

        <div class="pt-3" style="border-top:1px solid #e2e8f0;">
            <p class="text-xs font-medium mb-2" style="color:#64748b;">Relationship Type ปัจจุบัน:</p>
            <p class="text-sm font-bold" style="color:#15572e;" x-text="relType || '—'"></p>
        </div>

        <div class="pt-3" style="border-top:1px solid #e2e8f0;">
            <p class="text-xs font-medium mb-1" style="color:#64748b;">สร้างเมื่อ</p>
            <p class="text-xs" style="color:#374151;">{{ $party->created_at->format('d M Y H:i') }}</p>
            @if($party->creator)
            <p class="text-xs" style="color:#94a3b8;">โดย {{ $party->creator->name }}</p>
            @endif
        </div>

        @if($party->dpas->count() > 0)
        <div class="pt-3" style="border-top:1px solid #e2e8f0;">
            <p class="text-xs font-medium mb-1" style="color:#64748b;">DPA ที่เชื่อมอยู่</p>
            @foreach($party->dpas->take(3) as $dpa)
            <div class="text-xs py-1" style="color:#374151;">📄 {{ Str::limit($dpa->title,30) }}</div>
            @endforeach
            @if($party->dpas->count() > 3)
            <p class="text-xs" style="color:#94a3b8;">+{{ $party->dpas->count()-3 }} รายการ</p>
            @endif
        </div>
        @endif

        <div class="pt-3" style="border-top:1px solid #e2e8f0;">
            <form method="POST" action="{{ route('parties.destroy', $party) }}"
                  onsubmit="return confirm('ลบ External Party นี้? การดำเนินการนี้ไม่สามารถเลิกทำได้')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full text-sm py-1.5 px-3 rounded-lg border transition"
                        style="border-color:#fca5a5;color:#c0272d;"
                        onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='transparent'">
                    🗑 ลบ External Party นี้
                </button>
            </form>
        </div>
    </div>
</div>
</div>
</form>

<script>
function partyForm() {
    return { relType: '{{ $party->relationship_type }}' };
}
</script>
@endsection
