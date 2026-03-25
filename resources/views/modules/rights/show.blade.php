@extends('layouts.app')

@section('title', $rightsRequest->ticket_number . ' — PDPA Studio')
@section('page-title', 'คำขอสิทธิ์เจ้าของข้อมูล')

@section('content')
<div class="mb-4">
    <a href="{{ route('rights.index') }}" class="text-sm font-medium" style="color:#15572e;">← กลับรายการ</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main info --}}
    <div class="lg:col-span-2 space-y-6">

        <div class="card p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold" style="color:#0f3020;">{{ $rightsRequest->ticket_number }}</h2>
                    <p class="text-sm mt-0.5" style="color:#94a3b8;">ยื่นเมื่อ {{ $rightsRequest->created_at->format('d M Y H:i') }}</p>
                </div>
                @php
                    $statusColors = ['pending' => 'badge-yellow', 'in_review' => 'badge-blue', 'awaiting_info' => 'badge-yellow', 'approved' => 'badge-green', 'completed' => 'badge-green', 'rejected' => 'badge-red', 'withdrawn' => 'badge-gray'];
                    $statusLabels = ['pending' => 'รอดำเนินการ', 'in_review' => 'กำลัง Review', 'awaiting_info' => 'รอข้อมูลเพิ่ม', 'approved' => 'อนุมัติ', 'completed' => 'เสร็จสิ้น', 'rejected' => 'ปฏิเสธ', 'withdrawn' => 'ถอนคำขอ'];
                @endphp
                <span class="badge {{ $statusColors[$rightsRequest->status] ?? 'badge-gray' }}">{{ $statusLabels[$rightsRequest->status] ?? $rightsRequest->status }}</span>
            </div>

            <div class="grid grid-cols-2 gap-5 text-sm">
                <div>
                    <p class="text-xs mb-1" style="color:#94a3b8;">ชื่อผู้ยื่น</p>
                    <p class="font-semibold" style="color:#1e293b;">{{ $rightsRequest->requester_name }}</p>
                </div>
                <div>
                    <p class="text-xs mb-1" style="color:#94a3b8;">อีเมล</p>
                    <p style="color:#374151;">{{ $rightsRequest->requester_email }}</p>
                </div>
                @if($rightsRequest->requester_phone)
                <div>
                    <p class="text-xs mb-1" style="color:#94a3b8;">โทรศัพท์</p>
                    <p style="color:#374151;">{{ $rightsRequest->requester_phone }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs mb-1" style="color:#94a3b8;">ประเภทสิทธิ์</p>
                    @php
                        $typeLabels = ['access' => 'ขอเข้าถึงข้อมูล', 'rectification' => 'ขอแก้ไขข้อมูล', 'erasure' => 'ขอลบข้อมูล', 'restriction' => 'ขอระงับการใช้', 'portability' => 'ขอโอนย้ายข้อมูล', 'objection' => 'คัดค้านการใช้ข้อมูล', 'withdraw_consent' => 'ถอนความยินยอม', 'complaint' => 'ร้องเรียน'];
                    @endphp
                    <span class="badge badge-blue">{{ $typeLabels[$rightsRequest->type] ?? $rightsRequest->type }}</span>
                </div>
                @if($rightsRequest->due_date)
                <div>
                    <p class="text-xs mb-1" style="color:#94a3b8;">กำหนดเสร็จ</p>
                    <p style="color:{{ $rightsRequest->isOverdue() ? '#c0272d' : '#374151' }}; font-weight:{{ $rightsRequest->isOverdue() ? '600' : 'normal' }};">
                        {{ $rightsRequest->due_date->format('d M Y') }}
                        @if($rightsRequest->isOverdue() && in_array($rightsRequest->status, ['pending','in_review','awaiting_info']))
                            <span class="badge badge-red ml-1">เกินกำหนด</span>
                        @elseif(!$rightsRequest->due_date->isPast())
                            <span class="text-xs ml-1" style="color:#94a3b8;">({{ $rightsRequest->days_remaining }} วัน)</span>
                        @endif
                    </p>
                </div>
                @endif
            </div>

            @if($rightsRequest->description)
            <div class="mt-5 pt-5" style="border-top:1px solid #e8f0eb;">
                <p class="text-xs mb-2" style="color:#94a3b8;">รายละเอียดคำขอ</p>
                <p class="text-sm leading-relaxed" style="color:#374151;">{{ $rightsRequest->description }}</p>
            </div>
            @endif
        </div>

        {{-- Notes --}}
        <div class="card">
            <div class="p-5" style="border-bottom:1px solid #e8f0eb;">
                <h3 class="text-sm font-semibold" style="color:#1e293b;">บันทึกการดำเนินการ</h3>
            </div>
            <div>
                @forelse($notes as $note)
                <div class="px-5 py-4" style="{{ $note->is_internal ? 'background:#fffbeb;' : '' }} border-bottom:1px solid #f8faf9;">
                    <div class="flex items-center gap-2 mb-1.5">
                        <p class="text-xs font-semibold" style="color:#475569;">{{ $note->user->name ?? 'ระบบ' }}</p>
                        <span style="color:#cbd5e1;">·</span>
                        <p class="text-xs" style="color:#94a3b8;">{{ $note->created_at->format('d M Y H:i') }}</p>
                        @if($note->is_internal)
                        <span class="badge badge-yellow">ภายใน</span>
                        @endif
                    </div>
                    <p class="text-sm leading-relaxed" style="color:#374151;">{{ $note->note }}</p>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-sm" style="color:#94a3b8;">ยังไม่มีบันทึก</div>
                @endforelse
            </div>
            <div class="p-5" style="border-top:1px solid #e8f0eb;">
                <form action="{{ route('rights.add-note', $rightsRequest) }}" method="POST" class="space-y-3">
                    @csrf
                    <textarea name="note" rows="2" placeholder="เพิ่มบันทึก..." required class="form-input resize-none"></textarea>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-xs cursor-pointer" style="color:#64748b;">
                            <input type="checkbox" name="is_private" value="1" class="rounded" style="accent-color:#15572e;">
                            บันทึกภายใน (Internal)
                        </label>
                        <button type="submit" class="btn-primary" style="padding:8px 18px; font-size:13px;">เพิ่มบันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Actions sidebar --}}
    <div class="space-y-4">
        <div class="card p-5">
            <h3 class="text-sm font-semibold mb-4" style="color:#1e293b;">อัปเดตสถานะ</h3>
            <form action="{{ route('rights.update-status', $rightsRequest) }}" method="POST" class="space-y-3">
                @csrf @method('PATCH')
                <select name="status" class="form-input">
                    <option value="pending"       {{ $rightsRequest->status === 'pending'       ? 'selected' : '' }}>รอดำเนินการ</option>
                    <option value="in_review"    {{ $rightsRequest->status === 'in_review'    ? 'selected' : '' }}>กำลัง Review</option>
                    <option value="awaiting_info"{{ $rightsRequest->status === 'awaiting_info'? 'selected' : '' }}>รอข้อมูลเพิ่ม</option>
                    <option value="approved"     {{ $rightsRequest->status === 'approved'     ? 'selected' : '' }}>อนุมัติ</option>
                    <option value="completed"    {{ $rightsRequest->status === 'completed'    ? 'selected' : '' }}>เสร็จสิ้น</option>
                    <option value="rejected"     {{ $rightsRequest->status === 'rejected'     ? 'selected' : '' }}>ปฏิเสธ</option>
                    <option value="withdrawn"    {{ $rightsRequest->status === 'withdrawn'    ? 'selected' : '' }}>ถอนคำขอ</option>
                </select>
                <button type="submit" class="btn-primary w-full">อัปเดตสถานะ</button>
            </form>
        </div>

        <div class="card p-5 text-sm space-y-2.5">
            <p class="text-xs font-bold uppercase tracking-wider mb-3" style="color:#64748b;">ข้อมูลคำขอ</p>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">ช่องทาง</span>
                <span style="color:#374151;">{{ $rightsRequest->channel ?? '—' }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">ยื่นเมื่อ</span>
                <span style="color:#374151;">{{ $rightsRequest->submitted_at->diffForHumans() }}</span>
            </div>
            @if($rightsRequest->completed_at)
            <div class="flex justify-between text-xs">
                <span style="color:#94a3b8;">เสร็จเมื่อ</span>
                <span style="color:#374151;">{{ $rightsRequest->completed_at->format('d/m/Y') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
