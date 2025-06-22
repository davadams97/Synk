<template>
    <container>
        <template v-slot:header>
            <div class="text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    Choose Your Source
                </h2>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                    Select the music platform where your playlists are currently stored
                </p>
            </div>
        </template>
        
        <template v-slot:main>
            <!-- Progress Bar -->
            <ProgressBar :current-step="1" />
            
            <div class="flex flex-col md:flex-row gap-8 max-w-4xl mx-auto items-center justify-center">
                <div
                    v-for="button in buttonConfig"
                    :key="button.providerName"
                    class="group relative flex-1 max-w-sm"
                >
                    <a
                        class="block bg-white/5 backdrop-blur-sm rounded-3xl p-8 hover:bg-white/10 transition-all duration-500 hover:scale-105 group-hover:shadow-xl group-hover:shadow-purple-500/20"
                        :href="route(button.href, { source: button.providerName })"
                    >
                        <!-- Provider Logo -->
                        <div class="flex items-center justify-center mb-6">
                            <div class="w-20 h-20 flex items-center justify-center group-hover:scale-110 transition-all duration-500">
                                <img 
                                    :src="button.logo" 
                                    :alt="button.alt" 
                                    class="w-16 h-16 object-contain"
                                />
                            </div>
                        </div>

                        <!-- Provider Name -->
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-semibold text-white capitalize mb-2">
                                {{ button.providerName === 'ytMusic' ? 'YouTube Music' : button.providerName }}
                            </h3>
                            <div class="w-16 h-0.5 bg-gradient-to-r from-purple-400 to-pink-400 mx-auto rounded-full opacity-60"></div>
                        </div>

                        <!-- Connection Status -->
                        <div class="flex items-center justify-center space-x-3">
                            <div 
                                :class="[
                                    'w-3 h-3 rounded-full transition-all duration-300',
                                    button.isConnected ? 'bg-green-400 shadow-md shadow-green-400/30' : 'bg-red-400 shadow-md shadow-red-400/30'
                                ]"
                            ></div>
                            <span class="text-sm text-gray-300 font-medium">
                                {{ button.isConnected ? 'Connected' : 'Not Connected' }}
                            </span>
                        </div>

                        <!-- Subtle Hover Effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/5 to-pink-600/5 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </a>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-12">
                <a 
                    :href="route('home')"
                    class="inline-flex items-center px-6 py-3 text-lg font-medium text-gray-300 hover:text-white transition-all duration-300 hover:scale-105"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Home
                </a>
            </div>
        </template>
    </container>
</template>

<script setup lang="ts">
import Container from "@/Components/Container.vue";
import ProgressBar from "@/Components/ProgressBar.vue";

type Provider = "spotify" | "ytMusic";

defineProps<{
    buttonConfig: {
        providerName: Provider;
        isConnected: boolean;
        href: string;
        logo: any;
        alt: string;
    }[];
    header: string;
}>();
</script>
