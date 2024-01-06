<template>
    <div class="flex flex-col w-screen p-8 m-12">
        <div
            class="w-9/12 h-4/5 bg-gray-700/50 flex self-center justify-items-start rounded-2xl p-4 m-8"
        >
            <data-table
                class="w-full"
                no-data-text="No tracks found."
                :headers="['Song title', 'Album']"
                :data-list="trackList"
            >
            </data-table>
        </div>
    </div>
</template>
<script setup lang="ts">
import DataTable from "@/Components/DataTable.vue";
import { Link } from "@inertiajs/vue3";

defineProps<{
    trackList: [];
    playlistName: string;
    playlistId: string;
}>();

</script>
