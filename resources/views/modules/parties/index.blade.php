@extends('layouts.app')
@section('title', 'External Parties')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold" style="color:#15572e;">🌐 External Parties</h1>
        <p class="text-sm mt-1" style="color:#64748b;">องค์กร/บุคคลภายนอกที่เกี่ยวข้องกับการประมวลผลข้อมูลส่วนบุคคล</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('data-map.index') }}" class="btn-outline">🗺 Data Map</a>
        <a href="{{ route('parties.create') }}" class="btn-primary">+ เพิ่ม Party</a>
    </div>
</div>

{{-- Stats ──────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card text-center cursor-pointer hover:shadow-md transition" onclick="filterType('')">
        <p class="text-3xl font-black" style="color:#15572e;">{{ $stats['total'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">ทั้งหมด</p>
    </div>
    <div class="card" style="border-left:3px solid #dc2626;">
        <p class="text-xs font-medium mb-2" style="color:#64748b;">⚠️ ต้องดำเนินการ</p>
        <div class="space-y-1 text-sm">
            <div class="flex justify-between"><span style="color:#c0272d;">ไม่มี DPA</span><strong style="color:#c0272d;">{{ $stats['no_dpa'] }}</strong></div>
            <div class="flex justify-between"><span style="color:#d97706;">DPA ใกล้หมด</span><strong style="color:#d97706;">{{ $stats['dpa_expiring'] }}</strong></div>
            <div class="flex justify-between"><span style="color:#92400e;">ค้างรีวิว</span><strong style="color:#92400e;">{{ $stats['review_overdue'] }}</strong></div>
        </div>
    </div>
    <div class="card">
        <p class="text-xs font-medium mb-2" style="color:#64748b;">แบ่งตาม Role</p>
        <div class="space-y-1 text-sm">
            <div class="flex justify-between"><span style="color:#1d4ed8;">Data Processor</span><strong>{{ $stats['processors'] }}</strong></div>
            <div class="flex justify-between"><span style="color:#15572e;">Data Controller</span><strong>{{ $stats['controllers'] }}</strong></div>
            <div class="flex justify-between"><span style="color:#7c3aed;">Joint Controller</span><strong>{{ $stats['joint'] }}</strong></div>
        </div>
    </div>
    <div class="card text-center" style="{{ $stats['cross_border'] > 0 ? 'border-left:3px solid #d97706;' : '' }}">
        <p class="text-3xl font-black" style="color:#d97706;">{{ $stats['cross_border'] }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">Cross-border Transfer</p>
    </div>
</div>

{{-- Filter ──────────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <form method="GET" action="{{ route('parties.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Relationship Type</label>
            <select name="type" class="form-input" style="min-width:160px;">
                <option value="">ทั้งหมด</option>
                @foreach(['data_processor','data_controller','joint_controller','sub_processor','recipient','third_party','supervisory_authority'] as $t)
                    <option value="{{ $t }}" {{ request('type')===$t ? 'selected' : '' }}>
                        {{ \App\Models\ExternalParty::relationshipIcon($t) }} {{ \App\Models\ExternalParty::relationshipLabel($t) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Risk Level</label>
            <select name="risk" class="form-input">
                <option value="">ทั้งหมด</option>
                @foreach(['low','medium','high','critical'] as $r)
                    <option value="{{ $r }}" {{ request('risk')===$r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">Status</label>
            <select name="status" class="form-input">
                <option value="">ทั้งหมด</option>
                @foreach(['active','inactive','under_review','suspended','terminated'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-3">
            <label class="flex items-center gap-1.5 cursor-pointer pb-1.5">
                <input type="checkbox" name="cross" value="1" {{ request('cross') ? 'checked' : '' }} class="accent-green-700">
                <span class="text-sm" style="color:#374151;">Cross-border</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer pb-1.5">
                <input type="checkbox" name="no_dpa" value="1" {{ request('no_dpa') ? 'checked' : '' }} class="accent-red-600">
                <span class="text-sm" style="color:#c0272d;">ไม่มี DPA</span>
            </label>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">ค้นหา</label>
            <input type="text" name="search" class="form-input" placeholder="ชื่อ, รหัส..." value="{{ request('search') }}" style="width:180px;">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1" style="color:#64748b;">แสดง</label>
            <select name="per_page" class="form-input" style="width:auto;" onchange="this.form.submit()">
                <option value="50"  {{ request('per_page','50')=='50' ?'selected':'' }}>50</option>
                <option value="100" {{ request('per_page')=='100' ?'selected':'' }}>100</option>
                <option value="200" {{ request('per_page')=='200' ?'selected':'' }}>200</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">🔍 ค้นหา</button>
        <a href="{{ route('parties.index') }}" class="btn-outline">รีเซ็ต</a>
    </form>
</div>

{{-- Table ────────────────────────────────────────────────────────────────── --}}
<div class="card">
    @if($parties->count() > 0)
    <p class="text-xs mb-3" style="color:#94a3b8;">
        แสดง {{ $parties->firstItem() }}–{{ $parties->lastItem() }} จากทั้งหมด <strong>{{ number_format($parties->total()) }}</strong> รายการ
    </p>
    @endif

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>รหัส</th>
                    <th>ชื่อ</th>
                    <th>Relationship Type</th>
                    <th>DPA Status</th>
                    <th>Risk</th>
                    <th class="text-center">Cross-border</th>
                    <th>Review</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($parties as $party)
                <tr>
                    <td class="text-xs font-mono" style="color:#94a3b8;">{{ $party->code ?? '—' }}</td>
                    <td>
                        <div class="font-medium text-sm" style="color:#1e293b;">{{ $party->name }}</div>
                        @if($party->name_en && $party->name_en !== $party->name)
                            <div class="text-xs" style="color:#94a3b8;">{{ $party->name_en }}</div>
                        @endif
                        @if($party->country !== 'TH')
                            <span class="text-xs" style="color:#d97706;">🌐 {{ $party->country }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge text-xs"
                              style="background:{{ \App\Models\ExternalParty::relationshipBg($party->relationship_type) }};color:{{ \App\Models\ExternalParty::relationshipColor($party->relationship_type) }};">
                            {{ \App\Models\ExternalParty::relationshipIcon($party->relationship_type) }}
                            {{ \App\Models\ExternalParty::relationshipLabel($party->relationship_type) }}
                        </span>
                    </td>
                    <td>
                        @php $dpaStatus = $party->dpa_status; @endphp
                        @if($dpaStatus === 'active')
                            <span class="badge text-xs" style="background:#dcfce7;color:#15572e;">✅ Active</span>
                            @if($party->activeDpa?->expires_at)
                                <div class="text-xs mt-0.5" style="color:#94a3b8;">
                                    ถึง {{ $party->activeDpa->expires_at->format('d/m/Y') }}
                                </div>
                            @endif
                        @elseif($dpaStatus === 'expiring')
                            <span class="badge text-xs" style="background:#fef3c7;color:#92400e;">⚠️ ใกล้หมด</span>
                        @elseif($dpaStatus === 'expired')
                            <span class="badge text-xs" style="background:#fee2e2;color:#c0272d;">❌ หมดอายุ</span>
                        @else
                            @if(in_array($party->relationship_type,['data_processor','data_controller','joint_controller']))
                                <span class="badge text-xs" style="background:#fee2e2;color:#c0272d;">⚠️ ไม่มี DPA</span>
                            @else
                                <span style="color:#e2e8f0;">—</span>
                            @endif
                        @endif
                    </td>
                    <td>
                        <span class="badge text-xs font-bold"
                              style="background:{{ \App\Models\ExternalParty::riskBg($party->risk_level) }};color:{{ \App\Models\ExternalParty::riskColor($party->risk_level) }};">
                            {{ strtoupper($party->risk_level) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($party->is_cross_border)
                            <span title="{{ implode(', ', $party->transfer_countries ?? []) }}">
                                🌐 {{ implode(', ', $party->transfer_countries ?? []) }}
                            </span>
                        @else
                            <span style="color:#e2e8f0;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($party->next_review_date)
                            <span class="text-xs {{ $party->isOverdue() ? 'font-bold' : '' }}"
                                  style="color:{{ $party->isOverdue() ? '#c0272d' : '#64748b' }};">
                                {{ $party->isOverdue() ? '⚠️ ' : '' }}{{ $party->next_review_date->format('d/m/Y') }}
                            </span>
                        @else
                            <span style="color:#e2e8f0;">—</span>
                        @endif
                    </td>
                    <td>
                        @php $sc=['active'=>['#dcfce7','#15572e'],'inactive'=>['#f1f5f9','#64748b'],'under_review'=>['#fef3c7','#92400e'],'suspended'=>['#fee2e2','#c0272d'],'terminated'=>['#f1f5f9','#94a3b8']]; [$sbg,$scol]=$sc[$party->status]??['#f1f5f9','#64748b']; @endphp
                        <span class="badge text-xs" style="background:{{ $sbg }};color:{{ $scol }};">{{ $party->status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('parties.show', $party) }}" class="btn-outline text-xs px-2 py-1">ดู</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center py-8" style="color:#94a3b8;">ไม่พบข้อมูล</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($parties->hasPages())
    <div class="mt-4 flex justify-center">{{ $parties->links() }}</div>
    @endif
</div>
@endsection
