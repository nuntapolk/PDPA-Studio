@extends('layouts.app')

@section('title', 'Dashboard — PDPA Studio')
@section('page-title', 'Dashboard')

@section('content')

{{-- ═══ URGENT ALERTS ═══ --}}
@if($urgentBreaches->isNotEmpty())
<div class="space-y-2 mb-5">
    @foreach($urgentBreaches as $ub)
    <div class="alert-critical flex items-center gap-4">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(192,39,45,0.15);">
            <svg class="w-4.5 h-4.5" style="width:18px;height:18px;color:#c0272d;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold" style="color: #7f1d1d;">{{ $ub->incident_number }} — {{ $ub->title }}</p>
            <p class="text-xs mt-0.5" style="color: #b91c1c;">ต้องแจ้ง PDPC ภายใน <strong>{{ $ub->hours_until_deadline }} ชั่วโมง</strong> (deadline: {{ $ub->pdpc_notification_deadline->format('d/m/Y H:i') }})</p>
        </div>
        <a href="{{ route('breach.show', $ub) }}" class="flex-shrink-0 text-xs font-bold px-3 py-1.5 rounded-lg transition" style="background: #c0272d; color: white;">ดำเนินการ →</a>
    </div>
    @endforeach
</div>
@endif

@if($overdueRights > 0)
<div class="alert-warning flex items-center gap-4 mb-5">
    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background: rgba(180,83,9,0.12);">
        <svg class="w-4.5 h-4.5" style="width:18px;height:18px;color:#b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <p class="text-sm font-semibold flex-1" style="color: #78350f;">คำขอสิทธิ์เจ้าของข้อมูล <span style="color: #b45309;">{{ $overdueRights }} รายการ</span> เกินกำหนด 30 วัน</p>
    <a href="{{ route('rights.index') }}" class="flex-shrink-0 text-xs font-bold px-3 py-1.5 rounded-lg transition" style="background: #b45309; color: white;">All</a>
</div>
@endif

