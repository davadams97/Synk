<template>
    <div>
        <section>
            <h2 class="text-slate-50 w-50 h-12 m-2 p-4">
                Showing tracks for {{ playlistName }}
            </h2>

            <Link
                class="flex text-slate-50 w-50 h-12 m-2 p-4 items-center rounded-md bg-slate-600"
                :href="transferButtonConfig.href"
                method="post"
                as="button"
                :data="{
                    title: 'Test Playlist',
                    name: playlist.map(val => val['columns'][0]),
                }"
            >
                {{ transferButtonConfig.label }}
            </Link>

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
import { Link } from "@inertiajs/vue3";

defineProps<{
    playlist: [];
    playlistName: string;
}>();

const transferButtonConfig = {
    providerName: "Youtube Music",
    label: "Transfer Playlist",
    href: route("transfer.playlist"),
};
</script>
