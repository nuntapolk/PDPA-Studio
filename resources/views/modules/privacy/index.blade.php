@extends('layouts.app')
@section('title', 'Privacy Notice — PDPA Studio')
@section('page-title', 'Privacy Notice')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:linear-gradient(135deg,#e8f0eb,#d1e8d8);">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#15572e;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color:#0f3020;">{{ $totalCount }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">ทั้งหมด</p>
    </div>
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:#f0fdf4;">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color:#15572e;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color:#15572e;">{{ $publishedCount }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">เผยแพร่แล้ว</p>
    </div>
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:#f8fafc;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#94a3b8;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color:#64748b;">{{ $draftCount }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">ร่าง</p>
    </div>
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:#fffbeb;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#b45309;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color:{{ $expiringSoon > 0 ? '#b45309' : '#64748b' }};">{{ $expiringSoon }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">ใกล้หมดอายุ (30 วัน)</p>
        @if($expiringSoon > 0)<p class="text-xs font-medium mt-0.5" style="color:#b45309;">ต้องต่ออายุ!</p>@endif
    </div>
</div>

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-2">
    <form method="GET" class="flex items-center gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อ..." class="form-input" style="width:180px;">
        <select name="type" class="form-input" style="width:auto;">
            <option value="">ทุกประเภท</option>
            @foreach(['privacy_policy'=>'นโยบายความเป็นส่วนตัว','cookie_policy'=>'นโยบาย Cookie','employee_notice'=>'ประกาศพนักงาน','cctv_notice'=>'ประกาศ CCTV','marketing_notice'=>'ประกาศการตลาด','third_party_notice'=>'ประกาศบุคคลที่สาม'] as $val => $label)
            <option value="{{ $val }}" {{ request('type')===$val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="form-input" style="width:auto;">
            <option value="">ทุกสถานะ</option>
            <option value="published" {{ request('status')==='published' ? 'selected' : '' }}>เผยแพร่แล้ว</option>
            <option value="draft"     {{ request('status')==='draft'     ? 'selected' : '' }}>ร่าง</option>
            <option value="inactive"  {{ request('status')==='inactive'  ? 'selected' : '' }}>ปิดใช้งาน</option>
            <option value="expired"   {{ request('status')==='expired'   ? 'selected' : '' }}>หมดอายุ</option>
        </select>
        <select name="language" class="form-input" style="width:auto;">
            <option value="">ทุกภาษา</option>
            <option value="th" {{ request('language')==='th' ? 'selected' : '' }}>🇹🇭 ไทย</option>
            <option value="en" {{ request('language')==='en' ? 'selected' : '' }}>🇬🇧 English</option>
        </select>
        <select name="per_page" class="form-input" style="width:auto;" onchange="this.form.submit()">
            <option value="50"  {{ request('per_page','50')  == '50'  ? 'selected' : '' }}>แสดง 50</option>
            <option value="100" {{ request('per_page')       == '100' ? 'selected' : '' }}>แสดง 100</option>
            <option value="200" {{ request('per_page')       == '200' ? 'selected' : '' }}>แสดง 200</option>
        </select>
        <button type="submit" class="btn-outline">ค้นหา</button>
        @if(request()->hasAny(['search','type','status','language']))
        <a href="{{ route('privacy.index') }}" class="text-sm font-medium" style="color:#94a3b8;">ล้าง</a>
        @endif
    </form>
    <a href="{{ route('privacy.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        สร้างประกาศใหม่
    </a>
</div>

{{-- Showing --}}
<p class="text-xs mb-2" style="color:#94a3b8;">
    แสดง {{ $notices->firstItem() }}–{{ $notices->lastItem() }} จากทั้งหมด <strong style="color:#475569;">{{ number_format($notices->total()) }}</strong> รายการ
</p>

{{-- Table --}}
<div class="card overflow-hidden">
    <table class="w-full data-table">
        <thead>
            <tr>
                <th class="text-left">ประเภท / ชื่อ</th>
                <th class="text-left hidden md:table-cell">ภาษา</th>
                <th class="text-center hidden md:table-cell">เวอร์ชัน</th>
                <th class="text-left">สถานะ</th>
                <th class="text-left hidden lg:table-cell">วันมีผล</th>
                <th class="text-left hidden xl:table-cell">เผยแพร่เมื่อ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($notices as $n)
            @php $status = $n->getStatus(); @endphp
            <tr>
                <td>
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                             style="background:{{ \App\Models\PrivacyNotice::typeBg($n->type) }};">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 style="color:{{ \App\Models\PrivacyNotice::typeColor($n->type) }};">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ \App\Models\PrivacyNotice::typeIcon($n->type) }}"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-sm" style="color:#1e293b;">{{ $n->title }}</p>
                            <p class="text-xs mt-0.5" style="color:{{ \App\Models\PrivacyNotice::typeColor($n->type) }};">{{ $n->getTypeLabel() }}</p>
                        </div>
                    </div>
                </td>
                <td class="hidden md:table-cell">
                    <span class="text-sm font-medium">{{ $n->language === 'th' ? '🇹🇭 ไทย' : '🇬🇧 EN' }}</span>
                </td>
                <td class="text-center hidden md:table-cell">
                    <span class="text-xs font-mono font-bold px-2 py-1 rounded-md" style="background:#f1f5f9; color:#475569;">v{{ $n->version }}</span>
                </td>
                <td>
                    <span class="badge {{ $n->getStatusBadge() }}">{{ $n->getStatusLabel() }}</span>
                </td>
                <td class="hidden lg:table-cell text-sm" style="color:#475569;">
                    {{ $n->effective_date ?: '—' }}
                </td>
                <td class="hidden xl:table-cell text-xs" style="color:#94a3b8;">
                    {{ $n->published_at ? $n->published_at->format('d/m/Y') : '—' }}
                </td>
                <td class="text-right">
                    <div class="flex items-center justify-end gap-1.5">
                        @if($status === 'draft' || $status === 'inactive')
                        <form method="POST" action="{{ route('privacy.publish', $n) }}">@csrf
                            <button type="submit" class="btn-primary" style="padding:5px 12px; font-size:11px;">เผยแพร่</button>
                        </form>
                        @endif
                        <a href="{{ route('privacy.show', $n) }}" class="btn-outline" style="padding:5px 12px; font-size:11px;">ดู</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-16">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:#f1f5f9;">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#cbd5e1;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <p class="text-sm font-medium" style="color:#64748b;">ยังไม่มี Privacy Notice</p>
                        <a href="{{ route('privacy.create') }}" class="btn-primary" style="font-size:13px;">สร้างประกาศแรก</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($notices->hasPages())
    <div class="px-5 py-3" style="border-top:1px solid #f1f5f9;">{{ $notices->links() }}</div>
    @endif
</div>

@endsection
