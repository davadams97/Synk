<template>
    <div class="flex flex-col w-screen p-8 m-12">
        <h2 class="text-center font-bold text-3xl text-slate-200">
            Where would you like to transfer from?
        </h2>
        <div class="w-1/2 h-screen bg-gray-700/50 flex self-center justify-items-start rounded-2xl p-4 m-8">
            <div class="flex flex-row h-full items-start">
                <a
                    v-for="button in buttonConfig"
                    class="flex relative text-slate-50 w-50 h-50 m-4 p-4 hover:bg-slate-700 items-center rounded-md"
                    :href="button.href"
                >
                    <img :src="button.logo" :alt="button.alt" />
                    <span
                        v-if="button.isConnected"
                        class="w-[10px] h-[10px] rounded-full bg-green-500 border-2 border-white absolute right-6 bottom-5"
                    ></span>
                    <span
                        v-else
                        class="w-[10px] h-[10px] rounded-full bg-red-500 border-2 border-white absolute right-6 bottom-5"
                    ></span>
                </a>
            </div>
        </div>
    </div>

</template>

<script setup lang="ts">
defineProps<{
    buttonConfig: {
        providerName: string;
        isConnected: boolean;
        href: string;
        logo: any;
        alt: string;
    }[];
}>();
</script>
