<template>
    <div class="bg-transparent rounded-md overflow-x-auto">
        <table class="w-full whitespace-nowrap text-white">
            <tr class="text-left font-bold sticky">
                <th class="pb-4 pt-6">
                    {{ header }}
                </th>
            </tr>
            <tr
                v-for="playlist in playlists"
                :key="playlist.id"
                class=""
            >
                <td class="border-t border-gray-600">
                    <span
                        class="flex items-center px-6 py-4 focus:text-indigo-500"
                    >
                        <input
                            class="mr-2.5 rounded focus:ring-0 text-purple-900"
                            type="checkbox"
                        />

                        <img :src="playlist.coverURL" alt="album art" width="52" height="52"/>

                        <span class="ml-4">{{ playlist.name || "Not Available" }}</span>
                    </span>

                    <div
                        v-for="track in playlist.tracks"
                        class="flex pb-3 mr-3.5 ml-20"
                    >
                        <input
                            class="mr-2.5 rounded focus:ring-0 text-purple-900 flex self-center"
                            type="checkbox"
                        />
                        <img :src="track.albumArt" alt="album art" width="52" height="52"/>
                        <div class="flex flex-col self-center ml-4">
                            <span>{{ track.name }}</span>
                            <span class="text-sm">{{ track.albumName }}</span>
                        </div>
                    </div>
                </td>
            </tr>

            <tr v-if="playlists.length === 0">
                <td class="px-6 py-4 border-t" colspan="4">
                    {{ "No playlists found" }}
                </td>
            </tr>
        </table>
    </div>
</template>
<script setup lang="ts">
import { Playlist } from "@/Types/Playlist";

defineProps<{
    playlists: Playlist[];
    header: string;
}>();
</script>
