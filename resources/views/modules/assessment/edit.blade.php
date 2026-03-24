@extends('layouts.app')
@section('title', 'แก้ไขการประเมิน — PDPA Studio')
@section('page-title', 'DPIA / Assessment — แก้ไขการประเมิน')

@section('content')
<div class="mb-4">
    <a href="{{ route('assessment.show', $assessment) }}" class="text-sm font-medium" style="color:#15572e;">← กลับรายละเอียด</a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="card p-7">

        @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm mb-5" style="background:#fff1f2;border:1.5px solid #fca5a5;color:#991b1b;">
            <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('assessment.update', $assessment) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Section 1: ข้อมูลพื้นฐาน --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg,#15572e,#2a6b4d);">1</div>
                <h3 class="font-bold text-base" style="color:#0f3020;">ข้อมูลพื้นฐาน</h3>
            </div>

            <div class="space-y-5 mb-7 pl-10">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ประเภทการประเมิน</label>
                    @php
                        $typeLabels = ['dpia'=>'🔍 DPIA — Data Protection Impact Assessment','lia'=>'⚖️ LIA — Legitimate Interest Assessment','gap_analysis'=>'📊 Gap Analysis — PDPA Compliance Gap Analysis'];
                    @endphp
                    <div class="px-3 py-2.5 rounded-xl text-sm font-medium" style="background:#f8faf9;border:1px solid #e8f0eb;color:#475569;">
                        {{ $typeLabels[$assessment->type] ?? $assessment->type }}
                    </div>
                    <p class="text-xs mt-1" style="color:#94a3b8;">ไม่สามารถเปลี่ยนประเภทได้หลังจากสร้างแล้ว</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ชื่อการประเมิน <span style="color:#c0272d;">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $assessment->title) }}" required class="form-input"
                        placeholder="เช่น DPIA: ระบบ AI Recommendation">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">คำอธิบาย</label>
                    <textarea name="description" rows="3" class="form-input"
                        placeholder="อธิบายวัตถุประสงค์และที่มาของการประเมินนี้">{{ old('description', $assessment->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ขอบเขต (Scope)</label>
                    <input type="text" name="scope" value="{{ old('scope', $assessment->scope) }}" class="form-input"
                        placeholder="เช่น ระบบ CRM, กระบวนการ HR">
                </div>
            </div>

            <div style="border-top:1px solid #e8f0eb;" class="mb-7"></div>

            {{-- Section 2: สถานะ & ความเสี่ยง --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg,#15572e,#2a6b4d);">2</div>
                <h3 class="font-bold text-base" style="color:#0f3020;">สถานะ & ความเสี่ยง</h3>
            </div>

            <div class="space-y-5 mb-7 pl-10">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">สถานะ</label>
                        <select name="status" class="form-input">
                            <option value="draft"       {{ old('status',$assessment->status)==='draft'        ? 'selected' : '' }}>ร่าง</option>
                            <option value="in_progress" {{ old('status',$assessment->status)==='in_progress'  ? 'selected' : '' }}>กำลังดำเนินการ</option>
                            <option value="completed"   {{ old('status',$assessment->status)==='completed'    ? 'selected' : '' }}>เสร็จสิ้น</option>
                            <option value="approved"    {{ old('status',$assessment->status)==='approved'     ? 'selected' : '' }}>อนุมัติแล้ว</option>
                            <option value="archived"    {{ old('status',$assessment->status)==='archived'     ? 'selected' : '' }}>เก็บถาวร</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ระดับความเสี่ยง (Override)</label>
                        <select name="risk_level" class="form-input">
                            <option value="">— คำนวณอัตโนมัติ —</option>
                            <option value="low"       {{ old('risk_level',$assessment->risk_level)==='low'       ? 'selected' : '' }}>ต่ำ</option>
                            <option value="medium"    {{ old('risk_level',$assessment->risk_level)==='medium'    ? 'selected' : '' }}>ปานกลาง</option>
                            <option value="high"      {{ old('risk_level',$assessment->risk_level)==='high'      ? 'selected' : '' }}>สูง</option>
                            <option value="very_high" {{ old('risk_level',$assessment->risk_level)==='very_high' ? 'selected' : '' }}>สูงมาก</option>
                        </select>
                        <p class="text-xs mt-1" style="color:#94a3b8;">หากไม่เลือก ระบบจะคำนวณจากคำตอบ</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">วันที่เริ่ม</label>
                        <input type="date" name="started_at" value="{{ old('started_at', $assessment->started_at?->format('Y-m-d')) }}" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">วันที่เสร็จสิ้น</label>
                        <input type="date" name="completed_at" value="{{ old('completed_at', $assessment->completed_at?->format('Y-m-d')) }}" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">วันที่ครบกำหนดทบทวน</label>
                    <input type="date" name="next_review_date" value="{{ old('next_review_date', $assessment->next_review_date?->format('Y-m-d')) }}" class="form-input" style="max-width:200px;">
                </div>
            </div>

            <div style="border-top:1px solid #e8f0eb;" class="mb-7"></div>

            {{-- Section 3: ผลการประเมิน --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg,#15572e,#2a6b4d);">3</div>
                <h3 class="font-bold text-base" style="color:#0f3020;">ผลการประเมิน & ข้อเสนอแนะ</h3>
            </div>

            <div class="space-y-5 mb-7 pl-10">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ข้อค้นพบ (Findings)</label>
                    <textarea name="findings" rows="4" class="form-input"
                        placeholder="สรุปข้อค้นพบจากการประเมิน ความเสี่ยงที่พบ และประเด็นที่ต้องดำเนินการ">{{ old('findings', $assessment->findings) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ข้อเสนอแนะ (Recommendations)</label>
                    <textarea name="recommendations" rows="4" class="form-input"
                        placeholder="ข้อเสนอแนะในการแก้ไขและป้องกันความเสี่ยง">{{ old('recommendations', $assessment->recommendations) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">มาตรการลดความเสี่ยง (Mitigation Measures)</label>
                    <textarea name="mitigation_measures" rows="4" class="form-input"
                        placeholder="มาตรการที่จะนำไปใช้เพื่อลดความเสี่ยงที่ระบุไว้">{{ old('mitigation_measures', $assessment->mitigation_measures) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4" style="border-top:1px solid #e8f0eb;">
                <a href="{{ route('assessment.show', $assessment) }}" class="btn-outline">ยกเลิก</a>
                <button type="submit" class="btn-primary">บันทึกการเปลี่ยนแปลง</button>
            </div>
        </form>
    </div>
</div>
@endsection
