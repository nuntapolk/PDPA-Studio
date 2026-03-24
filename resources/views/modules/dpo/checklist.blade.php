@extends('layouts.app')
@section('title','Compliance Checklist — PDPA Studio')
@section('page-title','PDPA Compliance Checklist')

@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15572e;">✅ {{ session('success') }}</div>
@endif

<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('dpo.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium" style="color:#15572e;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        DPO Tasks
    </a>
    <span style="color:#e2e8f0;">/</span>
    <span class="text-sm" style="color:#64748b;">Compliance Checklist</span>
</div>

{{-- Overall Score --}}
<div class="card p-6 mb-6" style="background:linear-gradient(135deg,#0f3020,#15572e);">
    <div class="flex flex-col md:flex-row items-center gap-6">
        {{-- Gauge --}}
        <div class="flex-shrink-0 text-center">
            <div class="relative w-28 h-28">
                <svg viewBox="0 0 36 36" class="w-28 h-28 -rotate-90">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9" fill="none"
                        stroke="{{ $overallScore>=80?'#4ade80':($overallScore>=50?'#fbbf24':'#f87171') }}"
                        stroke-width="3"
                        stroke-dasharray="{{ $overallScore }} {{ 100-$overallScore }}"
                        stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-extrabold text-white">{{ $overallScore }}%</span>
                    <span class="text-xs" style="color:rgba(255,255,255,0.6);">Score</span>
                </div>
            </div>
            <p class="text-xs mt-2" style="color:rgba(255,255,255,0.7);">{{ $overallDone }}/{{ $overallTotal }} รายการ</p>
        </div>

        {{-- Category bars --}}
        <div class="flex-1 w-full">
            <p class="text-sm font-bold text-white mb-3">คะแนนตามหมวด PDPA</p>
            <div class="grid grid-cols-2 gap-x-6 gap-y-2.5">
                @php $catColors = ['consent'=>'#4ade80','rights'=>'#60a5fa','ropa'=>'#a78bfa','breach'=>'#f87171','security'=>'#fbbf24','policy'=>'#34d399','training'=>'#f9a8d4','vendor'=>'#7dd3fc']; @endphp
                @foreach(['consent','rights','ropa','breach','security','policy','training','vendor'] as $cat)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span style="color:rgba(255,255,255,0.8);">{{ \App\Models\ComplianceChecklist::categoryLabel($cat) }}</span>
                        <span class="font-bold" style="color:{{ $catColors[$cat] }};">{{ $scores[$cat] }}%</span>
                    </div>
                    <div class="h-1.5 rounded-full" style="background:rgba(255,255,255,0.1);">
                        <div class="h-1.5 rounded-full transition-all" style="width:{{ $scores[$cat] }}%;background:{{ $catColors[$cat] }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Category filter --}}
<div class="flex items-center gap-2 mb-5 flex-wrap">
    <a href="{{ route('dpo.checklist') }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition"
       style="background:{{ !$filterCat?'#15572e':'#f1f5f9' }};color:{{ !$filterCat?'white':'#475569' }};">ทั้งหมด</a>
    @foreach(['consent','rights','ropa','breach','security','policy','training','vendor'] as $cat)
    <a href="{{ route('dpo.checklist',['category'=>$cat]) }}" class="text-xs font-semibold px-3 py-1.5 rounded-lg transition"
       style="background:{{ $filterCat===$cat?'#15572e':'#f1f5f9' }};color:{{ $filterCat===$cat?'white':'#475569' }};">
        {{ \App\Models\ComplianceChecklist::categoryLabel($cat) }}
        <span class="ml-1 font-mono" style="color:{{ $filterCat===$cat?'rgba(255,255,255,0.7)':'#94a3b8' }};">{{ $scores[$cat] }}%</span>
    </a>
    @endforeach
</div>

{{-- Checklist Items --}}
@forelse($items as $category => $catItems)
<div class="card overflow-hidden mb-5">
    {{-- Category header --}}
    @php $sc = $scores[$category] ?? 0; @endphp
    <div class="px-5 py-3.5 flex items-center justify-between"
         style="background:linear-gradient(135deg,{{ $catColors[$category]??'#64748b' }}18,white);border-bottom:1px solid #f1f5f9;">
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold" style="color:#0f3020;">{{ \App\Models\ComplianceChecklist::categoryLabel($category) }}</span>
            <span class="text-xs font-mono px-2 py-0.5 rounded-full font-bold"
                  style="background:{{ $catColors[$category]??'#64748b' }}20;color:{{ $catColors[$category]??'#64748b' }};">{{ $sc }}%</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-24 h-2 rounded-full" style="background:#e2e8f0;">
                <div class="h-2 rounded-full" style="width:{{ $sc }}%;background:{{ $catColors[$category]??'#64748b' }};"></div>
            </div>
            <span class="text-xs" style="color:#94a3b8;">{{ $catItems->where('status','completed')->count() }}/{{ $catItems->where('status','!=','na')->count() }}</span>
        </div>
    </div>

    {{-- Items --}}
    <div class="divide-y" style="divide-color:#f8fafc;">
        @foreach($catItems as $item)
        <div class="px-5 py-3.5 {{ $item->status==='completed'?'':'hover:bg-gray-50' }}" style="{{ $item->status==='completed'?'background:#fafff9;':'' }}">
            <div class="flex items-start gap-3">
                {{-- Status toggle --}}
                <form method="POST" action="{{ route('dpo.checklist.update', $item) }}" class="flex-shrink-0 mt-0.5">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="{{ $item->status==='completed'?'not_started':'completed' }}">
                    @if($item->notes) <input type="hidden" name="notes" value="{{ $item->notes }}"> @endif
                    <button type="submit" class="w-5 h-5 rounded flex items-center justify-center transition"
                            style="border:2px solid {{ $item->status==='completed'?'#15572e':'#cbd5e1' }};background:{{ $item->status==='completed'?'#15572e':'white' }};">
                        @if($item->status==='completed')
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @endif
                    </button>
                </form>

                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-medium {{ $item->status==='completed'?'line-through':'' }}" style="color:{{ $item->status==='completed'?'#94a3b8':'#1e293b' }};">{{ $item->item }}</p>
                            @if($item->description)
                            <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $item->description }}</p>
                            @endif
                            @if($item->reference)
                            <span class="inline-block mt-1 text-xs px-1.5 py-0.5 rounded" style="background:#e8f0eb;color:#15572e;font-size:10px;">{{ $item->reference }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="badge {{ \App\Models\ComplianceChecklist::statusBadge($item->status) }}">
                                {{ \App\Models\ComplianceChecklist::statusLabel($item->status) }}
                            </span>
                            {{-- Inline status changer --}}
                            <form method="POST" action="{{ route('dpo.checklist.update', $item) }}">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-xs rounded-lg px-1.5 py-0.5 border" style="border-color:#e2e8f0;color:#64748b;background:white;">
                                    <option value="not_started" {{ $item->status==='not_started'?'selected':'' }}>ยังไม่เริ่ม</option>
                                    <option value="in_progress" {{ $item->status==='in_progress'?'selected':'' }}>กำลังดำเนินการ</option>
                                    <option value="completed"   {{ $item->status==='completed'  ?'selected':'' }}>เสร็จสิ้น</option>
                                    <option value="na"          {{ $item->status==='na'          ?'selected':'' }}>ไม่เกี่ยวข้อง</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    @if($item->notes)
                    <p class="text-xs mt-1.5 px-2 py-1 rounded" style="background:#f8fafc;color:#475569;">📝 {{ $item->notes }}</p>
                    @endif
                    @if($item->due_date && $item->status !== 'completed')
                    <p class="text-xs mt-1" style="color:{{ $item->due_date->isPast()?'#c0272d':'#b45309' }};">
                        📅 กำหนด: {{ $item->due_date->format('d/m/Y') }}
                        {{ $item->due_date->isPast() ? '(เกินกำหนด)' : '' }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
<div class="card p-12 text-center">
    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#f1f5f9;">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#cbd5e1;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
    </div>
    <p class="text-sm font-medium mb-1" style="color:#64748b;">ยังไม่มีรายการ Checklist</p>
    <p class="text-xs" style="color:#94a3b8;">กรุณา Seed ข้อมูล Checklist หรือสร้างรายการใหม่</p>
</div>
@endforelse

@endsection
