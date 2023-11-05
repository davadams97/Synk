<template>
    <div class="flex bg-sky-950 h-screen items-center justify-center">
        <button
            class="flex text-slate-50 w-50 h-12 m-2 p-4 items-center rounded-md bg-slate-600"
        >
            {{ buttonText.connectToSpotify }}
        </button>
    </div>
</template>

<script setup>
const buttonText = {
    connectToSpotify: "Connect to Spotify",
};
</script>
