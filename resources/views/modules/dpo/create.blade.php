@extends('layouts.app')
@section('title','สร้างงาน DPO — PDPA Studio')
@section('page-title','สร้างงาน DPO ใหม่')

@section('content')
<div class="mb-5">
    <a href="{{ route('dpo.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium" style="color:#15572e;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        กลับรายการงาน
    </a>
</div>

<form method="POST" action="{{ route('dpo.store') }}">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">

        {{-- Title --}}
        <div class="card p-5 space-y-4">
            <h3 class="text-sm font-bold" style="color:#0f3020;">ข้อมูลงาน</h3>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">ชื่องาน <span style="color:#c0272d;">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-input w-full"
                       placeholder="เช่น ทบทวน ROPA ประจำไตรมาส Q2/2568" required>
                @error('title')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">รายละเอียด</label>
                <textarea name="description" rows="3" class="form-input w-full"
                          placeholder="อธิบายรายละเอียดของงาน...">{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- Category Cards --}}
        <div class="card p-5">
            <h3 class="text-sm font-bold mb-4" style="color:#0f3020;">หมวดงาน <span style="color:#c0272d;">*</span></h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                @foreach([
                    ['compliance_review','ทบทวนความสอดคล้อง','#15572e','#e8f0eb','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['policy_update',    'อัปเดตนโยบาย',      '#0369a1','#e0f2fe','M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['training',         'จัดอบรม',            '#7c3aed','#ede9fe','M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253'],
                    ['audit',            'ตรวจสอบ (Audit)',    '#b45309','#fef3c7','M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['vendor_review',    'ทบทวน Vendor',       '#0891b2','#e0f7fa','M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857'],
                    ['incident_response','ตอบสนองเหตุการณ์',  '#c0272d','#fff1f2','M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                    ['reporting',        'รายงาน',             '#475569','#f1f5f9','M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['other',            'อื่นๆ',              '#64748b','#f8fafc','M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z'],
                ] as [$val,$label,$color,$bg,$icon])
                @php $sel = old('category','compliance_review') === $val; @endphp
                <label class="cat-card cursor-pointer rounded-xl p-3 flex flex-col items-center gap-1.5 transition-all text-center"
                       style="border:2px solid {{ $sel?$color:'#e2e8f0' }};background:{{ $sel?$bg:'white' }};"
                       data-color="{{ $color }}" data-bg="{{ $bg }}">
                    <input type="radio" name="category" value="{{ $val }}" class="hidden" {{ $sel?'checked':'' }}>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:{{ $bg }};">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:{{ $color }};"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                    </div>
                    <span class="text-xs font-semibold leading-tight" style="color:{{ $color }};">{{ $label }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Notes --}}
        <div class="card p-5">
            <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">หมายเหตุ / สิ่งที่ต้องทำ</label>
            <textarea name="notes" rows="3" class="form-input w-full" placeholder="รายละเอียดเพิ่มเติม...">{{ old('notes') }}</textarea>
        </div>
    </div>

    {{-- Right --}}
    <div class="space-y-5">
        <div class="card p-5 space-y-4">
            <h3 class="text-sm font-bold" style="color:#0f3020;">การจัดการ</h3>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">ความสำคัญ</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['urgent'=>['เร่งด่วน','#c0272d','#fff1f2'],'high'=>['สูง','#f97316','#fff7ed'],'medium'=>['ปานกลาง','#0369a1','#eff6ff'],'low'=>['ต่ำ','#64748b','#f8fafc']] as $pv=>[$pl,$pc,$pbg])
                    @php $psel = old('priority','medium')===$pv; @endphp
                    <label class="pri-card cursor-pointer rounded-lg py-2 px-3 text-center text-xs font-bold transition-all"
                           style="border:2px solid {{ $psel?$pc:'#e2e8f0' }};background:{{ $psel?$pbg:'white' }};color:{{ $psel?$pc:'#64748b' }};"
                           data-color="{{ $pc }}" data-bg="{{ $pbg }}">
                        <input type="radio" name="priority" value="{{ $pv }}" class="hidden" {{ $psel?'checked':'' }}>
                        {{ $pl }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">วันครบกำหนด</label>
                <input type="date" name="due_date" value="{{ old('due_date') }}" class="form-input w-full">
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">มอบหมายให้</label>
                <select name="assigned_to" class="form-input w-full">
                    <option value="">— ยังไม่มอบหมาย —</option>
                    @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ old('assigned_to')==$m->id?'selected':'' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card p-4 space-y-2.5">
            <button type="submit" class="btn-primary w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                สร้างงาน
            </button>
            <a href="{{ route('dpo.index') }}" class="btn-outline w-full justify-center">ยกเลิก</a>
        </div>
    </div>
</div>
</form>

@push('scripts')
<script>
document.querySelectorAll('.cat-card').forEach(c=>{
    c.addEventListener('click',()=>{
        document.querySelectorAll('.cat-card').forEach(x=>{ x.style.borderColor='#e2e8f0'; x.style.background='white'; });
        c.style.borderColor=c.dataset.color; c.style.background=c.dataset.bg;
        c.querySelector('input').checked=true;
    });
});
document.querySelectorAll('.pri-card').forEach(c=>{
    c.addEventListener('click',()=>{
        document.querySelectorAll('.pri-card').forEach(x=>{ x.style.borderColor='#e2e8f0'; x.style.background='white'; x.style.color='#64748b'; });
        c.style.borderColor=c.dataset.color; c.style.background=c.dataset.bg; c.style.color=c.dataset.color;
        c.querySelector('input').checked=true;
    });
});
</script>
@endpush
@endsection
