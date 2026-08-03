@extends('layouts.app')

@section('title', 'Laporan - E-Jurnal')

@section('content')
<div class="mb-4">
    <a href="{{ route('monitoring') }}" 
       class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition-all">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali ke Monitoring
    </a>
</div>
@endsection