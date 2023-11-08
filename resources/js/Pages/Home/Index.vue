<template>
    <div class="flex bg-sky-950 h-screen items-center justify-center">
        <a
            class="flex text-slate-50 w-50 h-12 m-2 p-4 items-center rounded-md bg-slate-600"
            :href="route('spotify.authorize')"
        >
            {{ buttonText.connectToSpotify }}
        </a>
    </div>
</template>

<script setup>

const buttonText = {
    connectToSpotify: "Connect to Spotify",
};
</script>
