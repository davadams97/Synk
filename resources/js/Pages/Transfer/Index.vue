<template>
    <div class="w-1/2 bg-gray-700/50 rounded-2xl p-4 m-8">
        <div class="flex h-full items-center justify-center">
            <a
                v-for="button in buttonConfig"
                class="flex text-slate-50 w-50 h-12 m-2 p-4 items-center rounded-md bg-slate-600"
                :href="button.href"
            >
                {{ button.label }}
            </a>
        </div>
    </div>

    <span class="flex items-center scale-150 text-white">&#8594;</span>

    <div class="w-1/2 bg-gray-700/50 rounded-2xl p-4 m-8">
        <div class="flex h-full items-center justify-center">
            <a
                v-for="button in buttonConfig"
                class="flex text-slate-50 w-50 h-12 m-2 p-4 items-center rounded-md bg-slate-600"
                :href="button.href"
            >
                {{ button.label }}
            </a>
        </div>
    </div>
</template>

<script setup lang="ts">

defineProps<{
    buttonConfig: {providerName: string, label: string, href: string}[]
}>();

</script>
