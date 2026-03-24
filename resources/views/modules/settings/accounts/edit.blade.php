@extends('layouts.app')
@section('title', 'แก้ไข Account — '.$user->name)
@section('content')

<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('settings.accounts.index') }}" class="btn-outline text-sm">← กลับ</a>
    <h1 class="text-2xl font-bold" style="color:#15572e;">✏️ แก้ไข Account</h1>
</div>

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#dcfce7;color:#15572e;border:1px solid #86efac;">
    ✅ {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="mb-4 px-4 py-3 rounded-xl text-sm" style="background:#fee2e2;color:#c0272d;border:1px solid #fca5a5;">
    ❌ {{ $errors->first() }}
</div>
@endif

<div class="grid gap-6" style="grid-template-columns:1fr 300px;" x-data="{ role: '{{ $user->role }}', showPw: false }">

{{-- Main form ───────────────────────────────────────────────────────────── --}}
<div class="space-y-5">

    {{-- Profile Card ─────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="flex items-center gap-4 mb-5 pb-4" style="border-bottom:1px solid #e2e8f0;">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-black text-xl"
                 style="background:{{ $user->getRoleBg() }};color:{{ $user->getRoleColor() }};">
                {{ strtoupper(mb_substr($user->name,0,1)) }}
            </div>
            <div>
                <p class="font-bold text-lg" style="color:#1e293b;">{{ $user->name }}</p>
                <p class="text-sm font-mono" style="color:#94a3b8;">{{ $user->email }}</p>
                @if($user->is_builtin)
                <span class="text-xs px-2 py-0.5 rounded-full mt-1 inline-block" style="background:#fef3c7;color:#92400e;">
                    🔒 Built-in Account
                </span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('settings.accounts.update', $user) }}">
        @csrf @method('PUT')
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">ชื่อ <span style="color:#c0272d;">*</span></label>
                    <input type="text" name="name" class="form-input" value="{{ old('name',$user->name) }}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">อีเมล <span style="color:#c0272d;">*</span></label>
                    <input type="email" name="email" class="form-input" value="{{ old('email',$user->email) }}" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">โทรศัพท์</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone',$user->phone) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">สถานะ</label>
                    <select name="status" class="form-input" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <option value="active"   {{ old('status',$user->status)==='active'  ?'selected':'' }}>✅ Active</option>
                        <option value="inactive" {{ old('status',$user->status)==='inactive'?'selected':'' }}>⏸ Inactive</option>
                        <option value="locked"   {{ old('status',$user->status)==='locked'  ?'selected':'' }}>🔒 Locked</option>
                    </select>
                    @if($user->id === auth()->id())
                    <input type="hidden" name="status" value="{{ $user->status }}">
                    @endif
                </div>
            </div>

            {{-- Role ──────────────────────────────────────────────────────── --}}
            <div>
                <label class="block text-sm font-medium mb-2" style="color:#374151;">Role</label>
                @if($user->id === auth()->id())
                <div class="px-3 py-2 rounded-lg text-sm" style="background:#fef3c7;color:#92400e;">
                    ⚠️ ไม่สามารถเปลี่ยน Role ของตัวเองได้
                </div>
                <input type="hidden" name="role" value="{{ $user->role }}">
                @else
                <div class="grid grid-cols-2 gap-2">
                    @foreach($roles as $roleKey => $roleDef)
                    <label class="cursor-pointer rounded-xl p-3 border-2 transition"
                           :class="role==='{{ $roleKey }}' ? 'border-current' : 'border-transparent'"
                           :style="role==='{{ $roleKey }}' ? 'background:{{ $roleDef['bg'] }};border-color:{{ $roleDef['color'] }}' : 'background:#f8fafc'"
                           @click="role='{{ $roleKey }}'">
                        <input type="radio" name="role" value="{{ $roleKey }}" class="sr-only"
                               x-model="role"
                               {{ old('role',$user->role) === $roleKey ? 'checked' : '' }}>
                        <div class="font-bold text-xs" style="color:{{ $roleDef['color'] }};">
                            {{ $roleDef['icon'] }} {{ $roleDef['label'] }}
                        </div>
                        <div class="text-xs mt-0.5 leading-tight" style="color:#64748b;">{{ $roleDef['description'] }}</div>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-primary">💾 บันทึกการแก้ไข</button>
            </div>
        </div>
        </form>
    </div>

    {{-- Reset Password ────────────────────────────────────────────────────── --}}
    <div class="card" x-data="{ open: false, showPw2: false }">
        <button type="button" @click="open=!open"
                class="w-full flex items-center justify-between font-semibold"
                style="color:#15572e;">
            <span>🔑 รีเซ็ตรหัสผ่าน</span>
            <svg class="w-4 h-4 transition" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" class="mt-4">
            <form method="POST" action="{{ route('settings.accounts.password', $user) }}">
            @csrf @method('PATCH')
            <div class="space-y-3">
                <div class="p-3 rounded-lg text-xs" style="background:#fef3c7;color:#92400e;">
                    ⚠️ Password ใหม่จะถูก Hash (bcrypt) ทันที
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color:#374151;">รหัสผ่านใหม่ <span style="color:#c0272d;">*</span></label>
                    <div class="relative">
                        <input :type="showPw2?'text':'password'" name="new_password"
                               class="form-input pr-10 {{ $errors->has('new_password')?'border-red-400':'' }}"
                               placeholder="อย่างน้อย 8 ตัว, ตัวพิมพ์ใหญ่+เล็ก+ตัวเลข">
                        <button type="button" @click="showPw2=!showPw2"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('new_password')<p class="text-xs mt-1" style="color:#c0272d;">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-danger text-sm">🔑 รีเซ็ตรหัสผ่าน</button>
            </div>
            </form>
        </div>
    </div>

</div>

{{-- Sidebar ─────────────────────────────────────────────────────────────── --}}
<div>
    <div class="card sticky top-6 space-y-4">
        <h3 class="font-semibold" style="color:#15572e;">ℹ️ ข้อมูล Account</h3>

        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span style="color:#64748b;">สร้างเมื่อ</span>
                <span style="color:#374151;">{{ $user->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:#64748b;">Login ล่าสุด</span>
                <span style="color:#374151;">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:#64748b;">IP ล่าสุด</span>
                <span class="font-mono text-xs" style="color:#374151;">{{ $user->last_login_ip ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span style="color:#64748b;">Failed Logins</span>
                <span class="{{ $user->failed_login_attempts > 0 ? 'font-bold' : '' }}"
                      style="color:{{ $user->failed_login_attempts > 2 ? '#c0272d' : '#374151' }};">
                    {{ $user->failed_login_attempts }}
                </span>
            </div>
            <div class="flex justify-between">
                <span style="color:#64748b;">Type</span>
                <span style="color:#374151;">{{ $user->is_builtin ? '🔒 Built-in' : '👤 Manual' }}</span>
            </div>
        </div>

        @if(!$user->is_builtin && $user->id !== auth()->id())
        <div class="pt-3" style="border-top:1px solid #e2e8f0;">
            <form method="POST" action="{{ route('settings.accounts.destroy', $user) }}"
                  onsubmit="return confirm('ลบ account {{ $user->name }}? ไม่สามารถเลิกทำได้')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full text-sm py-2 px-3 rounded-lg border transition"
                        style="border-color:#fca5a5;color:#c0272d;"
                        onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='transparent'">
                    🗑 ลบ Account นี้
                </button>
            </form>
        </div>
        @endif

        @if($user->is_builtin)
        <div class="text-xs p-2 rounded-lg" style="background:#fef3c7;color:#92400e;">
            Built-in account ไม่สามารถลบได้<br>ให้เปลี่ยนสถานะเป็น Inactive แทน
        </div>
        @endif
    </div>
</div>

</div>
@endsection
