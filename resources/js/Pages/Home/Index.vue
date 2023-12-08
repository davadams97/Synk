<template>
    <div class="flex h-full items-center justify-center">
        <a
            v-for="button in buttonConfig"
            class="flex text-slate-50 w-50 h-12 m-2 p-4 items-center rounded-md bg-slate-600"
            :href="button.href"
        >
            {{ button.label }}
        </a>
    </div>
</template>

<script setup>
const buttonConfig = [
    {
        providerName: "Spotify",
        label: "Connect to Spotify",
        href: route("spotify.authorize"),
    },
    {
        providerName: "Youtube Music",
        label: "Connect to Youtube Music",
        href: route("youtube.playlist"),
    },
];
</script>
