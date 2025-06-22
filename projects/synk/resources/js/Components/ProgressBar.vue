<template>
    <div class="w-full max-w-4xl mx-auto mb-8">
        <div class="flex items-center justify-between">
            <!-- Step 1: Source Selection -->
            <div class="flex flex-col items-center">
                <div 
                    :class="[
                        'w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300 cursor-pointer',
                        currentStep >= 1 
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg shadow-purple-500/25 hover:shadow-xl hover:shadow-purple-500/40 hover:scale-110' 
                            : 'bg-white/10 text-gray-400 border border-white/20 hover:bg-white/20 hover:scale-110'
                    ]"
                    @click="navigateToStep(1)"
                    :title="getStepTitle(1)"
                >
                    <svg v-if="currentStep > 1" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span v-else>1</span>
                </div>
                <span 
                    :class="[
                        'text-sm font-medium mt-2 transition-colors duration-300 text-center',
                        currentStep >= 1 ? 'text-white' : 'text-gray-400'
                    ]"
                >
                    Choose Source
                </span>
            </div>

            <!-- Connector Line 1 -->
            <div class="flex-1 h-0.5 mx-4">
                <div 
                    :class="[
                        'h-full transition-all duration-500',
                        currentStep >= 2 
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500' 
                            : 'bg-white/20'
                    ]"
                ></div>
            </div>

            <!-- Step 2: Target Selection -->
            <div class="flex flex-col items-center">
                <div 
                    :class="[
                        'w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300 cursor-pointer',
                        currentStep >= 2 
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg shadow-purple-500/25 hover:shadow-xl hover:shadow-purple-500/40 hover:scale-110' 
                            : 'bg-white/10 text-gray-400 border border-white/20 hover:bg-white/20 hover:scale-110'
                    ]"
                    @click="navigateToStep(2)"
                    :title="getStepTitle(2)"
                >
                    <svg v-if="currentStep > 2" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span v-else>2</span>
                </div>
                <span 
                    :class="[
                        'text-sm font-medium mt-2 transition-colors duration-300 text-center',
                        currentStep >= 2 ? 'text-white' : 'text-gray-400'
                    ]"
                >
                    Choose Destination
                </span>
            </div>

            <!-- Connector Line 2 -->
            <div class="flex-1 h-0.5 mx-4">
                <div 
                    :class="[
                        'h-full transition-all duration-500',
                        currentStep >= 3 
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500' 
                            : 'bg-white/20'
                    ]"
                ></div>
            </div>

            <!-- Step 3: Playlist Selection -->
            <div class="flex flex-col items-center">
                <div 
                    :class="[
                        'w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300 cursor-pointer',
                        currentStep >= 3 
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg shadow-purple-500/25 hover:shadow-xl hover:shadow-purple-500/40 hover:scale-110' 
                            : 'bg-white/10 text-gray-400 border border-white/20 hover:bg-white/20 hover:scale-110'
                    ]"
                    @click="navigateToStep(3)"
                    :title="getStepTitle(3)"
                >
                    <svg v-if="currentStep > 3" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span v-else>3</span>
                </div>
                <span 
                    :class="[
                        'text-sm font-medium mt-2 transition-colors duration-300 text-center',
                        currentStep >= 3 ? 'text-white' : 'text-gray-400'
                    ]"
                >
                    Select Playlists
                </span>
            </div>

            <!-- Connector Line 3 -->
            <div class="flex-1 h-0.5 mx-4">
                <div 
                    :class="[
                        'h-full transition-all duration-500',
                        currentStep >= 4 
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500' 
                            : 'bg-white/20'
                    ]"
                ></div>
            </div>

            <!-- Step 4: Transfer -->
            <div class="flex flex-col items-center">
                <div 
                    :class="[
                        'w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300 cursor-pointer',
                        currentStep >= 4 
                            ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg shadow-purple-500/25 hover:shadow-xl hover:shadow-purple-500/40 hover:scale-110' 
                            : 'bg-white/10 text-gray-400 border border-white/20 hover:bg-white/20 hover:scale-110'
                    ]"
                    @click="navigateToStep(4)"
                    :title="getStepTitle(4)"
                >
                    <svg v-if="currentStep > 4" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span v-else>4</span>
                </div>
                <span 
                    :class="[
                        'text-sm font-medium mt-2 transition-colors duration-300 text-center',
                        currentStep >= 4 ? 'text-white' : 'text-gray-400'
                    ]"
                >
                    Start Transfer
                </span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';

interface Props {
    currentStep: number;
    sourceProvider?: string;
    targetProvider?: string;
}

const props = withDefaults(defineProps<Props>(), {
    sourceProvider: '',
    targetProvider: ''
});

function getStepTitle(step: number): string {
    switch (step) {
        case 1:
            return 'Go to Source Selection';
        case 2:
            return 'Go to Target Selection';
        case 3:
            return 'Go to Playlist Selection';
        case 4:
            return 'Go to Transfer Progress';
        default:
            return '';
    }
}

function navigateToStep(step: number) {
    // Only allow navigation to completed steps or the current step
    if (step > props.currentStep) {
        return;
    }

    switch (step) {
        case 1:
            router.visit(route('transfer.source'));
            break;
        case 2:
            if (props.sourceProvider) {
                router.visit(route('transfer.target', { source: props.sourceProvider }));
            }
            break;
        case 3:
            if (props.sourceProvider && props.targetProvider) {
                router.visit(route('provider.index', { 
                    source: props.sourceProvider, 
                    target: props.targetProvider 
                }));
            }
            break;
        case 4:
            // Step 4 is the transfer progress page, which is typically reached via form submission
            // No navigation needed for this step
            break;
    }
}
</script> 