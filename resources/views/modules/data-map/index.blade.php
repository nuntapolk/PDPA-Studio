@extends('layouts.app')
@section('title', 'Data Map — แผนที่การไหลของข้อมูล')

@push('styles')
<style>
/* ── Hub-and-spoke layout ─────────────────────────────── */
.datamap-canvas { position: relative; width: 100%; min-height: 680px; overflow: hidden; }

/* Node cards */
.ep-node {
    position: absolute;
    width: 136px;
    background: #fff;
    border-radius: 12px;
    padding: 10px 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,.08);
    border: 2px solid transparent;
    cursor: pointer;
    transition: box-shadow .2s, transform .2s;
    font-size: 12px;
    line-height: 1.4;
    z-index: 10;
}
.ep-node:hover { box-shadow: 0 4px 20px rgba(0,0,0,.15); transform: translateY(-2px); }
.ep-node.selected { box-shadow: 0 0 0 3px currentColor, 0 4px 20px rgba(0,0,0,.15); }

/* Hub (our org) */
.hub-node {
    position: absolute;
    width: 160px;
    height: 160px;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    background: linear-gradient(135deg,#15572e,#2a6b4d);
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    box-shadow: 0 4px 24px rgba(21,87,46,.35);
    z-index: 20;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    border: 3px solid #3a8762;
}

/* SVG connector lines */
.datamap-svg {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    pointer-events: none;
    z-index: 5;
}
.flow-line { stroke-dasharray: 6 4; animation: dash 1s linear infinite; }
@keyframes dash { to { stroke-dashoffset: -20; } }

/* Detail panel */
.detail-panel {
    position: absolute;
    right: 16px;
    top: 16px;
    width: 280px;
    background: #fff;
    border-radius: 14px;
    padding: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,.12);
    z-index: 30;
    max-height: calc(100% - 32px);
    overflow-y: auto;
}

/* Legend */
.legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

