<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="text-center mb-4">
            <h3 class="text-2xl font-bold text-white mb-2">{{ header }}</h3>
            <p class="text-gray-300">Select the tracks you want to transfer</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
            <div class="flex items-center justify-center space-x-2 text-gray-400">
                <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="text-lg">Loading tracks...</span>
            </div>
        </div>

        <!-- No Playlist Selected -->
        <div v-else-if="!hasPlaylist" class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-white/5 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                </svg>
            </div>
            <p class="text-gray-400 text-lg">Select a playlist from the sidebar</p>
            <p class="text-gray-500 mt-2">Choose a playlist to view its tracks</p>
        </div>

        <!-- No Tracks Available -->
        <div v-else-if="tracks.length === 0" class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-white/5 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                </svg>
            </div>
            <p class="text-gray-400 text-lg">No tracks found in this playlist</p>
            <p class="text-gray-500 mt-2">This playlist appears to be empty</p>
        </div>

        <!-- Tracks Table -->
        <div v-else>
            <!-- Advanced Search and Select All -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <!-- Search Type Dropdown -->
                    <div class="relative">
                        <select
                            v-model="searchType"
                            class="px-3 py-2 rounded-lg bg-white/10 text-white border border-white/20 focus:outline-none focus:ring-2 focus:ring-purple-400 appearance-none cursor-pointer"
                        >
                            <option value="all">All Fields</option>
                            <option value="name">Track Name</option>
                            <option value="artist">Artist</option>
                            <option value="album">Album</option>
                            <option value="year">Year</option>
                            <option value="genre">Genre</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                    
                    <!-- Search Input -->
                    <input
                        v-model="search"
                        type="text"
                        :placeholder="getSearchPlaceholder()"
                        class="w-80 px-3 py-2 rounded-lg bg-white/10 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400"
                    />
                    
                    <!-- Clear Search Button -->
                    <button
                        v-if="search"
                        @click="clearSearch"
                        class="p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors text-gray-400 hover:text-white"
                        title="Clear search"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Select All -->
                <label class="flex items-center space-x-2 cursor-pointer select-none group">
                    <div class="relative">
                        <input 
                            type="checkbox" 
                            :checked="allSelected" 
                            @change="toggleSelectAll"
                            class="sr-only"
                        />
                        <div class="w-5 h-5 rounded-md border-2 border-white/30 group-hover:border-purple-400 transition-all duration-200 flex items-center justify-center">
                            <svg 
                                v-if="allSelected"
                                class="w-3 h-3 text-white" 
                                fill="currentColor" 
                                viewBox="0 0 20 20"
                            >
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div 
                            v-if="allSelected"
                            class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 rounded-md opacity-100 transition-opacity duration-200"
                        ></div>
                    </div>
                    <span class="text-sm text-white group-hover:text-purple-300 transition-colors">Select All</span>
                </label>
            </div>

            <!-- Search Results Summary -->
            <div v-if="search && filteredTracks.length !== tracks.length" class="mb-4 text-sm text-gray-400">
                Showing {{ filteredTracks.length }} of {{ tracks.length }} tracks
            </div>

            <div class="overflow-x-auto rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm">
                <table class="min-w-full text-sm text-left text-gray-400">
                    <thead class="sticky top-0 bg-white/10 text-xs uppercase text-gray-300">
                        <tr>
                            <th class="px-3 py-2 w-8"></th>
                            <th class="px-3 py-2">Album Art</th>
                            <th class="px-3 py-2">Track Name</th>
                            <th class="px-3 py-2">Artist</th>
                            <th class="px-3 py-2">Album</th>
                            <th class="px-3 py-2">Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="track in filteredTracks" :key="track.name" class="hover:bg-white/10 transition-colors group">
                            <td class="px-3 py-2">
                                <label class="flex items-center justify-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        :value="String(track.id)" 
                                        v-model="model"
                                        class="sr-only"
                                    />
                                    <div class="relative">
                                        <div class="w-5 h-5 rounded-md border-2 border-white/30 group-hover:border-purple-400 transition-all duration-200 flex items-center justify-center">
                                            <svg 
                                                v-if="model.has(String(track.id))"
                                                class="w-3 h-3 text-white relative z-10" 
                                                fill="currentColor" 
                                                viewBox="0 0 20 20"
                                            >
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div 
                                            v-if="model.has(String(track.id))"
                                            class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 rounded-md opacity-100 transition-opacity duration-200"
                                        ></div>
                                    </div>
                                </label>
                            </td>
                            <td class="px-3 py-2">
                                <img :src="track.albumArt" alt="album art" class="w-10 h-10 rounded object-cover" />
                            </td>
                            <td class="px-3 py-2 text-white font-medium truncate max-w-xs">{{ track.name }}</td>
                            <td class="px-3 py-2 truncate max-w-xs">{{ track.artist || 'Unknown Artist' }}</td>
                            <td class="px-3 py-2 truncate max-w-xs">{{ track.albumName }}</td>
                            <td class="px-3 py-2 text-gray-400">{{ track.year || '-' }}</td>
                        </tr>
                        <tr v-if="filteredTracks.length === 0 && tracks.length > 0">
                            <td colspan="6" class="text-center py-8 text-gray-400">No tracks match your search</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Playlist, Track } from '@/Types/Playlist';

const props = defineProps<{
    header: string;
    tracks: Track[];
    loading?: boolean;
}>();

const model = defineModel<Set<string>>({ default: () => new Set() });
const search = ref('');
const searchType = ref('all');

const hasPlaylist = computed(() => props.tracks !== undefined);
const loading = computed(() => props.loading || false);

const filteredTracks = computed(() => {
    if (!search.value) return props.tracks;
    
    const searchValue = search.value.toLowerCase();
    
    return props.tracks.filter(track => {
        switch (searchType.value) {
            case 'name':
                return track.name.toLowerCase().includes(searchValue);
            case 'artist':
                return track.artist && track.artist.toLowerCase().includes(searchValue);
            case 'album':
                return track.albumName && track.albumName.toLowerCase().includes(searchValue);
            case 'year':
                return track.year && track.year.toString().includes(searchValue);
            case 'genre':
                return track.genre && track.genre.toLowerCase().includes(searchValue);
            case 'all':
            default:
                return (
                    track.name.toLowerCase().includes(searchValue) ||
                    (track.artist && track.artist.toLowerCase().includes(searchValue)) ||
                    (track.albumName && track.albumName.toLowerCase().includes(searchValue)) ||
                    (track.year && track.year.toString().includes(searchValue)) ||
                    (track.genre && track.genre.toLowerCase().includes(searchValue))
                );
        }
    });
});

const allSelected = computed(() =>
    filteredTracks.value.length > 0 && filteredTracks.value.every(track => model.value.has(String(track.id)))
);

function toggleSelectAll() {
    if (allSelected.value) {
        filteredTracks.value.forEach(track => model.value.delete(String(track.id)));
    } else {
        filteredTracks.value.forEach(track => model.value.add(String(track.id)));
    }
}

function getSearchPlaceholder() {
    switch (searchType.value) {
        case 'name':
            return 'Search by track name...';
        case 'artist':
            return 'Search by artist...';
        case 'album':
            return 'Search by album...';
        case 'year':
            return 'Search by year...';
        case 'genre':
            return 'Search by genre...';
        default:
            return 'Search by track name, artist, album, or year...';
    }
}

function clearSearch() {
    search.value = '';
}
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
