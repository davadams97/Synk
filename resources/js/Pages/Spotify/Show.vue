<template>
    <div>
        <section>
            <h2 class="text-slate-50 w-50 h-12 m-2 p-4">
                Showing tracks for {{ playlistName }}
            </h2>

            <data-table
                no-data-text="No tracks found."
                :headers="['Song title', 'Album']"
                :data-list="playlist"
            >
            </data-table>
        </section>
    </div>
</template>
<script setup lang="ts">
import DataTable from "@/Components/DataTable.vue";

defineProps<{
    playlist: [];
    playlistName: string;
}>();
</script>
