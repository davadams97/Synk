import { ref, computed } from 'vue'

interface TransferTrack {
    name: string
    artist?: string
    album?: string
}

interface TransferRecord {
    id: number
    timestamp: Date
    source: string
    target: string
    tracks: TransferTrack[]
    failedTracks: TransferTrack[]
    status: string
}

// Create a reactive store for notifications
const transferHistory = ref<TransferRecord[]>([])
const notificationCount = ref(0)
const isSidebarOpen = ref(false)

// Add a new transfer to history
function addTransfer(transfer: {
    source: string
    target: string
    tracks: TransferTrack[]
    failedTracks?: TransferTrack[]
    status: string
}) {
    const transferRecord: TransferRecord = {
        id: Date.now(),
        timestamp: new Date(),
        source: transfer.source,
        target: transfer.target,
        tracks: transfer.tracks,
        failedTracks: transfer.failedTracks || [],
        status: transfer.status
    }
    
    transferHistory.value.unshift(transferRecord)
    notificationCount.value++
}

// Clear all notifications
function clearNotifications() {
    notificationCount.value = 0
}

// Toggle sidebar
function toggleSidebar() {
    isSidebarOpen.value = !isSidebarOpen.value
    if (isSidebarOpen.value) {
        clearNotifications()
    }
}

// Close sidebar
function closeSidebar() {
    isSidebarOpen.value = false
}

// Get successful transfers only
const successfulTransfers = computed(() => {
    return transferHistory.value.filter(transfer => 
        transfer.status === 'completed' || transfer.status === 'partial'
    )
})

// Get total tracks transferred
const totalTracksTransferred = computed(() => {
    return successfulTransfers.value.reduce((total, transfer) => {
        return total + (transfer.tracks.length - transfer.failedTracks.length)
    }, 0)
})

export {
    transferHistory,
    notificationCount,
    isSidebarOpen,
    addTransfer,
    clearNotifications,
    toggleSidebar,
    closeSidebar,
    successfulTransfers,
    totalTracksTransferred
} 