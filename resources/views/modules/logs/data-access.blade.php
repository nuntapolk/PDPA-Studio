@extends('layouts.app')
@section('title', 'Data Access Log')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold" style="color:#15572e;">📋 System Logs</h1>
</div>

@include('modules.logs._tabs')

<div class="mb-4 p-4 rounded-lg" style="background:#eff6ff;border:1px solid #bfdbfe;">
    <p class="text-sm" style="color:#1e40af;">
        <strong>📌 PDPA ม.40:</strong> บันทึกนี้เป็นหลักฐานการเข้าถึงข้อมูลส่วนบุคคลทุกครั้ง ใช้สนับสนุนการตรวจสอบ และการตอบสนองคำขอสิทธิ์ของเจ้าของข้อมูล
    </p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#15572e;">{{ $stats['exports_today'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">Export วันนี้</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#d97706;">{{ $stats['sensitive_access'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">เข้าถึงข้อมูลอ่อนไหววันนี้</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:{{ $stats['cross_border_month'] > 0 ? '#c0272d' : '#15572e' }};">{{ $stats['cross_border_month'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">Cross-border เดือนนี้</p>
    </div>
    <div class="card">
        <h4 class="text-xs font-medium mb-2" style="color:#64748b;">ประเภทข้อมูล</h4>
        @foreach($stats['by_category']->take(4) as $cat => $cnt)
        <div class="flex items-center justify-between text-xs mb-1">
            <span style="color:#64748b;">{{ $cat }}</span>
            <span class="font-bold" style="color:{{ \App\Models\DataAccessLog::categoryColor($cat) }};">{{ $cnt }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" action="{{ route('logs.data-access') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ประเภทการเข้าถึง</label>
            <select name="access_type" class="form-input">
                <option value="">ทั้งหมด</option>
                @foreach(['read','search','export','print','share','api','bulk_export','rights_request'] as $t)
                    <option value="{{ $t }}" {{ request('access_type')===$t ? 'selected' : '' }}>{{ \App\Models\DataAccessLog::accessTypeLabel($t) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ประเภทข้อมูล</label>
            <select name="data_category" class="form-input">
                <option value="">ทั้งหมด</option>
                @foreach(['personal','sensitive','health','financial','biometric'] as $c)
                    <option value="{{ $c }}" {{ request('data_category')===$c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <label class="flex items-center gap-2 cursor-pointer pb-1">
                <input type="checkbox" name="cross_border" value="1" {{ request('cross_border') ? 'checked' : '' }} class="accent-green-700">
                <span class="text-sm" style="color:#374151;">Cross-border เท่านั้น</span>
            </label>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ตั้งแต่</label>
            <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ถึง</label>
            <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">แสดง</label>
            <select name="per_page" class="form-input" style="width:auto;">
                <option value="50"  {{ request('per_page','50')=='50'  ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page')=='100' ? 'selected' : '' }}>100</option>
                <option value="200" {{ request('per_page')=='200' ? 'selected' : '' }}>200</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">🔍 ค้นหา</button>
        <a href="{{ route('logs.data-access') }}" class="btn-outline">รีเซ็ต</a>
    </form>
</div>

{{-- Table --}}
<div class="card">
    @if($logs->count() > 0)
    <p class="text-xs mb-3" style="color:#94a3b8;">
        แสดง {{ $logs->firstItem() }}–{{ $logs->lastItem() }} จากทั้งหมด <strong>{{ number_format($logs->total()) }}</strong> รายการ
    </p>
    @endif

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:130px;">เวลา</th>
                    <th>ผู้ใช้</th>
                    <th>ประเภทการเข้าถึง</th>
                    <th>ประเภทข้อมูล</th>
                    <th>ตาราง / Record</th>
                    <th class="text-right">จำนวน</th>
                    <th>วัตถุประสงค์</th>
                    <th>Cross-border</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr style="{{ in_array($log->data_category,['sensitive','health','biometric']) ? 'background:#fff8f8;' : '' }}">
                    <td class="text-xs" style="color:#64748b;white-space:nowrap;">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="text-sm" style="color:#334155;">{{ $log->user_name ?? '—' }}</td>
                    <td>
                        <span class="badge text-xs" style="background:#dbeafe;color:#1d4ed8;">
                            {{ \App\Models\DataAccessLog::accessTypeLabel($log->access_type) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge text-xs font-bold" style="color:{{ \App\Models\DataAccessLog::categoryColor($log->data_category) }};background:{{ in_array($log->data_category,['sensitive','health','biometric']) ? '#fee2e2' : '#f1f5f9' }};">
                            {{ $log->data_category }}
                        </span>
                    </td>
                    <td>
                        <span class="text-xs font-mono" style="color:#475569;">{{ $log->table_name ?? '—' }}</span>
                        @if($log->record_id)
                            <span class="text-xs" style="color:#94a3b8;">#{{ $log->record_id }}</span>
                        @endif
                    </td>
                    <td class="text-right text-sm font-bold" style="color:#334155;">
                        {{ number_format($log->record_count) }}
                    </td>
                    <td class="text-sm" style="color:#64748b;max-width:180px;">
                        {{ Str::limit($log->purpose ?? '—', 40) }}
                    </td>
                    <td class="text-center">
                        @if($log->is_cross_border)
                            <span class="badge text-xs" style="background:#fee2e2;color:#c0272d;">🌐 {{ $log->destination_country }}</span>
                        @else
                            <span style="color:#e2e8f0;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-8" style="color:#94a3b8;">ไม่พบข้อมูล</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="mt-4 flex justify-center">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
