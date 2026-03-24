@extends('layouts.app')
@section('title', 'Account Setup')
@section('content')

{{-- Header ────────────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold" style="color:#15572e;">⚙️ Account Setup</h1>
        <p class="text-sm mt-1" style="color:#64748b;">จัดการบัญชีผู้ใช้ทั้งหมดในระบบ</p>
    </div>
    <a href="{{ route('settings.accounts.create') }}" class="btn-primary">+ สร้าง Account ใหม่</a>
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

{{-- Stats ──────────────────────────────────────────────────────────────── --}}
<div class="grid gap-3 mb-6" style="grid-template-columns:repeat(auto-fit,minmax(120px,1fr));">
    <div class="card text-center py-4">
        <div class="text-3xl font-black" style="color:#15572e;">{{ $stats['total'] }}</div>
        <div class="text-xs mt-1" style="color:#64748b;">👤 ทั้งหมด</div>
    </div>
    @foreach($roles as $roleKey => $roleDef)
    <div class="card text-center py-4">
        <div class="text-2xl font-black" style="color:{{ $roleDef['color'] }};">{{ $stats[$roleKey] ?? 0 }}</div>
        <div class="text-xs mt-1" style="color:#64748b;">{{ $roleDef['icon'] }} {{ $roleDef['label'] }}</div>
    </div>
    @endforeach
    <div class="card text-center py-4">
        <div class="text-2xl font-black" style="color:#c0272d;">{{ $stats['inactive'] + $stats['locked'] }}</div>
        <div class="text-xs mt-1" style="color:#64748b;">🔴 ปิด/ล็อก</div>
    </div>
</div>

{{-- Filter ──────────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <form method="GET" action="{{ route('settings.accounts.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Role</label>
            <select name="role" class="form-input" style="width:160px;" onchange="this.form.submit()">
                <option value="">ทั้งหมด</option>
                @foreach($roles as $roleKey => $roleDef)
                    <option value="{{ $roleKey }}" {{ request('role')===$roleKey?'selected':'' }}>
                        {{ $roleDef['icon'] }} {{ $roleDef['label'] }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">สถานะ</label>
            <select name="status" class="form-input" style="width:130px;" onchange="this.form.submit()">
                <option value="">ทั้งหมด</option>
                <option value="active"   {{ request('status')==='active'  ?'selected':'' }}>Active</option>
                <option value="inactive" {{ request('status')==='inactive'?'selected':'' }}>Inactive</option>
                <option value="locked"   {{ request('status')==='locked'  ?'selected':'' }}>Locked</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ค้นหา</label>
            <input type="text" name="search" class="form-input" style="width:200px;"
                   placeholder="ชื่อ หรือ อีเมล..." value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn-primary">🔍 ค้นหา</button>
        <a href="{{ route('settings.accounts.index') }}" class="btn-outline">รีเซ็ต</a>
    </form>
</div>

{{-- Account Table ───────────────────────────────────────────────────────── --}}
<div class="card p-0">
    <table class="w-full text-sm">
        <thead>
            <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                <th class="text-left px-5 py-3 font-semibold text-xs uppercase tracking-wide" style="color:#64748b;">ชื่อ / อีเมล</th>
                <th class="text-left px-5 py-3 font-semibold text-xs uppercase tracking-wide" style="color:#64748b;">Role</th>
                <th class="text-left px-5 py-3 font-semibold text-xs uppercase tracking-wide" style="color:#64748b;">สถานะ</th>
                <th class="text-left px-5 py-3 font-semibold text-xs uppercase tracking-wide" style="color:#64748b;">Login ล่าสุด</th>
                <th class="text-left px-5 py-3 font-semibold text-xs uppercase tracking-wide" style="color:#64748b;">Type</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            @php
            $statusColors = [
                'active'   => ['#dcfce7','#15572e','Active'],
                'inactive' => ['#f1f5f9','#64748b','Inactive'],
                'locked'   => ['#fee2e2','#c0272d','Locked'],
            ];
            [$sbg,$sc,$sl] = $statusColors[$user->status] ?? ['#f1f5f9','#64748b',$user->status];
            $isSelf = $user->id === auth()->id();
            @endphp
            <tr style="border-bottom:1px solid #f1f5f9;" class="{{ $isSelf ? 'bg-green-50' : 'hover:bg-gray-50' }}">
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0"
                             style="background:{{ $user->getRoleBg() }};color:{{ $user->getRoleColor() }};">
                            {{ strtoupper(mb_substr($user->name,0,1)) }}
                        </div>
                        <div>
                            <div class="font-semibold" style="color:#1e293b;">
                                {{ $user->name }}
                                @if($isSelf)
                                    <span class="text-xs ml-1 px-1.5 py-0.5 rounded" style="background:#dcfce7;color:#15572e;">คุณ</span>
                                @endif
                            </div>
                            <div class="text-xs font-mono" style="color:#94a3b8;">{{ $user->email }}</div>
                            @if($user->phone)
                            <div class="text-xs" style="color:#94a3b8;">{{ $user->phone }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                          style="background:{{ $user->getRoleBg() }};color:{{ $user->getRoleColor() }};">
                        {{ $user->getRoleIcon() }} {{ $user->getRoleLabel() }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold" style="background:{{ $sbg }};color:{{ $sc }};">
                        {{ $sl }}
                    </span>
                    @if($user->locked_until && $user->locked_until->isFuture())
                    <div class="text-xs mt-0.5" style="color:#c0272d;">
                        ถึง {{ $user->locked_until->format('d/m H:i') }}
                    </div>
                    @endif
                </td>
                <td class="px-5 py-3 text-xs" style="color:#64748b;">
                    @if($user->last_login_at)
                        {{ $user->last_login_at->diffForHumans() }}
                        <div style="color:#94a3b8;">{{ $user->last_login_ip ?? '' }}</div>
                    @else
                        <span style="color:#cbd5e1;">ยังไม่เคย</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    @if($user->is_builtin)
                    <span class="text-xs px-2 py-0.5 rounded-full" style="background:#fef3c7;color:#92400e;" title="Built-in account จาก config">🔒 Built-in</span>
                    @else
                    <span class="text-xs px-2 py-0.5 rounded-full" style="background:#f1f5f9;color:#64748b;">👤 Manual</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2 justify-end">
                        {{-- Toggle Status --}}
                        @if(!$isSelf)
                        <form method="POST" action="{{ route('settings.accounts.toggle', $user) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs px-2.5 py-1 rounded-lg border transition"
                                    style="border-color:{{ $user->status==='active'?'#fca5a5':'#86efac' }};color:{{ $user->status==='active'?'#c0272d':'#15572e' }};"
                                    title="{{ $user->status==='active'?'Deactivate':'Activate' }}">
                                {{ $user->status === 'active' ? '⏸ ปิด' : '▶ เปิด' }}
                            </button>
                        </form>
                        @endif

                        {{-- Edit --}}
                        <a href="{{ route('settings.accounts.edit', $user) }}"
                           class="text-xs px-2.5 py-1 rounded-lg border border-gray-200 text-gray-600 hover:border-green-400 hover:text-green-700 transition">
                            ✏️ แก้ไข
                        </a>

                        {{-- Delete (non-builtin, non-self) --}}
                        @if(!$user->is_builtin && !$isSelf)
                        <form method="POST" action="{{ route('settings.accounts.destroy', $user) }}"
                              onsubmit="return confirm('ลบ account {{ $user->name }}? ไม่สามารถเลิกทำได้')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs px-2.5 py-1 rounded-lg border transition"
                                    style="border-color:#fca5a5;color:#c0272d;"
                                    onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='transparent'">
                                🗑
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-5 py-16 text-center" style="color:#94a3b8;">ไม่พบ Account</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>

{{-- Password Policy Info ────────────────────────────────────────────────── --}}
<div class="mt-4 p-4 rounded-xl text-sm" style="background:#f8fafc;border:1px solid #e2e8f0;">
    <p class="font-semibold mb-1" style="color:#374151;">🔑 Default Accounts (จาก config/accounts.php)</p>
    <div class="grid grid-cols-2 gap-2 text-xs mt-2" style="color:#64748b;">
        <div><strong>admin@pdpa.local</strong> — Admin@2025! (เปลี่ยนหลัง deploy)</div>
        <div><strong>nuntapol@pdpa.local</strong> — Nuntapol@2025! (เปลี่ยนหลัง deploy)</div>
        <div><strong>editor/dpo/reviewer accounts</strong> — Pdpa@2025!</div>
        <div class="col-span-2 mt-1">⚙️ เปลี่ยน password เริ่มต้นได้ที่ <code class="px-1 rounded" style="background:#e2e8f0;">.env</code> → <code class="px-1 rounded" style="background:#e2e8f0;">SEED_PASSWORD_*</code></div>
    </div>
</div>

@endsection
