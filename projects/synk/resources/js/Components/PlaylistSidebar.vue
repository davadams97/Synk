<template>
    <div class="h-full bg-white/5 border-r border-white/10 border-t border-white/5 flex flex-col">
        <!-- Search -->
        <div class="p-4 border-b border-white/10 flex-shrink-0">
            <input
                v-model="search"
                type="text"
                placeholder="Search playlists..."
                class="w-full px-3 py-2 rounded-lg bg-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400"
            />
        </div>
        <!-- Playlists List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            <div v-for="playlist in filteredPlaylists" :key="playlist.id"
                @click="$emit('select', playlist)"
                :class="[
                    'flex items-center space-x-3 px-4 py-3 cursor-pointer hover:bg-white/10 transition-all',
                    selectedId === playlist.id ? 'bg-purple-500/10 border-l-4 border-purple-400' : ''
                ]"
            >
                <img :src="playlist.coverURL" alt="cover" class="w-12 h-12 rounded-lg object-cover" />
                <div class="flex-1 min-w-0">
                    <div class="text-white font-medium truncate">{{ playlist.name }}</div>
                    <div class="text-xs text-gray-400">{{ playlist.trackCount }} tracks</div>
                </div>
            </div>
            <div v-if="filteredPlaylists.length === 0" class="p-8 text-center text-gray-400">
                No playlists found
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Playlist } from '@/Types/Playlist';

const props = defineProps<{
    playlists: Playlist[];
    selectedId: string | number | null;
}>();

const emit = defineEmits<{
    select: [playlist: Playlist];
}>();

const search = ref('');
const filteredPlaylists = computed(() => {
    if (!search.value) return props.playlists;
    return props.playlists.filter(p =>
        p.name.toLowerCase().includes(search.value.toLowerCase())
    );
});
</script>

<style scoped>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255, 255, 255, 0.3);
}
</style> 