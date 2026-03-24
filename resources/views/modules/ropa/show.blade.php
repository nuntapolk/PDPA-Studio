@extends('layouts.app')

@section('title', $ropa->process_name . ' — ROPA Studio')
@section('page-title', 'ROPA — รายละเอียดกิจกรรม')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('ropa.index') }}" class="text-sm font-medium" style="color:#15572e;">← กลับรายการ</a>
    <div class="flex items-center gap-2">
        <a href="{{ route('ropa.edit', $ropa) }}" class="btn-outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            แก้ไข
        </a>
        @if($ropa->needsReview() || $ropa->status === 'under_review')
        <form action="{{ route('ropa.mark-reviewed', $ropa) }}" method="POST">
            @csrf
            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                บันทึกการ Review
            </button>
        </form>
        @endif
    </div>
</div>

@if($ropa->needsReview() && $ropa->status !== 'archived')
<div class="mb-6 rounded-xl px-5 py-4 flex items-center gap-3" style="background:#fffbeb; border:1.5px solid #fcd34d;">
    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" style="color:#b45309;"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
    <p class="text-sm font-semibold" style="color:#92400e;">กิจกรรมนี้เลย Review Date แล้ว ({{ $ropa->next_review_date->format('d/m/Y') }}) — กรุณา Review และกดบันทึก</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main detail --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Header card --}}
        <div class="card p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold" style="color:#0f3020;">{{ $ropa->process_name }}</h2>
                    <p class="text-sm mt-0.5" style="color:#64748b;">
                        {{ $ropa->process_code ? $ropa->process_code . ' · ' : '' }}
                        {{ $ropa->department ?? '' }}
                        {{ $ropa->process_owner ? ' · เจ้าของ: ' . $ropa->process_owner : '' }}
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @php
                        $roleLabels = ['controller' => 'Controller', 'processor' => 'Processor', 'joint_controller' => 'Joint Controller'];
                        $statusColors = ['draft' => 'badge-gray', 'active' => 'badge-green', 'under_review' => 'badge-yellow', 'archived' => 'badge-gray'];
                        $statusLabels = ['draft' => 'ร่าง', 'active' => 'ใช้งาน', 'under_review' => 'กำลัง Review', 'archived' => 'เก็บถาวร'];
                    @endphp
                    <span class="badge badge-blue">{{ $roleLabels[$ropa->role] ?? $ropa->role }}</span>
                    <span class="badge {{ $statusColors[$ropa->status] ?? 'badge-gray' }}">{{ $statusLabels[$ropa->status] ?? $ropa->status }}</span>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#94a3b8;">วัตถุประสงค์การประมวลผล</p>
                    <p class="text-sm leading-relaxed" style="color:#374151;">{{ $ropa->purpose }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#94a3b8;">ฐานทางกฎหมาย (Lawful Basis)</p>
                    <span class="badge badge-blue">{{ $ropa->getLegalBasisLabel() }}</span>
                    @if($ropa->legal_basis === 'legitimate_interest' && $ropa->legitimate_interest_description)
                    <p class="text-xs mt-2" style="color:#64748b;">{{ $ropa->legitimate_interest_description }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ข้อมูลที่ประมวลผล --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold mb-4" style="color:#1e293b;">ข้อมูลที่ประมวลผล</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#94a3b8;">ประเภทข้อมูลส่วนบุคคล</p>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($ropa->data_categories ?? [] as $cat)
                        <span class="badge badge-blue">{{ $cat }}</span>
                        @empty
                        <span class="text-xs" style="color:#94a3b8;">ไม่ระบุ</span>
                        @endforelse
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#94a3b8;">ประเภทเจ้าของข้อมูล</p>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($ropa->data_subject_types ?? [] as $type)
                        <span class="badge badge-gray">{{ $type }}</span>
                        @empty
                        <span class="text-xs" style="color:#94a3b8;">ไม่ระบุ</span>
                        @endforelse
                    </div>
                </div>
            </div>

            @if($ropa->has_sensitive_data)
            <div class="mt-5 pt-4 rounded-xl px-4 py-3" style="background:#fff5f5; border:1px solid #fca5a5;">
                <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#c0272d;">
                    ข้อมูลอ่อนไหว (มาตรา 26)
                </p>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($ropa->sensitive_data_categories ?? [] as $s)
                    <span class="badge badge-red">{{ $s }}</span>
                    @empty
                    <span class="text-xs" style="color:#c0272d;">มีข้อมูลอ่อนไหว (ไม่ระบุประเภท)</span>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        {{-- การส่งต่อข้อมูล --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold mb-4" style="color:#1e293b;">ผู้รับและการส่งต่อข้อมูล</h3>

            <div class="mb-4">
                <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#94a3b8;">ผู้รับข้อมูล / ผู้ประมวลผลข้อมูล</p>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($ropa->recipients ?? [] as $r)
                    <span class="badge badge-gray">{{ $r }}</span>
                    @empty
                    <span class="text-xs" style="color:#94a3b8;">ไม่มี</span>
                    @endforelse
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4" style="border-top:1px solid #e8f0eb;">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $ropa->third_party_transfer ? '#b45309' : '#cbd5e1' }};"></div>
                    <p class="text-sm" style="color:#374151;">ส่งข้อมูลให้บุคคลที่สาม: <strong>{{ $ropa->third_party_transfer ? 'ใช่' : 'ไม่' }}</strong></p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $ropa->cross_border_transfer ? '#c0272d' : '#cbd5e1' }};"></div>
                    <p class="text-sm" style="color:#374151;">ส่งข้ามพรมแดน: <strong>{{ $ropa->cross_border_transfer ? 'ใช่' : 'ไม่' }}</strong></p>
                </div>
            </div>

            @if($ropa->cross_border_transfer)
            <div class="mt-4 rounded-xl px-4 py-3" style="background:#fffbeb; border:1px solid #fcd34d;">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs font-bold mb-1" style="color:#b45309;">ประเทศปลายทาง</p>
                        <p style="color:#374151;">{{ $ropa->cross_border_countries ?? 'ไม่ระบุ' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold mb-1" style="color:#b45309;">มาตรการคุ้มครอง</p>
                        <p style="color:#374151;">{{ $ropa->cross_border_safeguards ?? 'ไม่ระบุ' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- การเก็บรักษาและความปลอดภัย --}}
        <div class="card p-6">
            <h3 class="text-sm font-semibold mb-4" style="color:#1e293b;">การเก็บรักษาและมาตรการรักษาความปลอดภัย</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#94a3b8;">ระยะเวลาเก็บรักษา</p>
                    <p class="text-sm font-semibold" style="color:#374151;">{{ $ropa->retention_period }}</p>
                    @if($ropa->retention_criteria)
                    <p class="text-xs mt-1" style="color:#64748b;">{{ $ropa->retention_criteria }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#94a3b8;">วิธีทำลายข้อมูล</p>
                    <p class="text-sm" style="color:#374151;">{{ $ropa->deletion_method ?? 'ไม่ระบุ' }}</p>
                </div>
                @if($ropa->system_used)
                <div class="sm:col-span-2">
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:#94a3b8;">ระบบที่ใช้ประมวลผล</p>
                    <p class="text-sm" style="color:#374151;">{{ $ropa->system_used }}</p>
                </div>
                @endif
                <div class="sm:col-span-2">
                    <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color:#94a3b8;">มาตรการรักษาความปลอดภัย</p>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($ropa->security_measures ?? [] as $measure)
                        <span class="badge badge-green">{{ $measure }}</span>
                        @empty
                        <span class="text-xs" style="color:#94a3b8;">ไม่ระบุ</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="card p-5 text-sm space-y-2.5">
            <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#64748b;">ข้อมูลการ Review</p>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">Review ล่าสุด</span>
                <span style="color:#374151;">{{ $ropa->last_reviewed_at ? $ropa->last_reviewed_at->format('d/m/Y') : 'ยังไม่เคย' }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">Review ถัดไป</span>
                <span style="color:{{ $ropa->needsReview() ? '#b45309' : '#374151' }}; font-weight:{{ $ropa->needsReview() ? '600' : 'normal' }};">
                    {{ $ropa->next_review_date ? $ropa->next_review_date->format('d/m/Y') : 'ไม่กำหนด' }}
                </span>
            </div>
            @if($ropa->reviewer)
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">ผู้ Review</span>
                <span style="color:#374151;">{{ $ropa->reviewer->name }}</span>
            </div>
            @endif
            <div class="flex justify-between text-xs pt-2" style="border-top:1px solid #e8f0eb;">
                <span style="color:#94a3b8;">สร้างโดย</span>
                <span style="color:#374151;">{{ $ropa->creator->name ?? '—' }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">สร้างเมื่อ</span>
                <span style="color:#374151;">{{ $ropa->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">แก้ไขล่าสุด</span>
                <span style="color:#374151;">{{ $ropa->updated_at->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="card p-5">
            <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#64748b;">สรุป PDPA Risk</p>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-xs">
                    <span style="color:#475569;">ข้อมูลอ่อนไหว (มาตรา 26)</span>
                    <span style="color:{{ $ropa->has_sensitive_data ? '#c0272d' : '#15572e' }}; font-weight:{{ $ropa->has_sensitive_data ? '600' : 'normal' }};">
                        {{ $ropa->has_sensitive_data ? 'มี ⚠️' : 'ไม่มี ✓' }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span style="color:#475569;">ส่งข้ามพรมแดน</span>
                    <span style="color:{{ $ropa->cross_border_transfer ? '#b45309' : '#15572e' }}; font-weight:{{ $ropa->cross_border_transfer ? '600' : 'normal' }};">
                        {{ $ropa->cross_border_transfer ? 'ใช่ ⚠️' : 'ไม่มี ✓' }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span style="color:#475569;">ส่งให้บุคคลที่สาม</span>
                    <span style="color:{{ $ropa->third_party_transfer ? '#b45309' : '#15572e' }};">
                        {{ $ropa->third_party_transfer ? 'ใช่' : 'ไม่มี ✓' }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span style="color:#475569;">ต้อง Review</span>
                    <span style="color:{{ $ropa->needsReview() ? '#b45309' : '#15572e' }}; font-weight:{{ $ropa->needsReview() ? '600' : 'normal' }};">
                        {{ $ropa->needsReview() ? 'ค้างรีวิว ⚠️' : 'ปกติ ✓' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card p-5 space-y-2">
            <a href="{{ route('ropa.edit', $ropa) }}" class="btn-outline w-full" style="justify-content:center;">แก้ไขข้อมูล</a>
            @if($ropa->status !== 'archived')
            <form action="{{ route('ropa.update', $ropa) }}" method="POST">
                @csrf @method('PUT')
                @foreach($ropa->getFillable() as $field)
                    @if(!in_array($field, ['organization_id','created_by','status']))
                    <input type="hidden" name="{{ $field }}" value="{{ is_array($ropa->$field) ? json_encode($ropa->$field) : $ropa->$field }}">
                    @endif
                @endforeach
                <input type="hidden" name="status" value="archived">
                <button type="submit" onclick="return confirm('เก็บถาวรกิจกรรมนี้?')"
                    class="w-full text-center px-4 py-2.5 rounded-xl text-sm font-semibold transition"
                    style="border:1.5px solid #fca5a5; color:#c0272d; background:transparent;"
                    onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='transparent'">
                    เก็บถาวร (Archive)
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
