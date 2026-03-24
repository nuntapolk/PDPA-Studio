@extends('layouts.app')
@section('title', 'สร้าง Account ใหม่')
@section('content')

<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('settings.accounts.index') }}" class="btn-outline text-sm">← กลับ</a>
    <h1 class="text-2xl font-bold" style="color:#15572e;">➕ สร้าง Account ใหม่</h1>
</div>

<form method="POST" action="{{ route('settings.accounts.store') }}" x-data="{ showPw: false }">
@csrf
<div class="grid gap-6" style="grid-template-columns:1fr 280px;">
<div class="space-y-5">

    {{-- Role Selector ────────────────────────────────────────────────────── --}}
    <div class="card">
        <h3 class="font-semibold mb-4" style="color:#15572e;">🎭 Role</h3>
        <div class="grid grid-cols-2 gap-3">
            @foreach($roles as $roleKey => $roleDef)
            <label class="cursor-pointer rounded-xl p-3 border-2 transition"
                   x-bind:class="role==='{{ $roleKey }}' ? 'border-current' : 'border-transparent'"
                   x-bind:style="role==='{{ $roleKey }}' ? 'background:{{ $roleDef['bg'] }};border-color:{{ $roleDef['color'] }}' : 'background:#f8fafc'"
                   x-data x-init="$el.addEventListener('click', ()=>{ $root.querySelector('[name=role]').value='{{ $roleKey }}'; })"
                   @click="role='{{ $roleKey }}'">
                <input type="radio" name="role" value="{{ $roleKey }}" class="sr-only"
                       {{ old('role','editor') === $roleKey ? 'checked' : '' }}
                       x-model="role" required>
                <div class="font-bold text-sm" style="color:{{ $roleDef['color'] }};">
                    {{ $roleDef['icon'] }} {{ $roleDef['label'] }}
                </div>
                <div class="text-xs mt-0.5" style="color:#64748b;">{{ $roleDef['description'] }}</div>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Basic Info ───────────────────────────────────────────────────────── --}}
    <div class="card">
        <h3 class="font-semibold mb-4" style="color:#15572e;">👤 ข้อมูลผู้ใช้</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อ-นามสกุล <span style="color:#c0272d;">*</span></label>
                <input type="text" name="name" class="form-input {{ $errors->has('name')?'border-red-400':'' }}"
                       value="{{ old('name') }}" required placeholder="เช่น สมชาย ดีมาก">
                @error('name')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">อีเมล <span style="color:#c0272d;">*</span></label>
                <input type="email" name="email" class="form-input {{ $errors->has('email')?'border-red-400':'' }}"
                       value="{{ old('email') }}" required placeholder="user@example.com">
                @error('email')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" style="color:#374151;">โทรศัพท์</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="08x-xxx-xxxx">
            </div>
        </div>
    </div>

    {{-- Password ─────────────────────────────────────────────────────────── --}}
    <div class="card">
        <h3 class="font-semibold mb-4" style="color:#15572e;">🔑 รหัสผ่าน</h3>
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">รหัสผ่าน <span style="color:#c0272d;">*</span></label>
            <div class="relative">
                <input :type="showPw?'text':'password'" name="password"
                       class="form-input pr-10 {{ $errors->has('password')?'border-red-400':'' }}"
                       required placeholder="อย่างน้อย 8 ตัว, ตัวพิมพ์ใหญ่+เล็ก+ตัวเลข">
                <button type="button" @click="showPw=!showPw"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <template x-if="!showPw"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></template>
                        <template x-if="showPw"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></template>
                    </svg>
                </button>
            </div>
            @error('password')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
            <p class="text-xs mt-1.5" style="color:#94a3b8;">Password จะถูก Hash (bcrypt) ก่อนบันทึก</p>
        </div>
    </div>

</div>
{{-- Sidebar ─────────────────────────────────────────────────────────────── --}}
<div>
    <div class="card sticky top-6 space-y-3">
        <h3 class="font-semibold" style="color:#15572e;">⚡ บันทึก</h3>
        <div>
            <label class="block text-sm font-medium mb-1" style="color:#374151;">สถานะ</label>
            <select name="status" class="form-input">
                <option value="active"   {{ old('status','active')==='active'  ?'selected':'' }}>✅ Active</option>
                <option value="inactive" {{ old('status')==='inactive'?'selected':'' }}>⏸ Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn-primary w-full">💾 สร้าง Account</button>
        <a href="{{ route('settings.accounts.index') }}" class="btn-outline w-full text-center block">ยกเลิก</a>
        <div class="pt-2 text-xs" style="color:#94a3b8;border-top:1px solid #e2e8f0;">
            <p>🔒 Password ถูก Hashed ด้วย bcrypt</p>
            <p class="mt-1">📌 Account จะถูกเพิ่มใน Organization หลัก</p>
        </div>
    </div>
</div>
</div>
</form>

<script>
function accountForm() {
    return { role: '{{ old('role','editor') }}', showPw: false };
}
</script>
@endsection
