@extends('layouts.app')
@section('title','Training — PDPA Studio')
@section('page-title','Training & Certification')

@section('content')

@if(session('success'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15572e;">✅ {{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:linear-gradient(135deg,#e8f0eb,#d1e8d8);">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#15572e;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color:#0f3020;">{{ $totalCourses }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">คอร์สทั้งหมด</p>
    </div>
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:#f0fdf4;">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" style="color:#15572e;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color:#15572e;">{{ $myCompleted }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">ฉันผ่านแล้ว</p>
    </div>
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:#fffbeb;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#b45309;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color:#b45309;">{{ $certCount }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">ใบรับรองที่ออก</p>
    </div>
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3" style="background:#fff1f2;">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#c0272d;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-3xl font-extrabold" style="color:{{ $expiringSoon>0?'#c0272d':'#64748b' }};">{{ $expiringSoon }}</p>
        <p class="text-xs mt-1" style="color:#64748b;">ใกล้หมดอายุ</p>
    </div>
</div>

{{-- Toolbar --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
    <form method="GET" class="flex items-center gap-2 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาคอร์ส..." class="form-input" style="width:200px;">
        <label class="flex items-center gap-1.5 text-sm cursor-pointer select-none" style="color:#475569;">
            <input type="checkbox" name="required" value="1" {{ request('required')?'checked':'' }} class="w-3.5 h-3.5 rounded" style="accent-color:#15572e;" onchange="this.form.submit()">
            <span>เฉพาะคอร์สบังคับ</span>
        </label>
        <button type="submit" class="btn-outline">ค้นหา</button>
    </form>
    <div class="flex items-center gap-2">
        <a href="{{ route('training.report') }}" class="btn-outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            รายงานผล
        </a>
        <a href="{{ route('training.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            สร้างคอร์สใหม่
        </a>
    </div>
</div>

{{-- Course Cards --}}
@if($courses->isEmpty())
<div class="card p-16 text-center">
    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#f1f5f9;">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#cbd5e1;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
    </div>
    <p class="text-sm font-medium mb-3" style="color:#64748b;">ยังไม่มีคอร์สอบรม</p>
    <a href="{{ route('training.create') }}" class="btn-primary">สร้างคอร์สแรก</a>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($courses as $course)
    @php
        $myComp     = $course->completions->first();
        $passed     = $myComp && $myComp->passed;
        $expired    = $passed && $myComp->isExpired();
        $qCount     = $course->questions()->count();
        $totalUsers = $course->completions()->distinct('user_id')->count();
        $passedUsers= $course->passed_count ?? 0;
        $pct        = $orgUserCount > 0 ? round($passedUsers/$orgUserCount*100) : 0;
    @endphp
    <div class="card overflow-hidden flex flex-col hover:shadow-md transition-shadow">
        {{-- Color banner --}}
        <div class="h-2" style="background:{{ $passed&&!$expired?'#15572e':($course->is_required?'#c0272d':'#0369a1') }};"></div>
        <div class="p-5 flex-1 flex flex-col">
            {{-- Badges --}}
            <div class="flex items-center gap-2 mb-3 flex-wrap">
                @if($course->is_required)
                <span class="badge badge-red" style="font-size:10px;">บังคับ</span>
                @endif
                @if(!$course->is_active)
                <span class="badge badge-gray" style="font-size:10px;">ปิดใช้งาน</span>
                @endif
                @if($passed && !$expired)
                <span class="badge badge-green" style="font-size:10px;">✓ ผ่านแล้ว</span>
                @elseif($expired)
                <span class="badge badge-red" style="font-size:10px;">ใบรับรองหมดอายุ</span>
                @endif
            </div>

            {{-- Title --}}
            <h3 class="font-bold text-sm mb-1.5" style="color:#0f3020; line-height:1.4;">{{ $course->title }}</h3>
            @if($course->description)
            <p class="text-xs mb-3 line-clamp-2" style="color:#64748b; line-height:1.6;">{{ $course->description }}</p>
            @endif

            {{-- Meta --}}
            <div class="flex items-center gap-4 text-xs mb-4" style="color:#94a3b8;">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $course->duration_minutes }} นาที
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $qCount }} ข้อ
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    ผ่าน {{ $course->passing_score }}%
                </span>
            </div>

            {{-- Org completion progress --}}
            <div class="mb-4">
                <div class="flex justify-between text-xs mb-1" style="color:#94a3b8;">
                    <span>ผ่านในองค์กร</span>
                    <span class="font-semibold" style="color:#475569;">{{ $passedUsers }}/{{ $orgUserCount }} คน ({{ $pct }}%)</span>
                </div>
                <div class="h-2 rounded-full" style="background:#f1f5f9;">
                    <div class="h-2 rounded-full transition-all" style="width:{{ $pct }}%; background:{{ $pct>=80?'#15572e':($pct>=50?'#b45309':'#0369a1') }};"></div>
                </div>
            </div>

            {{-- My cert info --}}
            @if($passed && !$expired && $myComp->certificate_number)
            <div class="mb-3 px-3 py-2 rounded-lg text-xs" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                <span style="color:#15572e;">🏅 {{ $myComp->certificate_number }}</span>
                @if($myComp->expires_at)
                <span class="ml-2" style="color:#94a3b8;">หมดอายุ {{ $myComp->expires_at->format('d/m/Y') }}</span>
                @endif
            </div>
            @endif

            {{-- Action button --}}
            <div class="mt-auto flex gap-2">
                <a href="{{ route('training.show', $course) }}" class="{{ ($passed&&!$expired)?'btn-outline':'btn-primary' }} flex-1 justify-center" style="font-size:13px;">
                    @if($passed && !$expired)
                    ดูเนื้อหา / ทำซ้ำ
                    @elseif($expired)
                    ต่ออายุใบรับรอง
                    @else
                    เริ่มเรียน
                    @endif
                </a>
                <a href="{{ route('training.edit', $course) }}" class="btn-outline" style="padding:7px 10px;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
