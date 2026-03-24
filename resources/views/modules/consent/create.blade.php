@extends('layouts.app')
@section('title', 'สร้าง Consent Template — PDPA Studio')
@section('page-title', 'สร้าง Consent Template')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('consent.index') }}" class="text-sm font-medium" style="color:#15572e;">← กลับ</a>
    </div>

    <div class="card p-7">
        <h2 class="text-base font-bold mb-6" style="color:#0f3020;">ข้อมูล Consent Template</h2>

        @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm mb-5" style="background:#fff1f2;border:1.5px solid #fca5a5;color:#991b1b;">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <ul class="list-disc pl-3 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('consent.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ชื่อ Template <span style="color:#c0272d;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-input"
                    placeholder="เช่น ความยินยอมการตลาด, ความยินยอมวิเคราะห์ข้อมูล">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ประเภท <span style="color:#c0272d;">*</span></label>
                    <select name="category" required class="form-input">
                        <option value="">เลือกประเภท</option>
                        @foreach(['marketing'=>'การตลาด','analytics'=>'วิเคราะห์ข้อมูล','functional'=>'การทำงานของระบบ','research'=>'การวิจัย','sharing'=>'การแบ่งปันข้อมูล','hr'=>'HR/พนักงาน'] as $v => $l)
                        <option value="{{ $v }}" {{ old('category') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ฐานทางกฎหมาย <span style="color:#c0272d;">*</span></label>
                    <select name="legal_basis" required class="form-input">
                        <option value="">เลือกฐานกฎหมาย</option>
                        @foreach(['consent'=>'ความยินยอม (Consent)','contract'=>'สัญญา (Contract)','legitimate_interest'=>'ประโยชน์อันชอบธรรม','legal_obligation'=>'หน้าที่ตามกฎหมาย','vital_interest'=>'ผลประโยชน์สำคัญ','public_task'=>'ภารกิจสาธารณะ'] as $v => $l)
                        <option value="{{ $v }}" {{ old('legal_basis') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">วัตถุประสงค์ <span style="color:#c0272d;">*</span></label>
                <textarea name="purpose" rows="3" required class="form-input"
                    placeholder="อธิบายวัตถุประสงค์การเก็บและใช้ข้อมูลส่วนบุคคล">{{ old('purpose') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">อายุความยินยอม (วัน)</label>
                    <input type="number" name="validity_days" value="{{ old('validity_days', 365) }}" min="1" class="form-input" placeholder="365">
                    <p class="text-xs mt-1" style="color:#94a3b8;">ว่างไว้ = ไม่มีวันหมดอายุ</p>
                </div>
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-2.5 cursor-pointer p-3 rounded-xl border-2 w-full" style="border-color:#e2e8f0;">
                        <input type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}
                            class="rounded w-4 h-4" style="accent-color:#15572e;">
                        <div>
                            <p class="text-sm font-semibold" style="color:#374151;">บังคับให้ยินยอม</p>
                            <p class="text-xs" style="color:#94a3b8;">ต้องยินยอมก่อนใช้งาน</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('consent.index') }}" class="btn-outline">ยกเลิก</a>
                <button type="submit" class="btn-primary">สร้าง Template</button>
            </div>
        </form>
    </div>
</div>
@endsection
