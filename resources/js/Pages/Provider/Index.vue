<template>
    <container>
        <template v-slot:main>
            <data-table
                class="w-full"
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
        </template>
    </container>
</template>

<script setup lang="ts">
import DataTable from "@/Components/DataTable.vue";
import Container from "@/Components/Container.vue";

defineProps<{
    playlists: [];
}>();
</script>
