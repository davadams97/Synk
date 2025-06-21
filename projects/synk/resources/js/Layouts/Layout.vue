<template>
    <div class="relative">
        <!-- Transparent toolbar overlay -->
        <div class="absolute top-0 left-0 right-0 z-50 bg-transparent">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <a :href="route('home')" class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 tracking-widest hover:from-purple-300 hover:to-pink-300 transition-all duration-300 drop-shadow-lg">
                        SYNK
                    </a>
                    <div class="flex space-x-4">
                        <!-- Add navigation items here if needed -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="min-h-screen">
            <slot></slot>
        </div>
    </div>
</template>
