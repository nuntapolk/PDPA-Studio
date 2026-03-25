@extends('layouts.app')

@section('title', 'ROPA — PDPA Studio')
@section('page-title', 'ROPA — บันทึกกิจกรรมการประมวลผล')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">กิจกรรมทั้งหมด</p>
        <p class="text-3xl font-extrabold" style="color:#0f3020;">{{ number_format($totalCount) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">กำลังใช้งาน</p>
        <p class="text-3xl font-extrabold" style="color:#15572e;">{{ number_format($activeCount) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">ต้อง Review</p>
        <p class="text-3xl font-extrabold" style="color:{{ $needsReviewCount > 0 ? '#b45309' : '#64748b' }};">{{ number_format($needsReviewCount) }}</p>
        @if($needsReviewCount > 0)<p class="text-xs mt-1 font-medium" style="color:#b45309;">เกินกำหนด Review</p>@endif
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">มีข้อมูลอ่อนไหว</p>
        <p class="text-3xl font-extrabold" style="color:{{ $sensitiveCount > 0 ? '#c0272d' : '#64748b' }};">{{ number_format($sensitiveCount) }}</p>
    </div>
</div>

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
    <form method="GET" class="flex items-center gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหากิจกรรม..." class="form-input" style="width:200px;">
        <select name="status" class="form-input" style="width:auto;">
            <option value="">ทุกสถานะ</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>ร่าง</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>ใช้งาน</option>
            <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>กำลัง Review</option>
            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>เก็บถาวร</option>
        </select>
        <select name="department" class="form-input" style="width:auto;">
            <option value="">ทุกแผนก</option>
            @foreach($departments as $dept)
            <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
            @endforeach
        </select>
        <select name="per_page" class="form-input" style="width:auto;" onchange="this.form.submit()">
            <option value="50"  {{ request('per_page','50')  == '50'  ? 'selected' : '' }}>แสดง 50</option>
            <option value="100" {{ request('per_page')       == '100' ? 'selected' : '' }}>แสดง 100</option>
            <option value="200" {{ request('per_page')       == '200' ? 'selected' : '' }}>แสดง 200</option>
        </select>
        <button type="submit" class="btn-outline">ค้นหา</button>
        @if(request()->hasAny(['search','status','department']))
        <a href="{{ route('ropa.index') }}" class="text-sm font-medium" style="color:#94a3b8;">ล้าง</a>
        @endif
    </form>

    <div class="flex items-center gap-2">
        <a href="{{ route('ropa.export') }}" class="btn-outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export CSV
        </a>
        <a href="{{ route('ropa.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            เพิ่มกิจกรรมใหม่
        </a>
    </div>
</div>

{{-- Showing results --}}
<div class="flex items-center justify-between mb-2">
    <p class="text-xs" style="color:#94a3b8;">
        แสดง {{ $records->firstItem() }}–{{ $records->lastItem() }} จากทั้งหมด <strong style="color:#475569;">{{ number_format($records->total()) }}</strong> รายการ
    </p>
</div>

{{-- Table --}}
<div class="card overflow-hidden">
    <table class="w-full data-table">
        <thead>
            <tr>
                <th class="text-left">กิจกรรม</th>
                <th class="text-left hidden md:table-cell">แผนก</th>
                <th class="text-left hidden md:table-cell">ฐานกฎหมาย</th>
                <th class="text-center hidden lg:table-cell">ข้อมูลอ่อนไหว</th>
                <th class="text-left">สถานะ</th>
                <th class="text-left hidden xl:table-cell">Review ถัดไป</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
            @php
                $statusColors = ['draft' => 'badge-gray', 'active' => 'badge-green', 'under_review' => 'badge-yellow', 'archived' => 'badge-gray'];
                $statusLabels = ['draft' => 'ร่าง', 'active' => 'ใช้งาน', 'under_review' => 'Review', 'archived' => 'เก็บถาวร'];
                $needsReview  = $record->needsReview() && $record->status !== 'archived';
            @endphp
            <tr style="{{ $needsReview ? 'background:#fffbeb;' : '' }}">
                <td>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-sm" style="color:#1e293b;">{{ $record->process_name }}</p>
                            @if($record->has_sensitive_data)
                            <span class="badge badge-red">Sensitive</span>
                            @endif
                        </div>
                        <p class="text-xs mt-0.5" style="color:#94a3b8;">
                            {{ $record->process_code ? $record->process_code . ' · ' : '' }}
                            {{ match($record->role) { 'controller' => 'Controller', 'processor' => 'Processor', 'joint_controller' => 'Joint Controller', default => $record->role } }}
                        </p>
                    </div>
                </td>
                <td class="hidden md:table-cell text-sm" style="color:#64748b;">{{ $record->department ?? '—' }}</td>
                <td class="hidden md:table-cell">
                    <span class="badge badge-blue">{{ $record->getLegalBasisLabel() }}</span>
                </td>
                <td class="text-center hidden lg:table-cell">
                    @if($record->has_sensitive_data)
                    <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20" style="color:#c0272d;"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    @else
                    <span style="color:#cbd5e1;">—</span>
                    @endif
                </td>
                <td>
                    <span class="badge {{ $statusColors[$record->status] ?? 'badge-gray' }}">{{ $statusLabels[$record->status] ?? $record->status }}</span>
                </td>
                <td class="hidden xl:table-cell text-xs">
                    @if($record->next_review_date)
                        <span style="color:{{ $needsReview ? '#b45309' : '#64748b' }}; font-weight:{{ $needsReview ? '600' : 'normal' }};">
                            {{ $record->next_review_date->format('d/m/Y') }}
                            @if($needsReview)<span class="badge badge-yellow ml-1">ค้างรีวิว</span>@endif
                        </span>
                    @else
                        <span style="color:#94a3b8;">ไม่กำหนด</span>
                    @endif
                </td>
                <td class="text-right">
                    <a href="{{ route('ropa.show', $record) }}" style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:20px;background:#f0fdf4;color:#15572e;border:1px solid #bbf7d0;font-size:12px;font-weight:500;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg></a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-sm" style="color:#94a3b8;">
                    ยังไม่มี ROPA Record — <a href="{{ route('ropa.create') }}" style="color:#15572e; font-weight:600;">เพิ่มกิจกรรมแรก</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($records->hasPages())
    <div class="px-5 py-3" style="border-top:1px solid #f1f5f9;">{{ $records->links() }}</div>
    @endif
</div>

@endsection
