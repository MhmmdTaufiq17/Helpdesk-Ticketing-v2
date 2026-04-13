<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.07 0a5 5 0 010 7.07" />
            </svg>
            Admin Online
        </h2>
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full {{ $onlineCount > 0 ? 'bg-green-500 animate-pulse' : 'bg-gray-300' }}"></div>
            <span class="text-xs font-semibold {{ $onlineCount > 0 ? 'text-green-600' : 'text-gray-400' }}">
                {{ $onlineCount }} Online
            </span>
        </div>
    </div>

    <div class="px-5 py-4 space-y-3 max-h-80 overflow-y-auto">
        @forelse($admins as $admin)
            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold text-sm">
                            {{ $admin['avatar'] }}
                        </div>
                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white
                            {{ $admin['is_online'] ? 'bg-green-500' : 'bg-gray-300' }}">
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $admin['name'] }}</p>
                        <p class="text-xs {{ $admin['is_online'] ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $admin['is_online'] ? 'Online' : 'Last seen ' . $admin['last_seen'] }}
                        </p>
                    </div>
                </div>
                @if($admin['is_online'])
                    <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-400 text-center py-4">Tidak ada admin</p>
        @endforelse
    </div>
</div>
