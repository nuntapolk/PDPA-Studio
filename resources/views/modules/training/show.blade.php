@extends('layouts.app')
@section('title', $course->title)

@section('content')
<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('training.index') }}" class="btn-outline text-sm">← กลับ</a>
    <h1 class="text-2xl font-bold" style="color:#15572e;">{{ $course->title }}</h1>
    @if($course->is_required)
        <span class="badge" style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;">บังคับ</span>
    @endif
    @if(!$course->is_active)
        <span class="badge" style="background:#f1f5f9;color:#64748b;">ปิดใช้งาน</span>
    @endif
</div>

<div class="grid gap-6" style="grid-template-columns:1fr 320px;">
    {{-- LEFT: Content + Quiz --}}
    <div>
        {{-- Course Info --}}
        <div class="card mb-6">
            <div class="flex items-center gap-6 mb-4 flex-wrap">
                <div class="text-sm" style="color:#64748b;">
                    <span class="font-medium">⏱ ระยะเวลา:</span>
                    {{ $course->duration_minutes }} นาที
                </div>
                <div class="text-sm" style="color:#64748b;">
                    <span class="font-medium">🎯 คะแนนผ่าน:</span>
                    {{ $course->passing_score }}%
                </div>
                @if($course->certificate_enabled)
                <div class="text-sm" style="color:#64748b;">
                    <span class="font-medium">🏆 ใบรับรอง:</span>
                    {{ $course->validity_months }} เดือน
                </div>
                @endif
                <div class="text-sm" style="color:#64748b;">
                    <span class="font-medium">❓ คำถาม:</span>
                    {{ $questions->count() }} ข้อ
                </div>
            </div>
            @if($course->description)
                <p style="color:#475569;">{{ $course->description }}</p>
            @endif
        </div>

        {{-- Tab: เนื้อหา / แบบทดสอบ --}}
        <div class="card mb-6" x-data="{ tab: 'content' }">
            <div class="flex gap-1 mb-6 p-1 rounded-lg" style="background:#f1f5f9;width:fit-content;">
                <button @click="tab='content'" :class="tab==='content' ? 'btn-primary text-sm' : 'btn-outline text-sm'">📖 เนื้อหา</button>
                <button @click="tab='quiz'"    :class="tab==='quiz'    ? 'btn-primary text-sm' : 'btn-outline text-sm'">📝 แบบทดสอบ ({{ $questions->count() }} ข้อ)</button>
            </div>

            {{-- Content Tab --}}
            <div x-show="tab==='content'">
                @if($course->content)
                    <div class="prose max-w-none" style="color:#334155;line-height:1.8;">
                        {!! $course->content !!}
                    </div>
                @else
                    <div class="text-center py-12" style="color:#94a3b8;">
                        <div style="font-size:3rem;">📄</div>
                        <p class="mt-2">ยังไม่มีเนื้อหาในคอร์สนี้</p>
                    </div>
                @endif
            </div>

            {{-- Quiz Tab --}}
            <div x-show="tab==='quiz'">
                @if($questions->isEmpty())
                    <div class="text-center py-12" style="color:#94a3b8;">
                        <div style="font-size:3rem;">❓</div>
                        <p class="mt-2">ยังไม่มีคำถาม</p>
                    </div>
                @else
                    {{-- My latest result banner --}}
                    @if($myLatest)
                        <div class="rounded-lg p-4 mb-6" style="background:{{ $myLatest->passed ? '#f0fdf4' : '#fff1f2' }};border:1px solid {{ $myLatest->passed ? '#bbf7d0' : '#fecdd3' }};">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div>
                                    <span style="font-size:1.5rem;">{{ $myLatest->passed ? '✅' : '❌' }}</span>
                                    <strong style="color:{{ $myLatest->passed ? '#15572e' : '#c0272d' }};">
                                        {{ $myLatest->passed ? 'ผ่านแล้ว' : 'ไม่ผ่าน' }}
                                    </strong>
                                    — คะแนน {{ $myLatest->score }}%
                                    (ครั้งที่ {{ $myLatest->attempt_number }})
                                </div>
                                @if($myLatest->passed && $myLatest->certificate_number)
                                    <a href="{{ route('training.result', [$course, $myLatest]) }}" class="btn-outline text-sm">🏆 ดูใบรับรอง</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('training.quiz.submit', $course) }}" onsubmit="return confirmSubmit()">
                        @csrf
                        <div class="space-y-6">
                        @foreach($questions as $i => $q)
                            <div class="rounded-lg p-5" style="background:#f8fafc;border:1px solid #e2e8f0;">
                                <p class="font-semibold mb-3" style="color:#1e293b;">
                                    {{ $i+1 }}. {{ $q->question }}
                                </p>
                                <div class="space-y-2">
                                @foreach($q->options as $key => $label)
                                    <label class="flex items-center gap-3 cursor-pointer rounded-lg px-3 py-2 hover:bg-white transition" style="border:1px solid transparent;" onmouseover="this.style.borderColor='#cbd5e1'" onmouseout="this.style.borderColor='transparent'">
                                        <input type="radio" name="answer_{{ $q->id }}" value="{{ $key }}" class="accent-green-700" required>
                                        <span style="color:#334155;"><strong>{{ $key }}.</strong> {{ $label }}</span>
                                    </label>
                                @endforeach
                                </div>
                            </div>
                        @endforeach
                        </div>
                        <div class="mt-6 flex gap-3">
                            <button type="submit" class="btn-primary">
                                ส่งคำตอบ ({{ $questions->count() }} ข้อ)
                            </button>
                            <span class="text-sm" style="color:#94a3b8;align-self:center;">
                                ต้องได้ {{ $course->passing_score }}% ขึ้นไปเพื่อผ่าน
                            </span>
                        </div>
                    </form>
                    <script>
                    function confirmSubmit() {
                        const radios = document.querySelectorAll('input[type=radio]');
                        const names  = new Set([...radios].map(r => r.name));
                        const answered = [...names].filter(n => document.querySelector(`input[name="${n}"]:checked`));
                        if (answered.length < names.size) {
                            return confirm(`ยังตอบไม่ครบ (${answered.length}/${names.size} ข้อ) ต้องการส่งต่อไหม?`);
                        }
                        return true;
                    }
                    </script>
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT: Sidebar --}}
    <div class="space-y-4">
        {{-- Admin Actions --}}
        @if(Auth::user()->isAdmin())
        <div class="card">
            <h3 class="font-semibold mb-3" style="color:#15572e;">⚙️ จัดการคอร์ส</h3>
            <div class="space-y-2">
                <a href="{{ route('training.edit', $course) }}" class="btn-outline text-sm w-full text-center block">✏️ แก้ไขคอร์ส</a>
                <form method="POST" action="{{ route('training.toggle', $course) }}">
                    @csrf
                    <button type="submit" class="w-full text-sm py-2 px-3 rounded-lg border font-medium transition"
                        style="border-color:#cbd5e1;color:#475569;background:white;"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        {{ $course->is_active ? '⏸ ปิดคอร์ส' : '▶️ เปิดคอร์ส' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('training.destroy', $course) }}" onsubmit="return confirm('ลบคอร์สนี้?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger text-sm w-full">🗑 ลบคอร์ส</button>
                </form>
            </div>
        </div>
        @endif

        {{-- My Attempt History --}}
        <div class="card">
            <h3 class="font-semibold mb-3" style="color:#15572e;">📊 ประวัติการทำแบบทดสอบ</h3>
            @if($attempts->isEmpty())
                <p class="text-sm" style="color:#94a3b8;">ยังไม่เคยทำแบบทดสอบ</p>
            @else
                <div class="space-y-2">
                @foreach($attempts as $a)
                    <div class="flex items-center justify-between rounded-lg px-3 py-2" style="background:#f8fafc;">
                        <div>
                            <span class="text-xs font-medium" style="color:{{ $a->passed ? '#15572e' : '#c0272d' }};">
                                {{ $a->passed ? '✅' : '❌' }} ครั้งที่ {{ $a->attempt_number }}
                            </span>
                            <p class="text-xs" style="color:#94a3b8;">{{ $a->completed_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-sm" style="color:{{ $a->passed ? '#15572e' : '#c0272d' }};">{{ $a->score }}%</span>
                            @if($a->certificate_number)
                                <p class="text-xs" style="color:#64748b;">
                                    <a href="{{ route('training.result', [$course, $a]) }}" style="color:#15572e;">🏆 cert</a>
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
                </div>
            @endif
        </div>

        {{-- Org Completions --}}
        @if(Auth::user()->isAdmin())
        <div class="card">
            <h3 class="font-semibold mb-3" style="color:#15572e;">👥 ผู้ผ่านในองค์กร</h3>
            <p class="text-2xl font-bold mb-1" style="color:#15572e;">{{ $allCompletions->count() }} คน</p>
            @if($allCompletions->count())
                <div class="space-y-1 mt-3 max-h-48 overflow-y-auto">
                @foreach($allCompletions->take(20) as $c)
                    <div class="flex items-center gap-2 text-xs" style="color:#64748b;">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:#15572e;">
                            {{ strtoupper(substr($c->user->name ?? '?', 0, 1)) }}
                        </div>
                        {{ $c->user->name ?? '—' }}
                    </div>
                @endforeach
                </div>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
