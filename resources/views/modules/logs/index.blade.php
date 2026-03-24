@extends('layouts.app')
@section('title', 'Audit Log')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold" style="color:#15572e;">📋 System Logs</h1>
</div>

@include('modules.logs._tabs')

{{-- Stats --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#15572e;">{{ number_format($stats['total_today']) }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">Actions วันนี้</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#0f3020;">{{ $stats['unique_users'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">ผู้ใช้ที่ Active</p>
    </div>
    @foreach($stats['actions_by_type']->take(2) as $action => $count)
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#1d4ed8;">{{ number_format($count) }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">{{ $action }}</p>
    </div>
    @endforeach
</div>

{{-- Filter --}}
<div class="card mb-4">
    <form method="GET" action="{{ route('logs.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Module</label>
            <select name="module" class="form-input" style="min-width:140px;">
                <option value="">ทั้งหมด</option>
                @foreach($modules as $m)
                    <option value="{{ $m }}" {{ request('module')===$m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Action</label>
            <select name="action" class="form-input" style="min-width:130px;">
                <option value="">ทั้งหมด</option>
                @foreach($actions as $a)
                    <option value="{{ $a }}" {{ request('action')===$a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
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
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ค้นหา</label>
            <input type="text" name="search" class="form-input" placeholder="ชื่อ, URL..." value="{{ request('search') }}">
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
        <a href="{{ route('logs.index') }}" class="btn-outline">รีเซ็ต</a>
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
                    <th style="width:140px;">เวลา</th>
                    <th>ผู้ใช้</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Entity</th>
                    <th>IP</th>
                    <th>เปลี่ยนแปลง</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                @php
                $actionColors = [
                    'created'=>['#dcfce7','#15572e'], 'updated'=>['#dbeafe','#1d4ed8'],
                    'deleted'=>['#fee2e2','#c0272d'],  'viewed'=>['#f1f5f9','#64748b'],
                    'exported'=>['#fef3c7','#92400e'], 'login'=>['#d1fae5','#065f46'],
                    'logout'=>['#f1f5f9','#475569'],   'approved'=>['#dcfce7','#15572e'],
                    'published'=>['#d1fae5','#065f46'],'withdrawn'=>['#fee2e2','#c0272d'],
                ];
                [$bg,$color] = $actionColors[$log->action] ?? ['#f1f5f9','#475569'];
                @endphp
                <tr>
                    <td class="text-xs" style="color:#64748b;white-space:nowrap;">
                        {{ $log->created_at->format('d/m/Y') }}<br>
                        <span style="color:#94a3b8;">{{ $log->created_at->format('H:i:s') }}</span>
                    </td>
                    <td>
                        <div class="text-sm font-medium" style="color:#1e293b;">{{ $log->user_name ?? '—' }}</div>
                        @if($log->user)
                            <div class="text-xs" style="color:#94a3b8;">{{ $log->user->email }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge text-xs font-medium" style="background:{{ $bg }};color:{{ $color }};">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td><span class="text-sm" style="color:#475569;">{{ $log->module }}</span></td>
                    <td>
                        @if($log->entity_name)
                            <span class="text-sm font-medium" style="color:#334155;">{{ Str::limit($log->entity_name, 30) }}</span>
                            @if($log->entity_id)
                                <span class="text-xs ml-1" style="color:#94a3b8;">#{{ $log->entity_id }}</span>
                            @endif
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td class="text-xs font-mono" style="color:#64748b;">{{ $log->ip_address ?? '—' }}</td>
                    <td>
                        @if($log->old_values || $log->new_values)
                            <div class="text-xs" style="color:#64748b;max-width:200px;">
                                @if($log->old_values)
                                    <span style="color:#c0272d;">−</span>
                                    {{ implode(', ', array_keys($log->old_values)) }}
                                @endif
                                @if($log->new_values)
                                    <span style="color:#15572e;">+</span>
                                    {{ implode(', ', array_keys($log->new_values)) }}
                                @endif
                            </div>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-8" style="color:#94a3b8;">ไม่พบข้อมูล</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="mt-4 flex justify-center">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
