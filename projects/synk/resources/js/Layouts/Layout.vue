<template>
    <div class="relative">
        <!-- Header bar -->
        <div class="fixed top-0 left-0 right-0 z-50 bg-white/5 backdrop-blur-sm border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <a :href="route('home')" class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 tracking-widest hover:from-purple-300 hover:to-pink-300 transition-all duration-300 drop-shadow-lg relative z-10">
                        SYNK
                    </a>
                    
                    <!-- Notification Icon -->
                    <button 
                        @click="toggleSidebar"
                        class="relative p-3 rounded-xl hover:bg-white/10 transition-all duration-300 group focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:ring-offset-2 focus:ring-offset-gray-900"
                    >
                        <!-- Bell Icon -->
                        <svg 
                            class="w-6 h-6 text-gray-300 group-hover:text-white transition-all duration-300 group-hover:scale-110" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2" 
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                            />
                        </svg>
                        
                        <!-- Notification Badge -->
                        <div 
                            v-if="notificationCount > 0"
                            class="absolute -top-2 -right-2 min-w-[20px] h-5 bg-gradient-to-r from-red-500 to-pink-500 rounded-full flex items-center justify-center px-1 shadow-lg border border-white/20"
                        >
                            <span class="text-xs font-bold text-white">{{ notificationCount > 99 ? '99+' : notificationCount }}</span>
                        </div>
                        
                        <!-- Pulse Animation for New Notifications -->
                        <div 
                            v-if="notificationCount > 0"
                            class="absolute -top-2 -right-2 w-5 h-5 bg-red-400 rounded-full animate-ping opacity-75"
                        ></div>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="min-h-screen">
            <slot></slot>
        </div>
        
        <!-- Notification Sidebar -->
        <NotificationSidebar :is-open="isSidebarOpen" />
    </div>
</template>

<script setup lang="ts">
import NotificationSidebar from '@/Components/NotificationSidebar.vue'
import { isSidebarOpen, notificationCount, toggleSidebar } from '@/stores/notificationStore'
</script>
