@extends('layouts.app')
@section('title', 'DPIA & Assessment — PDPA Studio')
@section('page-title', 'DPIA / Assessment')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#e8f0eb,#d1e8d8);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#15572e;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
        <p class="text-3xl font-extrabold" style="color:#0f3020;">{{ $totalCount }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">การประเมินทั้งหมด</p>
    </div>
    <div class="card p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#fff1f2;">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color:#c0272d;"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            </div>
        </div>
        <p class="text-3xl font-extrabold" style="color:{{ $highRiskCount > 0 ? '#c0272d' : '#64748b' }};">{{ $highRiskCount }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">ความเสี่ยงสูง</p>
        @if($highRiskCount > 0)<p class="text-xs font-medium mt-0.5" style="color:#c0272d;">ต้องดำเนินการ!</p>@endif
    </div>
    <div class="card p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#fffbeb;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#b45309;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-3xl font-extrabold" style="color:#b45309;">{{ $pendingCount }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">กำลังดำเนินการ</p>
    </div>
    <div class="card p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color:#15572e;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
        </div>
        <p class="text-3xl font-extrabold" style="color:#15572e;">{{ $completedCount }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">เสร็จสิ้น/อนุมัติ</p>
    </div>
</div>

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
    <form method="GET" class="flex items-center gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหา..." class="form-input" style="width:180px;">
        <select name="type" class="form-input" style="width:auto;">
            <option value="">ทุกประเภท</option>
            <option value="dpia" {{ request('type')==='dpia' ? 'selected' : '' }}>DPIA</option>
            <option value="lia" {{ request('type')==='lia' ? 'selected' : '' }}>LIA</option>
            <option value="gap_analysis" {{ request('type')==='gap_analysis' ? 'selected' : '' }}>Gap Analysis</option>
        </select>
        <select name="status" class="form-input" style="width:auto;">
            <option value="">ทุกสถานะ</option>
            <option value="draft" {{ request('status')==='draft' ? 'selected' : '' }}>ร่าง</option>
            <option value="in_progress" {{ request('status')==='in_progress' ? 'selected' : '' }}>กำลังดำเนินการ</option>
            <option value="completed" {{ request('status')==='completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
            <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>อนุมัติแล้ว</option>
        </select>
        <select name="risk" class="form-input" style="width:auto;">
            <option value="">ทุกระดับความเสี่ยง</option>
            <option value="low" {{ request('risk')==='low' ? 'selected' : '' }}>ต่ำ</option>
            <option value="medium" {{ request('risk')==='medium' ? 'selected' : '' }}>ปานกลาง</option>
            <option value="high" {{ request('risk')==='high' ? 'selected' : '' }}>สูง</option>
            <option value="very_high" {{ request('risk')==='very_high' ? 'selected' : '' }}>สูงมาก</option>
        </select>
        <select name="per_page" class="form-input" style="width:auto;" onchange="this.form.submit()">
            <option value="50"  {{ request('per_page','50')  == '50'  ? 'selected' : '' }}>แสดง 50</option>
            <option value="100" {{ request('per_page')       == '100' ? 'selected' : '' }}>แสดง 100</option>
            <option value="200" {{ request('per_page')       == '200' ? 'selected' : '' }}>แสดง 200</option>
        </select>
        <button type="submit" class="btn-outline">ค้นหา</button>
        @if(request()->hasAny(['search','type','status','risk']))
        <a href="{{ route('assessment.index') }}" class="text-sm font-medium" style="color:#94a3b8;">ล้าง</a>
        @endif
    </form>
    <a href="{{ route('assessment.create') }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        สร้างการประเมินใหม่
    </a>
</div>

{{-- Showing results --}}
<div class="flex items-center justify-between mb-2">
    <p class="text-xs" style="color:#94a3b8;">
        แสดง {{ $assessments->firstItem() }}–{{ $assessments->lastItem() }} จากทั้งหมด <strong style="color:#475569;">{{ number_format($assessments->total()) }}</strong> รายการ
    </p>
</div>

{{-- Table --}}
<div class="card overflow-hidden">
    <table class="w-full data-table">
        <thead>
            <tr>
                <th class="text-left">เลขที่ / ชื่อ</th>
                <th class="text-left hidden md:table-cell">ประเภท</th>
                <th class="text-left">สถานะ</th>
                <th class="text-left hidden lg:table-cell">ความเสี่ยง</th>
                <th class="text-left hidden xl:table-cell">วันที่</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($assessments as $a)
            @php
                $typeColors = ['dpia'=>'badge-red','lia'=>'badge-blue','gap_analysis'=>'badge-green'];
                $typeLabels = ['dpia'=>'DPIA','lia'=>'LIA','gap_analysis'=>'Gap Analysis'];
                $statusColors = ['draft'=>'badge-gray','in_progress'=>'badge-yellow','completed'=>'badge-blue','approved'=>'badge-green','archived'=>'badge-gray'];
                $statusLabels = ['draft'=>'ร่าง','in_progress'=>'กำลังดำเนินการ','completed'=>'เสร็จสิ้น','approved'=>'อนุมัติแล้ว','archived'=>'เก็บถาวร'];
                $riskColors = ['low'=>'#15572e','medium'=>'#b45309','high'=>'#c05621','very_high'=>'#c0272d'];
                $riskBg = ['low'=>'#f0fdf4','medium'=>'#fffbeb','high'=>'#fff7ed','very_high'=>'#fff1f2'];
                $riskLabels = ['low'=>'ต่ำ','medium'=>'ปานกลาง','high'=>'สูง','very_high'=>'สูงมาก'];
            @endphp
            <tr>
                <td>
                    <p class="font-mono text-xs mb-0.5" style="color:#94a3b8;">{{ $a->assessment_number }}</p>
                    <p class="font-semibold text-sm" style="color:#1e293b;">{{ $a->title }}</p>
                    @if($a->scope)<p class="text-xs mt-0.5 truncate max-w-xs" style="color:#94a3b8;">{{ $a->scope }}</p>@endif
                </td>
                <td class="hidden md:table-cell">
                    <span class="badge {{ $typeColors[$a->type] ?? 'badge-gray' }}">{{ $typeLabels[$a->type] ?? $a->type }}</span>
                </td>
                <td>
                    <span class="badge {{ $statusColors[$a->status] ?? 'badge-gray' }}">{{ $statusLabels[$a->status] ?? $a->status }}</span>
                </td>
                <td class="hidden lg:table-cell">
                    @if($a->risk_level)
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-2 rounded-full" style="background:#f1f5f9; max-width:80px;">
                            <div class="h-2 rounded-full" style="width:{{ $a->risk_score ?? 0 }}%; background:{{ $riskColors[$a->risk_level] ?? '#64748b' }};"></div>
                        </div>
                        <span class="text-xs font-semibold" style="color:{{ $riskColors[$a->risk_level] ?? '#64748b' }};">{{ $riskLabels[$a->risk_level] }} {{ $a->risk_score !== null ? '('.$a->risk_score.')' : '' }}</span>
                    </div>
                    @else
                    <span class="text-xs" style="color:#94a3b8;">ยังไม่ประเมิน</span>
                    @endif
                </td>
                <td class="hidden xl:table-cell text-xs" style="color:#64748b;">
                    @if($a->started_at)<p>เริ่ม: {{ $a->started_at->format('d/m/Y') }}</p>@endif
                    @if($a->completed_at)<p>เสร็จ: {{ $a->completed_at->format('d/m/Y') }}</p>@endif
                </td>
                <td class="text-right">
                    <a href="{{ route('assessment.show', $a) }}" class="btn-outline" style="padding:6px 14px; font-size:12px;">ดูรายละเอียด</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-14">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#cbd5e1;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-sm" style="color:#94a3b8;">ยังไม่มีการประเมิน</p>
                        <a href="{{ route('assessment.create') }}" class="btn-primary" style="font-size:13px;">สร้างการประเมินแรก</a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($assessments->hasPages())
    <div class="px-5 py-3" style="border-top:1px solid #f1f5f9;">{{ $assessments->links() }}</div>
    @endif
</div>

@endsection
