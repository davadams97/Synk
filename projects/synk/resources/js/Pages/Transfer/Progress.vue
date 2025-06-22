<template>
    <div>
        <container>
            <template v-slot:header>
                <div class="text-center">
                    <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                        {{ isCompleted ? 'Transfer Complete!' : 'Transferring Your Music' }}
                    </h2>
                    <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                        {{ isCompleted ? 'Your tracks have been successfully transferred' : 'Please wait while we transfer your selected tracks' }}
                    </p>
                </div>
            </template>
            
            <template v-slot:main>
                <!-- Progress Bar -->
                <ProgressBar :current-step="4" :source-provider="source" :target-provider="target" />
                
                <div class="max-w-2xl mx-auto space-y-8">
                    <!-- Transfer Info -->
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-8">
                        <div class="text-center space-y-4">
                            <div class="flex items-center justify-center space-x-3">
                                <div 
                                    :class="[
                                        'w-3 h-3 rounded-full',
                                        isCompleted ? 'bg-green-400' : 'bg-green-400 animate-pulse'
                                    ]"
                                ></div>
                                <span class="text-green-300 font-medium">
                                    {{ getCompletionMessage() }}
                                </span>
                            </div>
                            
                            <div class="space-y-2">
                                <h3 class="text-2xl font-semibold text-white">
                                    {{ selectedTracksSet.size }} tracks
                                </h3>
                                <p class="text-gray-400">
                                    {{ getCompletionDescription() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div v-if="!isCompleted" class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-8">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-white font-medium">Transfer Progress</span>
                                <span class="text-purple-300 font-medium">{{ Math.round(progress) }}%</span>
                            </div>
                            
                            <div class="relative">
                                <div class="w-full bg-white/10 rounded-full h-3 overflow-hidden">
                                    <div 
                                        class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-500 ease-out"
                                        :style="{ width: `${progress}%` }"
                                    ></div>
                                </div>
                                
                                <!-- Animated dots -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="flex space-x-1">
                                        <div 
                                            v-for="i in 3" 
                                            :key="i"
                                            class="w-2 h-2 bg-white rounded-full animate-pulse"
                                            :style="{ animationDelay: `${i * 0.2}s` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <p class="text-gray-400 text-sm">{{ currentStatus }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Completion Actions -->
                    <div v-if="isCompleted" class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-8">
                        <div class="text-center space-y-6">
                            <div class="w-16 h-16 mx-auto rounded-full bg-green-500/20 flex items-center justify-center">
                                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            
                            <div class="space-y-4">
                                <h3 class="text-xl font-semibold text-white">What would you like to do next?</h3>
                                
                                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                    <button
                                        @click="transferMoreSongs"
                                        class="group relative inline-flex items-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-2xl hover:shadow-purple-500/25 transition-all duration-300 hover:scale-105 hover:from-purple-500 hover:to-pink-500"
                                    >
                                        <span class="relative z-10">Transfer More Songs</span>
                                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </button>
                                    
                                    <a
                                        :href="route('home')"
                                        class="inline-flex items-center px-8 py-4 text-lg font-medium text-gray-300 hover:text-white transition-colors duration-300 hover:scale-105"
                                    >
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        Back to Home
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Details -->
                    <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-8">
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold text-white text-center">Transfer Details</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center space-x-3 p-3 rounded-lg bg-white/5">
                                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">Source</p>
                                        <p class="text-gray-400 text-sm capitalize">{{ source }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-3 p-3 rounded-lg bg-white/5">
                                    <div class="w-10 h-10 rounded-lg bg-pink-500/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium">Destination</p>
                                        <p class="text-gray-400 text-sm capitalize">{{ target }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Failed Tracks Section -->
                    <div v-if="isCompleted && props.failedTracks.length > 0" class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 p-8">
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold text-white text-center flex items-center justify-center space-x-2">
                                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span>Tracks That Couldn't Be Found ({{ props.failedTracks.length }})</span>
                            </h3>
                            
                            <div class="max-h-64 overflow-y-auto space-y-2">
                                <div 
                                    v-for="track in props.failedTracks" 
                                    :key="track.name"
                                    class="flex items-center justify-between p-3 rounded-lg bg-white/5 border border-white/10"
                                >
                                    <div class="flex-1">
                                        <p class="text-white font-medium">{{ track.name }}</p>
                                        <p class="text-gray-400 text-sm">{{ track.artist || 'Unknown Artist' }}</p>
                                        <p v-if="track.album" class="text-gray-500 text-xs">{{ track.album }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-yellow-400 text-sm">Not found</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <p class="text-gray-400 text-sm">
                                    These tracks couldn't be found on {{ target }}. This might be due to:
                                </p>
                                <ul class="text-gray-500 text-xs mt-2 space-y-1">
                                    <li>• Different track titles or artist names</li>
                                    <li>• Regional availability restrictions</li>
                                    <li>• Tracks not available on the destination platform</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </container>

        <!-- Notification -->
        <Notification 
            v-if="showNotification"
            :message="notificationMessage"
            :type="notificationType"
            @close="showNotification = false"
        />
    </div>
</template>

<script setup lang="ts">
import Container from "@/Components/Container.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import Notification from "@/Components/Notification.vue";
import { ref, computed, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";
import { addTransfer } from "@/stores/notificationStore";

const props = defineProps<{
    selectedTracks: any; // Laravel Collection
    source: string;
    target: string;
    failedTracks: any[]; // Failed tracks from backend
}>();

const progress = ref(0);
const currentStatus = ref("Initializing transfer...");
const showNotification = ref(false);
const notificationMessage = ref("");
const notificationType = ref<"success" | "error">("success");
const isCompleted = ref(false);

// Convert Laravel Collection to Set for easier handling
const selectedTracksSet = computed(() => {
    if (Array.isArray(props.selectedTracks)) {
        return new Set(props.selectedTracks);
    }
    // If it's a Laravel Collection, convert to array first
    return new Set(Object.values(props.selectedTracks || {}));
});

// Calculate estimated time based on number of tracks
const estimatedTime = computed(() => {
    const trackCount = selectedTracksSet.value.size;
    if (trackCount <= 10) return "1-2 minutes";
    if (trackCount <= 50) return "3-5 minutes";
    if (trackCount <= 100) return "5-8 minutes";
    return "8-12 minutes";
});

// Status messages for different progress stages
const statusMessages = [
    "Initializing transfer...",
    "Connecting to source platform...",
    "Reading playlist data...",
    "Processing track information...",
    "Connecting to destination platform...",
    "Creating new playlist...",
    "Transferring tracks...",
    "Finalizing transfer...",
    "Transfer complete!"
];

let progressInterval: number | null = null;

function startProgress() {
    progressInterval = window.setInterval(() => {
        if (progress.value < 100) {
            progress.value += Math.random() * 3 + 1; // Random increment between 1-4%
            
            // Update status message based on progress
            const statusIndex = Math.floor((progress.value / 100) * (statusMessages.length - 1));
            currentStatus.value = statusMessages[statusIndex];
            
            if (progress.value >= 100) {
                progress.value = 100;
                currentStatus.value = statusMessages[statusMessages.length - 1];
                completeTransfer();
            }
        }
    }, 800); // Update every 800ms
}

function completeTransfer() {
    if (progressInterval) {
        clearInterval(progressInterval);
        progressInterval = null;
    }
    
    // Set completion state
    isCompleted.value = true;
    
    // Add transfer to notification store if successful
    const successfulCount = selectedTracksSet.value.size - props.failedTracks.length;
    if (successfulCount > 0) {
        const transferData = {
            source: props.source,
            target: props.target,
            tracks: Array.from(selectedTracksSet.value),
            failedTracks: props.failedTracks,
            status: props.failedTracks.length > 0 ? 'partial' : 'completed'
        };
        addTransfer(transferData);
    }
    
    // Show appropriate notification based on results
    setTimeout(() => {
        showNotification.value = true;
        if (props.failedTracks.length > 0) {
            const successfulCount = selectedTracksSet.value.size - props.failedTracks.length;
            if (successfulCount === 0) {
                notificationMessage.value = `No tracks could be found on ${props.target}`;
                notificationType.value = "error";
            } else if (successfulCount === 1) {
                notificationMessage.value = `1 track transferred to ${props.target}`;
                notificationType.value = "success";
            } else {
                notificationMessage.value = `${successfulCount} tracks transferred to ${props.target}`;
                notificationType.value = "success";
            }
        } else {
            notificationMessage.value = `Successfully transferred ${selectedTracksSet.value.size} tracks to ${props.target}!`;
            notificationType.value = "success";
        }
    }, 1000);
}

function transferMoreSongs() {
    router.visit(route('transfer.target', { source: props.source }));
}

function getCompletionMessage() {
    if (!isCompleted.value) {
        return 'Transfer in progress...';
    }
    if (props.failedTracks.length > 0) {
        const successfulCount = selectedTracksSet.value.size - props.failedTracks.length;
        if (successfulCount === 0) {
            return "Transfer incomplete - no tracks found";
        } else if (successfulCount === 1) {
            return "Transfer completed with 1 track transferred";
        } else {
            return `Transfer completed with ${successfulCount} tracks transferred`;
        }
    }
    return "Transfer completed successfully!";
}

function getCompletionDescription() {
    if (!isCompleted.value) {
        return 'Estimated time: ' + estimatedTime.value;
    }
    if (props.failedTracks.length > 0) {
        const successfulCount = selectedTracksSet.value.size - props.failedTracks.length;
        if (successfulCount === 0) {
            return `None of the ${selectedTracksSet.value.size} tracks could be found on ${props.target}`;
        } else if (successfulCount === 1) {
            return `1 of ${selectedTracksSet.value.size} tracks transferred to ${props.target}`;
        } else {
            return `${successfulCount} of ${selectedTracksSet.value.size} tracks transferred to ${props.target}`;
        }
    }
    return `Successfully transferred ${selectedTracksSet.value.size} tracks to ${props.target}`;
}

onMounted(() => {
    // Start the progress simulation after a short delay
    setTimeout(() => {
        startProgress();
    }, 1000);
});

onUnmounted(() => {
    if (progressInterval) {
        clearInterval(progressInterval);
    }
});
</script> 