@extends('layouts.app')
@section('title', 'Security Log')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold" style="color:#15572e;">📋 System Logs</h1>
</div>

@include('modules.logs._tabs')

{{-- Stats --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card text-center" style="{{ $stats['critical_unresolved'] > 0 ? 'border-left:4px solid #c0272d;' : '' }}">
        <p class="text-3xl font-black" style="color:#c0272d;">{{ $stats['critical_unresolved'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">Critical ยังไม่แก้</p>
    </div>
    <div class="card text-center" style="{{ $stats['high_unresolved'] > 0 ? 'border-left:4px solid #dc2626;' : '' }}">
        <p class="text-3xl font-black" style="color:#dc2626;">{{ $stats['high_unresolved'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">High ยังไม่แก้</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#d97706;">{{ $stats['login_failed_today'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">Login Fail วันนี้</p>
    </div>
    <div class="card">
        <h4 class="text-xs font-medium mb-2" style="color:#64748b;">แบ่งตาม Severity</h4>
        @foreach(['critical','high','medium','low'] as $sev)
        @php $cnt = $stats['by_severity'][$sev] ?? 0; @endphp
        <div class="flex items-center gap-2 text-xs mb-1">
            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ \App\Models\SecurityLog::severityColor($sev) }};"></span>
            <span style="color:#64748b;">{{ $sev }}</span>
            <span class="ml-auto font-bold" style="color:{{ \App\Models\SecurityLog::severityColor($sev) }};">{{ $cnt }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" action="{{ route('logs.security') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Severity</label>
            <select name="severity" class="form-input">
                <option value="">ทั้งหมด</option>
                @foreach(['low','medium','high','critical'] as $s)
                    <option value="{{ $s }}" {{ request('severity')===$s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Event Type</label>
            <select name="event_type" class="form-input" style="min-width:180px;">
                <option value="">ทั้งหมด</option>
                @foreach(['login_failed','login_success','account_locked','mfa_failed','permission_denied','brute_force_detected','data_export','suspicious_ip','password_changed'] as $e)
                    <option value="{{ $e }}" {{ request('event_type')===$e ? 'selected' : '' }}>{{ $e }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <label class="flex items-center gap-2 cursor-pointer pb-1">
                <input type="checkbox" name="unresolved" value="1" {{ request('unresolved') ? 'checked' : '' }} class="accent-green-700">
                <span class="text-sm" style="color:#374151;">เฉพาะที่ยังไม่แก้</span>
            </label>
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
        <a href="{{ route('logs.security') }}" class="btn-outline">รีเซ็ต</a>
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
                    <th>Event</th>
                    <th>Severity</th>
                    <th>ผู้ใช้</th>
                    <th>รายละเอียด</th>
                    <th>IP</th>
                    <th>สถานะ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr style="{{ !$log->is_resolved && in_array($log->severity,['critical','high']) ? 'background:#fff8f8;' : '' }}">
                    <td class="text-xs" style="color:#64748b;white-space:nowrap;">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td>
                        <span class="text-sm">{{ \App\Models\SecurityLog::eventIcon($log->event_type) }}</span>
                        <span class="text-xs font-medium ml-1" style="color:#334155;">{{ $log->event_type }}</span>
                    </td>
                    <td>
                        <span class="badge text-xs font-bold" style="background:{{ \App\Models\SecurityLog::severityBg($log->severity) }};color:{{ \App\Models\SecurityLog::severityColor($log->severity) }};">
                            {{ strtoupper($log->severity) }}
                        </span>
                    </td>
                    <td class="text-sm" style="color:#334155;">{{ $log->user_name ?? '<span style="color:#94a3b8;">—</span>' }}</td>
                    <td>
                        <div class="text-sm" style="color:#475569;max-width:250px;">{{ $log->description ?? '—' }}</div>
                        @if($log->metadata)
                            <div class="text-xs font-mono mt-1" style="color:#94a3b8;">
                                {{ collect($log->metadata)->map(fn($v,$k)=>"$k: $v")->take(2)->implode(' | ') }}
                            </div>
                        @endif
                    </td>
                    <td class="text-xs font-mono" style="color:#64748b;">{{ $log->ip_address ?? '—' }}</td>
                    <td>
                        @if($log->is_resolved)
                            <span class="badge text-xs" style="background:#dcfce7;color:#15572e;">✅ แก้แล้ว</span>
                            <div class="text-xs mt-1" style="color:#94a3b8;">{{ $log->resolved_by }}</div>
                        @else
                            <span class="badge text-xs" style="background:#fef3c7;color:#92400e;">⏳ รอแก้ไข</span>
                        @endif
                    </td>
                    <td>
                        @if(!$log->is_resolved)
                        <form method="POST" action="{{ route('logs.security.resolve', $log) }}">
                            @csrf
                            <button type="submit" class="text-xs px-2 py-1 rounded border transition"
                                style="border-color:#15572e;color:#15572e;"
                                onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='white'">
                                ✓ แก้แล้ว
                            </button>
                        </form>
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
