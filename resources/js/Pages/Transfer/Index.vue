<template>
    <div class="w-1/2 bg-gray-700/50 rounded-2xl p-4 m-8">
        <div class="flex h-full items-center justify-center">
            <a
                v-for="button in buttonConfig"
                class="flex relative text-slate-50 w-50 h-50 m-4 p-4 hover:bg-slate-700 focus:ring-4 focus:ring-blue-300
                 items-center rounded-md bg-slate-600/40"
                :href="button.href"
            >
                <img :src="button.logo" :alt="button.alt">
                <span v-if="button.isConnected"
                      class="w-[10px] h-[10px] rounded-full bg-green-500 border-2 border-white absolute right-6 bottom-5"></span>
                <span v-else
                      class="w-[10px] h-[10px] rounded-full bg-red-500 border-2 border-white absolute right-6 bottom-5 "></span>

            </a>
        </div>
    </div>

    <span class="flex items-center scale-150 text-white">&#8594;</span>

    <div class="w-1/2 bg-gray-700/50 rounded-2xl p-4 m-8">
        <div class="flex h-full items-center justify-center">
            <a
                v-for="button in buttonConfig"
                class="flex relative text-slate-50 w-50 h-50 m-4 p-4 hover:bg-slate-700 focus:ring-4 focus:ring-blue-300
                 items-center rounded-md bg-slate-600/40"
                :href="button.href"
            >
                <img :src="button.logo" :alt="button.alt">
                <span v-if="button.isConnected"
                      class="w-[10px] h-[10px] rounded-full bg-green-500 border-2 border-white absolute right-6 bottom-5"></span>
                <span v-else
                      class="w-[10px] h-[10px] rounded-full bg-red-500 border-2 border-white absolute right-6 bottom-5 "></span>
            </a>
        </div>
    </div>
</template>

<script setup lang="ts">

defineProps<{
    buttonConfig: { providerName: string, isConnected: boolean, href: string, logo: any, alt: string }[]
}>();

</script>
