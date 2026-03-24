@extends('layouts.app')
@section('title', 'รายงานการอบรม')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-2">
        <a href="{{ route('training.index') }}" class="btn-outline text-sm">← กลับ</a>
        <h1 class="text-2xl font-bold" style="color:#15572e;">รายงานการอบรม</h1>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#15572e;">{{ $totalUsers }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">พนักงานทั้งหมด</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#0f3020;">{{ $courses->count() }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">คอร์สทั้งหมด</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#1d4ed8;">{{ $requiredIds->count() }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">คอร์สบังคับ</p>
    </div>
    <div class="card text-center">
        <p class="text-3xl font-black" style="color:#15572e;">{{ $fullCompleted }}</p>
        <p class="text-sm mt-1" style="color:#64748b;">ผ่านครบทุกคอร์สบังคับ</p>
    </div>
</div>

{{-- Per-page + table --}}
<div class="card">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <h3 class="font-semibold" style="color:#15572e;">สถานะการอบรมรายคน</h3>
        <form method="GET" action="{{ route('training.report') }}">
            <select name="per_page" class="form-input" style="width:auto;" onchange="this.form.submit()">
                <option value="50"  {{ request('per_page','50') == '50'  ? 'selected' : '' }}>แสดง 50</option>
                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>แสดง 100</option>
                <option value="200" {{ request('per_page') == '200' ? 'selected' : '' }}>แสดง 200</option>
            </select>
        </form>
    </div>

    @if($users->count() > 0)
    <p class="text-xs mb-3" style="color:#94a3b8;">
        แสดง {{ $users->firstItem() }}–{{ $users->lastItem() }} จากทั้งหมด <strong>{{ number_format($users->total()) }}</strong> คน
    </p>
    @endif

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>พนักงาน</th>
                    <th>บทบาท</th>
                    @foreach($courses as $c)
                    <th class="text-center" style="min-width:80px;" title="{{ $c->title }}">
                        <span class="text-xs">{{ Str::limit($c->title, 15) }}</span>
                        @if($requiredIds->contains($c->id))
                            <span style="color:#c0272d;">*</span>
                        @endif
                    </th>
                    @endforeach
                    <th class="text-center">ผ่านแล้ว</th>
                </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                @php
                    $passedIds = $user->completions->pluck('course_id')->unique();
                    $passedCount = $passedIds->count();
                @endphp
                <tr>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:#15572e;">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-sm" style="color:#1e293b;">{{ $user->name }}</p>
                                <p class="text-xs" style="color:#94a3b8;">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge text-xs" style="background:#f1f5f9;color:#475569;">{{ $user->getRoleLabel() }}</span>
                    </td>
                    @foreach($courses as $c)
                    <td class="text-center">
                        @if($passedIds->contains($c->id))
                            @php $comp = $user->completions->where('course_id', $c->id)->first(); @endphp
                            <span title="{{ $comp->score }}% — {{ $comp->completed_at?->format('d/m/Y') }}" style="color:#15572e;cursor:default;">✅</span>
                        @else
                            <span style="color:#e2e8f0;">○</span>
                        @endif
                    </td>
                    @endforeach
                    <td class="text-center">
                        <span class="font-bold text-sm" style="color:{{ $passedCount === $courses->count() ? '#15572e' : '#64748b' }};">
                            {{ $passedCount }}/{{ $courses->count() }}
                        </span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="mt-4 flex justify-center">
        {{ $users->links() }}
    </div>
    @endif
</div>

<p class="text-xs mt-4" style="color:#94a3b8;">* คอร์สบังคับ | ✅ = ผ่าน | ○ = ยังไม่ผ่าน</p>
@endsection
