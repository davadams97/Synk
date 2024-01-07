<template>
    <container>
        <template v-slot:main>
            <data-table
                class="w-full"
                no-data-text="No tracks found."
                :headers="['Song title', 'Album']"
                :data-list="trackList"
            >
            </data-table>
        </template>
    </container>
</template>
<script setup lang="ts">
import DataTable from "@/Components/DataTable.vue";
import Container from "@/Components/Container.vue";

import { Link } from "@inertiajs/vue3";

defineProps<{
    trackList: [];
    playlistName: string;
    playlistId: string;
}>();
</script>
