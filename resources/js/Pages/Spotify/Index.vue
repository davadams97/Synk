<template>
    <div>
        <section>
            <h2 class="text-slate-50 w-50 h-12 m-2 p-4">
                {{ userName }}'s current playlist for Spotify
            </h2>

            <data-table
                header="Name of Playlist"
                no-data-text="No playlists found."
                unnamed-entry="Unnamed playlist"
                :route-data="{
                    name: 'spotify.playlist.list',
                    params: 'playlist.id',
                }"
                :data-list="playlists"
            >
            </data-table>
        </section>
    </div>
</template>

<script setup>
import DataTable from "@/Components/DataTable.vue";
const props = defineProps({
    userName: String,
    playlists: Array,
});
</script>
