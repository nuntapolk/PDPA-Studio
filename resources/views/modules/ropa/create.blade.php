@extends('layouts.app')

@section('title', 'เพิ่มกิจกรรม ROPA — PDPA Studio')
@section('page-title', 'ROPA — เพิ่มกิจกรรมการประมวลผล')

@section('content')
<div class="mb-4">
    <a href="{{ route('ropa.index') }}" class="text-sm font-medium" style="color:#15572e;">← กลับ</a>
</div>

@include('modules.ropa._form', ['ropa' => null, 'action' => route('ropa.store'), 'method' => 'POST'])
@endsection
