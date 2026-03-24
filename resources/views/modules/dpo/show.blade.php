@extends('layouts.app')
@section('title',$dpo->title.' — PDPA Studio')
@section('page-title','DPO Task')

@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15572e;">✅ {{ session('success') }}</div>
@endif

<div class="flex items-center gap-3 mb-5">
    <a href="{{ route('dpo.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium" style="color:#15572e;">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        รายการงาน
    </a>
    <span style="color:#e2e8f0;">/</span>
    <span class="text-sm" style="color:#64748b;">{{ Str::limit($dpo->title,40) }}</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Detail --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="card overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 flex items-start gap-4"
                 style="background:linear-gradient(135deg,{{ \App\Models\DpoTask::categoryColor($dpo->category) }}10,white);border-bottom:1px solid #f1f5f9;">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:{{ \App\Models\DpoTask::categoryColor($dpo->category) }}15;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color:{{ \App\Models\DpoTask::categoryColor($dpo->category) }};"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h2 class="text-base font-bold" style="color:#0f3020;">{{ $dpo->title }}</h2>
                        @if($dpo->isOverdue())
                        <span class="badge badge-red animate-pulse">เกินกำหนด!</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-lg"
                              style="background:{{ \App\Models\DpoTask::categoryColor($dpo->category) }}15;color:{{ \App\Models\DpoTask::categoryColor($dpo->category) }};">
                            {{ \App\Models\DpoTask::categoryLabel($dpo->category) }}
                        </span>
                        <span class="badge {{ \App\Models\DpoTask::priorityBadge($dpo->priority) }}">{{ \App\Models\DpoTask::priorityLabel($dpo->priority) }}</span>
                        <span class="badge {{ \App\Models\DpoTask::statusBadge($dpo->status) }}">{{ \App\Models\DpoTask::statusLabel($dpo->status) }}</span>
                    </div>
                </div>
            </div>
            {{-- Body --}}
            <div class="px-6 py-5">
                @if($dpo->description)
                <div class="mb-5">
                    <p class="text-xs font-semibold mb-1.5" style="color:#64748b;">รายละเอียด</p>
                    <p class="text-sm" style="color:#1e293b;line-height:1.8;">{{ $dpo->description }}</p>
                </div>
                @endif
                @if($dpo->notes)
                <div class="p-4 rounded-xl" style="background:#f8fafc;border:1px solid #f1f5f9;">
                    <p class="text-xs font-semibold mb-1.5" style="color:#64748b;">หมายเหตุ / สิ่งที่ต้องทำ</p>
                    <p class="text-sm whitespace-pre-wrap" style="color:#475569;line-height:1.7;">{{ $dpo->notes }}</p>
                </div>
                @endif
                @if($dpo->status === 'completed' && $dpo->completed_at)
                <div class="mt-4 px-4 py-3 rounded-xl flex items-center gap-2" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" style="color:#15572e;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-medium" style="color:#15572e;">เสร็จสิ้นเมื่อ {{ $dpo->completed_at->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Sidebar --}}
    <div class="space-y-5">
        {{-- Quick Status Update --}}
        <div class="card p-5">
            <p class="text-xs font-bold mb-3" style="color:#64748b;text-transform:uppercase;letter-spacing:.06em;">อัปเดตสถานะ</p>
            <form method="POST" action="{{ route('dpo.status', $dpo) }}" class="space-y-2.5">
                @csrf @method('PATCH')
                <select name="status" class="form-input w-full">
                    <option value="pending"     {{ $dpo->status==='pending'    ?'selected':'' }}>รอดำเนินการ</option>
                    <option value="in_progress" {{ $dpo->status==='in_progress'?'selected':'' }}>กำลังดำเนินการ</option>
                    <option value="completed"   {{ $dpo->status==='completed'  ?'selected':'' }}>✅ เสร็จสิ้น</option>
                    <option value="cancelled"   {{ $dpo->status==='cancelled'  ?'selected':'' }}>ยกเลิก</option>
                </select>
                <button type="submit" class="btn-primary w-full justify-center">บันทึกสถานะ</button>
            </form>
        </div>

        {{-- Meta --}}
        <div class="card p-5">
            <p class="text-xs font-bold mb-4" style="color:#64748b;text-transform:uppercase;letter-spacing:.06em;">ข้อมูล</p>
            <dl class="space-y-3 text-xs">
                <div class="flex justify-between"><dt style="color:#94a3b8;">ความสำคัญ</dt>
                    <dd><span class="badge {{ \App\Models\DpoTask::priorityBadge($dpo->priority) }}">{{ \App\Models\DpoTask::priorityLabel($dpo->priority) }}</span></dd></div>
                @if($dpo->due_date)
                <div class="flex justify-between"><dt style="color:#94a3b8;">ครบกำหนด</dt>
                    <dd style="color:{{ $dpo->isOverdue()?'#c0272d':'#475569' }};font-weight:{{ $dpo->isOverdue()?'600':'normal' }};">
                        {{ $dpo->due_date->format('d/m/Y') }}
                        @if($dpo->isOverdue())<span class="block" style="color:#c0272d;font-size:10px;">เกินกำหนด {{ abs($dpo->getDaysUntilDue()) }} วัน</span>@endif
                    </dd></div>
                @endif
                <div class="flex justify-between"><dt style="color:#94a3b8;">ผู้รับผิดชอบ</dt>
                    <dd style="color:#475569;">{{ $dpo->assignedTo?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt style="color:#94a3b8;">สร้างโดย</dt>
                    <dd style="color:#475569;">{{ $dpo->createdBy?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt style="color:#94a3b8;">สร้างเมื่อ</dt>
                    <dd style="color:#475569;">{{ $dpo->created_at->format('d/m/Y') }}</dd></div>
            </dl>
        </div>

        {{-- Actions --}}
        <div class="card p-4 space-y-2.5">
            <a href="{{ route('dpo.edit', $dpo) }}" class="btn-outline w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                แก้ไขงาน
            </a>
            <form method="POST" action="{{ route('dpo.destroy', $dpo) }}"
                  onsubmit="return confirm('ยืนยันการลบงานนี้?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full text-xs font-medium py-2 rounded-lg transition"
                        style="color:#c0272d;border:1px solid #fecaca;background:#fff5f5;">🗑 ลบงาน</button>
            </form>
        </div>
    </div>
</div>

@endsection
