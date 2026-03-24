@extends('layouts.app')
@section('title', $party->name)
@section('content')

<div class="flex items-center gap-2 mb-6">
    <a href="{{ route('parties.index') }}" class="btn-outline text-sm">← กลับ</a>
    <h1 class="text-xl font-bold" style="color:#15572e;">{{ $party->name }}</h1>
    <span class="badge text-xs" style="background:{{ \App\Models\ExternalParty::relationshipBg($party->relationship_type) }};color:{{ \App\Models\ExternalParty::relationshipColor($party->relationship_type) }};">
        {{ \App\Models\ExternalParty::relationshipIcon($party->relationship_type) }} {{ \App\Models\ExternalParty::relationshipLabel($party->relationship_type) }}
    </span>
</div>

<div class="grid gap-6" style="grid-template-columns:1fr 320px;">
{{-- LEFT ────────────────────────────────────────────────────────────────── --}}
<div class="space-y-6">

    {{-- Overview Card --}}
    <div class="card" style="border-top:4px solid {{ \App\Models\ExternalParty::riskColor($party->risk_level) }};">
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <p class="text-xs mb-1" style="color:#94a3b8;">ประเภทองค์กร</p>
                <p class="font-medium" style="color:#334155;">{{ ucfirst($party->type) }}</p>
            </div>
            <div>
                <p class="text-xs mb-1" style="color:#94a3b8;">ประเทศ</p>
                <p class="font-medium" style="color:#334155;">{{ $party->country }} {{ $party->is_cross_border ? '🌐' : '' }}</p>
            </div>
            <div>
                <p class="text-xs mb-1" style="color:#94a3b8;">อุตสาหกรรม</p>
                <p class="font-medium" style="color:#334155;">{{ $party->industry ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs mb-1" style="color:#94a3b8;">Risk Level</p>
                <span class="badge font-bold" style="background:{{ \App\Models\ExternalParty::riskBg($party->risk_level) }};color:{{ \App\Models\ExternalParty::riskColor($party->risk_level) }};">
                    {{ strtoupper($party->risk_level) }}
                </span>
            </div>
            <div>
                <p class="text-xs mb-1" style="color:#94a3b8;">Status</p>
                <p class="font-medium" style="color:#334155;">{{ $party->status }}</p>
            </div>
            <div>
                <p class="text-xs mb-1" style="color:#94a3b8;">เริ่มสัมพันธ์</p>
                <p class="font-medium" style="color:#334155;">{{ $party->relationship_started_at?->format('d/m/Y') ?? '—' }}</p>
            </div>
        </div>
        @if($party->services_description)
            <p class="text-sm" style="color:#475569;">{{ $party->services_description }}</p>
        @endif
        @if($party->data_types_shared)
        <div class="mt-3 flex flex-wrap gap-1">
            @foreach($party->data_types_shared as $dt)
                <span class="badge text-xs" style="background:#f1f5f9;color:#475569;">{{ $dt }}</span>
            @endforeach
        </div>
        @endif
        @if($party->is_cross_border)
        <div class="mt-3 p-3 rounded-lg text-sm" style="background:#fff7ed;border:1px solid #fed7aa;">
            🌐 <strong>Cross-border Transfer</strong> → {{ implode(', ',$party->transfer_countries??[]) }}
            | กลไก: {{ str_replace('_',' ',strtoupper($party->transfer_mechanism??'')) }}
            @if($party->tia_required) | TIA: {{ $party->tia_completed_at ? '✅ '.$party->tia_completed_at->format('d/m/Y') : '⚠️ ยังไม่ทำ' }} @endif
        </div>
        @endif
    </div>

    {{-- DPA Section --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold" style="color:#15572e;">📄 Data Processing Agreements</h3>
        </div>
        @if($party->dpas->isEmpty())
            @if(in_array($party->relationship_type,['data_processor','data_controller','joint_controller']))
            <div class="p-4 rounded-lg" style="background:#fff1f2;border:1px solid #fecdd3;">
                <p class="text-sm font-medium" style="color:#c0272d;">⚠️ ยังไม่มี DPA — ต้องทำ DPA ก่อนส่งข้อมูล</p>
            </div>
            @endif
        @else
            <div class="space-y-3 mb-4">
            @foreach($party->dpas as $dpa)
                <div class="rounded-lg p-4" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-medium text-sm" style="color:#1e293b;">{{ $dpa->title }}</p>
                            <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $dpa->dpa_number }} | {{ \App\Models\DataProcessingAgreement::typeLabel($dpa->type) }} | v{{ $dpa->version }}</p>
                            <p class="text-xs mt-1" style="color:#64748b;">
                                เรา: <strong>{{ $dpa->our_role }}</strong> | เขา: <strong>{{ $dpa->their_role }}</strong>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="badge text-xs font-bold" style="background:{{ \App\Models\DataProcessingAgreement::statusBg($dpa->status) }};color:{{ \App\Models\DataProcessingAgreement::statusColor($dpa->status) }};">
                                {{ strtoupper($dpa->status) }}
                            </span>
                            @if($dpa->expires_at)
                            <p class="text-xs mt-1" style="color:{{ $dpa->isExpiringSoon() ? '#d97706' : '#94a3b8' }};">
                                {{ $dpa->isExpiringSoon() ? '⚠️ ' : '' }}หมด {{ $dpa->expires_at->format('d/m/Y') }}
                                @if($dpa->getDaysUntilExpiry() !== null)
                                    ({{ $dpa->getDaysUntilExpiry() }} วัน)
                                @endif
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endif

        {{-- Add DPA form (collapsible) --}}
        <details class="mt-2">
            <summary class="cursor-pointer text-sm font-medium" style="color:#15572e;">+ เพิ่ม DPA ใหม่</summary>
            <form method="POST" action="{{ route('parties.dpa.store', $party) }}" class="mt-3 space-y-3">
            @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium mb-1" style="color:#374151;">ชื่อ DPA <span style="color:#c0272d;">*</span></label>
                        <input type="text" name="title" class="form-input" required placeholder="เช่น DPA v2.0 2025">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#374151;">ประเภท</label>
                        <select name="type" class="form-input">
                            @foreach(['dpa'=>'DPA','jca'=>'JCA','addendum'=>'Addendum','nda'=>'NDA','data_sharing_agreement'=>'DSA'] as $v=>$l)
                                <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#374151;">สถานะ</label>
                        <select name="status" class="form-input">
                            @foreach(['draft','pending_signature','active'] as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#374151;">Role เรา</label>
                        <select name="our_role" class="form-input">
                            <option value="controller">Controller</option>
                            <option value="processor">Processor</option>
                            <option value="joint_controller">Joint Controller</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#374151;">Role เขา</label>
                        <select name="their_role" class="form-input">
                            <option value="processor">Processor</option>
                            <option value="controller">Controller</option>
                            <option value="joint_controller">Joint Controller</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#374151;">วันที่ลงนาม</label>
                        <input type="date" name="signed_at" class="form-input">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1" style="color:#374151;">วันหมดอายุ</label>
                        <input type="date" name="expires_at" class="form-input">
                    </div>
                </div>
                <button type="submit" class="btn-primary text-sm">บันทึก DPA</button>
            </form>
        </details>
    </div>

    {{-- Assessment History --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold" style="color:#15572e;">📊 ผลการประเมินความเสี่ยง</h3>
        </div>
        @forelse($party->assessments as $a)
        <div class="rounded-lg p-4 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium" style="color:#334155;">{{ ucfirst($a->assessment_type) }} Assessment</p>
                    <p class="text-xs mt-0.5" style="color:#94a3b8;">{{ $a->assessor->name ?? '—' }} | {{ $a->created_at->format('d/m/Y') }}</p>
                    @if($a->findings) <p class="text-xs mt-1" style="color:#64748b;">{{ Str::limit($a->findings,100) }}</p> @endif
                </div>
                <div class="text-center">
                    <p class="text-2xl font-black" style="color:{{ \App\Models\ExternalPartyAssessment::riskColor($a->risk_level) }};">{{ $a->score }}</p>
                    <p class="text-xs" style="color:#94a3b8;">/100</p>
                </div>
            </div>
        </div>
        @empty
            <p class="text-sm" style="color:#94a3b8;">ยังไม่มีการประเมิน</p>
        @endforelse

        <details class="mt-2">
            <summary class="cursor-pointer text-sm font-medium" style="color:#15572e;">+ บันทึกผลการประเมิน</summary>
            <form method="POST" action="{{ route('parties.assessment.store', $party) }}" class="mt-3 space-y-3">
            @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-xs font-medium mb-1" style="color:#374151;">ประเภท</label>
                        <select name="assessment_type" class="form-input">
                            @foreach(['initial','periodic','triggered','post_incident'] as $at)
                                <option value="{{ $at }}">{{ $at }}</option>
                            @endforeach
                        </select></div>
                    <div><label class="block text-xs font-medium mb-1" style="color:#374151;">คะแนน (0-100)</label>
                        <input type="number" name="score" class="form-input" min="0" max="100" required></div>
                    <div><label class="block text-xs font-medium mb-1" style="color:#374151;">Risk Level</label>
                        <select name="risk_level" class="form-input">
                            @foreach(['low','medium','high','critical'] as $rl)
                                <option value="{{ $rl }}">{{ ucfirst($rl) }}</option>
                            @endforeach
                        </select></div>
                    <div><label class="block text-xs font-medium mb-1" style="color:#374151;">วันนัดทบทวนครั้งถัดไป</label>
                        <input type="date" name="next_assessment_date" class="form-input"></div>
                    <div class="col-span-2"><label class="block text-xs font-medium mb-1" style="color:#374151;">ผลการค้นพบ</label>
                        <textarea name="findings" class="form-input" rows="2"></textarea></div>
                    <div class="col-span-2"><label class="block text-xs font-medium mb-1" style="color:#374151;">ข้อแนะนำ</label>
                        <textarea name="recommendations" class="form-input" rows="2"></textarea></div>
                </div>
                <button type="submit" class="btn-primary text-sm">บันทึกผล</button>
            </form>
        </details>
    </div>

    {{-- Linked ROPA --}}
    @if($party->ropaRecords->isNotEmpty())
    <div class="card">
        <h3 class="font-semibold mb-4" style="color:#15572e;">📋 ROPA ที่เกี่ยวข้อง</h3>
        <div class="space-y-2">
        @foreach($party->ropaRecords as $ropa)
            <div class="flex items-center justify-between rounded-lg px-3 py-2" style="background:#f8fafc;">
                <div>
                    <p class="text-sm font-medium" style="color:#334155;">{{ $ropa->process_name }}</p>
                    <p class="text-xs" style="color:#94a3b8;">Role: {{ $ropa->pivot->party_role }}</p>
                </div>
                <a href="{{ route('ropa.show', $ropa) }}" class="text-xs" style="color:#15572e;">ดู ROPA →</a>
            </div>
        @endforeach
        </div>
    </div>
    @endif

</div>

{{-- RIGHT Sidebar ────────────────────────────────────────────────────────── --}}
<div class="space-y-4">
    <div class="card">
        <h3 class="font-semibold mb-3" style="color:#15572e;">⚙️ การจัดการ</h3>
        <div class="space-y-2">
            <a href="{{ route('parties.edit', $party) }}" class="btn-outline text-sm w-full text-center block">✏️ แก้ไข</a>
            <form method="POST" action="{{ route('parties.destroy', $party) }}" onsubmit="return confirm('ลบ party นี้?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger text-sm w-full">🗑 ลบ</button>
            </form>
        </div>
    </div>

    <div class="card">
        <h3 class="font-semibold mb-3" style="color:#15572e;">📞 ผู้ติดต่อ</h3>
        <div class="space-y-2 text-sm">
            @if($party->contact_name)<div><span style="color:#94a3b8;">ผู้ติดต่อ:</span> {{ $party->contact_name }}</div>@endif
            @if($party->contact_email)<div><span style="color:#94a3b8;">อีเมล:</span> <a href="mailto:{{ $party->contact_email }}" style="color:#15572e;">{{ $party->contact_email }}</a></div>@endif
            @if($party->contact_phone)<div><span style="color:#94a3b8;">โทร:</span> {{ $party->contact_phone }}</div>@endif
            @if($party->dpo_name)
                <div class="mt-2 pt-2" style="border-top:1px solid #e2e8f0;">
                    <p class="font-medium text-xs mb-1" style="color:#64748b;">DPO ของเขา</p>
                    <div>{{ $party->dpo_name }}</div>
                    @if($party->dpo_email)<div><a href="mailto:{{ $party->dpo_email }}" style="color:#15572e;">{{ $party->dpo_email }}</a></div>@endif
                </div>
            @endif
        </div>
    </div>

    @if($party->next_review_date)
    <div class="card" style="{{ $party->isOverdue() ? 'border-left:3px solid #c0272d;' : '' }}">
        <h3 class="font-semibold mb-2" style="color:{{ $party->isOverdue() ? '#c0272d' : '#15572e' }};">📅 การทบทวน</h3>
        <p class="text-sm" style="color:{{ $party->isOverdue() ? '#c0272d' : '#64748b' }};">
            {{ $party->isOverdue() ? '⚠️ เลยกำหนด: ' : 'ครบกำหนด: ' }}
            {{ $party->next_review_date->format('d/m/Y') }}
        </p>
        <p class="text-xs mt-1" style="color:#94a3b8;">ทุก {{ $party->review_frequency_months }} เดือน</p>
    </div>
    @endif
</div>
</div>
@endsection
