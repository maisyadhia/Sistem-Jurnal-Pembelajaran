@extends('layouts.app')

@section('title', 'Detail Log - E-Jurnal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h2 class="font-headline-md text-headline-md text-on-background">Detail Log Aktivitas</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Informasi lengkap tentang aktivitas ini.</p>
            </div>
            <a href="{{ route('admin.logs') }}" class="text-primary hover:underline text-sm">Kembali</a>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-slate-500">Waktu</label>
                    <p class="text-sm font-medium">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                <div>
                    <label class="block text-xs text-slate-500">IP Address</label>
                    <p class="text-sm font-medium">{{ $log->ip_address ?? '-' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-slate-500">Admin</label>
                    <p class="text-sm font-medium">{{ $log->admin_name }}</p>
                    <p class="text-xs text-slate-400">Role: {{ ucfirst($log->admin_role) }}</p>
                </div>
                <div>
                    <label class="block text-xs text-slate-500">Aksi</label>
                    <p class="text-sm font-medium">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $log->action == 'login' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $log->action == 'logout' ? 'bg-gray-100 text-gray-700' : '' }}
                            {{ $log->action == 'create' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $log->action == 'update' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $log->action == 'delete' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $log->action == 'remind' ? 'bg-purple-100 text-purple-700' : '' }}
                        ">
                            {{ ucfirst($log->action) }}
                        </span>
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-xs text-slate-500">Modul</label>
                <p class="text-sm font-medium">{{ ucfirst($log->module ?? '-') }}</p>
            </div>

            <div>
                <label class="block text-xs text-slate-500">Deskripsi</label>
                <p class="text-sm">{{ $log->description }}</p>
            </div>

            @if($log->old_data || $log->new_data)
            <div class="border-t border-outline-variant pt-4">
                <h3 class="font-medium text-sm mb-2">Data Perubahan</h3>
                <div class="grid grid-cols-2 gap-4">
                    @if($log->old_data)
                    <div>
                        <label class="block text-xs text-slate-500">Data Lama</label>
                        <pre class="bg-slate-50 p-3 rounded-lg text-xs overflow-x-auto">{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    @endif
                    @if($log->new_data)
                    <div>
                        <label class="block text-xs text-slate-500">Data Baru</label>
                        <pre class="bg-slate-50 p-3 rounded-lg text-xs overflow-x-auto">{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="border-t border-outline-variant pt-4">
                <label class="block text-xs text-slate-500">User Agent</label>
                <p class="text-xs text-slate-600 break-all">{{ $log->user_agent ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection