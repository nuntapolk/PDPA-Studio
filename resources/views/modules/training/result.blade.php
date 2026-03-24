@extends('layouts.app')
@section('title', 'ผลการทดสอบ — ' . $course->title)

@section('content')
<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('training.show', $course) }}" class="btn-outline text-sm">← กลับ</a>
    <h1 class="text-2xl font-bold" style="color:#15572e;">ผลการทดสอบ</h1>
</div>

{{-- Result Card --}}
<div class="card mb-6 text-center" style="border-top:4px solid {{ $completion->passed ? '#15572e' : '#c0272d' }};">
    <div style="font-size:4rem;" class="mb-2">{{ $completion->passed ? '🎉' : '😔' }}</div>
    <h2 class="text-3xl font-bold mb-1" style="color:{{ $completion->passed ? '#15572e' : '#c0272d' }};">
        {{ $completion->passed ? 'ผ่าน!' : 'ไม่ผ่าน' }}
    </h2>
    <p class="text-lg mb-2" style="color:#475569;">{{ $course->title }}</p>
    <div class="flex items-center justify-center gap-8 mt-4 flex-wrap">
        <div>
            <p class="text-4xl font-black" style="color:{{ $completion->passed ? '#15572e' : '#c0272d' }};">
                {{ $completion->score }}%
            </p>
            <p class="text-sm" style="color:#94a3b8;">คะแนนที่ได้</p>
        </div>
        <div>
            <p class="text-4xl font-black" style="color:#64748b;">{{ $course->passing_score }}%</p>
            <p class="text-sm" style="color:#94a3b8;">คะแนนผ่าน</p>
        </div>
        <div>
            <p class="text-4xl font-black" style="color:#334155;">ครั้งที่ {{ $completion->attempt_number }}</p>
            <p class="text-sm" style="color:#94a3b8;">ครั้งที่สอบ</p>
        </div>
    </div>
</div>

{{-- Certificate --}}
@if($completion->passed && $completion->certificate_number)
<div class="card mb-6" id="certificate" style="border:2px solid #15572e;background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);">
    <div class="text-center py-8">
        <div style="font-size:3rem;">🏆</div>
        <h2 class="text-2xl font-bold mt-2" style="color:#15572e;">ใบรับรองการอบรม</h2>
        <p class="mt-4 text-lg" style="color:#334155;">ขอมอบให้แก่</p>
        <p class="text-3xl font-black my-2" style="color:#0f3020;">{{ Auth::user()->name }}</p>
        <p class="text-base mb-4" style="color:#475569;">ได้ผ่านการอบรมหลักสูตร</p>
        <p class="text-xl font-bold" style="color:#15572e;">{{ $course->title }}</p>
        <p class="mt-2 text-sm" style="color:#64748b;">คะแนน {{ $completion->score }}% — สอบผ่านเมื่อ {{ $completion->completed_at?->format('d/m/Y') }}</p>

        <div class="mt-6 inline-block px-6 py-3 rounded-xl" style="background:#15572e;">
            <p class="text-sm font-mono font-bold" style="color:#fff;">{{ $completion->certificate_number }}</p>
        </div>

        @if($completion->expires_at)
        <p class="mt-3 text-sm" style="color:#64748b;">
            มีผลถึง: <strong>{{ $completion->expires_at->format('d/m/Y') }}</strong>
            @if($completion->isExpired())
                <span style="color:#c0272d;">(หมดอายุแล้ว)</span>
            @endif
        </p>
        @endif
    </div>
</div>
<div class="flex gap-3 mb-6 justify-center">
    <button onclick="window.print()" class="btn-primary">🖨 พิมพ์ใบรับรอง</button>
    <a href="{{ route('training.show', $course) }}" class="btn-outline">กลับไปที่คอร์ส</a>
</div>
@else
<div class="flex gap-3 mb-6">
    <a href="{{ route('training.show', $course) }}" class="btn-primary">🔄 ทำแบบทดสอบอีกครั้ง</a>
    <a href="{{ route('training.index') }}" class="btn-outline">กลับหน้าหลัก</a>
</div>
@endif

{{-- Question Review --}}
@if($questions->isNotEmpty())
<div class="card">
    <h3 class="font-semibold mb-4" style="color:#15572e;">📋 เฉลยแบบทดสอบ</h3>
    <div class="space-y-4">
    @foreach($questions as $i => $q)
        <div class="rounded-lg p-4" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <p class="font-medium mb-2" style="color:#1e293b;">{{ $i+1 }}. {{ $q->question }}</p>
            <div class="space-y-1">
            @foreach($q->options as $key => $label)
                @php $isCorrect = strtoupper($key) === strtoupper($q->correct_answer); @endphp
                <div class="flex items-center gap-2 text-sm px-3 py-1.5 rounded"
                     style="{{ $isCorrect ? 'background:#dcfce7;color:#15572e;font-weight:600;' : 'color:#64748b;' }}">
                    {{ $isCorrect ? '✅' : '⬜' }} <strong>{{ $key }}.</strong> {{ $label }}
                </div>
            @endforeach
            </div>
            @if($q->explanation)
                <div class="mt-2 text-sm px-3 py-2 rounded" style="background:#eff6ff;color:#1e40af;">
                    💡 {{ $q->explanation }}
                </div>
            @endif
        </div>
    @endforeach
    </div>
</div>
@endif

<style>
@media print {
    nav, header, .btn-primary, .btn-outline, .card:not(#certificate), h1, .flex.items-center.gap-2 { display:none!important; }
    #certificate { border:3px solid #15572e!important; }
}
</style>
@endsection
