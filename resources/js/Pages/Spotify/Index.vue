<template>
    <div>
        <section>
            <h2 class="text-slate-50 w-50 h-12 m-2 p-4">
                Showing {{ userName }}'s Spotify playlists
            </h2>

            <data-table
                no-data-text="No playlists found."
                unnamed-entry="Unnamed playlist"
                :headers="['Name of Playlist']"
                :route-data="{
                    name: 'spotify.playlist.list',
                    params: 'id',
                }"
                :data-list="playlists"
            >
            </data-table>
        </section>
    </div>
</template>

<script setup lang="ts">
import DataTable from "@/Components/DataTable.vue";

defineProps<{
    userName: string,
    playlists: [],
}>();
</script>
