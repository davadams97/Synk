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
                    name: trackList.map((val) => val['columns'][0]),
                    currentProvider: 'ytmusic',
                    targetProvider: 'spotify',
                }"
            >
                {{ transferButtonConfig.label }}
            </Link>

            <data-table
                no-data-text="No tracks found."
                :headers="['Song title', 'Album']"
                :data-list="trackList"
            >
            </data-table>
        </section>
    </div>
</template>
<script setup lang="ts">
import DataTable from "@/Components/DataTable.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps<{
    trackList: [];
    playlistName: string;
    playlistId: string
}>();

const transferButtonConfig = {
    label: "Transfer Playlist",
    href: route("youtube.playlist.transfer", props.playlistId),
};
</script>
