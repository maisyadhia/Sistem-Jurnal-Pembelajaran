@extends('layouts.app')

@section('title', 'Profil Admin - E-Jurnal')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
        
        <!-- Header Profil -->
        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-slate-200">
            <div class="w-16 h-16 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-2xl font-bold">
                {{ strtoupper(substr($admin->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ $admin->name }}</h2>
                <p class="text-sm text-slate-500">{{ ucfirst($admin->role) }} - {{ $admin->username }}</p>
            </div>
        </div>

        <!-- Form Ganti Password -->
        <h3 class="text-lg font-bold text-slate-800 mb-4">Ganti Password</h3>
        
        <form action="{{ route('admin.profile.password') }}" method="POST">
            @csrf
            
            <div class="space-y-4">
                <!-- Password Lama -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password Saat Ini</label>
                    <input type="password" name="password_lama" required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary @error('password_lama') border-red-500 @enderror">
                    @error('password_lama') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Password Baru -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                    <input type="password" name="password_baru" required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary @error('password_baru') border-red-500 @enderror">
                    <p class="text-xs text-slate-500 mt-1">Minimal 6 karakter.</p>
                    @error('password_baru') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Konfirmasi Password Baru -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_baru_confirmation" required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-primary/90 transition-colors">
                    Simpan Password Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection