@extends('layouts.app')
@section('title','แก้ไขงาน DPO — PDPA Studio')
@section('page-title','แก้ไขงาน DPO')

@section('content')
<div class="mb-5">
    <a href="{{ route('dpo.show', $dpo) }}" class="inline-flex items-center gap-1.5 text-sm font-medium" style="color:#15572e;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        กลับรายละเอียด
    </a>
</div>

<form method="POST" action="{{ route('dpo.update', $dpo) }}">
@csrf @method('PUT')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-5">
        <div class="card p-5 space-y-4">
            <h3 class="text-sm font-bold" style="color:#0f3020;">ข้อมูลงาน</h3>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">ชื่องาน <span style="color:#c0272d;">*</span></label>
                <input type="text" name="title" value="{{ old('title',$dpo->title) }}" class="form-input w-full" required>
                @error('title')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">รายละเอียด</label>
                <textarea name="description" rows="3" class="form-input w-full">{{ old('description',$dpo->description) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">หมวดงาน</label>
                <select name="category" class="form-input w-full">
                    @foreach(['compliance_review'=>'ทบทวนความสอดคล้อง','policy_update'=>'อัปเดตนโยบาย','training'=>'จัดอบรม','audit'=>'ตรวจสอบ (Audit)','vendor_review'=>'ทบทวน Vendor','incident_response'=>'ตอบสนองเหตุการณ์','reporting'=>'รายงาน','other'=>'อื่นๆ'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('category',$dpo->category)===$v?'selected':'' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">หมายเหตุ</label>
                <textarea name="notes" rows="3" class="form-input w-full">{{ old('notes',$dpo->notes) }}</textarea>
            </div>
        </div>
    </div>

    <div class="space-y-5">
        <div class="card p-5 space-y-4">
            <h3 class="text-sm font-bold" style="color:#0f3020;">การจัดการ</h3>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">สถานะ</label>
                <select name="status" class="form-input w-full">
                    <option value="pending"     {{ old('status',$dpo->status)==='pending'    ?'selected':'' }}>รอดำเนินการ</option>
                    <option value="in_progress" {{ old('status',$dpo->status)==='in_progress'?'selected':'' }}>กำลังดำเนินการ</option>
                    <option value="completed"   {{ old('status',$dpo->status)==='completed'  ?'selected':'' }}>เสร็จสิ้น</option>
                    <option value="cancelled"   {{ old('status',$dpo->status)==='cancelled'  ?'selected':'' }}>ยกเลิก</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">ความสำคัญ</label>
                <select name="priority" class="form-input w-full">
                    <option value="urgent" {{ old('priority',$dpo->priority)==='urgent'?'selected':'' }}>🔴 เร่งด่วน</option>
                    <option value="high"   {{ old('priority',$dpo->priority)==='high'  ?'selected':'' }}>🟠 สูง</option>
                    <option value="medium" {{ old('priority',$dpo->priority)==='medium'?'selected':'' }}>🟡 ปานกลาง</option>
                    <option value="low"    {{ old('priority',$dpo->priority)==='low'   ?'selected':'' }}>🟢 ต่ำ</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">วันครบกำหนด</label>
                <input type="date" name="due_date" value="{{ old('due_date',$dpo->due_date?->format('Y-m-d')) }}" class="form-input w-full">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color:#475569;">มอบหมายให้</label>
                <select name="assigned_to" class="form-input w-full">
                    <option value="">— ยังไม่มอบหมาย —</option>
                    @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ old('assigned_to',$dpo->assigned_to)==$m->id?'selected':'' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card p-4 space-y-2.5">
            <button type="submit" class="btn-primary w-full justify-center">บันทึกการแก้ไข</button>
            <a href="{{ route('dpo.show', $dpo) }}" class="btn-outline w-full justify-center">ยกเลิก</a>
        </div>
    </div>
</div>
</form>
@endsection
