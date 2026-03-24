@extends('layouts.app')
@section('title', 'เพิ่ม External Party')
@section('content')
<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('parties.index') }}" class="btn-outline text-sm">← กลับ</a>
    <h1 class="text-2xl font-bold" style="color:#15572e;">เพิ่ม External Party</h1>
</div>

<form method="POST" action="{{ route('parties.store') }}" x-data="partyForm()">
@csrf
<div class="grid gap-6" style="grid-template-columns:1fr 300px;">
<div class="space-y-6">

{{-- Relationship Type ────────────────────────────────────────────────────── --}}
<div class="card">
    <h3 class="font-semibold mb-4" style="color:#15572e;">🔗 Relationship กับองค์กรเรา</h3>
    <div class="grid grid-cols-2 gap-3">
        @foreach([
            ['data_processor',   'Data Processor',   '⚙️', 'เราจ้างเขาเป็น DP — Cloud, Vendor, SaaS','#dbeafe','#1d4ed8'],
            ['data_controller',  'Data Controller',  '🏢', 'เขาจ้างเราเป็น DP — ลูกค้าที่เราให้บริการ','#dcfce7','#15572e'],
            ['joint_controller', 'Joint Controller', '🤝', 'ควบคุมข้อมูลร่วมกัน — บริษัทในเครือ','#ede9fe','#7c3aed'],
            ['sub_processor',    'Sub-Processor',    '🔗', 'ประมวลผลต่อจาก Processor ของเรา','#e0f2fe','#0369a1'],
            ['recipient',        'Recipient',        '📤', 'รับข้อมูลที่เราเปิดเผย — หน่วยงานรัฐ, Partner','#fef3c7','#d97706'],
            ['third_party',      'Third Party',      '👥', 'บุคคลที่สามทั่วไป — ไม่ต้องมี DPA','#f1f5f9','#64748b'],
        ] as [$val,$label,$icon,$desc,$bg,$color])
        <label class="cursor-pointer rounded-xl p-3 border-2 transition" :class="relType==='{{ $val }}' ? 'border-current' : 'border-transparent'"
               :style="relType==='{{ $val }}' ? 'background:{{ $bg }};border-color:{{ $color }};' : 'background:#f8fafc;'"
               @click="relType='{{ $val }}'">
            <input type="radio" name="relationship_type" value="{{ $val }}" x-model="relType" class="sr-only" required>
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
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อ (ไทย) <span style="color:#c0272d;">*</span></label>
                <input type="text" name="name" class="form-input" required placeholder="บริษัท XYZ จำกัด">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อ (อังกฤษ)</label>
                <input type="text" name="name_en" class="form-input" placeholder="XYZ Co., Ltd.">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ประเภท</label>
                <select name="type" class="form-input">
                    @foreach(['company'=>'บริษัท','individual'=>'บุคคล','government'=>'หน่วยงานรัฐ','ngo'=>'NGO','academic'=>'สถาบันการศึกษา','other'=>'อื่นๆ'] as $v=>$l)
                        <option value="{{ $v }}">{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ประเทศ</label>
                <input type="text" name="country" class="form-input" value="TH" maxlength="2" placeholder="TH" style="text-transform:uppercase;">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">อุตสาหกรรม</label>
                <input type="text" name="industry" class="form-input" placeholder="Cloud Computing">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">เลขนิติบุคคล/Tax ID</label>
                <input type="text" name="tax_id" class="form-input" placeholder="0105556000000">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">Risk Level</label>
                <select name="risk_level" class="form-input" required>
                    @foreach(['low','medium','high','critical'] as $r)
                        <option value="{{ $r }}" {{ $r==='medium'?'selected':'' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">รายละเอียดบริการ/ความสัมพันธ์</label>
            <textarea name="services_description" class="form-input" rows="2" placeholder="อธิบายว่าเขาให้บริการอะไร หรือเราทำอะไรให้เขา"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">Status</label>
                <select name="status" class="form-input" required>
                    @foreach(['active','under_review','inactive'] as $s)
                        <option value="{{ $s }}" {{ $s==='active'?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">วันเริ่มสัมพันธ์</label>
                <input type="date" name="relationship_started_at" class="form-input">
            </div>
        </div>
    </div>
</div>

{{-- Cross-border ─────────────────────────────────────────────────────────── --}}
<div class="card" x-data="{ cross: false }">
    <div class="flex items-center gap-3 mb-4">
        <h3 class="font-semibold" style="color:#15572e;">🌐 Cross-border Transfer</h3>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_cross_border" value="1" x-model="cross" class="accent-orange-500">
            <span class="text-sm">มีการส่งข้อมูลข้ามพรมแดน</span>
        </label>
    </div>
    <div x-show="cross" class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">กลไกการโอน</label>
                <select name="transfer_mechanism" class="form-input">
                    @foreach(['adequacy_decision'=>'Adequacy Decision','scc'=>'Standard Contractual Clauses (SCC)','bcr'=>'Binding Corporate Rules','explicit_consent'=>'Explicit Consent','public_interest'=>'Public Interest','derogation'=>'Derogation','other'=>'อื่นๆ'] as $v=>$l)
                        <option value="{{ $v }}">{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ประเทศปลายทาง (คั่นด้วย ,)</label>
                <input type="text" name="transfer_countries_raw" class="form-input" placeholder="SG, US, IE">
            </div>
        </div>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="tia_required" value="1" class="accent-orange-500">
            <span class="text-sm">ต้องทำ Transfer Impact Assessment (TIA)</span>
        </label>
    </div>
</div>

{{-- Contact ──────────────────────────────────────────────────────────────── --}}
<div class="card">
    <h3 class="font-semibold mb-4" style="color:#15572e;">📞 ผู้ติดต่อ & DPO</h3>
    <div class="grid grid-cols-2 gap-3">
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อผู้ติดต่อ</label><input type="text" name="contact_name" class="form-input"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">อีเมล</label><input type="email" name="contact_email" class="form-input"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อ DPO</label><input type="text" name="dpo_name" class="form-input"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">อีเมล DPO</label><input type="email" name="dpo_email" class="form-input"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">เว็บไซต์</label><input type="url" name="website" class="form-input" placeholder="https://"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">โทรศัพท์</label><input type="text" name="phone" class="form-input"></div>
    </div>
</div>

{{-- Initial DPA ──────────────────────────────────────────────────────────── --}}
<div class="card" x-show="['data_processor','data_controller','joint_controller'].includes(relType)">
    <h3 class="font-semibold mb-4" style="color:#15572e;">📄 DPA เริ่มต้น (ไม่บังคับ)</h3>
    <div class="grid grid-cols-2 gap-3">
        <div class="col-span-2"><label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อ DPA</label><input type="text" name="dpa_title" class="form-input" placeholder="เช่น DPA 2025 v1.0"></div>
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">ประเภท</label>
            <select name="dpa_type" class="form-input">
                <option value="dpa">DPA</option><option value="jca">JCA</option><option value="data_sharing_agreement">DSA</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">สถานะ</label>
            <select name="dpa_status" class="form-input">
                <option value="draft">Draft</option><option value="pending_signature">Pending Signature</option><option value="active">Active</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">Role เรา</label>
            <select name="dpa_our_role" class="form-input">
                <option value="controller">Controller</option><option value="processor">Processor</option><option value="joint_controller">Joint Controller</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">Role เขา</label>
            <select name="dpa_their_role" class="form-input">
                <option value="processor">Processor</option><option value="controller">Controller</option><option value="joint_controller">Joint Controller</option>
            </select>
        </div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">วันที่ลงนาม</label><input type="date" name="dpa_signed_at" class="form-input"></div>
        <div><label class="block text-sm font-medium mb-1" style="color:#374151;">วันหมดอายุ</label><input type="date" name="dpa_expires_at" class="form-input"></div>
    </div>
</div>

</div>
{{-- Sidebar ─────────────────────────────────────────────────────────────── --}}
<div>
    <div class="card sticky top-6">
        <h3 class="font-semibold mb-4" style="color:#15572e;">⚡ บันทึก</h3>
        <button type="submit" class="btn-primary w-full">💾 สร้าง External Party</button>
        <a href="{{ route('parties.index') }}" class="btn-outline w-full text-center block mt-2">ยกเลิก</a>
        <div class="mt-4 pt-3" style="border-top:1px solid #e2e8f0;">
            <p class="text-xs" style="color:#94a3b8;">Relationship Type ที่เลือก:</p>
            <p class="text-sm font-bold mt-1" style="color:#15572e;" x-text="relType || '—'"></p>
        </div>
    </div>
</div>
</div>
</form>

<script>
function partyForm() { return { relType: 'data_processor' }; }
</script>
@endsection
