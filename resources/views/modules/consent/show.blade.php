@extends('layouts.app')

@section('title', $template->name . ' — PDPA Studio')
@section('page-title', 'Consent Template')

@section('content')
<div class="mb-4">
    <a href="{{ route('consent.index') }}" class="text-sm font-medium" style="color:#15572e;">← กลับ</a>
</div>

{{-- Template info --}}
<div class="card p-6 mb-6">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-lg font-bold" style="color:#0f3020;">{{ $template->name }}</h2>
            <p class="text-sm mt-1" style="color:#64748b;">{{ $template->purpose }}</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <span class="badge badge-blue">{{ $template->category }}</span>
            <span class="badge badge-gray">v{{ $template->version }}</span>
            @if($template->is_active)
            <span class="badge badge-green">Active</span>
            @else
            <span class="badge badge-gray">Inactive</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5" style="border-top:1px solid #e8f0eb;">
        <div>
            <p class="text-xs mb-1" style="color:#94a3b8;">ฐานกฎหมาย</p>
            <p class="text-sm font-medium" style="color:#374151;">{{ $template->getLegalBasisLabel() }}</p>
        </div>
        <div>
            <p class="text-xs mb-1" style="color:#94a3b8;">บังคับ</p>
            <p class="text-sm font-medium" style="color:#374151;">{{ $template->is_required ? 'บังคับ' : 'ไม่บังคับ' }}</p>
        </div>
        <div>
            <p class="text-xs mb-1" style="color:#94a3b8;">อายุ</p>
            <p class="text-sm font-medium" style="color:#374151;">{{ $template->validity_days ? $template->validity_days . ' วัน' : 'ไม่มีวันหมดอายุ' }}</p>
        </div>
        <div>
            <p class="text-xs mb-1" style="color:#94a3b8;">สร้างเมื่อ</p>
            <p class="text-sm font-medium" style="color:#374151;">{{ $template->created_at->format('d/m/Y') }}</p>
        </div>
    </div>
</div>

{{-- Consent records --}}
<div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold" style="color:#475569;">รายการความยินยอม ({{ $consents->total() }})</h3>
</div>

<div class="card overflow-hidden">
    <table class="w-full data-table">
        <thead>
            <tr>
                <th class="text-left">เจ้าของข้อมูล</th>
                <th class="text-left hidden sm:table-cell">สถานะ</th>
                <th class="text-left hidden md:table-cell">ช่องทาง</th>
                <th class="text-left hidden lg:table-cell">วันที่ยินยอม</th>
                <th class="text-left hidden lg:table-cell">หมดอายุ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($consents as $consent)
            <tr>
                <td>
                    @if($consent->dataSubject)
                    <p class="font-semibold text-sm" style="color:#1e293b;">{{ $consent->dataSubject->full_name }}</p>
                    <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $consent->dataSubject->email }}</p>
                    @else
                    <p class="text-xs" style="color:#94a3b8;">—</p>
                    @endif
                </td>
                <td class="hidden sm:table-cell">
                    @php
                        $consentStatus = $consent->withdrawn_at ? 'withdrawn' : ($consent->expires_at && $consent->expires_at->isPast() ? 'expired' : ($consent->granted ? 'active' : 'declined'));
                        $statusMap = ['active' => 'badge-green', 'withdrawn' => 'badge-red', 'expired' => 'badge-gray', 'declined' => 'badge-yellow'];
                        $labelMap  = ['active' => 'Active', 'withdrawn' => 'ถอนแล้ว', 'expired' => 'หมดอายุ', 'declined' => 'ไม่ยินยอม'];
                    @endphp
                    <span class="badge {{ $statusMap[$consentStatus] ?? 'badge-gray' }}">{{ $labelMap[$consentStatus] ?? $consentStatus }}</span>
                </td>
                <td class="hidden md:table-cell text-sm" style="color:#64748b;">{{ $consent->getChannelLabel() }}</td>
                <td class="hidden lg:table-cell text-xs" style="color:#64748b;">{{ $consent->granted_at ? $consent->granted_at->format('d/m/Y') : $consent->created_at->format('d/m/Y') }}</td>
                <td class="hidden lg:table-cell text-xs">
                    @if($consent->expires_at)
                        <span style="color:{{ $consent->expires_at->isPast() ? '#c0272d' : '#64748b' }};">
                            {{ $consent->expires_at->format('d/m/Y') }}
                        </span>
                    @else
                        <span style="color:#94a3b8;">ไม่มีวันหมดอายุ</span>
                    @endif
                </td>
                <td class="text-right">
                    @if($consent->granted && !$consent->withdrawn_at)
                    <form action="{{ route('consent.withdraw', $consent) }}" method="POST" onsubmit="return confirm('ยืนยันถอนความยินยอม?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs font-semibold" style="color:#c0272d;">ถอน</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-12 text-sm" style="color:#94a3b8;">ยังไม่มีรายการความยินยอม</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($consents->hasPages())
    <div class="px-5 py-3" style="border-top:1px solid #f1f5f9;">{{ $consents->links() }}</div>
    @endif
</div>
@endsection
