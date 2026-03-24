@extends('layouts.app')
@section('title', 'แก้ไขคอร์ส — ' . $course->title)

@section('content')
<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('training.show', $course) }}" class="btn-outline text-sm">← กลับ</a>
    <h1 class="text-2xl font-bold" style="color:#15572e;">แก้ไขคอร์ส</h1>
</div>

<form method="POST" action="{{ route('training.update', $course) }}" x-data="quizBuilderEdit()">
@csrf @method('PUT')

<div class="grid gap-6" style="grid-template-columns:1fr 320px;">
    {{-- LEFT --}}
    <div class="space-y-6">
        {{-- Basic Info --}}
        <div class="card">
            <h3 class="font-semibold mb-4" style="color:#15572e;">📋 ข้อมูลคอร์ส</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อคอร์ส <span style="color:#c0272d;">*</span></label>
                    <input type="text" name="title" class="form-input" required value="{{ old('title', $course->title) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">คำอธิบายย่อ</label>
                    <textarea name="description" class="form-input" rows="2">{{ old('description', $course->description) }}</textarea>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#374151;">ระยะเวลา (นาที) <span style="color:#c0272d;">*</span></label>
                        <input type="number" name="duration_minutes" class="form-input" min="1" value="{{ old('duration_minutes', $course->duration_minutes) }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#374151;">คะแนนผ่าน (%) <span style="color:#c0272d;">*</span></label>
                        <input type="number" name="passing_score" class="form-input" min="1" max="100" value="{{ old('passing_score', $course->passing_score) }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#374151;">อายุใบรับรอง (เดือน)</label>
                        <input type="number" name="validity_months" class="form-input" min="1" value="{{ old('validity_months', $course->validity_months) }}">
                    </div>
                </div>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" value="1" {{ $course->is_required ? 'checked' : '' }} class="accent-green-700">
                        <span class="text-sm" style="color:#374151;">คอร์สบังคับ</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="certificate_enabled" value="1" {{ $course->certificate_enabled ? 'checked' : '' }} class="accent-green-700">
                        <span class="text-sm" style="color:#374151;">ออกใบรับรอง</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="card" x-data="{ tab: 'edit' }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold" style="color:#15572e;">📄 เนื้อหา (HTML)</h3>
                <div class="flex gap-1 p-1 rounded-lg" style="background:#f1f5f9;">
                    <button type="button" @click="tab='edit'" :class="tab==='edit' ? 'btn-primary text-xs' : 'btn-outline text-xs'">✏️ แก้ไข</button>
                    <button type="button" @click="tab='preview'" :class="tab==='preview' ? 'btn-primary text-xs' : 'btn-outline text-xs'">👁 ตัวอย่าง</button>
                </div>
            </div>
            <div x-show="tab==='edit'">
                <textarea name="content" id="contentEditor" class="form-input font-mono text-sm" rows="14"
                    x-on:input="updatePreview($event.target.value)">{{ old('content', $course->content) }}</textarea>
            </div>
            <div x-show="tab==='preview'" class="min-h-32 rounded-lg p-4" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <div id="contentPreview" class="prose max-w-none" style="color:#334155;line-height:1.8;"></div>
            </div>
        </div>

        {{-- Quiz Builder --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold" style="color:#15572e;">❓ แบบทดสอบ</h3>
                <button type="button" @click="addQuestion()" class="btn-primary text-sm">+ เพิ่มคำถาม</button>
            </div>
            <div class="mb-4 p-3 rounded-lg text-sm" style="background:#fef9c3;color:#713f12;border:1px solid #fef08a;">
                ⚠️ การบันทึกจะแทนที่คำถามทั้งหมด
            </div>
            <div class="space-y-4">
                <template x-for="(q, idx) in questions" :key="q.id">
                    <div class="rounded-lg p-4" style="border:1px solid #e2e8f0;background:#f8fafc;">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-medium text-sm" style="color:#15572e;" x-text="'คำถามที่ ' + (idx+1)"></span>
                            <button type="button" @click="removeQuestion(idx)" class="text-sm" style="color:#c0272d;">✕ ลบ</button>
                        </div>
                        <div class="space-y-3">
                            <input type="text" :name="'questions[' + idx + '][question]'" class="form-input" placeholder="คำถาม..." x-model="q.question">
                            <div class="space-y-2">
                                <template x-for="(opt, key) in ['A','B','C','D']" :key="key">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" :name="'questions[' + idx + '][correct_answer]'" :value="opt" x-model="q.correct" class="accent-green-700">
                                        <span class="text-sm font-bold w-4" style="color:#64748b;" x-text="opt + '.'"></span>
                                        <input type="text" :name="'questions[' + idx + '][options][' + opt + ']'" class="form-input text-sm" :placeholder="'ตัวเลือก ' + opt" x-model="q.options[opt]">
                                    </div>
                                </template>
                            </div>
                            <input type="text" :name="'questions[' + idx + '][explanation]'" class="form-input text-sm" placeholder="คำอธิบายเฉลย" x-model="q.explanation">
                        </div>
                    </div>
                </template>
                <div x-show="questions.length === 0" class="text-center py-8" style="color:#94a3b8;border:2px dashed #e2e8f0;border-radius:8px;">
                    <p>ไม่มีคำถาม — กด <strong>+ เพิ่มคำถาม</strong></p>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT --}}
    <div>
        <div class="card sticky top-6">
            <h3 class="font-semibold mb-4" style="color:#15572e;">⚡ บันทึก</h3>
            <div class="space-y-2">
                <button type="submit" class="btn-primary w-full">💾 บันทึกการแก้ไข</button>
                <a href="{{ route('training.show', $course) }}" class="btn-outline w-full text-center block">ยกเลิก</a>
            </div>
            <div class="mt-4 pt-4" style="border-top:1px solid #e2e8f0;">
                <p class="text-xs" style="color:#94a3b8;">คำถามปัจจุบัน: <strong x-text="questions.length"></strong></p>
                <p class="text-xs mt-1" style="color:#94a3b8;">สร้างเมื่อ: {{ $course->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>
</div>
</form>

<script>
function quizBuilderEdit() {
    const existing = @json($questions->map(fn($q) => [
        'id'          => $q->id,
        'question'    => $q->question,
        'correct'     => $q->correct_answer,
        'explanation' => $q->explanation ?? '',
        'options'     => $q->options,
    ]));
    return {
        questions: existing.map((q,i) => ({...q, id: i})),
        nextId: existing.length,
        addQuestion() {
            this.questions.push({ id: this.nextId++, question:'', correct:'A', explanation:'', options:{A:'',B:'',C:'',D:''} });
        },
        removeQuestion(idx) { this.questions.splice(idx,1); },
        updatePreview(html) { document.getElementById('contentPreview').innerHTML = html || ''; }
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const ta = document.getElementById('contentEditor');
    if (ta && ta.value) document.getElementById('contentPreview').innerHTML = ta.value;
});
</script>
@endsection
