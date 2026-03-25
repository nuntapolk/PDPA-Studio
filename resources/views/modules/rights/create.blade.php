@extends('layouts.app')
@section('title', 'เพิ่มคำขอสิทธิ์ — PDPA Studio')
@section('page-title', 'Data Subject Right')

@section('content')

<div class="mb-5">
    <a href="{{ route('rights.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium" style="color:#15572e;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        กลับรายการ
    </a>
</div>

<div class="max-w-2xl">
    <div class="card p-6">
        <h2 class="text-base font-bold mb-6" style="color:#1e293b;">เพิ่มคำขอสิทธิ์เจ้าของข้อมูล</h2>

        @if($errors->any())
        <div class="mb-4 p-3 rounded-lg text-sm" style="background:#fef2f2;border:1px solid #fecaca;color:#c0272d;">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('rights.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- ประเภทสิทธิ์ --}}
            <div>
                <label class="block text-sm font-semibold mb-2" style="color:#374151;">ประเภทสิทธิ์ <span style="color:#c0272d;">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([
                        ['access',           'ขอเข้าถึงข้อมูล',    'มาตรา 30 — ขอดูข้อมูลที่เก็บไว้',       '🔍'],
                        ['rectification',    'ขอแก้ไขข้อมูล',     'มาตรา 35 — แก้ไขข้อมูลที่ไม่ถูกต้อง',   '✏️'],
                        ['erasure',          'ขอลบข้อมูล',        'มาตรา 33 — ลบ/ทำลายข้อมูล',             '🗑️'],
                        ['restriction',      'ขอระงับการใช้',     'มาตรา 34 — ระงับการประมวลผล',           '⏸️'],
                        ['portability',      'ขอโอนย้ายข้อมูล',  'มาตรา 36 — รับข้อมูลในรูปแบบดิจิตอล',   '📦'],
                        ['objection',        'คัดค้านการใช้',     'มาตรา 32 — คัดค้านการประมวลผล',         '🚫'],
                        ['withdraw_consent', 'ถอนความยินยอม',     'ถอนการยินยอมที่ให้ไว้',                  '↩️'],
                    ] as [$val, $label, $desc, $icon])
                    <label class="flex items-start gap-3 p-3 rounded-xl cursor-pointer transition"
                           style="border:1.5px solid {{ old('type') === $val ? '#15572e' : '#e2e8f0' }};
                                  background:{{ old('type') === $val ? '#f0fdf4' : 'white' }};">
                        <input type="radio" name="type" value="{{ $val }}"
                               {{ old('type') === $val ? 'checked' : '' }}
                               class="mt-0.5 flex-shrink-0" style="accent-color:#15572e;">
                        <div>
                            <p class="text-sm font-semibold" style="color:#1e293b;">{{ $icon }} {{ $label }}</p>
                            <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $desc }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('type')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
            </div>

            {{-- ข้อมูลผู้ยื่น --}}
            <div style="border-top:1px solid #e8f0eb;padding-top:1.25rem;">
                <p class="text-sm font-semibold mb-3" style="color:#374151;">ข้อมูลผู้ยื่นคำขอ</p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-medium mb-1" style="color:#6b7280;">ชื่อ-นามสกุล <span style="color:#c0272d;">*</span></label>
                        <input type="text" name="requester_name" value="{{ old('requester_name') }}"
                               class="form-input" placeholder="ชื่อผู้ยื่นคำขอ" required>
                        @error('requester_name')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-medium mb-1" style="color:#6b7280;">อีเมล <span style="color:#c0272d;">*</span></label>
                        <input type="email" name="requester_email" value="{{ old('requester_email') }}"
                               class="form-input" placeholder="email@example.com" required>
                        @error('requester_email')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-medium mb-1" style="color:#6b7280;">เบอร์โทร</label>
                        <input type="text" name="requester_phone" value="{{ old('requester_phone') }}"
                               class="form-input" placeholder="0812345678">
                    </div>
                </div>
            </div>

            {{-- รายละเอียด --}}
            <div>
                <label class="block text-xs font-medium mb-1" style="color:#6b7280;">รายละเอียดคำขอ <span style="color:#c0272d;">*</span></label>
                <textarea name="description" rows="4" required
                          class="form-input resize-none"
                          placeholder="อธิบายรายละเอียดของคำขอ เช่น ต้องการเข้าถึงข้อมูลอะไร หรือต้องการลบข้อมูลส่วนใด...">{{ old('description') }}</textarea>
                @error('description')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
            </div>

            <div class="p-3 rounded-lg text-xs" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15572e;">
                📅 ระบบจะตั้งกำหนดเสร็จ <strong>30 วัน</strong> นับจากวันที่รับคำขอ ตามมาตรา 31 พ.ร.บ.คุ้มครองข้อมูลส่วนบุคคล
            </div>

            {{-- Buttons --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary" style="padding:10px 24px;">
                    + บันทึกคำขอ
                </button>
                <a href="{{ route('rights.index') }}" class="btn-outline" style="padding:10px 24px;">ยกเลิก</a>
            </div>
        </form>
    </div>
</div>

@endsection
