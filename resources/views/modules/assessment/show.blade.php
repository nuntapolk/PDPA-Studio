@extends('layouts.app')
@section('title', $assessment->assessment_number . ' — PDPA Studio')
@section('page-title', 'DPIA / Assessment — รายละเอียด')

@section('content')
@php
    $typeColors  = ['dpia'=>'badge-red','lia'=>'badge-blue','gap_analysis'=>'badge-green'];
    $typeLabels  = ['dpia'=>'DPIA','lia'=>'LIA','gap_analysis'=>'Gap Analysis'];
    $statusColors= ['draft'=>'badge-gray','in_progress'=>'badge-yellow','completed'=>'badge-blue','approved'=>'badge-green','archived'=>'badge-gray'];
    $statusLabels= ['draft'=>'ร่าง','in_progress'=>'กำลังดำเนินการ','completed'=>'เสร็จสิ้น','approved'=>'อนุมัติแล้ว','archived'=>'เก็บถาวร'];
    $riskColor   = ['low'=>'#15572e','medium'=>'#b45309','high'=>'#c05621','very_high'=>'#c0272d'];
    $riskBg      = ['low'=>'#f0fdf4','medium'=>'#fffbeb','high'=>'#fff7ed','very_high'=>'#fff1f2'];
    $riskBorder  = ['low'=>'#86efac','medium'=>'#fcd34d','high'=>'#fdba74','very_high'=>'#fca5a5'];
    $riskLabels  = ['low'=>'ต่ำ','medium'=>'ปานกลาง','high'=>'สูง','very_high'=>'สูงมาก'];

    $answeredCount = $assessment->questions()->whereNotNull('answer')->count();
    $totalQ        = $assessment->questions()->count();
    $pct           = $totalQ > 0 ? round($answeredCount / $totalQ * 100) : 0;
@endphp

{{-- Top bar --}}
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('assessment.index') }}" class="text-sm font-medium" style="color:#15572e;">← กลับรายการ</a>
    <div class="flex items-center gap-2">
        <a href="{{ route('assessment.export', $assessment) }}" class="btn-outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </a>
        <a href="{{ route('assessment.edit', $assessment) }}" class="btn-outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            แก้ไข
        </a>
        @if($assessment->status === 'completed')
        <form action="{{ route('assessment.approve', $assessment) }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                อนุมัติ
            </button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))
