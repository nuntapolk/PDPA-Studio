@extends('layouts.app')
@section('title', 'สร้างคอร์สอบรม')

@section('content')
<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('training.index') }}" class="btn-outline text-sm">← กลับ</a>
    <h1 class="text-2xl font-bold" style="color:#15572e;">สร้างคอร์สอบรม</h1>
</div>

<form method="POST" action="{{ route('training.store') }}" x-data="quizBuilder()">
@csrf

<div class="grid gap-6" style="grid-template-columns:1fr 320px;">
    {{-- LEFT --}}
    <div class="space-y-6">
        {{-- Basic Info --}}
        <div class="card">
            <h3 class="font-semibold mb-4" style="color:#15572e;">📋 ข้อมูลคอร์ส</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อคอร์ส <span style="color:#c0272d;">*</span></label>
                    <input type="text" name="title" class="form-input" required placeholder="เช่น PDPA พื้นฐานสำหรับพนักงาน" value="{{ old('title') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">คำอธิบายย่อ</label>
                    <textarea name="description" class="form-input" rows="2" placeholder="สรุปสั้นๆ เกี่ยวกับคอร์ส">{{ old('description') }}</textarea>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#374151;">ระยะเวลา (นาที) <span style="color:#c0272d;">*</span></label>
                        <input type="number" name="duration_minutes" class="form-input" min="1" value="{{ old('duration_minutes', 30) }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#374151;">คะแนนผ่าน (%) <span style="color:#c0272d;">*</span></label>
                        <input type="number" name="passing_score" class="form-input" min="1" max="100" value="{{ old('passing_score', 70) }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color:#374151;">อายุใบรับรอง (เดือน)</label>
                        <input type="number" name="validity_months" class="form-input" min="1" value="{{ old('validity_months', 12) }}">
                    </div>
                </div>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }} class="accent-green-700">
                        <span class="text-sm" style="color:#374151;">คอร์สบังคับ</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="certificate_enabled" value="1" {{ old('certificate_enabled', true) ? 'checked' : '' }} class="accent-green-700">
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
                    placeholder="<h2>เนื้อหาหลัก</h2><p>...</p>" x-on:input="updatePreview($event.target.value)">{{ old('content') }}</textarea>
                <div class="flex gap-2 mt-2 flex-wrap">
                    @foreach(['<h2>หัวข้อ</h2>','<p>ย่อหน้า</p>','<ul><li>รายการ</li></ul>','<strong>ตัวหนา</strong>','<blockquote>อ้างอิง</blockquote>'] as $tmpl)
                    <button type="button" onclick="insertTemplate(this.dataset.tmpl)" data-tmpl="{{ $tmpl }}"
                        class="text-xs px-2 py-1 rounded border" style="color:#475569;border-color:#cbd5e1;">
                        {{ explode('>', $tmpl)[0] . '>' }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div x-show="tab==='preview'" class="min-h-32 rounded-lg p-4" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <div id="contentPreview" class="prose max-w-none" style="color:#334155;line-height:1.8;">
                    <p style="color:#94a3b8;">พิมพ์เนื้อหาด้านซ้ายเพื่อดูตัวอย่าง</p>
                </div>
            </div>
        </div>

        {{-- Quiz Builder --}}
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold" style="color:#15572e;">❓ แบบทดสอบ</h3>
                <button type="button" @click="addQuestion()" class="btn-primary text-sm">+ เพิ่มคำถาม</button>
            </div>

            <div class="space-y-4" x-ref="questionList">
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
                                        <input type="radio" :name="'questions[' + idx + '][correct_answer]'" :value="opt" x-model="q.correct" class="accent-green-700" :required="questions.length > 0">
                                        <span class="text-sm font-bold w-4" style="color:#64748b;" x-text="opt + '.'"></span>
                                        <input type="text" :name="'questions[' + idx + '][options][' + opt + ']'" class="form-input text-sm" :placeholder="'ตัวเลือก ' + opt" x-model="q.options[opt]">
                                    </div>
                                </template>
                            </div>
                            <input type="text" :name="'questions[' + idx + '][explanation]'" class="form-input text-sm" placeholder="คำอธิบายเฉลย (ไม่บังคับ)" x-model="q.explanation">
                        </div>
                    </div>
                </template>
                <div x-show="questions.length === 0" class="text-center py-8" style="color:#94a3b8;border:2px dashed #e2e8f0;border-radius:8px;">
                    <p>ยังไม่มีคำถาม — กด <strong>+ เพิ่มคำถาม</strong> เพื่อเริ่ม</p>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Sidebar --}}
    <div>
        <div class="card sticky top-6">
            <h3 class="font-semibold mb-4" style="color:#15572e;">⚡ บันทึก</h3>
            <div class="space-y-2">
                <button type="submit" class="btn-primary w-full">💾 สร้างคอร์ส</button>
                <a href="{{ route('training.index') }}" class="btn-outline w-full text-center block">ยกเลิก</a>
            </div>
            <div class="mt-4 pt-4" style="border-top:1px solid #e2e8f0;">
                <p class="text-xs" style="color:#94a3b8;">จำนวนคำถาม: <strong x-text="questions.length"></strong></p>
            </div>
        </div>
    </div>
</div>
</form>

<script>
function quizBuilder() {
    return {
        questions: [],
        nextId: 1,
        addQuestion() {
            this.questions.push({ id: this.nextId++, question: '', correct: 'A', explanation: '', options: { A:'', B:'', C:'', D:'' } });
        },
        removeQuestion(idx) { this.questions.splice(idx, 1); },
        updatePreview(html) { document.getElementById('contentPreview').innerHTML = html || '<p style="color:#94a3b8;">ตัวอย่างจะแสดงที่นี่</p>'; }
    }
}
function insertTemplate(tmpl) {
    const ta = document.getElementById('contentEditor');
    const s = ta.selectionStart, e = ta.selectionEnd;
    ta.value = ta.value.slice(0,s) + tmpl + ta.value.slice(e);
    ta.focus(); ta.selectionStart = ta.selectionEnd = s + tmpl.length;
}
document.addEventListener('DOMContentLoaded', () => {
    const ta = document.getElementById('contentEditor');
    if (ta && ta.value) document.getElementById('contentPreview').innerHTML = ta.value;
});
</script>
@endsection
