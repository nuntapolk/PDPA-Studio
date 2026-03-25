@extends('layouts.app')

@section('title', 'สิทธิ์เจ้าของข้อมูล — PDPA Studio')
@section('page-title', 'สิทธิ์เจ้าของข้อมูล')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">รอดำเนินการ</p>
        <p class="text-3xl font-extrabold" style="color:#b45309;">{{ number_format($pendingCount) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">เกินกำหนด 30 วัน</p>
        <p class="text-3xl font-extrabold" style="color:{{ $overdueCount > 0 ? '#c0272d' : '#64748b' }};">{{ number_format($overdueCount) }}</p>
        @if($overdueCount > 0)<p class="text-xs mt-1 font-medium" style="color:#c0272d;">ต้องดำเนินการด่วน!</p>@endif
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">เสร็จสิ้นแล้ว</p>
        <p class="text-3xl font-extrabold" style="color:#15572e;">{{ number_format($resolvedCount) }}</p>
    </div>
</div>

{{-- Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-2">
    <p class="text-sm font-semibold" style="color:#475569;">คำขอทั้งหมด ({{ $requests->total() }})</p>
    <form method="GET" class="flex items-center gap-2">
        <select name="per_page" class="form-input" style="width:auto;" onchange="this.form.submit()">
            <option value="50"  {{ request('per_page','50')  == '50'  ? 'selected' : '' }}>แสดง 50</option>
            <option value="100" {{ request('per_page')       == '100' ? 'selected' : '' }}>แสดง 100</option>
            <option value="200" {{ request('per_page')       == '200' ? 'selected' : '' }}>แสดง 200</option>
        </select>
    </form>
</div>
{{-- Showing results --}}
<p class="text-xs mb-3" style="color:#94a3b8;">
    แสดง {{ $requests->firstItem() }}–{{ $requests->lastItem() }} จากทั้งหมด <strong style="color:#475569;">{{ number_format($requests->total()) }}</strong> รายการ
</p>

{{-- Table --}}
<div class="card overflow-hidden">
    <table class="w-full data-table">
        <thead>
            <tr>
                <th class="text-left">เลขที่</th>
                <th class="text-left">ผู้ยื่น</th>
                <th class="text-left hidden md:table-cell">ประเภทสิทธิ์</th>
                <th class="text-left">สถานะ</th>
                <th class="text-left hidden lg:table-cell">กำหนดเสร็จ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $rr)
            @php
                $isOverdue = $rr->isOverdue() && in_array($rr->status, ['pending','in_review','awaiting_info']);
                $statusColors = ['pending' => 'badge-yellow', 'in_review' => 'badge-blue', 'awaiting_info' => 'badge-yellow', 'approved' => 'badge-green', 'completed' => 'badge-green', 'rejected' => 'badge-red', 'withdrawn' => 'badge-gray'];
                $statusLabels = ['pending' => 'รอดำเนินการ', 'in_review' => 'กำลัง Review', 'awaiting_info' => 'รอข้อมูลเพิ่ม', 'approved' => 'อนุมัติ', 'completed' => 'เสร็จสิ้น', 'rejected' => 'ปฏิเสธ', 'withdrawn' => 'ถอนคำขอ'];
                $typeLabels = ['access' => 'ขอเข้าถึงข้อมูล', 'rectification' => 'ขอแก้ไข', 'erasure' => 'ขอลบข้อมูล', 'restriction' => 'ขอระงับ', 'portability' => 'ขอโอนย้าย', 'objection' => 'คัดค้านการใช้', 'withdraw_consent' => 'ถอนความยินยอม'];
            @endphp
            <tr style="{{ $isOverdue ? 'background:#fff5f5;' : '' }}">
                <td>
                    <span class="font-mono text-xs" style="color:#475569;">{{ $rr->ticket_number }}</span>
                    @if($isOverdue)
                    <span class="badge badge-red ml-1">เกินกำหนด</span>
                    @endif
                </td>
                <td>
                    <p class="font-semibold text-sm" style="color:#1e293b;">{{ $rr->requester_name }}</p>
                    <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $rr->requester_email }}</p>
                </td>
                <td class="hidden md:table-cell">
                    <span class="badge badge-blue">{{ $typeLabels[$rr->type] ?? $rr->type }}</span>
                </td>
                <td>
                    <span class="badge {{ $statusColors[$rr->status] ?? 'badge-gray' }}">{{ $statusLabels[$rr->status] ?? $rr->status }}</span>
                </td>
                <td class="hidden lg:table-cell text-xs">
                    @if($rr->due_date)
                        <span style="color:{{ $rr->due_date->isPast() ? '#c0272d' : '#475569' }}; font-weight:{{ $rr->due_date->isPast() ? '600' : 'normal' }};">
                            {{ $rr->due_date->format('d/m/Y') }}
                        </span>
                        @if(!$rr->due_date->isPast())
                        <span style="color:#94a3b8;"> ({{ $rr->due_date->diffForHumans() }})</span>
                        @endif
                    @else
                        <span style="color:#94a3b8;">—</span>
                    @endif
                </td>
                <td class="text-right">
                    <a href="{{ route('rights.show', $rr) }}" style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:20px;background:#f0fdf4;color:#15572e;border:1px solid #bbf7d0;font-size:12px;font-weight:500;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-12 text-sm" style="color:#94a3b8;">ยังไม่มีคำขอสิทธิ์</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($requests->hasPages())
    <div class="px-5 py-3" style="border-top:1px solid #f1f5f9;">{{ $requests->links() }}</div>
    @endif
</div>

@endsection
