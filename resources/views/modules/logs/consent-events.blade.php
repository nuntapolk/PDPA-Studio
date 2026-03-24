@extends('layouts.app')
@section('title', 'Consent Event Log')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold" style="color:#15572e;">📋 System Logs</h1>
</div>

@include('modules.logs._tabs')

<div class="mb-4 p-4 rounded-lg" style="background:#f0fdf4;border:1px solid #bbf7d0;">
    <p class="text-sm" style="color:#15572e;">
        <strong>📌 Legal Record:</strong> บันทึกนี้คือหลักฐานทางกฎหมายของการให้/ถอนความยินยอม ห้ามลบหรือแก้ไข — เก็บเป็น Immutable Log
    </p>
</div>

{{-- Stats --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#15572e;">{{ $stats['granted_month'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">ให้ความยินยอมเดือนนี้</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#c0272d;">{{ $stats['withdrawn_month'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">ถอนความยินยอมเดือนนี้</p>
    </div>
    <div class="card">
        <h4 class="text-xs font-medium mb-2" style="color:#64748b;">แบ่งตาม Event</h4>
        @foreach($stats['by_event'] as $event => $cnt)
        <div class="flex items-center justify-between text-xs mb-1">
            <span style="color:{{ \App\Models\ConsentEventLog::eventColor($event) }};">{{ \App\Models\ConsentEventLog::eventLabel($event) }}</span>
            <span class="font-bold">{{ $cnt }}</span>
        </div>
        @endforeach
    </div>
    <div class="card">
        <h4 class="text-xs font-medium mb-2" style="color:#64748b;">ช่องทาง</h4>
        @foreach($stats['by_channel'] as $ch => $cnt)
        <div class="flex items-center justify-between text-xs mb-1">
            <span style="color:#64748b;">{{ $ch }}</span>
            <span class="font-bold">{{ $cnt }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" action="{{ route('logs.consent-events') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Event</label>
            <select name="event_type" class="form-input">
                <option value="">ทั้งหมด</option>
                @foreach(['granted','withdrawn','expired','renewed','amended','imported','rejected'] as $e)
                    <option value="{{ $e }}" {{ request('event_type')===$e ? 'selected' : '' }}>{{ \App\Models\ConsentEventLog::eventLabel($e) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ช่องทาง</label>
            <select name="channel" class="form-input">
                <option value="">ทั้งหมด</option>
                @foreach(['web','api','paper','email','in_person','import'] as $ch)
                    <option value="{{ $ch }}" {{ request('channel')===$ch ? 'selected' : '' }}>{{ $ch }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ค้นหาเจ้าของข้อมูล</label>
            <input type="text" name="search" class="form-input" placeholder="ชื่อหรืออีเมล..." value="{{ request('search') }}" style="width:200px;">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ตั้งแต่</label>
            <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
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
        <a href="{{ route('logs.consent-events') }}" class="btn-outline">รีเซ็ต</a>
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
                    <th>เจ้าของข้อมูล</th>
                    <th>Event</th>
                    <th>วัตถุประสงค์</th>
                    <th>เวอร์ชัน</th>
                    <th>ช่องทาง</th>
                    <th>บันทึกโดย</th>
                    <th>หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="text-xs" style="color:#64748b;white-space:nowrap;">
                        {{ $log->event_at->format('d/m/Y H:i') }}<br>
                        <span style="color:#94a3b8;font-size:10px;">บันทึก {{ $log->created_at->format('H:i') }}</span>
                    </td>
                    <td>
                        <div class="text-sm font-medium" style="color:#1e293b;">{{ $log->data_subject_name ?? '—' }}</div>
                        <div class="text-xs" style="color:#94a3b8;">{{ $log->data_subject_email ?? '' }}</div>
                    </td>
                    <td>
                        <span class="badge text-xs font-bold"
                              style="background:{{ \App\Models\ConsentEventLog::eventBg($log->event_type) }};color:{{ \App\Models\ConsentEventLog::eventColor($log->event_type) }};">
                            {{ \App\Models\ConsentEventLog::eventLabel($log->event_type) }}
                        </span>
                    </td>
                    <td class="text-sm" style="color:#475569;max-width:180px;">
                        {{ Str::limit($log->consent_purpose ?? '—', 40) }}
                    </td>
                    <td class="text-xs font-mono" style="color:#64748b;">{{ $log->consent_version ?? '—' }}</td>
                    <td>
                        <span class="badge text-xs" style="background:#f1f5f9;color:#475569;">{{ $log->channel ?? '—' }}</span>
                    </td>
                    <td class="text-sm" style="color:#64748b;">{{ $log->recorder?->name ?? 'ระบบ' }}</td>
                    <td class="text-sm" style="color:#94a3b8;">{{ Str::limit($log->notes ?? '—', 30) }}</td>
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