<div class="flex items-center gap-3 px-5 py-3 rounded-xl mb-5" style="background:#f0fdf4; border:1.5px solid #86efac;">
    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" style="color:#15572e;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    <p class="text-sm font-semibold" style="color:#15572e;">{{ session('success') }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main content --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Header Card --}}
        <div class="card p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="badge {{ $typeColors[$assessment->type] ?? 'badge-gray' }}">{{ $typeLabels[$assessment->type] ?? $assessment->type }}</span>
                        <span class="badge {{ $statusColors[$assessment->status] ?? 'badge-gray' }}">{{ $statusLabels[$assessment->status] ?? $assessment->status }}</span>
                    </div>
                    <h2 class="text-lg font-bold mt-2" style="color:#0f3020;">{{ $assessment->title }}</h2>
                    <p class="font-mono text-xs mt-0.5" style="color:#94a3b8;">{{ $assessment->assessment_number }}</p>
                </div>
                @if($assessment->risk_level)
                <div class="text-center flex-shrink-0 ml-4">
                    @php $score = $assessment->risk_score ?? 0; @endphp
                    <div class="relative w-20 h-20">
                        <svg class="w-20 h-20 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f1f5f9" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.9" fill="none"
                                stroke="{{ $riskColor[$assessment->risk_level] ?? '#64748b' }}"
                                stroke-width="3" stroke-dasharray="{{ $score }}, 100"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-xl font-extrabold leading-none" style="color:{{ $riskColor[$assessment->risk_level] ?? '#64748b' }};">{{ $score }}</span>
                            <span class="text-xs" style="color:#94a3b8;">/ 100</span>
                        </div>
                    </div>
                    <p class="text-xs font-semibold mt-1" style="color:{{ $riskColor[$assessment->risk_level] ?? '#64748b' }};">{{ $riskLabels[$assessment->risk_level] ?? '' }}</p>
                </div>
                @endif
            </div>

            @if($assessment->description)
            <div class="mt-3 pt-4" style="border-top:1px solid #e8f0eb;">
                <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#94a3b8;">คำอธิบาย</p>
                <p class="text-sm leading-relaxed" style="color:#374151;">{{ $assessment->description }}</p>
            </div>
            @endif
            @if($assessment->scope)
            <div class="mt-3 pt-3" style="border-top:1px solid #e8f0eb;">
                <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#94a3b8;">ขอบเขต</p>
                <p class="text-sm" style="color:#374151;">{{ $assessment->scope }}</p>
            </div>
            @endif
        </div>

        {{-- Progress bar --}}
        @if($totalQ > 0)
        <div class="card px-6 py-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold" style="color:#1e293b;">ความคืบหน้าการตอบคำถาม</p>
                <span class="text-sm font-bold" style="color:#15572e;">{{ $answeredCount }}/{{ $totalQ }} ({{ $pct }}%)</span>
            </div>
            <div class="w-full h-2.5 rounded-full" style="background:#e8f0eb;">
                <div class="h-2.5 rounded-full transition-all" style="width:{{ $pct }}%; background:linear-gradient(90deg,#15572e,#2a6b4d);"></div>
            </div>
        </div>
        @endif

        {{-- Sections & Questions --}}
        @if($sections->isNotEmpty())
        <form action="{{ route('assessment.save-answers', $assessment) }}" method="POST">
            @csrf
            <div class="space-y-4">
                @foreach($sections as $section)
                <div class="card overflow-hidden">
                    <div class="px-6 py-4" style="background:linear-gradient(135deg,#f8faf9,#f0f7f3); border-bottom:1px solid #e8f0eb;">
                        <h3 class="text-sm font-bold" style="color:#0f3020;">{{ $section->title }}</h3>
                    </div>
                    <div class="divide-y" style="border-color:#f1f5f9;">
                        @foreach($section->questions as $q)
                        <div class="px-6 py-5">
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5"
                                    style="background:{{ $q->answer ? '#f0fdf4' : '#f8faf9' }}; color:{{ $q->answer ? '#15572e' : '#94a3b8' }}; border:1.5px solid {{ $q->answer ? '#86efac' : '#e8f0eb' }};">
                                    {{ $q->answer ? '✓' : $loop->iteration }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold mb-3" style="color:#1e293b;">{{ $q->question }}
                                        @if($q->risk_score > 0)<span class="ml-2 text-xs px-1.5 py-0.5 rounded" style="background:#fff1f2; color:#c0272d;">น้ำหนัก: {{ $q->risk_score }}</span>@endif
                                    </p>

                                    @if($q->answer_type === 'yes_no')
                                    <div class="flex gap-3">
                                        @foreach(['yes'=>'ใช่ (Yes)','no'=>'ไม่ใช่ (No)'] as $val => $label)
                                        <label class="flex items-center gap-2 px-4 py-2 rounded-xl cursor-pointer transition text-sm"
                                            style="border:2px solid {{ $q->answer===$val ? ($val==='yes' ? '#86efac' : '#fca5a5') : '#e8f0eb' }}; background:{{ $q->answer===$val ? ($val==='yes' ? '#f0fdf4' : '#fff1f2') : '#fff' }};">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $val }}" {{ $q->answer===$val ? 'checked' : '' }} style="accent-color:#15572e;">
                                            <span style="color:{{ $q->answer===$val ? ($val==='yes' ? '#15572e' : '#c0272d') : '#374151' }}; font-weight:{{ $q->answer===$val ? '600' : 'normal' }};">{{ $label }}</span>
                                        </label>
                                        @endforeach
                                    </div>

                                    @elseif($q->answer_type === 'scale')
                                    <div class="flex items-center gap-3">
                                        <input type="range" name="answers[{{ $q->id }}]" min="0" max="10"
                                            value="{{ $q->answer ?? 5 }}"
                                            class="flex-1 h-2 rounded-full appearance-none" style="accent-color:#15572e;"
                                            oninput="this.nextElementSibling.textContent = this.value">
                                        <span class="w-8 text-center font-bold text-sm" style="color:#15572e;">{{ $q->answer ?? 5 }}</span>
                                        <span class="text-xs" style="color:#94a3b8;">/ 10</span>
                                    </div>
                                    <div class="flex justify-between text-xs mt-1" style="color:#94a3b8;">
                                        <span>ต่ำ (0)</span><span>ปานกลาง (5)</span><span>สูงมาก (10)</span>
                                    </div>

                                    @else
                                    <textarea name="answers[{{ $q->id }}]" rows="2" class="form-input resize-none"
                                        placeholder="พิมพ์คำตอบ...">{{ $q->answer }}</textarea>
                                    @endif

                                    <div class="mt-3">
                                        <input type="text" name="notes[{{ $q->id }}]" value="{{ $q->notes }}" class="form-input"
                                            style="font-size:12px; padding:6px 12px;"
                                            placeholder="หมายเหตุเพิ่มเติม (ไม่บังคับ)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-3 mt-4">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    บันทึกคำตอบทั้งหมด
                </button>
            </div>
        </form>
        @endif

        {{-- Findings --}}
        @if($assessment->findings || $assessment->recommendations || $assessment->mitigation_measures)
        <div class="card p-6 space-y-5">
            <h3 class="text-sm font-bold" style="color:#1e293b;">ผลการประเมินและข้อเสนอแนะ</h3>
            @if($assessment->findings)
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#c0272d;">สิ่งที่พบ (Findings)</p>
                <div class="p-4 rounded-xl text-sm leading-relaxed" style="background:#fff5f5; border:1px solid #fca5a5; color:#374151;">{{ $assessment->findings }}</div>
            </div>
            @endif
            @if($assessment->recommendations)
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#b45309;">ข้อเสนอแนะ (Recommendations)</p>
                <div class="p-4 rounded-xl text-sm leading-relaxed" style="background:#fffbeb; border:1px solid #fcd34d; color:#374151;">{{ $assessment->recommendations }}</div>
            </div>
            @endif
            @if($assessment->mitigation_measures)
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#15572e;">มาตรการลดความเสี่ยง</p>
                <div class="p-4 rounded-xl text-sm leading-relaxed" style="background:#f0fdf4; border:1px solid #86efac; color:#374151;">{{ $assessment->mitigation_measures }}</div>
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">

        {{-- Risk Summary --}}
        @if($assessment->risk_level)
        <div class="card p-5" style="background:{{ $riskBg[$assessment->risk_level] ?? '#fff' }}; border:1.5px solid {{ $riskBorder[$assessment->risk_level] ?? '#e8f0eb' }};">
            <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:{{ $riskColor[$assessment->risk_level] ?? '#64748b' }};">ระดับความเสี่ยง</p>
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:{{ $riskColor[$assessment->risk_level] ?? '#64748b' }};">
                    <span class="text-xl font-black text-white">{{ $assessment->risk_score }}</span>
                </div>
                <div>
                    <p class="text-lg font-extrabold" style="color:{{ $riskColor[$assessment->risk_level] ?? '#64748b' }};">{{ $riskLabels[$assessment->risk_level] ?? '' }}</p>
                    <p class="text-xs" style="color:#64748b;">คะแนน {{ $assessment->risk_score }}/100</p>
                </div>
            </div>
            <div class="w-full h-2 rounded-full mt-3" style="background:rgba(0,0,0,0.1);">
                <div class="h-2 rounded-full" style="width:{{ $assessment->risk_score }}%; background:{{ $riskColor[$assessment->risk_level] ?? '#64748b' }};"></div>
            </div>
        </div>
        @endif

        {{-- Info --}}
        <div class="card p-5 space-y-3">
            <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#64748b;">ข้อมูลการประเมิน</p>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">ผู้สร้าง</span>
                <span style="color:#374151;">{{ $assessment->creator->name ?? '—' }}</span>
            </div>
            @if($assessment->started_at)
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">วันเริ่ม</span>
                <span style="color:#374151;">{{ $assessment->started_at->format('d/m/Y') }}</span>
            </div>
            @endif
            @if($assessment->completed_at)
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">วันเสร็จสิ้น</span>
                <span style="color:#374151;">{{ $assessment->completed_at->format('d/m/Y') }}</span>
            </div>
            @endif
            @if($assessment->approved_at)
            <div class="flex justify-between text-xs pt-2" style="border-top:1px solid #e8f0eb;">
                <span style="color:#94a3b8;">อนุมัติโดย</span>
                <span style="color:#15572e; font-weight:600;">{{ $assessment->approver->name ?? '—' }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">วันอนุมัติ</span>
                <span style="color:#374151;">{{ $assessment->approved_at->format('d/m/Y') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-xs pt-2" style="border-top:1px solid #e8f0eb;">
                <span style="color:#94a3b8;">คำถามทั้งหมด</span>
                <span style="color:#374151;">{{ $totalQ }} ข้อ</span>
            </div>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">ตอบแล้ว</span>
                <span style="color:{{ $answeredCount === $totalQ && $totalQ > 0 ? '#15572e' : '#374151' }}; font-weight:{{ $answeredCount === $totalQ && $totalQ > 0 ? '600' : 'normal' }};">{{ $answeredCount }} ข้อ</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="card p-5 space-y-2">
            <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#64748b;">การดำเนินการ</p>
            <a href="{{ route('assessment.edit', $assessment) }}" class="btn-outline w-full" style="justify-content:center;">แก้ไขข้อมูล</a>
            @if($assessment->status !== 'approved')
            <form action="{{ route('assessment.update', $assessment) }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $assessment->title }}">
                <input type="hidden" name="description" value="{{ $assessment->description }}">
                <input type="hidden" name="scope" value="{{ $assessment->scope }}">
                <input type="hidden" name="risk_level" value="{{ $assessment->risk_level }}">
                <input type="hidden" name="risk_score" value="{{ $assessment->risk_score }}">
                <input type="hidden" name="findings" value="{{ $assessment->findings }}">
                <input type="hidden" name="recommendations" value="{{ $assessment->recommendations }}">
                <input type="hidden" name="mitigation_measures" value="{{ $assessment->mitigation_measures }}">
                <input type="hidden" name="status" value="completed">
                @if($assessment->status !== 'completed')
                <button type="submit" class="btn-primary w-full" onclick="return confirm('บันทึกสถานะเป็นเสร็จสิ้น?')">
                    บันทึกว่าเสร็จสิ้น
                </button>
                @endif
            </form>
            @if($assessment->status === 'completed')
            <form action="{{ route('assessment.approve', $assessment) }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary w-full">อนุมัติการประเมิน</button>
            </form>
            @endif
            @endif
        </div>
    </div>
</div>

@endsection