/* Risk glow */
.risk-critical { box-shadow: 0 0 0 2px #dc2626, 0 2px 10px rgba(0,0,0,.08); }
.risk-high      { box-shadow: 0 0 0 2px #d97706, 0 2px 10px rgba(0,0,0,.08); }

/* Switch tabs */
.view-tab { padding: 6px 16px; border-radius: 8px; font-size: 13px; cursor: pointer; transition: background .15s; }
.view-tab.active { background: #15572e; color: #fff; }
.view-tab:not(.active) { color: #64748b; }
.view-tab:not(.active):hover { background: #f1f5f9; }
</style>
@endpush

@section('content')
{{-- Page header ────────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6" x-data="dataMap()">
    <div>
        <h1 class="text-2xl font-bold" style="color:#15572e;">🗺️ Data Map</h1>
        <p class="text-sm mt-0.5" style="color:#64748b;">แผนที่การไหลของข้อมูลส่วนบุคคลระหว่างองค์กร</p>
    </div>
    <div class="flex items-center gap-2">
        <div class="flex gap-1 p-1 rounded-lg" style="background:#f1f5f9;">
            <button @click="viewMode='map'" :class="viewMode==='map'?'active':''" class="view-tab">🗺️ Map</button>
            <button @click="viewMode='table'" :class="viewMode==='table'?'active':''" class="view-tab">📋 Table</button>
            <button @click="viewMode='flows'" :class="viewMode==='flows'?'active':''" class="view-tab">🔀 Data Flows</button>
        </div>
        <a href="{{ route('parties.create') }}" class="btn-primary text-sm">+ เพิ่ม Party</a>
    </div>
</div>

{{-- Stats ──────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-5 gap-3 mb-6">
    @php
    $statCards = [
        ['total_parties',   'External Parties ทั้งหมด', '#1d4ed8','#dbeafe','🏢'],
        ['with_active_dpa', 'มี Active DPA',            '#15572e','#dcfce7','📄'],
        ['cross_border',    'Cross-border Transfer',    '#d97706','#fef3c7','🌐'],
        ['high_risk',       'ความเสี่ยงสูง/วิกฤต',     '#c0272d','#fee2e2','⚠️'],
        ['no_dpa',          'ไม่มี DPA (ต้องการ)',      '#64748b','#f1f5f9','🔴'],
    ];
    @endphp
    @foreach($statCards as [$key,$label,$color,$bg,$icon])
    <div class="card text-center py-4">
        <div class="text-2xl font-bold" style="color:{{ $color }};">{{ $stats[$key] }}</div>
        <div class="text-xs mt-1" style="color:#64748b;">{{ $icon }} {{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Main content ────────────────────────────────────────────────────────── --}}
<div x-data="dataMap()">

{{-- ══ MAP VIEW ═══════════════════════════════════════════════════════════ --}}
<div x-show="viewMode==='map'">
    <div class="card p-0 overflow-hidden">
        {{-- Legend bar --}}
        <div class="flex items-center gap-6 px-5 py-3 flex-wrap" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
            @foreach([
                ['data_processor','⚙️ Data Processor','#1d4ed8'],
                ['data_controller','🏢 Data Controller','#15572e'],
                ['joint_controller','🤝 Joint Controller','#7c3aed'],
                ['sub_processor','🔗 Sub-Processor','#0369a1'],
                ['recipient','📤 Recipient','#d97706'],
                ['third_party','👥 Third Party','#64748b'],
                ['supervisory_authority','⚖️ Supervisory','#c0272d'],
            ] as [$type,$label,$color])
            @if(isset($grouped[$type]) && $grouped[$type]->count() > 0)
            <div class="flex items-center gap-1.5 text-xs">
                <div class="legend-dot" style="background:{{ $color }};"></div>
                <span style="color:#374151;">{{ $label }}</span>
                <span class="font-bold" style="color:{{ $color }};">{{ $grouped[$type]->count() }}</span>
            </div>
            @endif
            @endforeach
            <div class="ml-auto flex items-center gap-3 text-xs" style="color:#94a3b8;">
                <span>🔴 Critical/High risk</span>
                <span>🌐 Cross-border</span>
            </div>
        </div>

        {{-- Canvas --}}
        <div class="datamap-canvas p-6" id="mapCanvas" style="min-height:700px;">
            {{-- SVG for connector lines --}}
            <svg class="datamap-svg" id="mapSvg" xmlns="http://www.w3.org/2000/svg"></svg>

            {{-- Hub: YOUR ORG --}}
            <div class="hub-node" id="hubNode">
                <div style="font-size:22px;margin-bottom:4px;">🏛️</div>
                <div style="font-size:13px;">{{ $org?->name ?? 'องค์กรของเรา' }}</div>
                <div style="font-size:10px;opacity:.8;margin-top:2px;">
                    @if($org?->primary_pdpa_role === 'both') DC + DP
                    @elseif($org?->primary_pdpa_role === 'controller') Data Controller
                    @else Data Processor @endif
                </div>
            </div>

            {{-- Party nodes (positioned by JS) --}}
            @foreach($flows as $p)
            @php
            $nodeColors = [
                'data_processor'       => ['#1d4ed8','#dbeafe'],
                'data_controller'      => ['#15572e','#dcfce7'],
                'joint_controller'     => ['#7c3aed','#ede9fe'],
                'sub_processor'        => ['#0369a1','#e0f2fe'],
                'recipient'            => ['#d97706','#fef3c7'],
                'third_party'          => ['#64748b','#f1f5f9'],
                'supervisory_authority'=> ['#c0272d','#fde8d8'],
            ];
            [$nc,$nb] = $nodeColors[$p['type']] ?? ['#64748b','#f1f5f9'];
            $riskClass = in_array($p['risk'],['critical']) ? 'risk-critical' : (in_array($p['risk'],['high']) ? 'risk-high' : '');
            @endphp
            <div class="ep-node {{ $riskClass }}"
                 id="node-{{ $p['id'] }}"
                 data-id="{{ $p['id'] }}"
                 data-type="{{ $p['type'] }}"
                 style="border-color:{{ $nc }};background:{{ $nb }};"
                 @click="selectParty({{ $p['id'] }})"
                 :class="selectedId === {{ $p['id'] }} ? 'selected' : ''">
                <div class="font-semibold truncate" style="color:{{ $nc }};font-size:11px;">
                    {{ Str::limit($p['name'],18) }}
                </div>
                @if($p['code'])
                <div style="color:#94a3b8;font-size:10px;">{{ $p['code'] }}</div>
                @endif
                <div class="flex items-center gap-1 mt-1 flex-wrap">
                    @if($p['cross'])
                    <span style="background:#fff7ed;color:#c2410c;font-size:9px;padding:1px 4px;border-radius:4px;">🌐 Cross</span>
                    @endif
                    @if($p['dpa_status'] === 'none' && in_array($p['type'],['data_processor','data_controller','joint_controller']))
                    <span style="background:#fee2e2;color:#c0272d;font-size:9px;padding:1px 4px;border-radius:4px;">⚠️ No DPA</span>
                    @elseif($p['dpa_status'] === 'active')
                    <span style="background:#dcfce7;color:#15572e;font-size:9px;padding:1px 4px;border-radius:4px;">✓ DPA</span>
                    @elseif($p['dpa_status'] === 'expiring')
                    <span style="background:#fef3c7;color:#d97706;font-size:9px;padding:1px 4px;border-radius:4px;">⏰ Exp.</span>
                    @endif
                    @if(in_array($p['risk'],['high','critical']))
                    <span style="background:#fee2e2;color:#c0272d;font-size:9px;padding:1px 4px;border-radius:4px;">{{ strtoupper($p['risk']) }}</span>
                    @endif
                </div>
            </div>
            @endforeach

            {{-- Detail panel (Alpine) --}}
            <div class="detail-panel" x-show="selectedId !== null" x-cloak>
                <template x-if="selectedParty">
                    <div>
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-bold text-sm" style="color:#15572e;" x-text="selectedParty.name"></h3>
                                <div class="text-xs mt-0.5" style="color:#94a3b8;" x-text="selectedParty.code || ''"></div>
                            </div>
                            <button @click="selectedId=null" class="text-gray-400 hover:text-gray-600 text-lg leading-none">×</button>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex items-center gap-2">
                                <span style="color:#64748b;">ความสัมพันธ์:</span>
                                <span class="font-medium" :style="'color:'+typeColor(selectedParty.type)" x-text="typeLabel(selectedParty.type)"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span style="color:#64748b;">Risk Level:</span>
                                <span class="font-bold uppercase" :style="'color:'+riskColor(selectedParty.risk)" x-text="selectedParty.risk"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span style="color:#64748b;">DPA Status:</span>
                                <span class="font-medium" x-text="dpaLabel(selectedParty.dpa_status)"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span style="color:#64748b;">สถานะ:</span>
                                <span x-text="selectedParty.status" class="capitalize"></span>
                            </div>
                            <template x-if="selectedParty.cross">
                                <div class="rounded-lg p-2 mt-2" style="background:#fff7ed;">
                                    <div class="font-medium" style="color:#c2410c;">🌐 Cross-border Transfer</div>
                                    <div style="color:#92400e;" x-text="selectedParty.countries.join(', ') || '—'"></div>
                                </div>
                            </template>
                            <template x-if="selectedParty.data_types && selectedParty.data_types.length > 0">
                                <div class="mt-2">
                                    <div class="font-medium mb-1" style="color:#374151;">ประเภทข้อมูล:</div>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="dt in selectedParty.data_types">
                                            <span class="px-1.5 py-0.5 rounded text-xs" style="background:#f1f5f9;color:#374151;" x-text="dt"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <a :href="'/parties/'+selectedParty.id" class="btn-primary text-xs flex-1 text-center py-1.5">ดูรายละเอียด</a>
                            <a :href="'/parties/'+selectedParty.id+'/edit'" class="btn-outline text-xs flex-1 text-center py-1.5">แก้ไข</a>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

{{-- ══ TABLE VIEW ══════════════════════════════════════════════════════════ --}}
<div x-show="viewMode==='table'" x-cloak>
    <div class="card p-0">
        <table class="w-full text-sm">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                    <th class="text-left px-4 py-3 font-semibold" style="color:#374151;">ชื่อ / Code</th>
                    <th class="text-left px-4 py-3 font-semibold" style="color:#374151;">Relationship</th>
                    <th class="text-left px-4 py-3 font-semibold" style="color:#374151;">Risk</th>
                    <th class="text-left px-4 py-3 font-semibold" style="color:#374151;">DPA</th>
                    <th class="text-left px-4 py-3 font-semibold" style="color:#374151;">Cross-border</th>
                    <th class="text-left px-4 py-3 font-semibold" style="color:#374151;">สถานะ</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($flows as $p)
                @php
                $rc = ['low'=>['#15572e','#dcfce7'],'medium'=>['#d97706','#fef3c7'],'high'=>['#c0272d','#fee2e2'],'critical'=>['#7f1d1d','#fde8d8']][$p['risk']] ?? ['#64748b','#f1f5f9'];
                $tc = ['data_processor'=>'#1d4ed8','data_controller'=>'#15572e','joint_controller'=>'#7c3aed','sub_processor'=>'#0369a1','recipient'=>'#d97706','third_party'=>'#64748b','supervisory_authority'=>'#c0272d'][$p['type']] ?? '#64748b';
                $dpaBg = ['active'=>['#dcfce7','#15572e'],'expiring'=>['#fef3c7','#d97706'],'expired'=>['#fee2e2','#c0272d'],'none'=>['#f1f5f9','#64748b']][$p['dpa_status']] ?? ['#f1f5f9','#64748b'];
                @endphp
                <tr style="border-bottom:1px solid #f1f5f9;" class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium" style="color:#1e293b;">{{ $p['name'] }}</div>
                        @if($p['code'])<div class="text-xs font-mono" style="color:#94a3b8;">{{ $p['code'] }}</div>@endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs font-medium" style="color:{{ $tc }};">
                            {{ \App\Models\ExternalParty::relationshipLabel($p['type']) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold" style="background:{{ $rc[1] }};color:{{ $rc[0] }};">
                            {{ strtoupper($p['risk']) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background:{{ $dpaBg[0] }};color:{{ $dpaBg[1] }};">
                            {{ ['active'=>'✓ Active','expiring'=>'⏰ Expiring','expired'=>'✗ Expired','none'=>'— ไม่มี'][$p['dpa_status']] ?? $p['dpa_status'] }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($p['cross'])
                        <span class="text-xs" style="color:#c2410c;">🌐 {{ implode(', ',$p['countries']) }}</span>
                        @else
                        <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs capitalize" style="color:#64748b;">{{ $p['status'] }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('parties.show', $p['id']) }}" class="text-xs" style="color:#1d4ed8;">ดู</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center" style="color:#94a3b8;">ยังไม่มี External Party</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══ DATA FLOWS VIEW ═════════════════════════════════════════════════════ --}}
<div x-show="viewMode==='flows'" x-cloak>
    @if($ropaLinks->count() > 0)
    <div class="space-y-4">
        @foreach($ropaLinks->groupBy('process_name') as $process => $links)
        <div class="card">
            <h3 class="font-semibold mb-3" style="color:#15572e;">📋 {{ $process }}</h3>
            <div class="space-y-2">
                @foreach($links as $link)
                @php
                $rtc = ['data_processor'=>'#1d4ed8','data_controller'=>'#15572e','joint_controller'=>'#7c3aed','sub_processor'=>'#0369a1','recipient'=>'#d97706','third_party'=>'#64748b','supervisory_authority'=>'#c0272d'][$link->relationship_type] ?? '#64748b';
                $roles = ['recipient'=>'📤 Recipient','processor'=>'⚙️ Processor','source'=>'📥 Source','joint_controller'=>'🤝 Joint Ctrl'];
                @endphp
                <div class="flex items-center gap-3 p-2 rounded-lg text-sm" style="background:#f8fafc;">
                    <div class="text-center" style="min-width:120px;">
                        <div class="font-semibold" style="color:#15572e;">{{ $org?->name ?? 'เรา' }}</div>
                        <div class="text-xs" style="color:#94a3b8;">ผู้ควบคุม/ประมวลผล</div>
                    </div>
                    <div class="flex-1 flex items-center justify-center gap-2">
                        <div class="flex-1 border-t-2 border-dashed" style="border-color:#d1d5db;"></div>
                        <div class="px-2 py-0.5 rounded text-xs font-medium" style="color:{{ $rtc }};background:#f8fafc;border:1px solid {{ $rtc }};">
                            {{ $roles[$link->party_role] ?? $link->party_role }}
                        </div>
                        @if($link->party_role === 'source')
                        <div class="text-gray-400">←</div>
                        @else
                        <div class="text-gray-400">→</div>
                        @endif
                        <div class="flex-1 border-t-2 border-dashed" style="border-color:#d1d5db;"></div>
                    </div>
                    <div class="text-center" style="min-width:120px;">
                        <div class="font-semibold" style="color:{{ $rtc }};">{{ $link->party_name }}</div>
                        <div class="text-xs" style="color:#94a3b8;">{{ \App\Models\ExternalParty::relationshipLabel($link->relationship_type) }}</div>
                    </div>
                    @if($link->data_categories)
                    @php $cats = is_string($link->data_categories) ? json_decode($link->data_categories,true) : (array)$link->data_categories; @endphp
                    <div class="flex flex-wrap gap-1 max-w-32">
                        @foreach(array_slice($cats,0,3) as $cat)
                        <span class="text-xs px-1.5 py-0.5 rounded" style="background:#e2e8f0;color:#475569;">{{ $cat }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card text-center py-16">
        <div style="font-size:48px;">🔀</div>
        <p class="mt-3 font-medium" style="color:#374151;">ยังไม่มี Data Flow Records</p>
        <p class="text-sm mt-1" style="color:#94a3b8;">เชื่อม ROPA กับ External Party เพื่อดู Flow ที่นี่</p>
        <a href="{{ route('ropa.index') }}" class="btn-primary inline-block mt-4">ไปที่ ROPA</a>
    </div>
    @endif
</div>

</div>{{-- /Alpine data-map --}}
@endsection

@push('scripts')
<script>
const FLOWS = @json($flows);

function dataMap() {
    return {
        viewMode: 'map',
        selectedId: null,
        get selectedParty() {
            if (!this.selectedId) return null;
            return FLOWS.find(f => f.id === this.selectedId) || null;
        },
        selectParty(id) {
            this.selectedId = this.selectedId === id ? null : id;
        },
        typeLabel(t) {
            const m = {
                data_processor: '⚙️ Data Processor',
                data_controller: '🏢 Data Controller',
                joint_controller: '🤝 Joint Controller',
                sub_processor: '🔗 Sub-Processor',
                recipient: '📤 Recipient',
                third_party: '👥 Third Party',
                supervisory_authority: '⚖️ Supervisory Authority',
            };
            return m[t] || t;
        },
        typeColor(t) {
            const m = { data_processor:'#1d4ed8', data_controller:'#15572e', joint_controller:'#7c3aed',
                        sub_processor:'#0369a1', recipient:'#d97706', third_party:'#64748b',
                        supervisory_authority:'#c0272d' };
            return m[t] || '#64748b';
        },
        riskColor(r) {
            return { low:'#15572e', medium:'#d97706', high:'#c0272d', critical:'#7f1d1d' }[r] || '#64748b';
        },
        dpaLabel(s) {
            return { active:'✅ Active', expiring:'⏰ กำลังจะหมด', expired:'❌ หมดอายุ', none:'— ไม่มี DPA' }[s] || s;
        },
    };
}

// ── Position nodes in hub-and-spoke layout ────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('mapCanvas');
    const svg    = document.getElementById('mapSvg');
    if (!canvas || !svg) return;

    // Group nodes by type
    const typeOrder = ['data_processor','sub_processor','data_controller','joint_controller','recipient','third_party','supervisory_authority'];
    const grouped = {};
    typeOrder.forEach(t => grouped[t] = []);
    FLOWS.forEach(f => { if (grouped[f.type]) grouped[f.type].push(f.id); });

    const allNodes = [];
    typeOrder.forEach(t => grouped[t].forEach(id => allNodes.push({ id, type: t })));
    const total = allNodes.length;
    if (total === 0) return;

    // Canvas dimensions
    const W = canvas.clientWidth  || 900;
    const H = canvas.clientHeight || 700;
    const cx = W / 2;
    const cy = H / 2;
    const nodeW = 136, nodeH = 68;

    // Orbit radii (inner ring: 4, outer ring: rest)
    const innerCount = Math.min(total, 6);
    const outerCount = total - innerCount;
    const R1 = Math.min(W, H) * 0.30;
    const R2 = Math.min(W, H) * 0.45;

    const colorMap = {
        data_processor:'#1d4ed8', data_controller:'#15572e', joint_controller:'#7c3aed',
        sub_processor:'#0369a1', recipient:'#d97706', third_party:'#64748b',
        supervisory_authority:'#c0272d'
    };

    allNodes.forEach((n, i) => {
        const isInner = i < innerCount;
        const idx     = isInner ? i : i - innerCount;
        const cnt     = isInner ? innerCount : outerCount;
        const r       = isInner ? R1 : R2;
        const angle   = (2 * Math.PI * idx / cnt) - Math.PI / 2;

        const nx = cx + r * Math.cos(angle) - nodeW / 2;
        const ny = cy + r * Math.sin(angle) - nodeH / 2;

        const el = document.getElementById('node-' + n.id);
        if (el) {
            el.style.left = Math.max(4, Math.min(W - nodeW - 4, nx)) + 'px';
            el.style.top  = Math.max(4, Math.min(H - nodeH - 4, ny)) + 'px';
        }

        // Draw connector line
        const lineX = cx + r * Math.cos(angle);
        const lineY = cy + r * Math.sin(angle);
        const color = colorMap[n.type] || '#94a3b8';

        const line = document.createElementNS('http://www.w3.org/2000/svg','line');
        line.setAttribute('x1', cx);
        line.setAttribute('y1', cy);
        line.setAttribute('x2', lineX);
        line.setAttribute('y2', lineY);
        line.setAttribute('stroke', color);
        line.setAttribute('stroke-width', '1.5');
        line.setAttribute('opacity', '0.4');
        line.classList.add('flow-line');
        svg.appendChild(line);
    });

    // Adjust SVG viewBox
    svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
});
</script>
@endpush
