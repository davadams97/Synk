<template>
    <container>
        <template v-slot:header>
            <h2 class="text-center font-bold text-3xl text-slate-200">
                {{ header }}
            </h2>
        </template>
        <template v-slot:main>
            <div
                class="flex flex-row h-full items-start"
                v-for="button in buttonConfig"
            >
                <a
                    class="flex focus:outline-none focus:ring focus:ring-violet-300 relative text-slate-50 w-50 h-50 m-4 p-4 hover:bg-slate-700 items-center rounded-md"
                    :href="route(button.href, { source: button.providerName })"
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
        </template>
    </container>
</template>

<script setup lang="ts">
import Container from "@/Components/Container.vue";

type Provider = "spotify" | "ytmusic";

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
