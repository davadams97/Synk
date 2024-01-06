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
