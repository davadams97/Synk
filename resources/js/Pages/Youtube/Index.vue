<template>
    <div class="flex bg-sky-950 h-screen justify-center">
        <section>
            <h2 class="text-slate-50 w-50 h-12 m-2 p-4">
                Showing your current playlist for Youtube
            </h2>
            <div>
                <span class="text-slate-50 w-50 h-12 m-2 p-4"
                    >Showing Youtube playlist for {{ userName }}</span
                >
                <ul v-for="playlist of playlists">
                    <li>{{ playlist.title }}</li>
                </ul>
            </div>
        </section>
    </div>
</template>

<script setup>
const { userName } = defineProps(["userName", "playlists"]);
</script>
