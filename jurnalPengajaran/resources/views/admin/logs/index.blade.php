@extends('layouts.app')

@section('title', 'Log Aktivitas Admin - E-Jurnal')

@section('content')
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center">
        <div>
            <h2 class="font-headline-md text-headline-md text-on-background">Log Aktivitas Admin</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Riwayat aktivitas semua admin dan humas.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.location.href='{{ route('admin.logs') }}'" 
                    class="bg-primary text-on-primary px-4 py-2 rounded-lg font-label-caps text-[11px] flex items-center gap-2 hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm">refresh</span> REFRESH
            </button>
            <form method="POST" action="{{ route('admin.logs.clear') }}" 
                  class="inline-block" 
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua log?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-error text-white px-4 py-2 rounded-lg font-label-caps text-[11px] flex items-center gap-2 hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-sm">delete_sweep</span> HAPUS SEMUA
                </button>
            </form>
        </div>
    </div>

    <!-- Filter -->
    <div class="p-4 border-b border-outline-variant bg-surface-container-low">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-slate-500 mb-1">Aksi</label>
                <select name="action" class="w-full bg-white border border-outline-variant rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Aksi</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Modul</label>
                <select name="module" class="w-full bg-white border border-outline-variant rounded-lg px-3 py-2 text-sm">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                            {{ ucfirst($module) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="w-full bg-white border border-outline-variant rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="w-full bg-white border border-outline-variant rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="md:col-span-4 flex justify-end gap-2">
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm">Filter</button>
                <a href="{{ route('admin.logs') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm">Reset</a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low border-b border-outline-variant">
                    <tr>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">WAKTU</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">ADMIN</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">AKSI</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">MODUL</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">DESKRIPSI</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant text-right">DETAIL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($logs as $log)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-4 py-4 text-sm">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full 
                                        {{ $log->admin_role == 'admin' ? 'bg-red-100 text-red-700' : 'bg-purple-100 text-purple-700' }} 
                                        flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($log->admin_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ $log->admin_name }}</p>
                                        <p class="text-xs text-slate-400">{{ ucfirst($log->admin_role) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $log->action == 'login' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $log->action == 'logout' ? 'bg-gray-100 text-gray-700' : '' }}
                                    {{ $log->action == 'create' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $log->action == 'update' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $log->action == 'delete' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $log->action == 'remind' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ !in_array($log->action, ['login','logout','create','update','delete','remind']) ? 'bg-slate-100 text-slate-700' : '' }}
                                ">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm">{{ ucfirst($log->module ?? '-') }}</td>
                            <td class="px-4 py-4 text-sm max-w-xs truncate">{{ $log->description }}</td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('admin.logs.show', $log->id) }}" 
                                   class="text-primary hover:underline text-sm">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-3xl block mx-auto mb-2">inbox</span>
                                Belum ada log aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection