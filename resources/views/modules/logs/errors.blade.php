@extends('layouts.app')
@section('title', 'System Error Log')

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
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#dc2626;">{{ $stats['errors_today'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">Errors วันนี้</p>
    </div>
    <div class="card" style="grid-column:span 2;">
        <h4 class="text-xs font-medium mb-2" style="color:#64748b;">แบ่งตาม Level</h4>
        <div class="flex gap-3 flex-wrap">
        @foreach(['emergency','critical','alert','error','warning','notice','info','debug'] as $lv)
        @php $cnt = $stats['by_level'][$lv] ?? 0; if(!$cnt) continue; @endphp
        <span class="badge text-xs font-bold" style="background:{{ \App\Models\SystemErrorLog::levelBg($lv) }};color:{{ \App\Models\SystemErrorLog::levelColor($lv) }};">
            {{ strtoupper($lv) }} {{ $cnt }}
        </span>
        @endforeach
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" action="{{ route('logs.errors') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Level</label>
            <select name="level" class="form-input">
                <option value="">ทั้งหมด</option>
                @foreach(['emergency','critical','alert','error','warning','notice','info','debug'] as $lv)
                    <option value="{{ $lv }}" {{ request('level')===$lv ? 'selected' : '' }}>{{ strtoupper($lv) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Channel</label>
            <select name="channel" class="form-input">
                <option value="">ทั้งหมด</option>
                @foreach(['app','queue','api','auth','scheduler'] as $ch)
                    <option value="{{ $ch }}" {{ request('channel')===$ch ? 'selected' : '' }}>{{ $ch }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <label class="flex items-center gap-2 cursor-pointer pb-1">
                <input type="checkbox" name="unresolved" value="1" {{ request('unresolved') ? 'checked' : '' }} class="accent-green-700">
                <span class="text-sm" style="color:#374151;">เฉพาะที่ยังไม่แก้</span>
            </label>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ค้นหา</label>
            <input type="text" name="search" class="form-input" placeholder="error message..." value="{{ request('search') }}" style="width:200px;">
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
        <a href="{{ route('logs.errors') }}" class="btn-outline">รีเซ็ต</a>
    </form>
</div>

{{-- Table --}}
<div class="card">
    @if($logs->count() > 0)
    <p class="text-xs mb-3" style="color:#94a3b8;">
        แสดง {{ $logs->firstItem() }}–{{ $logs->lastItem() }} จากทั้งหมด <strong>{{ number_format($logs->total()) }}</strong> รายการ
    </p>
    @endif

    <div class="space-y-3">
    @forelse($logs as $log)
        <div class="rounded-lg overflow-hidden" style="border:1px solid {{ \App\Models\SystemErrorLog::levelColor($log->level) }}40;">
            {{-- Header --}}
            <div class="flex items-start justify-between p-3 gap-3" style="background:{{ \App\Models\SystemErrorLog::levelBg($log->level) }};">
                <div class="flex items-start gap-3 flex-1 min-w-0">
                    <span class="badge text-xs font-bold flex-shrink-0" style="background:{{ \App\Models\SystemErrorLog::levelColor($log->level) }};color:#fff;">
                        {{ strtoupper($log->level) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium break-words" style="color:#1e293b;">{{ Str::limit($log->message, 120) }}</p>
                        @if($log->exception_class)
                        <p class="text-xs font-mono mt-0.5" style="color:#64748b;">{{ $log->exception_class }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <div class="text-right">
                        <p class="text-xs" style="color:#64748b;white-space:nowrap;">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                        @if($log->occurrence_count > 1)
                        <p class="text-xs font-bold" style="color:#c0272d;">เกิดซ้ำ {{ $log->occurrence_count }}x</p>
                        @endif
                    </div>
                    @if($log->is_resolved)
                        <span class="badge text-xs" style="background:#dcfce7;color:#15572e;">✅ แก้แล้ว</span>
                    @else
                        <form method="POST" action="{{ route('logs.errors.resolve', $log) }}" class="flex items-center gap-1">
                            @csrf
                            <input type="text" name="note" class="form-input text-xs" placeholder="หมายเหตุ..." style="width:150px;padding:4px 8px;">
                            <button type="submit" class="text-xs px-2 py-1 rounded border" style="border-color:#15572e;color:#15572e;white-space:nowrap;">✓ แก้แล้ว</button>
                        </form>
                    @endif
                </div>
            </div>
            {{-- Detail --}}
            <div class="px-3 py-2 text-xs" style="background:#fff;border-top:1px solid {{ \App\Models\SystemErrorLog::levelColor($log->level) }}20;">
                <div class="flex gap-4 flex-wrap" style="color:#64748b;">
                    @if($log->file)
                    <span>📁 {{ $log->file }}:{{ $log->line }}</span>
                    @endif
                    @if($log->channel)
                    <span>📡 {{ $log->channel }}</span>
                    @endif
                    @if($log->request_url)
                    <span>🔗 {{ $log->request_method }} {{ Str::limit($log->request_url, 60) }}</span>
                    @endif
                    @if($log->ip_address)
                    <span>🌐 {{ $log->ip_address }}</span>
                    @endif
                </div>
                @if($log->stack_trace)
                <details class="mt-2">
                    <summary class="cursor-pointer" style="color:#64748b;">Stack Trace</summary>
                    <pre class="mt-2 text-xs overflow-x-auto p-2 rounded" style="background:#f8fafc;color:#334155;max-height:200px;white-space:pre-wrap;">{{ $log->stack_trace }}</pre>
                </details>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-12" style="color:#94a3b8;">
            <div style="font-size:3rem;">✅</div>
            <p class="mt-2">ไม่พบ Error ในช่วงที่เลือก</p>
        </div>
    @endforelse
    </div>

    @if($logs->hasPages())
    <div class="mt-4 flex justify-center">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
