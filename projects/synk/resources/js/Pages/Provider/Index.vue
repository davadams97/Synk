<template>
    <div class="flex h-screen bg-gradient-to-br from-gray-900 via-gray-950 to-purple-950 pt-16">
        <!-- Playlist Sidebar -->
        <div class="w-72 h-full flex flex-col">
            <PlaylistSidebar
                :playlists="playlists"
                :selected-id="selectedPlaylist?.id ?? null"
                @select="handleSelectPlaylist"
            />
        </div>

        <!-- Main Area: Tracks Table -->
        <div class="flex-1 p-8 overflow-y-auto pb-32">
            <!-- Progress Bar -->
            <div class="mb-8">
                <ProgressBar :current-step="3" :source-provider="source || undefined" :target-provider="target || undefined" />
            </div>

            <data-table
                class="w-full"
                :header="header"
                :tracks="selectedPlaylist?.tracks ?? []"
                :loading="isLoadingTracks"
                v-model="selectedTracks"
            />

            <!-- Sticky Transfer Action Bar -->
            <div
                class="fixed left-0 right-0 bottom-0 z-50 flex flex-col items-center justify-end pointer-events-none"
                style="padding-bottom: max(env(safe-area-inset-bottom), 1.5rem);"
            >
                <button
                    @click="startTransfer"
                    class="pointer-events-auto group relative inline-flex items-center px-8 py-4 mb-2 text-lg font-semibold text-white bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-2xl hover:shadow-purple-500/25 transition-all duration-300 hover:scale-105 hover:from-purple-500 hover:to-pink-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="{ 'opacity-50 cursor-not-allowed': selectedTracks.size === 0 }"
                    :disabled="selectedTracks.size === 0"
                >
                    <span class="relative z-10">
                        {{ selectedTracks.size === 0 ? 'Select tracks to transfer' : `Start Transfer (${selectedTracks.size} tracks)` }}
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <svg
                        v-if="selectedTracks.size > 0"
                        class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform duration-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import DataTable from "@/Components/DataTable.vue";
import PlaylistSidebar from "@/Components/PlaylistSidebar.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import { Playlist } from "@/Types/Playlist";
import { ref, computed, watch } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";

const props = defineProps<{
    playlists: Playlist[];
    header: string;
    transferRoute: string;
}>();

const searchParams = new URLSearchParams(window.location.search);
const source = searchParams.has("source") ? searchParams.get("source") : null;
const target = searchParams.has("target") ? searchParams.get("target") : null;

const selectedTracks = ref<Set<string>>(new Set());
const selectedPlaylist = ref<Playlist | null>(props.playlists[0] ?? null);
const loadingTracks = ref<Set<string | number>>(new Set());

// Debug: Log the playlists to see what we're working with
console.log('Available playlists:', props.playlists);

async function handleSelectPlaylist(playlist: Playlist) {
    console.log('Selected playlist:', playlist);
    selectedPlaylist.value = playlist;
    selectedTracks.value = new Set(); // Reset selection when switching playlists
    
    // Load tracks if they haven't been loaded yet
    if (playlist.tracks.length === 0 && (playlist.trackCount || 0) > 0) {
        await loadPlaylistTracks(playlist.id);
    }
}

async function loadPlaylistTracks(playlistId: string | number) {
    loadingTracks.value.add(playlistId);
    
    try {
        // Determine the route based on the current URL
        const currentPath = window.location.pathname;
        let route;
        
        if (currentPath.includes('/spotify/playlist')) {
            route = `/spotify/playlist/${playlistId}/tracks`;
        } else if (currentPath.includes('/youtube/playlist')) {
            route = `/youtube/playlist/${playlistId}/tracks`;
        } else {
            throw new Error('Unknown playlist provider');
        }
        
        console.log('Loading tracks from:', route);
        const response = await axios.get(route);
        const tracks = response.data.tracks;
        
        console.log('Loaded tracks:', tracks);
        
        // Find and update the playlist
        const playlist = props.playlists.find(p => p.id === playlistId);
        if (playlist) {
            playlist.tracks = tracks;
            // Update the selected playlist reference
            if (selectedPlaylist.value?.id === playlistId) {
                selectedPlaylist.value = playlist;
            }
        }
    } catch (error) {
        console.error('Failed to load playlist tracks:', error);
    } finally {
        loadingTracks.value.delete(playlistId);
    }
}

function startTransfer() {
    // Get the full track objects for selected tracks
    const selectedTrackObjects = Array.from(selectedTracks.value).map(trackId => {
        return selectedPlaylist.value?.tracks.find(track => String(track.id) === trackId);
    }).filter(track => track !== undefined);
    
    router.visit('/transfer/progress', {
        method: 'post',
        data: {
            selectedTracks: selectedTrackObjects,
            source: source,
            target: target
        }
    });
}

// Load tracks for the first playlist if it has tracks but they're not loaded
if (selectedPlaylist.value && selectedPlaylist.value.tracks.length === 0 && (selectedPlaylist.value.trackCount || 0) > 0) {
    loadPlaylistTracks(selectedPlaylist.value.id);
}

const isLoadingTracks = computed(() => {
    return loadingTracks.value.size > 0;
});
</script>
