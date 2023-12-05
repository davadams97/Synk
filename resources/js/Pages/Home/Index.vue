<template>
    <div class="flex h-full items-center justify-center">
        <a
            v-for="button in buttonConfig"
            class="flex text-slate-50 w-50 h-12 m-2 p-4 items-center rounded-md bg-slate-600"
            :href="button.href"
        >
            {{ button.label }}
        </a>

        <Link
            class="flex text-slate-50 w-50 h-12 m-2 p-4 items-center rounded-md bg-slate-600"
            :href="tranferButtonConfig.href"
            method="post"
            as="button"
            :data="{ title: 'Test Playlist' }"
        >
            {{ tranferButtonConfig.label }}
        </Link>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

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

const tranferButtonConfig = {
    providerName: "Youtube Music",
    label: "Transfer Playlist",
    href: route("transfer.playlist"),
};
</script>
