<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform translate-x-full opacity-0"
        enter-to-class="transform translate-x-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform translate-x-0 opacity-100"
        leave-to-class="transform translate-x-full opacity-0"
    >
        <div 
            v-if="isVisible"
            class="fixed top-4 right-4 z-50 max-w-sm w-full"
        >
            <div 
                class="bg-white/10 backdrop-blur-md border rounded-2xl p-4 shadow-2xl"
                :class="{
                    'border-green-500/30': type === 'success',
                    'border-red-500/30': type === 'error'
                }"
            >
                <div class="flex items-start space-x-3">
                    <!-- Icon -->
                    <div 
                        class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                        :class="{
                            'bg-green-500/20': type === 'success',
                            'bg-red-500/20': type === 'error'
                        }"
                    >
                        <svg 
                            v-if="type === 'success'"
                            class="w-5 h-5 text-green-400" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <svg 
                            v-else-if="type === 'error'"
                            class="w-5 h-5 text-red-400" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-medium text-sm">
                            {{ message }}
                        </p>
                    </div>

                    <!-- Close Button -->
                    <button
                        @click="close"
                        class="flex-shrink-0 w-6 h-6 rounded-full bg-white/10 hover:bg-white/20 transition-colors duration-200 flex items-center justify-center"
                    >
                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Progress Bar -->
                <div 
                    v-if="autoDismiss"
                    class="mt-3 w-full bg-white/10 rounded-full h-1 overflow-hidden"
                >
                    <div 
                        class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-100 ease-linear"
                        :style="{ width: `${dismissProgress}%` }"
                    ></div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";

const props = withDefaults(defineProps<{
    message: string;
    type: "success" | "error";
    autoDismiss?: boolean;
    dismissDelay?: number;
}>(), {
    autoDismiss: true,
    dismissDelay: 5000
});

const emit = defineEmits<{
    close: [];
}>();

const isVisible = ref(true);
const dismissProgress = ref(0);

let dismissInterval: number | null = null;

function close() {
    isVisible.value = false;
    emit('close');
}

function startAutoDismiss() {
    if (!props.autoDismiss) return;
    
    const startTime = Date.now();
    const duration = props.dismissDelay;
    
    dismissInterval = window.setInterval(() => {
        const elapsed = Date.now() - startTime;
        dismissProgress.value = (elapsed / duration) * 100;
        
        if (elapsed >= duration) {
            close();
        }
    }, 50); // Update every 50ms for smooth progress
}

onMounted(() => {
    if (props.autoDismiss) {
        startAutoDismiss();
    }
});

onUnmounted(() => {
    if (dismissInterval) {
        clearInterval(dismissInterval);
    }
});
</script> 