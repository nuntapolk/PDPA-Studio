@extends('layouts.app')

@section('title', 'รายงาน Data Breach — PDPA Studio')
@section('page-title', 'รายงาน Data Breach')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('breach.index') }}" class="text-sm font-medium" style="color:#15572e;">← กลับ</a>
    </div>

    {{-- 72h warning banner --}}
    <div class="flex items-start gap-3 px-5 py-4 rounded-xl mb-6" style="background:#fff5f5; border:1.5px solid #fca5a5;">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20" style="color:#c0272d;"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <div>
            <p class="text-sm font-semibold" style="color:#991b1b;">ข้อกำหนดตาม PDPA มาตรา 37(4)</p>
            <p class="text-xs mt-0.5" style="color:#b91c1c;">เมื่อพบเหตุการณ์ละเมิดข้อมูล ต้องแจ้ง PDPC ภายใน <strong>72 ชั่วโมง</strong> นับจากที่ทราบเหตุ (กรณีมีความเสี่ยงต่อสิทธิ์เจ้าของข้อมูล) ระบบจะตั้ง deadline ให้อัตโนมัติ</p>
        </div>
    </div>

    <div class="card p-7">
        <h2 class="text-base font-bold mb-6" style="color:#0f3020;">รายละเอียดเหตุการณ์</h2>

        @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm mb-5" style="background:#fff1f2;border:1.5px solid #fca5a5;color:#991b1b;">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            <ul class="list-disc pl-3 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('breach.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ชื่อเหตุการณ์ <span style="color:#c0272d;">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required class="form-input"
                    placeholder="เช่น Database ถูก Expose, Phishing Attack">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ประเภท Breach <span style="color:#c0272d;">*</span></label>
                    <select name="breach_type" required class="form-input">
                        <option value="">เลือกประเภท</option>
                        <option value="unauthorized_access" {{ old('breach_type') === 'unauthorized_access' ? 'selected' : '' }}>Unauthorized Access</option>
                        <option value="data_leak" {{ old('breach_type') === 'data_leak' ? 'selected' : '' }}>Data Leak / Exposure</option>
                        <option value="phishing" {{ old('breach_type') === 'phishing' ? 'selected' : '' }}>Phishing / Social Engineering</option>
                        <option value="ransomware" {{ old('breach_type') === 'ransomware' ? 'selected' : '' }}>Ransomware / Malware</option>
                        <option value="insider_threat" {{ old('breach_type') === 'insider_threat' ? 'selected' : '' }}>Insider Threat</option>
                        <option value="accidental_disclosure" {{ old('breach_type') === 'accidental_disclosure' ? 'selected' : '' }}>Accidental Disclosure</option>
                        <option value="theft" {{ old('breach_type') === 'theft' ? 'selected' : '' }}>Physical Theft / Loss</option>
                        <option value="other" {{ old('breach_type') === 'other' ? 'selected' : '' }}>อื่นๆ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ระดับความรุนแรง <span style="color:#c0272d;">*</span></label>
                    <select name="severity" required class="form-input">
                        <option value="">เลือกระดับ</option>
                        <option value="low" {{ old('severity') === 'low' ? 'selected' : '' }}>Low — ผลกระทบน้อย</option>
                        <option value="medium" {{ old('severity') === 'medium' ? 'selected' : '' }}>Medium — มีผลกระทบปานกลาง</option>
                        <option value="high" {{ old('severity') === 'high' ? 'selected' : '' }}>High — ผลกระทบสูง ต้องแจ้ง PDPC</option>
                        <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Critical — วิกฤต ต้องดำเนินการทันที</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">วันที่พบเหตุการณ์ <span style="color:#c0272d;">*</span></label>
                    <input type="datetime-local" name="discovered_at" value="{{ old('discovered_at', now()->format('Y-m-d\TH:i')) }}" required class="form-input">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">จำนวนผู้ได้รับผลกระทบ</label>
                    <input type="number" name="affected_count" value="{{ old('affected_count') }}" min="0" class="form-input" placeholder="0">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2" style="color:#374151;">ข้อมูลที่ได้รับผลกระทบ</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 p-4 rounded-xl" style="background:#f8faf9; border:1px solid #e8f0eb;">
                    @foreach([
                        'personal_info' => 'ข้อมูลส่วนตัว (ชื่อ/ที่อยู่)',
                        'contact' => 'ข้อมูลติดต่อ (อีเมล/โทร)',
                        'financial' => 'ข้อมูลการเงิน',
                        'health' => 'ข้อมูลสุขภาพ',
                        'id_card' => 'เลขบัตรประชาชน',
                        'login_credentials' => 'Username/Password',
                        'biometric' => 'ข้อมูลชีวมิติ',
                        'location' => 'ข้อมูลตำแหน่ง',
                        'other' => 'อื่นๆ',
                    ] as $val => $label)
                    <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:#374151;">
                        <input type="checkbox" name="data_types_affected[]" value="{{ $val }}"
                            {{ in_array($val, old('data_types_affected', [])) ? 'checked' : '' }}
                            class="rounded w-4 h-4" style="accent-color:#c0272d;">
                        {{ $label }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">รายละเอียดเหตุการณ์ <span style="color:#c0272d;">*</span></label>
                <textarea name="description" rows="4" required class="form-input"
                    placeholder="อธิบายรายละเอียดของเหตุการณ์ สาเหตุที่ทราบ และผลกระทบ">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('breach.index') }}" class="btn-outline">ยกเลิก</a>
                <button type="submit" class="btn-danger">รายงาน Data Breach</button>
            </div>
        </form>
    </div>
</div>
@endsection
