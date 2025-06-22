<template>
    <div>
        <!-- Backdrop -->
        <div 
            v-if="isOpen" 
            @click="closeSidebar"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 transition-opacity duration-300"
        ></div>
        
        <!-- Sidebar -->
        <div 
            :class="[
                'fixed top-0 right-0 h-full w-96 bg-white/5 backdrop-blur-md border-l border-white/10 z-50 transform transition-transform duration-300 ease-in-out',
                isOpen ? 'translate-x-0' : 'translate-x-full'
            ]"
        >
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-white/10">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-white mb-1">Transfer History</h2>
                        <p class="text-sm text-gray-400">{{ totalTracksTransferred }} tracks transferred</p>
                    </div>
                </div>
                
                <button 
                    @click="closeSidebar"
                    class="p-2 rounded-lg hover:bg-white/10 transition-colors duration-200"
                >
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <div v-if="successfulTransfers.length === 0" class="text-center py-12">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gray-500/20 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No transfers yet</h3>
                    <p class="text-gray-400">Your transfer history will appear here after you complete transfers.</p>
                </div>
                
                <div v-else class="space-y-4">
                    <div 
                        v-for="transfer in successfulTransfers" 
                        :key="transfer.id"
                        class="bg-white/5 rounded-xl border border-white/10 p-4"
                    >
                        <!-- Transfer Header -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">
                                        {{ transfer.source }} → {{ transfer.target }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ formatTime(transfer.timestamp) }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-sm font-medium text-white">
                                    {{ transfer.tracks.length - transfer.failedTracks.length }} tracks
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ transfer.status === 'partial' ? 'Partial' : 'Complete' }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- Tracks List -->
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <div 
                                v-for="track in transfer.tracks" 
                                :key="track.name"
                                class="flex items-center justify-between p-2 rounded-lg bg-white/5"
                            >
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white truncate">{{ track.name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ track.artist || 'Unknown Artist' }}</p>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <div 
                                        v-if="transfer.failedTracks.some((f: any) => f.name === track.name)"
                                        class="w-2 h-2 rounded-full bg-yellow-400"
                                        title="Failed to transfer"
                                    ></div>
                                    <div 
                                        v-else
                                        class="w-2 h-2 rounded-full bg-green-400"
                                        title="Successfully transferred"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Failed Tracks Summary -->
                        <div v-if="transfer.failedTracks.length > 0" class="mt-3 pt-3 border-t border-white/10">
                            <p class="text-xs text-yellow-400">
                                {{ transfer.failedTracks.length }} track{{ transfer.failedTracks.length > 1 ? 's' : '' }} couldn't be found
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { 
    closeSidebar, 
    successfulTransfers, 
    totalTracksTransferred 
} from '@/stores/notificationStore'

const props = defineProps<{
    isOpen: boolean
}>()

function formatTime(date: Date): string {
    const now = new Date()
    const diff = now.getTime() - date.getTime()
    const minutes = Math.floor(diff / 60000)
    const hours = Math.floor(diff / 3600000)
    const days = Math.floor(diff / 86400000)
    
    if (minutes < 1) return 'Just now'
    if (minutes < 60) return `${minutes}m ago`
    if (hours < 24) return `${hours}h ago`
    if (days < 7) return `${days}d ago`
    
    return date.toLocaleDateString()
}
</script> 