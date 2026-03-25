@extends('layouts.app')

@section('title', 'Data Breach — PDPA Studio')
@section('page-title', 'Data Breach Management')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">เหตุการณ์ที่เปิดอยู่</p>
        <p class="text-3xl font-extrabold" style="color:{{ $openCount > 0 ? '#c0272d' : '#64748b' }};">{{ number_format($openCount) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">ระดับ Critical</p>
        <p class="text-3xl font-extrabold" style="color:{{ $criticalCount > 0 ? '#991b1b' : '#64748b' }};">{{ number_format($criticalCount) }}</p>
        @if($criticalCount > 0)<p class="text-xs mt-1 font-medium" style="color:#c0272d;">ต้องดำเนินการด่วน!</p>@endif
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">แก้ไขแล้ว</p>
        <p class="text-3xl font-extrabold" style="color:#15572e;">{{ number_format($resolvedCount) }}</p>
    </div>
</div>

{{-- Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-2">
    <div class="flex items-center gap-3">
        <p class="text-sm font-semibold" style="color:#475569;">เหตุการณ์ทั้งหมด ({{ $breaches->total() }})</p>
        <form method="GET" class="flex items-center gap-2">
            <select name="per_page" class="form-input" style="width:auto;" onchange="this.form.submit()">
                <option value="50"  {{ request('per_page','50')  == '50'  ? 'selected' : '' }}>แสดง 50</option>
                <option value="100" {{ request('per_page')       == '100' ? 'selected' : '' }}>แสดง 100</option>
                <option value="200" {{ request('per_page')       == '200' ? 'selected' : '' }}>แสดง 200</option>
            </select>
        </form>
    </div>
    <a href="{{ route('breach.create') }}" class="btn-danger">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        รายงาน Breach ใหม่
    </a>
</div>
{{-- Showing results --}}
<p class="text-xs mb-3" style="color:#94a3b8;">
    แสดง {{ $breaches->firstItem() }}–{{ $breaches->lastItem() }} จากทั้งหมด <strong style="color:#475569;">{{ number_format($breaches->total()) }}</strong> รายการ
</p>

{{-- Table --}}
<div class="card overflow-hidden">
    <table class="w-full data-table">
        <thead>
            <tr>
                <th class="text-left">เลขที่</th>
                <th class="text-left">เหตุการณ์</th>
                <th class="text-left hidden md:table-cell">ระดับ</th>
                <th class="text-left">สถานะ</th>
                <th class="text-left hidden lg:table-cell">Deadline แจ้ง PDPC</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($breaches as $breach)
            @php
                $severityColors = ['critical' => 'badge-red', 'high' => 'badge-yellow', 'medium' => 'badge-blue', 'low' => 'badge-gray'];
                $severityLabels = ['critical' => 'Critical', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];
                $statusColors   = ['open' => 'badge-red', 'investigating' => 'badge-yellow', 'notified' => 'badge-blue', 'resolved' => 'badge-green', 'closed' => 'badge-gray'];
                $statusLabels   = ['open' => 'เปิด', 'investigating' => 'กำลังสอบสวน', 'notified' => 'แจ้งแล้ว', 'resolved' => 'แก้ไขแล้ว', 'closed' => 'ปิด'];
                $urgent = $breach->pdpc_notification_deadline && $breach->pdpc_notification_deadline->isFuture() && $breach->hours_until_deadline < 24 && !in_array($breach->status, ['resolved','closed','notified']);
            @endphp
            <tr style="{{ $breach->severity === 'critical' && !in_array($breach->status, ['resolved','closed']) ? 'background:#fff5f5;' : '' }}">
                <td>
                    <span class="font-mono text-xs" style="color:#475569;">{{ $breach->incident_number }}</span>
                    @if($urgent)<span class="badge badge-red ml-1 animate-pulse">ด่วน!</span>@endif
                </td>
                <td>
                    <p class="font-semibold text-sm truncate max-w-xs" style="color:#1e293b;">{{ $breach->title }}</p>
                    <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $breach->breach_type }} · พบเมื่อ {{ $breach->discovered_at->format('d/m/Y') }}</p>
                </td>
                <td class="hidden md:table-cell">
                    <span class="badge {{ $severityColors[$breach->severity] ?? 'badge-gray' }}">{{ $severityLabels[$breach->severity] ?? $breach->severity }}</span>
                </td>
                <td>
                    <span class="badge {{ $statusColors[$breach->status] ?? 'badge-gray' }}">{{ $statusLabels[$breach->status] ?? $breach->status }}</span>
                </td>
                <td class="hidden lg:table-cell text-xs">
                    @if($breach->pdpc_notification_deadline && !in_array($breach->status, ['notified','resolved','closed']))
                        @if($breach->pdpc_notification_deadline->isPast())
                            <span style="color:#c0272d; font-weight:600;">เกินกำหนดแล้ว!</span>
                        @else
                            <span style="color:{{ $breach->hours_until_deadline < 12 ? '#c0272d' : ($breach->hours_until_deadline < 24 ? '#b45309' : '#475569') }}; font-weight:{{ $breach->hours_until_deadline < 24 ? '600' : 'normal' }};">
                                {{ $breach->hours_until_deadline }}h ({{ $breach->pdpc_notification_deadline->format('d/m/Y H:i') }})
                            </span>
                        @endif
                    @elseif($breach->pdpc_notified_at)
                        <span style="color:#15572e;">แจ้งแล้ว {{ $breach->pdpc_notified_at->format('d/m/Y') }}</span>
                    @else
                        <span style="color:#94a3b8;">—</span>
                    @endif
                </td>
                <td class="text-right">
                    <a href="{{ route('breach.show', $breach) }}" style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:20px;background:#f0fdf4;color:#15572e;border:1px solid #bbf7d0;font-size:12px;font-weight:500;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg>ดู</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-12 text-sm" style="color:#94a3b8;">ไม่มีเหตุการณ์ Data Breach</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($breaches->hasPages())
    <div class="px-5 py-3" style="border-top:1px solid #f1f5f9;">{{ $breaches->links() }}</div>
    @endif
</div>

@endsection
