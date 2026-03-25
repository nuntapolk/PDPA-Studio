@extends('layouts.app')
@section('title','DPO Tasks — PDPA Studio')
@section('page-title','DPO Tasks')

@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15572e;">✅ {{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">งานทั้งหมด</p>
        <p class="text-3xl font-extrabold" style="color:#0f3020;">{{ $totalCount }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">เกินกำหนด</p>
        <p class="text-3xl font-extrabold" style="color:{{ $overdueCount>0?'#c0272d':'#64748b' }};">{{ $overdueCount }}</p>
        @if($overdueCount>0)<p class="text-xs font-medium mt-0.5" style="color:#c0272d;">ต้องดำเนินการด่วน!</p>@endif
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">กำลังดำเนินการ</p>
        <p class="text-3xl font-extrabold" style="color:#0369a1;">{{ $inProgCount }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">เสร็จสิ้น</p>
        <p class="text-3xl font-extrabold" style="color:#15572e;">{{ $completedCount }}</p>
    </div>
    {{-- Compliance Score --}}
    <div class="card p-4 flex flex-col items-center justify-center" style="border:2px solid {{ $complianceScore>=80?'#15572e':($complianceScore>=50?'#b45309':'#c0272d') }};">
        <p class="text-xs font-semibold mb-2" style="color:#64748b;">Compliance Score</p>
        <div class="relative w-16 h-16">
            <svg viewBox="0 0 36 36" class="w-16 h-16 -rotate-90">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f1f5f9" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none"
                    stroke="{{ $complianceScore>=80?'#15572e':($complianceScore>=50?'#b45309':'#c0272d') }}"
                    stroke-width="3"
                    stroke-dasharray="{{ $complianceScore }} {{ 100-$complianceScore }}"
                    stroke-linecap="round"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-sm font-extrabold" style="color:#0f3020;">{{ $complianceScore }}%</span>
            </div>
        </div>
        <p class="text-xs mt-1" style="color:#94a3b8;">{{ $clDone }}/{{ $clTotal }}</p>
        <a href="{{ route('dpo.checklist') }}" style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:20px;background:#f0fdf4;color:#15572e;border:1px solid #bbf7d0;font-size:12px;font-weight:500;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>Checklist</a>
    </div>
</div>

{{-- Due this week banner --}}
@if($dueThisWeek > 0)
<div class="mb-4 px-4 py-3 rounded-xl flex items-center gap-2 text-sm" style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span>มี <strong>{{ $dueThisWeek }} งาน</strong> ครบกำหนดภายใน 7 วัน</span>
</div>
@endif

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-2">
    <form method="GET" class="flex items-center gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหา..." class="form-input" style="width:170px;">
        <select name="category" class="form-input" style="width:auto;">
            <option value="">ทุกหมวด</option>
            @foreach(['compliance_review'=>'ทบทวนความสอดคล้อง','policy_update'=>'อัปเดตนโยบาย','training'=>'จัดอบรม','audit'=>'ตรวจสอบ','vendor_review'=>'ทบทวน Vendor','incident_response'=>'ตอบสนองเหตุการณ์','reporting'=>'รายงาน','other'=>'อื่นๆ'] as $v=>$l)
            <option value="{{ $v }}" {{ request('category')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <select name="priority" class="form-input" style="width:auto;">
            <option value="">ทุกความสำคัญ</option>
            <option value="urgent" {{ request('priority')==='urgent'?'selected':'' }}>🔴 เร่งด่วน</option>
            <option value="high"   {{ request('priority')==='high'  ?'selected':'' }}>🟠 สูง</option>
            <option value="medium" {{ request('priority')==='medium'?'selected':'' }}>🟡 ปานกลาง</option>
            <option value="low"    {{ request('priority')==='low'   ?'selected':'' }}>🟢 ต่ำ</option>
        </select>
        <select name="status" class="form-input" style="width:auto;">
            <option value="">ทุกสถานะ</option>
            <option value="pending"     {{ request('status')==='pending'    ?'selected':'' }}>รอดำเนินการ</option>
            <option value="in_progress" {{ request('status')==='in_progress'?'selected':'' }}>กำลังดำเนินการ</option>
            <option value="completed"   {{ request('status')==='completed'  ?'selected':'' }}>เสร็จสิ้น</option>
            <option value="cancelled"   {{ request('status')==='cancelled'  ?'selected':'' }}>ยกเลิก</option>
        </select>
        <select name="per_page" class="form-input" style="width:auto;" onchange="this.form.submit()">
            <option value="50"  {{ request('per_page','50') =='50' ?'selected':'' }}>แสดง 50</option>
            <option value="100" {{ request('per_page')=='100'?'selected':'' }}>แสดง 100</option>
            <option value="200" {{ request('per_page')=='200'?'selected':'' }}>แสดง 200</option>
        </select>
        <button type="submit" class="btn-outline">ค้นหา</button>
        @if(request()->hasAny(['search','category','priority','status']))
        <a href="{{ route('dpo.index') }}" class="text-sm" style="color:#94a3b8;">ล้าง</a>
        @endif
    </form>
    <div class="flex items-center gap-2">
        <a href="{{ route('dpo.checklist') }}" class="btn-outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Compliance Checklist
        </a>
        <a href="{{ route('dpo.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            สร้างงานใหม่
        </a>
    </div>
</div>

<p class="text-xs mb-2" style="color:#94a3b8;">
    แสดง {{ $tasks->firstItem() }}–{{ $tasks->lastItem() }} จากทั้งหมด <strong style="color:#475569;">{{ number_format($tasks->total()) }}</strong> รายการ
</p>

{{-- Task Table --}}
<div class="card overflow-hidden">
    <table class="w-full data-table">
        <thead>
            <tr>
                <th class="text-left">งาน</th>
                <th class="text-left hidden md:table-cell">หมวด</th>
                <th class="text-center">ความสำคัญ</th>
                <th class="text-left">สถานะ</th>
                <th class="text-left hidden lg:table-cell">ครบกำหนด</th>
                <th class="text-left hidden xl:table-cell">ผู้รับผิดชอบ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            @php $overdue = $task->isOverdue(); @endphp
            <tr style="{{ $overdue ? 'background:#fff5f5;' : '' }}">
                <td>
                    <div class="flex items-start gap-2">
                        @if($overdue)
                        <span class="mt-0.5 w-2 h-2 rounded-full flex-shrink-0 animate-pulse" style="background:#c0272d; margin-top:6px;"></span>
                        @elseif($task->priority === 'urgent')
                        <span class="mt-0.5 w-2 h-2 rounded-full flex-shrink-0" style="background:#f97316; margin-top:6px;"></span>
                        @else
                        <span class="mt-0.5 w-2 h-2 rounded-full flex-shrink-0" style="background:#e2e8f0; margin-top:6px;"></span>
                        @endif
                        <div>
                            <p class="font-semibold text-sm" style="color:#1e293b;">{{ $task->title }}</p>
                            @if($task->description)
                            <p class="text-xs mt-0.5 line-clamp-1" style="color:#94a3b8;">{{ Str::limit($task->description,60) }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="hidden md:table-cell">
                    <span class="text-xs font-medium px-2 py-1 rounded-lg"
                          style="background:{{ \App\Models\DpoTask::categoryColor($task->category) }}15; color:{{ \App\Models\DpoTask::categoryColor($task->category) }};">
                        {{ \App\Models\DpoTask::categoryLabel($task->category) }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge {{ \App\Models\DpoTask::priorityBadge($task->priority) }}">{{ \App\Models\DpoTask::priorityLabel($task->priority) }}</span>
                </td>
                <td>
                    <form method="POST" action="{{ route('dpo.status', $task) }}">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()"
                                class="text-xs font-medium rounded-lg px-2 py-1 border-0 outline-none cursor-pointer"
                                style="background:{{ $task->status==='completed'?'#f0fdf4':($task->status==='in_progress'?'#eff6ff':($task->status==='cancelled'?'#f8fafc':'#fffbeb')) }};
                                       color:{{ $task->status==='completed'?'#15572e':($task->status==='in_progress'?'#0369a1':($task->status==='cancelled'?'#64748b':'#92400e')) }};">
                            <option value="pending"     {{ $task->status==='pending'    ?'selected':'' }}>รอดำเนินการ</option>
                            <option value="in_progress" {{ $task->status==='in_progress'?'selected':'' }}>กำลังดำเนินการ</option>
                            <option value="completed"   {{ $task->status==='completed'  ?'selected':'' }}>เสร็จสิ้น</option>
                            <option value="cancelled"   {{ $task->status==='cancelled'  ?'selected':'' }}>ยกเลิก</option>
                        </select>
                    </form>
                </td>
                <td class="hidden lg:table-cell text-xs">
                    @if($task->due_date)
                        @php $days = $task->getDaysUntilDue(); @endphp
                        <span style="color:{{ $overdue?'#c0272d':($days<=3?'#b45309':'#475569') }};font-weight:{{ $overdue||$days<=3?'600':'normal' }};">
                            {{ $task->due_date->format('d/m/Y') }}
                        </span>
                        @if(!in_array($task->status,['completed','cancelled']))
                        <span class="block" style="color:{{ $overdue?'#c0272d':($days<=7?'#b45309':'#94a3b8') }}; font-size:10px;">
                            {{ $overdue ? 'เกิน '.abs($days).' วัน' : ($days===0?'วันนี้!':'อีก '.$days.' วัน') }}
                        </span>
                        @endif
                    @else
                        <span style="color:#94a3b8;">—</span>
                    @endif
                </td>
                <td class="hidden xl:table-cell text-xs" style="color:#475569;">
                    {{ $task->assignedTo?->name ?? '—' }}
                </td>
                <td class="text-right">
                    <a href="{{ route('dpo.show', $task) }}" style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:20px;background:#f0fdf4;color:#15572e;border:1px solid #bbf7d0;font-size:12px;font-weight:500;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-16">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:#f1f5f9;">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#cbd5e1;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <p class="text-sm font-medium" style="color:#64748b;">ยังไม่มีงาน DPO</p>
                        <a href="{{ route('dpo.create') }}" class="btn-primary" style="font-size:13px;">สร้างงานแรก</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($tasks->hasPages())
    <div class="px-5 py-3" style="border-top:1px solid #f1f5f9;">{{ $tasks->links() }}</div>
    @endif
</div>

@endsection
