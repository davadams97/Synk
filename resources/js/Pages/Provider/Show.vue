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

        <template v-slot:footer>
            <Link
                :href="''"
                class="inline-flex items-center px-5 py-3 text-base max-w-fit self-center font-medium text-center text-white bg-purple-700 rounded-lg hover:bg-purple-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900"
                method="post"
                as="button"
                :data="{
                    // title: 'Test Playlist',
                    // name: trackList.map((val) => val['columns'][0]),
                    // currentProvider: 'spotify',
                    // targetProvider: 'ytmusic',
                }"
            >
                Transfer playlist
                <svg
                    aria-hidden="true"
                    class="w-3.5 h-3.5 ms-2 rtl:rotate-180"
                    fill="none"
                    viewBox="0 0 14 10"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M1 5h12m0 0L9 1m4 4L9 9"
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                    />
                </svg>
            </Link>
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
