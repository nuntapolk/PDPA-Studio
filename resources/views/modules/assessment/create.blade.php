@extends('layouts.app')
@section('title', 'สร้างการประเมินใหม่ — PDPA Studio')
@section('page-title', 'DPIA / Assessment — สร้างการประเมินใหม่')

@section('content')
<div class="mb-4">
    <a href="{{ route('assessment.index') }}" class="text-sm font-medium" style="color:#15572e;">← กลับรายการ</a>
</div>

<div class="max-w-2xl mx-auto">

    {{-- Type selector --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        @php
            $types = [
                'dpia' => ['icon'=>'🔍','label'=>'DPIA','sub'=>'Data Protection Impact Assessment','color'=>'#c0272d','bg'=>'#fff1f2','border'=>'#fca5a5'],
                'lia'  => ['icon'=>'⚖️','label'=>'LIA','sub'=>'Legitimate Interest Assessment','color'=>'#1d4ed8','bg'=>'#eff6ff','border'=>'#93c5fd'],
                'gap_analysis' => ['icon'=>'📊','label'=>'Gap Analysis','sub'=>'PDPA Compliance Gap Analysis','color'=>'#15572e','bg'=>'#f0fdf4','border'=>'#86efac'],
            ];
        @endphp
        @foreach($types as $key => $t)
        <label class="type-card cursor-pointer rounded-xl p-4 text-center transition" id="card-{{ $key }}"
            style="border:2px solid #e8f0eb; background:#fff;"
            onclick="selectType('{{ $key }}')">
            <input type="radio" name="_type_select" value="{{ $key }}" class="sr-only">
            <div class="text-3xl mb-2">{{ $t['icon'] }}</div>
            <p class="text-sm font-bold" style="color:#1e293b;">{{ $t['label'] }}</p>
            <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $t['sub'] }}</p>
        </label>
        @endforeach
    </div>

    <div class="card p-7">
        @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl text-sm mb-5" style="background:#fff1f2;border:1.5px solid #fca5a5;color:#991b1b;">
            <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('assessment.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" id="type_input" value="{{ old('type','dpia') }}">

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ชื่อการประเมิน <span style="color:#c0272d;">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="form-input"
                        placeholder="เช่น DPIA: ระบบ AI Recommendation, Gap Analysis ประจำปี 2568">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">คำอธิบาย</label>
                    <textarea name="description" rows="3" class="form-input"
                        placeholder="อธิบายวัตถุประสงค์และที่มาของการประเมินนี้">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#374151;">ขอบเขต (Scope)</label>
                    <input type="text" name="scope" value="{{ old('scope') }}" class="form-input"
                        placeholder="เช่น ระบบ CRM, กระบวนการ HR, บริษัทในเครือ">
                </div>
            </div>

            {{-- Preview questions --}}
            <div id="q-preview" class="mt-6 p-4 rounded-xl" style="background:#f8faf9; border:1px solid #e8f0eb;">
                <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#64748b;">ตัวอย่างคำถามที่จะสร้าง</p>
                <div id="q-list" class="space-y-1.5 text-xs" style="color:#475569;"></div>
            </div>

            <div class="flex justify-end gap-3 mt-6 pt-4" style="border-top:1px solid #e8f0eb;">
                <a href="{{ route('assessment.index') }}" class="btn-outline">ยกเลิก</a>
                <button type="submit" class="btn-primary">สร้างและเริ่มประเมิน →</button>
            </div>
        </form>
    </div>
</div>

<script>
const typeColors = {
    dpia:         {color:'#c0272d', bg:'#fff1f2', border:'#fca5a5'},
    lia:          {color:'#1d4ed8', bg:'#eff6ff', border:'#93c5fd'},
    gap_analysis: {color:'#15572e', bg:'#f0fdf4', border:'#86efac'},
};

const dpiaQ = @json(collect($dpiaQuestions)->flatMap(fn($s) => collect($s['questions'])->pluck('question'))->values());
const liaQ  = @json(collect($liaQuestions)->flatMap(fn($s) => collect($s['questions'])->pluck('question'))->values());
const gapQ  = @json(collect($gapQuestions)->flatMap(fn($s) => collect($s['questions'])->pluck('question'))->values());
const qMap  = { dpia: dpiaQ, lia: liaQ, gap_analysis: gapQ };

function selectType(type) {
    document.getElementById('type_input').value = type;
    // Update cards
    Object.keys(typeColors).forEach(t => {
        const card = document.getElementById('card-' + t);
        if (t === type) {
            card.style.borderColor = typeColors[t].border;
            card.style.background  = typeColors[t].bg;
        } else {
            card.style.borderColor = '#e8f0eb';
            card.style.background  = '#fff';
        }
    });
    // Update preview
    const list = document.getElementById('q-list');
    const questions = qMap[type] || [];
    list.innerHTML = questions.slice(0, 6).map(q => `<p>• ${q}</p>`).join('') +
        (questions.length > 6 ? `<p style="color:#94a3b8;">...และอีก ${questions.length - 6} ข้อ</p>` : '');
}

// Init
selectType('{{ old('type','dpia') }}');
</script>

@endsection