{{-- ═══ KPI CARDS ═══ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="card p-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full opacity-5 -mt-6 -mr-6" style="background: #15572e;"></div>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background: #f0fdf4;">
            <svg class="w-5 h-5" style="color: #15572e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color: #0f3020;">{{ number_format($activeConsents) }}</p>
        <p class="text-xs font-medium mt-1" style="color: #64748b;">Active Consent</p>
    </div>

    <div class="card p-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full opacity-5 -mt-6 -mr-6" style="background: #b45309;"></div>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background: #fffbeb;">
            <svg class="w-5 h-5" style="color: #b45309;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color: #0f3020;">{{ $pendingRights }}</p>
        <p class="text-xs font-medium mt-1" style="color: #64748b;">คำขอสิทธิ์รอดำเนินการ</p>
        @if($overdueRights > 0)
        <p class="text-xs font-bold mt-1.5" style="color: #c0272d;">⚠ เกินกำหนด {{ $overdueRights }} รายการ</p>
        @endif
    </div>

    <div class="card p-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full opacity-5 -mt-6 -mr-6" style="background: #c0272d;"></div>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background: #fff1f2;">
            <svg class="w-5 h-5" style="color: #c0272d;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color: {{ $openBreaches > 0 ? '#c0272d' : '#0f3020' }};">{{ $openBreaches }}</p>
        <p class="text-xs font-medium mt-1" style="color: #64748b;">Data Breach เปิดอยู่</p>
    </div>

    <div class="card p-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full opacity-5 -mt-6 -mr-6" style="background: #15572e;"></div>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style="background: {{ $complianceScore >= 80 ? '#f0fdf4' : ($complianceScore >= 60 ? '#fffbeb' : '#fff1f2') }};">
            <svg class="w-5 h-5" style="color: {{ $complianceScore >= 80 ? '#15572e' : ($complianceScore >= 60 ? '#b45309' : '#c0272d') }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color: #0f3020;">{{ $complianceScore }}<span class="text-lg">%</span></p>
        <p class="text-xs font-medium mt-1" style="color: #64748b;">Compliance Score</p>
        <div class="mt-2.5 rounded-full overflow-hidden" style="height: 4px; background: #e2e8f0;">
            <div class="h-full rounded-full transition-all" style="width: {{ $complianceScore }}%; background: {{ $complianceScore >= 80 ? 'linear-gradient(90deg, #15572e, #2a6b4d)' : ($complianceScore >= 60 ? 'linear-gradient(90deg, #b45309, #d97706)' : 'linear-gradient(90deg, #c0272d, #e53e3e)') }};"></div>
        </div>
    </div>
</div>

{{-- ═══ TWO COLUMNS ═══ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    <!-- Rights Requests -->
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #f1f5f9;">
            <h2 class="text-sm font-bold" style="color: #0f3020;">คำขอสิทธิ์ล่าสุด</h2>
            <a href="{{ route('rights.index') }}" class="text-xs font-semibold" style="color: #15572e;">All</a>
        </div>
        @forelse($recentRights as $rr)
        @php
            $statusMap = ['pending' => ['badge-yellow','รอดำเนินการ'], 'in_progress' => ['badge-blue','กำลังดำเนินการ'], 'in_review' => ['badge-blue','กำลัง Review'], 'completed' => ['badge-green','เสร็จสิ้น'], 'rejected' => ['badge-red','ปฏิเสธ'], 'cancelled' => ['badge-gray','ยกเลิก']];
            $typeMap = ['access'=>'ACCESS','rectification'=>'EDIT','erasure'=>'DELETE','restriction'=>'STOP','portability'=>'MOVE','objection'=>'OBJ','withdraw_consent'=>'WITH','complaint'=>'COMP'];
            $sm = $statusMap[$rr->status] ?? ['badge-gray', $rr->status];
            $isOverdue = $rr->isOverdue();
        @endphp
        <a href="{{ route('rights.show', $rr) }}" class="flex items-center gap-3 px-5 py-3.5 transition {{ $isOverdue ? '' : '' }}" style="{{ $isOverdue ? 'background: #fffbeb;' : '' }}; border-bottom: 1px solid #f8fafc;">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-xs font-bold" style="background: #f0f7f2; color: #15572e;">{{ $typeMap[$rr->type] ?? 'REQ' }}</div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate" style="color: #1e293b;">{{ $rr->requester_name }}</p>
                <p class="text-xs" style="color: #94a3b8;">{{ $rr->ticket_number }} · {{ $rr->created_at->diffForHumans() }}</p>
            </div>
            <span class="{{ $sm[0] }} text-xs">{{ $sm[1] }}</span>
        </a>
        @empty
        <div class="px-5 py-10 text-center text-sm" style="color: #94a3b8;">ยังไม่มีคำขอสิทธิ์</div>
        @endforelse
    </div>

    <!-- Breaches -->
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid #f1f5f9;">
            <h2 class="text-sm font-bold" style="color: #0f3020;">Data Breach ล่าสุด</h2>
            <a href="{{ route('breach.index') }}" class="text-xs font-semibold" style="color: #15572e;">All</a>
        </div>
        @forelse($recentBreaches as $breach)
        @php
            $sevColors = ['critical' => '#c0272d', 'high' => '#d97706', 'medium' => '#2563eb', 'low' => '#64748b'];
            $statusMap2 = ['open' => ['badge-red','เปิด'], 'investigating' => ['badge-yellow','สอบสวน'], 'notified' => ['badge-blue','แจ้งแล้ว'], 'resolved' => ['badge-green','แก้ไขแล้ว'], 'closed' => ['badge-gray','ปิด']];
            $sm2 = $statusMap2[$breach->status] ?? ['badge-gray', $breach->status];
        @endphp
        <a href="{{ route('breach.show', $breach) }}" class="flex items-center gap-3 px-5 py-3.5 transition" style="border-bottom: 1px solid #f8fafc;">
            <div class="w-2.5 h-2.5 rounded-full flex-shrink-0 mt-1" style="background: {{ $sevColors[$breach->severity] ?? '#94a3b8' }};"></div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate" style="color: #1e293b;">{{ $breach->title }}</p>
                <p class="text-xs" style="color: #94a3b8;">{{ $breach->incident_number }} · {{ $breach->created_at->diffForHumans() }}</p>
            </div>
            <span class="{{ $sm2[0] }} text-xs">{{ $sm2[1] }}</span>
        </a>
        @empty
        <div class="px-5 py-10 text-center text-sm" style="color: #94a3b8;">ไม่มีเหตุการณ์ Data Breach</div>
        @endforelse
    </div>
</div>

{{-- ═══ QUICK ACTIONS ═══ --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <a href="{{ route('consent.create') }}" class="card card-hover p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #15572e, #2a6b4d);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold" style="color: #0f3020;">สร้าง Consent Template</p>
            <p class="text-xs mt-0.5" style="color: #94a3b8;">เพิ่ม template ความยินยอมใหม่</p>
        </div>
    </a>
    <a href="{{ route('breach.create') }}" class="card card-hover p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #c0272d, #e53e3e);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold" style="color: #0f3020;">รายงาน Data Breach</p>
            <p class="text-xs mt-0.5" style="color: #94a3b8;">บันทึกเหตุการณ์ละเมิดข้อมูล</p>
        </div>
    </a>
    <a href="{{ route('ropa.create') }}" class="card card-hover p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #1e40af, #2563eb);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold" style="color: #0f3020;">เพิ่มกิจกรรม ROPA</p>
            <p class="text-xs mt-0.5" style="color: #94a3b8;">บันทึกกิจกรรมการประมวลผล</p>
        </div>
    </a>
</div>

@endsection
