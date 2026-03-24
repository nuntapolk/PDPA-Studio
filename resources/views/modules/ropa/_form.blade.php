@php
    $isEdit = isset($ropa) && $ropa !== null;
    $v = fn($field, $default = '') => old($field, $isEdit ? ($ropa->$field ?? $default) : $default);
    $checked = fn($field) => old($field, $isEdit ? ($ropa->$field ?? false) : false);
    $inArr = fn($field, $val) => in_array($val, old($field, $isEdit ? ($ropa->$field ?? []) : []));
@endphp

@if($errors->any())
<div class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm mb-6" style="background:#fff1f2;border:1.5px solid #fca5a5;color:#991b1b;">
    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
    <ul class="list-disc pl-3 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form action="{{ $action }}" method="POST">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    {{-- Step 1: ข้อมูลพื้นฐาน --}}
    <div class="card p-6 mb-5">
        <h2 class="text-sm font-bold mb-5 flex items-center gap-2.5" style="color:#0f3020;">
            <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:linear-gradient(135deg,#15572e,#2a6b4d);">1</span>
            ข้อมูลพื้นฐานของกิจกรรม
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ชื่อกิจกรรมการประมวลผล <span style="color:#c0272d;">*</span></label>
                <input type="text" name="process_name" value="{{ $v('process_name') }}" required class="form-input"
                    placeholder="เช่น การสั่งซื้อสินค้าออนไลน์, การบริหารทรัพยากรบุคคล">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">รหัสกิจกรรม</label>
                <input type="text" name="process_code" value="{{ $v('process_code') }}" class="form-input" placeholder="เช่น PROC-001">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">แผนก / หน่วยงาน</label>
                <input type="text" name="department" value="{{ $v('department') }}" class="form-input" placeholder="เช่น ฝ่ายขาย, ฝ่าย HR">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">เจ้าของกิจกรรม (Process Owner)</label>
                <input type="text" name="process_owner" value="{{ $v('process_owner') }}" class="form-input" placeholder="เช่น ผู้จัดการฝ่ายขาย">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">บทบาทองค์กร <span style="color:#c0272d;">*</span></label>
                <select name="role" required class="form-input">
                    <option value="controller" {{ $v('role') === 'controller' ? 'selected' : '' }}>Controller — ผู้ควบคุมข้อมูล</option>
                    <option value="processor" {{ $v('role') === 'processor' ? 'selected' : '' }}>Processor — ผู้ประมวลผลข้อมูล</option>
                    <option value="joint_controller" {{ $v('role') === 'joint_controller' ? 'selected' : '' }}>Joint Controller — ผู้ควบคุมร่วม</option>
                </select>
            </div>
            @if($isEdit)
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">สถานะ</label>
                <select name="status" class="form-input">
                    <option value="draft" {{ $v('status') === 'draft' ? 'selected' : '' }}>ร่าง</option>
                    <option value="active" {{ $v('status') === 'active' ? 'selected' : '' }}>ใช้งาน</option>
                    <option value="under_review" {{ $v('status') === 'under_review' ? 'selected' : '' }}>กำลัง Review</option>
                    <option value="archived" {{ $v('status') === 'archived' ? 'selected' : '' }}>เก็บถาวร</option>
                </select>
            </div>
            @endif
        </div>
    </div>

    {{-- Step 2: วัตถุประสงค์และฐานกฎหมาย --}}
    <div class="card p-6 mb-5">
        <h2 class="text-sm font-bold mb-5 flex items-center gap-2.5" style="color:#0f3020;">
            <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:linear-gradient(135deg,#15572e,#2a6b4d);">2</span>
            วัตถุประสงค์และฐานทางกฎหมาย
        </h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">วัตถุประสงค์การประมวลผล <span style="color:#c0272d;">*</span></label>
                <textarea name="purpose" rows="3" required class="form-input"
                    placeholder="อธิบายว่าทำไมจึงต้องเก็บและใช้ข้อมูลนี้">{{ $v('purpose') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ฐานทางกฎหมาย (Lawful Basis) <span style="color:#c0272d;">*</span></label>
                <select name="legal_basis" id="legal_basis" required class="form-input"
                    onchange="document.getElementById('li_desc').classList.toggle('hidden', this.value !== 'legitimate_interest')">
                    <option value="">เลือกฐานกฎหมาย</option>
                    <option value="consent" {{ $v('legal_basis') === 'consent' ? 'selected' : '' }}>ความยินยอม (Consent) — มาตรา 19</option>
                    <option value="contract" {{ $v('legal_basis') === 'contract' ? 'selected' : '' }}>สัญญา (Contract) — มาตรา 24(3)</option>
                    <option value="legal_obligation" {{ $v('legal_basis') === 'legal_obligation' ? 'selected' : '' }}>หน้าที่ตามกฎหมาย (Legal Obligation) — มาตรา 24(1)</option>
                    <option value="legitimate_interest" {{ $v('legal_basis') === 'legitimate_interest' ? 'selected' : '' }}>ประโยชน์อันชอบธรรม (Legitimate Interest) — มาตรา 24(5)</option>
                    <option value="vital_interest" {{ $v('legal_basis') === 'vital_interest' ? 'selected' : '' }}>ประโยชน์ต่อชีวิต (Vital Interest) — มาตรา 24(2)</option>
                    <option value="public_interest" {{ $v('legal_basis') === 'public_interest' ? 'selected' : '' }}>ประโยชน์สาธารณะ (Public Interest) — มาตรา 24(4)</option>
                </select>
            </div>
            <div id="li_desc" class="{{ $v('legal_basis') === 'legitimate_interest' ? '' : 'hidden' }}">
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">รายละเอียด Legitimate Interest (LIA)</label>
                <textarea name="legitimate_interest_description" rows="2" class="form-input"
                    placeholder="อธิบายการทำ Legitimate Interest Assessment (LIA)">{{ $v('legitimate_interest_description') }}</textarea>
            </div>
        </div>
    </div>

    {{-- Step 3: ประเภทข้อมูล --}}
    <div class="card p-6 mb-5">
        <h2 class="text-sm font-bold mb-5 flex items-center gap-2.5" style="color:#0f3020;">
            <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:linear-gradient(135deg,#15572e,#2a6b4d);">3</span>
            ประเภทข้อมูลและเจ้าของข้อมูล
        </h2>

        <div class="mb-5">
            <label class="block text-sm font-semibold mb-2" style="color:#374151;">ประเภทข้อมูลส่วนบุคคลที่ประมวลผล</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-4 rounded-xl" style="background:#f8faf9; border:1px solid #e8f0eb;">
                @foreach([
                    'ชื่อ-นามสกุล', 'วันเกิด', 'เลขบัตรประชาชน', 'ที่อยู่',
                    'อีเมล', 'เบอร์โทรศัพท์', 'ข้อมูลการชำระเงิน', 'บัญชีธนาคาร',
                    'ข้อมูลภาษี', 'เงินเดือน', 'IP Address', 'Cookie/Tracking',
                    'ประวัติการซื้อ', 'ความสนใจ', 'ข้อมูลสุขภาพ', 'ภาพถ่าย',
                    'ข้อมูลตำแหน่ง (GPS)', 'ข้อมูลอื่นๆ',
                ] as $cat)
                <label class="flex items-center gap-2 cursor-pointer text-sm" style="color:#374151;">
                    <input type="checkbox" name="data_categories[]" value="{{ $cat }}"
                        {{ $inArr('data_categories', $cat) ? 'checked' : '' }}
                        class="rounded w-4 h-4" style="accent-color:#15572e;">
                    {{ $cat }}
                </label>
                @endforeach
            </div>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold mb-2" style="color:#374151;">ประเภทเจ้าของข้อมูล (Data Subjects)</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-4 rounded-xl" style="background:#f8faf9; border:1px solid #e8f0eb;">
                @foreach(['ลูกค้าทั่วไป', 'สมาชิก/ผู้ใช้งาน', 'พนักงาน', 'ผู้สมัครงาน', 'คู่ค้า/ซัพพลายเออร์', 'ผู้ป่วย', 'นักศึกษา', 'บุคคลภายนอกอื่นๆ'] as $type)
                <label class="flex items-center gap-2 cursor-pointer text-sm" style="color:#374151;">
                    <input type="checkbox" name="data_subject_types[]" value="{{ $type }}"
                        {{ $inArr('data_subject_types', $type) ? 'checked' : '' }}
                        class="rounded w-4 h-4" style="accent-color:#15572e;">
                    {{ $type }}
                </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="flex items-center gap-3 cursor-pointer p-3.5 rounded-xl border-2" id="sensitive_toggle"
                style="{{ $checked('has_sensitive_data') ? 'border-color:#fca5a5; background:#fff5f5;' : 'border-color:#e8f0eb;' }}">
                <input type="checkbox" name="has_sensitive_data" value="1" id="has_sensitive"
                    {{ $checked('has_sensitive_data') ? 'checked' : '' }}
                    class="rounded w-4 h-4" style="accent-color:#c0272d;"
                    onchange="
                        document.getElementById('sensitive_categories').classList.toggle('hidden', !this.checked);
                        document.getElementById('sensitive_toggle').style.borderColor = this.checked ? '#fca5a5' : '#e8f0eb';
                        document.getElementById('sensitive_toggle').style.background = this.checked ? '#fff5f5' : 'transparent';
                    ">
                <div>
                    <p class="text-sm font-semibold" style="color:#374151;">มีข้อมูลอ่อนไหว (มาตรา 26)</p>
                    <p class="text-xs" style="color:#94a3b8;">ข้อมูลสุขภาพ, เชื้อชาติ, ศาสนา, ข้อมูลชีวมิติ, ความเชื่อทางการเมือง, ประวัติอาชญากรรม</p>
                </div>
            </label>
            <div id="sensitive_categories" class="{{ $checked('has_sensitive_data') ? '' : 'hidden' }} mt-3 p-4 rounded-xl" style="background:#fff5f5; border:1px solid #fca5a5;">
                <p class="text-xs font-bold mb-2" style="color:#c0272d;">ระบุประเภทข้อมูลอ่อนไหว:</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['ข้อมูลสุขภาพ/การแพทย์', 'ข้อมูลชีวมิติ (ลายนิ้วมือ/ใบหน้า)', 'เชื้อชาติหรือเผ่าพันธุ์', 'ความคิดเห็นทางการเมือง', 'ความเชื่อทางศาสนา', 'พฤติกรรมทางเพศ', 'ประวัติอาชญากรรม', 'ความพิการ', 'ข้อมูลสหภาพแรงงาน', 'ข้อมูลพันธุกรรม'] as $s)
                    <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#991b1b;">
                        <input type="checkbox" name="sensitive_data_categories[]" value="{{ $s }}"
                            {{ $inArr('sensitive_data_categories', $s) ? 'checked' : '' }}
                            class="rounded w-4 h-4" style="accent-color:#c0272d;">
                        {{ $s }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Step 4: ผู้รับและการส่งต่อ --}}
    <div class="card p-6 mb-5">
        <h2 class="text-sm font-bold mb-5 flex items-center gap-2.5" style="color:#0f3020;">
            <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:linear-gradient(135deg,#15572e,#2a6b4d);">4</span>
            ผู้รับข้อมูลและการส่งต่อ
        </h2>

        <div class="mb-5">
            <label class="block text-sm font-semibold mb-2" style="color:#374151;">ผู้รับข้อมูล / Data Processors</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-4 rounded-xl" style="background:#f8faf9; border:1px solid #e8f0eb;">
                @foreach(['บริษัทขนส่ง', 'ผู้ให้บริการชำระเงิน', 'ธนาคาร', 'กรมสรรพากร', 'ประกันสังคม', 'ผู้ให้บริการ Cloud', 'ผู้ให้บริการอีเมล', 'ผู้ให้บริการ Analytics', 'โรงพยาบาล/คลินิก', 'บริษัทประกัน', 'หน่วยงานรัฐ'] as $r)
                <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#374151;">
                    <input type="checkbox" name="recipients[]" value="{{ $r }}"
                        {{ $inArr('recipients', $r) ? 'checked' : '' }}
                        class="rounded w-4 h-4" style="accent-color:#15572e;">
                    {{ $r }}
                </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <label class="flex items-center gap-3 cursor-pointer p-3.5 rounded-xl border-2" style="border-color:#e8f0eb;">
                <input type="checkbox" name="third_party_transfer" value="1"
                    {{ $checked('third_party_transfer') ? 'checked' : '' }}
                    class="rounded w-4 h-4" style="accent-color:#15572e;">
                <div>
                    <p class="text-sm font-semibold" style="color:#374151;">ส่งข้อมูลให้บุคคลที่สาม</p>
                    <p class="text-xs" style="color:#94a3b8;">มีการส่ง/เปิดเผยข้อมูลให้บุคคลภายนอก</p>
                </div>
            </label>
            <label class="flex items-center gap-3 cursor-pointer p-3.5 rounded-xl border-2" id="cross_border_toggle" style="border-color:#e8f0eb;">
                <input type="checkbox" name="cross_border_transfer" value="1" id="cross_border"
                    {{ $checked('cross_border_transfer') ? 'checked' : '' }}
                    class="rounded w-4 h-4" style="accent-color:#b45309;"
                    onchange="document.getElementById('cross_border_details').classList.toggle('hidden', !this.checked)">
                <div>
                    <p class="text-sm font-semibold" style="color:#374151;">ส่งข้อมูลข้ามพรมแดน</p>
                    <p class="text-xs" style="color:#94a3b8;">ส่งข้อมูลไปยังต่างประเทศ</p>
                </div>
            </label>
        </div>

        <div id="cross_border_details" class="{{ $checked('cross_border_transfer') ? '' : 'hidden' }} mt-3 p-4 rounded-xl" style="background:#fffbeb; border:1px solid #fcd34d;">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#92400e;">ประเทศปลายทาง</label>
                    <input type="text" name="cross_border_countries" value="{{ $v('cross_border_countries') }}" class="form-input"
                        placeholder="เช่น สหรัฐอเมริกา, สิงคโปร์">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#92400e;">มาตรการคุ้มครอง</label>
                    <input type="text" name="cross_border_safeguards" value="{{ $v('cross_border_safeguards') }}" class="form-input"
                        placeholder="เช่น SCCs, Adequacy Decision">
                </div>
            </div>
        </div>
    </div>

    {{-- Step 5: การเก็บรักษา --}}
    <div class="card p-6 mb-5">
        <h2 class="text-sm font-bold mb-5 flex items-center gap-2.5" style="color:#0f3020;">
            <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:linear-gradient(135deg,#15572e,#2a6b4d);">5</span>
            การเก็บรักษาและมาตรการความปลอดภัย
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ระยะเวลาเก็บรักษา <span style="color:#c0272d;">*</span></label>
                <input type="text" name="retention_period" value="{{ $v('retention_period') }}" required class="form-input"
                    placeholder="เช่น 5 ปี นับจากวันสิ้นสุดสัญญา">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">วันที่ Review ครั้งถัดไป</label>
                <input type="date" name="next_review_date" value="{{ $v('next_review_date', now()->addYear()->format('Y-m-d')) }}" class="form-input">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">หลักเกณฑ์การกำหนดระยะเวลา</label>
                <input type="text" name="retention_criteria" value="{{ $v('retention_criteria') }}" class="form-input"
                    placeholder="เช่น ตามกฎหมายภาษีอากร, ตามระยะเวลาอายุความ">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">วิธีทำลายข้อมูล</label>
                <input type="text" name="deletion_method" value="{{ $v('deletion_method') }}" class="form-input"
                    placeholder="เช่น Secure Delete, Physical Shredding">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ระบบที่ใช้ประมวลผล</label>
                <input type="text" name="system_used" value="{{ $v('system_used') }}" class="form-input"
                    placeholder="เช่น SAP, ระบบพัฒนาเอง, Salesforce">
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-semibold mb-2" style="color:#374151;">มาตรการรักษาความปลอดภัย</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-4 rounded-xl" style="background:#f8faf9; border:1px solid #e8f0eb;">
                @foreach(['HTTPS/TLS', 'การเข้ารหัสข้อมูล (Encryption)', 'Access Control/RBAC', 'Multi-Factor Authentication', 'Firewall', 'Intrusion Detection (IDS/IPS)', 'Audit Log', 'Data Masking', 'Regular Backup', 'Vulnerability Assessment', 'Security Training', 'Physical Access Control'] as $m)
                <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#374151;">
                    <input type="checkbox" name="security_measures[]" value="{{ $m }}"
                        {{ $inArr('security_measures', $m) ? 'checked' : '' }}
                        class="rounded w-4 h-4" style="accent-color:#15572e;">
                    {{ $m }}
                </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="flex justify-end gap-3">
        <a href="{{ $isEdit ? route('ropa.show', $ropa) : route('ropa.index') }}" class="btn-outline">ยกเลิก</a>
        <button type="submit" class="btn-primary">
            {{ $isEdit ? 'บันทึกการแก้ไข' : 'บันทึก ROPA Record' }}
        </button>
    </div>
</form>
