@extends('layouts.app')

@section('title', 'แก้ไข ROPA — PDPA Studio')
@section('page-title', 'ROPA — แก้ไขกิจกรรม')

@section('content')
<div class="mb-4">
    <a href="{{ route('ropa.show', $ropa) }}" class="text-sm font-medium" style="color:#15572e;">← กลับ</a>
</div>

@include('modules.ropa._form', ['action' => route('ropa.update', $ropa), 'method' => 'PUT'])
@endsection
