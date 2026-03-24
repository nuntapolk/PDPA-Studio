@extends('layouts.app')
@section('title', 'Operation Log')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold" style="color:#15572e;">📋 System Logs</h1>
</div>

@include('modules.logs._tabs')

{{-- Stats --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#15572e;">{{ number_format($stats['total_requests']) }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">Requests วันนี้</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#1d4ed8;">{{ $stats['avg_duration'] }} ms</p>
        <p class="text-sm mt-1" style="color:#64748b;">Avg Duration</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:{{ $stats['error_rate'] > 0 ? '#c0272d' : '#15572e' }};">{{ $stats['error_rate'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">Errors (4xx/5xx)</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:{{ $stats['slow_requests'] > 0 ? '#d97706' : '#15572e' }};">{{ $stats['slow_requests'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">Slow (&gt;1s)</p>
    </div>
</div>

<div class="grid gap-6 mb-6" style="grid-template-columns:1fr 280px;">
    {{-- Filters --}}
    <div class="card">
        <form method="GET" action="{{ route('logs.operation') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium mb-1" style="color:#64748b;">Status</label>
                <select name="status" class="form-input" style="width:120px;">
                    <option value="">ทั้งหมด</option>
                    <option value="200" {{ request('status')=='200' ? 'selected' : '' }}>200 OK</option>
                    <option value="301" {{ request('status')=='301' ? 'selected' : '' }}>3xx Redirect</option>
                    <option value="404" {{ request('status')=='404' ? 'selected' : '' }}>404 Not Found</option>
                    <option value="422" {{ request('status')=='422' ? 'selected' : '' }}>422 Validation</option>
                    <option value="500" {{ request('status')=='500' ? 'selected' : '' }}>500 Error</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color:#64748b;">Slow (ms ขึ้นไป)</label>
                <select name="slow" class="form-input" style="width:120px;">
                    <option value="">ทั้งหมด</option>
                    <option value="500"  {{ request('slow')=='500'  ? 'selected' : '' }}>&gt; 500ms</option>
                    <option value="1000" {{ request('slow')=='1000' ? 'selected' : '' }}>&gt; 1s</option>
                    <option value="3000" {{ request('slow')=='3000' ? 'selected' : '' }}>&gt; 3s</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color:#64748b;">Route</label>
                <input type="text" name="route" class="form-input" placeholder="เช่น ropa.show" value="{{ request('route') }}" style="width:160px;">
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
            <a href="{{ route('logs.operation') }}" class="btn-outline">รีเซ็ต</a>
        </form>
    </div>

    {{-- Top Routes --}}
    <div class="card">
        <h3 class="font-semibold mb-3 text-sm" style="color:#15572e;">Top Routes วันนี้</h3>
        <div class="space-y-2">
        @foreach($stats['top_routes'] as $r)
            <div class="flex items-center justify-between gap-2">
                <span class="text-xs font-mono truncate" style="color:#334155;max-width:150px;" title="{{ $r->route_name }}">
                    {{ $r->route_name ?? '—' }}
                </span>
                <div class="text-right flex-shrink-0">
                    <span class="text-xs font-bold" style="color:#15572e;">{{ $r->cnt }}x</span>
                    <span class="text-xs ml-1" style="color:#94a3b8;">{{ round($r->avg_ms) }}ms</span>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    @if($logs->count() > 0)
    <p class="text-xs mb-3" style="color:#94a3b8;">
        แสดง {{ $logs->firstItem() }}–{{ $logs->lastItem() }} จากทั้งหมด <strong>{{ number_format($logs->total()) }}</strong> requests
    </p>
    @endif

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:130px;">เวลา</th>
                    <th>ผู้ใช้</th>
                    <th>Method</th>
                    <th>Route / URL</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Duration</th>
                    <th class="text-right">Memory</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                @php
                $badge = $log->status_badge;
                $dur   = $log->duration_ms;
                $durColor = match(true) {
                    $dur < 200  => '#15572e',
                    $dur < 1000 => '#475569',
                    $dur < 3000 => '#d97706',
                    default     => '#c0272d',
                };
                $methodColors = ['GET'=>'#dbeafe','POST'=>'#dcfce7','PUT'=>'#fef3c7','PATCH'=>'#fef3c7','DELETE'=>'#fee2e2'];
                $mColor = $methodColors[$log->method] ?? '#f1f5f9';
                @endphp
                <tr>
                    <td class="text-xs" style="color:#64748b;white-space:nowrap;">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="text-sm" style="color:#334155;">{{ $log->user_name ?? '<span style="color:#94a3b8;">Guest</span>' }}</td>
                    <td>
                        <span class="badge text-xs font-mono font-bold" style="background:{{ $mColor }};color:#334155;">
                            {{ $log->method }}
                        </span>
                    </td>
                    <td>
                        <div class="text-xs font-medium" style="color:#334155;">{{ $log->route_name ?? '—' }}</div>
                        <div class="text-xs font-mono" style="color:#94a3b8;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->url }}">
                            {{ $log->url }}
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge text-xs" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">
                            {{ $log->status_code }}
                        </span>
                    </td>
                    <td class="text-right">
                        <span class="text-sm font-bold font-mono" style="color:{{ $durColor }};">
                            {{ $dur }}ms
                        </span>
                    </td>
                    <td class="text-right text-xs font-mono" style="color:#64748b;">
                        {{ $log->memory_mb }}MB
                    </td>
                    <td class="text-xs font-mono" style="color:#64748b;">{{ $log->ip_address }}</td>
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
