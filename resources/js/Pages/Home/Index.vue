<template>
    <div class="flex bg-sky-950 h-screen items-center justify-center">
        <Link
            class="flex text-slate-50 w-50 h-12 m-2 p-4 items-center rounded-md bg-slate-600"
            as="button"
            type="button"
            :href="route('spotify.authorize')"
        >
            {{ buttonText.connectToSpotify }}
        </Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const buttonText = {
    connectToSpotify: "Connect to Spotify",
};
</script>
