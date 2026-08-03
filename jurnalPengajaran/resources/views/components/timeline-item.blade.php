<div class="relative pl-12 md:pl-16 group">
    <div class="absolute left-0 top-0 w-10 h-10 rounded-full {{ $activity->color ?? 'bg-primary' }} flex items-center justify-center z-10 border-4 border-surface-container-lowest shadow-md">
        <span class="material-symbols-outlined text-white text-sm">{{ $activity->icon ?? 'science' }}</span>
    </div>
    
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-2">
        <span class="font-label-caps text-label-caps {{ $activity->is_past ? 'text-on-surface-variant' : 'text-secondary' }}">
            {{ $activity->date_time }}
        </span>
        @if(!$activity->is_past)
            <div class="flex items-center gap-1 text-on-surface-variant">
                <span class="material-symbols-outlined text-xs" style="font-variation-settings: 'FILL' 1;">star</span>
                <span class="text-[10px] font-bold">TERVERIFIKASI</span>
            </div>
        @endif
    </div>
    
    <div class="bg-surface border border-outline-variant rounded-xl p-5 hover:border-primary transition-colors cursor-default group-hover:bg-white">
        <div class="flex items-center justify-between mb-3">
            <h4 class="font-headline-md text-headline-md text-on-background">{{ $activity->subject }}</h4>
            <div class="flex items-center gap-2">
                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $activity->teacher }}</span>
                <div class="w-1.5 h-1.5 rounded-full bg-secondary"></div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-3 bg-surface-container-low rounded-lg border-l-4 border-primary">
                <p class="font-label-caps text-[10px] text-primary uppercase mb-1">Materi Hari Ini</p>
                <p class="text-body-sm font-medium">{{ $activity->topic }}</p>
            </div>
            @if($activity->next_topic)
                <div class="p-3 bg-surface-container-highest/50 rounded-lg border-l-4 border-outline">
                    <p class="font-label-caps text-[10px] text-on-surface-variant uppercase mb-1">Materi Berikutnya</p>
                    <p class="text-body-sm font-medium">{{ $activity->next_topic }}</p>
                </div>
            @endif
        </div>
    </div>
</div>