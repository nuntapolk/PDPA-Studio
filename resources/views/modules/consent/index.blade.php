@extends('layouts.app')
@section('title', 'ความยินยอม — PDPA Studio')
@section('page-title', 'ความยินยอม (Consent Management)')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">Active Consent</p>
        <p class="text-3xl font-extrabold" style="color:#15572e;">{{ number_format($totalActive) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">ถูกถอนแล้ว</p>
        <p class="text-3xl font-extrabold" style="color:#64748b;">{{ number_format($totalWithdrawn) }}</p>
    </div>
    <div class="card p-5">
        <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:#64748b;">ใกล้หมดอายุ (30 วัน)</p>
        <p class="text-3xl font-extrabold" style="color:{{ $expiringSoon > 0 ? '#b45309' : '#64748b' }};">{{ number_format($expiringSoon) }}</p>
    </div>
</div>

{{-- Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
    <div class="flex items-center gap-3">
        <p class="text-sm font-semibold" style="color:#475569;">Templates ทั้งหมด ({{ $templates->total() }})</p>
        <form method="GET" class="flex items-center gap-2">
            <select name="per_page" class="form-input" style="width:auto;" onchange="this.form.submit()">
                <option value="50"  {{ request('per_page','50')  == '50'  ? 'selected' : '' }}>แสดง 50</option>
                <option value="100" {{ request('per_page')       == '100' ? 'selected' : '' }}>แสดง 100</option>
                <option value="200" {{ request('per_page')       == '200' ? 'selected' : '' }}>แสดง 200</option>
            </select>
        </form>
    </div>
    <a href="{{ route('consent.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        สร้าง Template ใหม่
    </a>
</div>
{{-- Showing results --}}
<p class="text-xs mb-2" style="color:#94a3b8;">
    แสดง {{ $templates->firstItem() }}–{{ $templates->lastItem() }} จากทั้งหมด <strong style="color:#475569;">{{ number_format($templates->total()) }}</strong> รายการ
</p>

{{-- Table --}}
<div class="card overflow-hidden">
    <table class="w-full data-table">
        <thead>
            <tr>
                <th class="text-left">ชื่อ Template</th>
                <th class="text-left hidden md:table-cell">ประเภท</th>
                <th class="text-left hidden lg:table-cell">ฐานกฎหมาย</th>
                <th class="text-center">Active</th>
                <th class="text-left hidden sm:table-cell">Version</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($templates as $t)
            <tr>
                <td>
                    <p class="font-semibold text-sm" style="color:#1e293b;">{{ $t->name }}</p>
                    <p class="text-xs mt-0.5 truncate max-w-xs" style="color:#94a3b8;">{{ Str::limit($t->purpose, 60) }}</p>
                </td>
                <td class="hidden md:table-cell"><span class="badge badge-blue">{{ $t->category }}</span></td>
                <td class="hidden lg:table-cell text-sm" style="color:#475569;">{{ $t->getLegalBasisLabel() }}</td>
                <td class="text-center">
                    <span class="text-sm font-bold" style="color:{{ $t->consents_count > 0 ? '#15572e' : '#94a3b8' }};">{{ number_format($t->consents_count) }}</span>
                </td>
                <td class="hidden sm:table-cell"><span class="text-xs font-mono" style="color:#94a3b8;">v{{ $t->version }}</span></td>
                <td class="text-right">
                    <a href="{{ route('consent.show', $t) }}" style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;border-radius:20px;background:#f0fdf4;color:#15572e;border:1px solid #bbf7d0;font-size:12px;font-weight:500;"><svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/></svg></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-12 text-sm" style="color:#94a3b8;">ยังไม่มี Template — <a href="{{ route('consent.create') }}" style="color:#15572e;">สร้างใหม่</a></td></tr>
            @endforelse
        </tbody>
    </table>
    @if($templates->hasPages())
    <div class="px-5 py-3" style="border-top:1px solid #f1f5f9;">{{ $templates->links() }}</div>
    @endif
</div>
@endsection
