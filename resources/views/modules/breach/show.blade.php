@extends('layouts.app')

@section('title', $breach->incident_number . ' — PDPA Studio')
@section('page-title', 'Data Breach Detail')

@section('content')
<div class="mb-4">
    <a href="{{ route('breach.index') }}" class="text-sm font-medium" style="color:#15572e;">← กลับรายการ</a>
</div>

@php
    $severityColors = ['critical' => 'badge-red', 'high' => 'badge-yellow', 'medium' => 'badge-blue', 'low' => 'badge-gray'];
    $severityLabels = ['critical' => 'Critical', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];
    $statusColors   = ['open' => 'badge-red', 'investigating' => 'badge-yellow', 'notified' => 'badge-blue', 'resolved' => 'badge-green', 'closed' => 'badge-gray'];
    $statusLabels   = ['open' => 'เปิด', 'investigating' => 'กำลังสอบสวน', 'notified' => 'แจ้ง PDPC แล้ว', 'resolved' => 'แก้ไขแล้ว', 'closed' => 'ปิด'];
@endphp

{{-- 72h deadline banner --}}
@if($breach->pdpc_notification_deadline && !$breach->pdpc_notified_at && !in_array($breach->status, ['resolved','closed']))
@php $hoursLeft = $breach->hours_until_deadline; @endphp
<div class="mb-6 rounded-xl px-5 py-4 flex items-center gap-4"
    style="background:{{ $breach->pdpc_notification_deadline->isPast() ? '#fff1f2' : ($hoursLeft < 12 ? '#fff5f5' : '#fffbeb') }}; border:1.5px solid {{ $breach->pdpc_notification_deadline->isPast() ? '#fca5a5' : ($hoursLeft < 12 ? '#fca5a5' : '#fcd34d') }};">
    <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" style="color:{{ $breach->pdpc_notification_deadline->isPast() ? '#c0272d' : '#b45309' }};">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
    </svg>
    <div class="flex-1">
        @if($breach->pdpc_notification_deadline->isPast())
        <p class="font-bold text-sm" style="color:#991b1b;">เกิน Deadline แจ้ง PDPC แล้ว!</p>
        <p class="text-xs mt-0.5" style="color:#b91c1c;">ต้องแจ้งภายใน {{ $breach->pdpc_notification_deadline->format('d/m/Y H:i') }} (ล่าช้าไปแล้ว {{ now()->diffForHumans($breach->pdpc_notification_deadline) }})</p>
        @else
        <p class="font-bold text-sm" style="color:#92400e;">ต้องแจ้ง PDPC ภายใน {{ $hoursLeft }} ชั่วโมง</p>
        <p class="text-xs mt-0.5" style="color:#b45309;">Deadline: {{ $breach->pdpc_notification_deadline->format('d/m/Y H:i') }}</p>
        @endif
    </div>
    <form action="{{ route('breach.notify-pdpc', $breach) }}" method="POST">
        @csrf
        <button type="submit" class="btn-danger" style="white-space:nowrap;">
            บันทึกการแจ้ง PDPC
        </button>
    </form>
</div>
@elseif($breach->pdpc_notified_at)
<div class="mb-6 rounded-xl px-5 py-3 flex items-center gap-3" style="background:#f0fdf4; border:1.5px solid #86efac;">
    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" style="color:#15572e;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    <p class="text-sm font-semibold" style="color:#15572e;">แจ้ง PDPC แล้วเมื่อ {{ $breach->pdpc_notified_at->format('d M Y H:i') }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main info --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold" style="color:#0f3020;">{{ $breach->title }}</h2>
                    <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $breach->incident_number }} · รายงานเมื่อ {{ $breach->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="badge {{ $severityColors[$breach->severity] ?? 'badge-gray' }}">{{ $severityLabels[$breach->severity] ?? $breach->severity }}</span>
                    <span class="badge {{ $statusColors[$breach->status] ?? 'badge-gray' }}">{{ $statusLabels[$breach->status] ?? $breach->status }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm mb-5">
                <div>
                    <p class="text-xs mb-1" style="color:#94a3b8;">ประเภท</p>
                    <p class="font-semibold" style="color:#374151;">{{ $breach->breach_type }}</p>
                </div>
                <div>
                    <p class="text-xs mb-1" style="color:#94a3b8;">วันที่พบ</p>
                    <p class="font-semibold" style="color:#374151;">{{ $breach->discovered_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs mb-1" style="color:#94a3b8;">ผู้ได้รับผลกระทบ</p>
                    <p class="font-semibold" style="color:#374151;">{{ $breach->affected_count ? number_format($breach->affected_count) . ' ราย' : 'ไม่ทราบ' }}</p>
                </div>
            </div>

            <div>
                <p class="text-xs mb-2" style="color:#94a3b8;">รายละเอียด</p>
                <p class="text-sm leading-relaxed" style="color:#374151;">{{ $breach->description }}</p>
            </div>

            @if($breach->data_types_affected)
            @php $dataTypes = is_array($breach->data_types_affected) ? $breach->data_types_affected : json_decode($breach->data_types_affected, true) ?? []; @endphp
            @if(!empty($dataTypes))
            <div class="mt-4 pt-4" style="border-top:1px solid #e8f0eb;">
                <p class="text-xs mb-2" style="color:#94a3b8;">ข้อมูลที่ได้รับผลกระทบ</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($dataTypes as $dt)
                    <span class="badge badge-red">{{ $dt }}</span>
                    @endforeach
                </div>
            </div>
            @endif
            @endif
        </div>

        {{-- Timeline --}}
        <div class="card">
            <div class="p-5" style="border-bottom:1px solid #e8f0eb;">
                <h3 class="text-sm font-semibold" style="color:#1e293b;">Timeline เหตุการณ์</h3>
            </div>
            <div class="p-5">
                @forelse($timelines as $tl)
                <div class="flex gap-3 mb-4 last:mb-0">
                    <div class="flex flex-col items-center">
                        <div class="w-2.5 h-2.5 rounded-full mt-1.5 flex-shrink-0" style="background:#15572e;"></div>
                        @if(!$loop->last)<div class="w-0.5 flex-1 mt-1" style="background:#e8f0eb;"></div>@endif
                    </div>
                    <div class="flex-1 pb-4">
                        <div class="flex items-center gap-2 mb-0.5">
                            <p class="text-sm font-semibold" style="color:#1e293b;">{{ $tl->action }}</p>
                            <span class="text-xs" style="color:#94a3b8;">{{ $tl->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($tl->description)
                        <p class="text-sm" style="color:#64748b;">{{ $tl->description }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-sm text-center py-4" style="color:#94a3b8;">ยังไม่มี Timeline</p>
                @endforelse
            </div>

            <div class="p-5" style="border-top:1px solid #e8f0eb;">
                <form action="{{ route('breach.timeline', $breach) }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="text" name="event" placeholder="เหตุการณ์ (เช่น ระงับเซิร์ฟเวอร์, แจ้งทีม IT)" required class="form-input">
                    <div class="flex gap-2">
                        <input type="text" name="description" placeholder="รายละเอียดเพิ่มเติม (ไม่บังคับ)" class="form-input flex-1">
                        <button type="submit" class="btn-primary" style="white-space:nowrap;">เพิ่ม</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="card p-5 text-sm space-y-2.5">
            <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#64748b;">สรุปเหตุการณ์</p>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">เลขที่</span>
                <span class="font-mono" style="color:#374151;">{{ $breach->incident_number }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">72h Deadline</span>
                @if($breach->pdpc_notification_deadline)
                <span style="color:{{ $breach->pdpc_notification_deadline->isPast() && !$breach->pdpc_notified_at ? '#c0272d' : '#374151' }}; font-weight:{{ $breach->pdpc_notification_deadline->isPast() && !$breach->pdpc_notified_at ? '600' : 'normal' }};">
                    {{ $breach->pdpc_notification_deadline->format('d/m/Y H:i') }}
                </span>
                @else
                <span style="color:#94a3b8;">—</span>
                @endif
            </div>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">แจ้ง PDPC</span>
                <span style="color:{{ $breach->pdpc_notified_at ? '#15572e' : '#94a3b8' }}; font-weight:{{ $breach->pdpc_notified_at ? '600' : 'normal' }};">
                    {{ $breach->pdpc_notified_at ? $breach->pdpc_notified_at->format('d/m/Y') : 'ยังไม่แจ้ง' }}
                </span>
            </div>
        </div>

        @if(!in_array($breach->status, ['resolved','closed']))
        <div class="card p-5">
            <h3 class="text-sm font-semibold mb-3" style="color:#1e293b;">ดำเนินการ</h3>
            @if(!$breach->pdpc_notified_at)
            <form action="{{ route('breach.notify-pdpc', $breach) }}" method="POST">
                @csrf
                <button type="submit" class="btn-danger w-full">
                    บันทึกการแจ้ง PDPC
                </button>
            </form>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
