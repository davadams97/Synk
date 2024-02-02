<template>
    <div class="bg-transparent rounded-md overflow-x-auto">
        <table class="w-full whitespace-nowrap text-white">
            <tr class="text-left font-bold sticky">
                <th class="pb-4 pt-6">
                    {{ header }}
                </th>
            </tr>
            <tr v-for="playlist in playlists" :key="playlist.id">
                <td class="border-t border-gray-600">
                    <span
                        class="flex items-center px-6 py-4 focus:text-indigo-500"
                    >
                        <input
                            class="mr-2.5 rounded focus:ring-0 text-purple-900"
                            type="checkbox"
                            @change="updateSongSelection(playlist.id)"
                        />

                        <a :href="playlist.href">
                            <img
                                :src="playlist.coverURL"
                                alt="album art"
                                width="52"
                                height="52"
                            />
                        </a>

                        <a class="ml-4" :href="playlist.href">{{
                            playlist.name || "Not Available"
                        }}</a>
                    </span>

                    <div
                        v-for="track in playlist.tracks"
                        class="flex pb-3 mr-3.5 ml-20"
                    >
                        <input
                            class="mr-2.5 rounded focus:ring-0 text-purple-900 flex self-center"
                            type="checkbox"
                            :value="track.name"
                            v-model="model"
                        />
                        <a :href="track.href">
                            <img
                                :src="track.albumArt"
                                alt="album art"
                                width="52"
                                height="52"
                            />
                        </a>
                        <div class="flex flex-col self-center ml-4">
                            <a :href="track.href">{{ track.name }}</a>
                            <a :href="track.href" class="text-sm">{{ track.albumName }}</a>
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

const props = defineProps<{
    playlists: Playlist[];
    header: string;
}>();

const model = defineModel({ default: new Set() });
function updateSongSelection(id) {
    let matchingPlaylist = props.playlists.find(
        (playlist) => playlist.id === id,
    );
    matchingPlaylist.isSelected = !matchingPlaylist.isSelected;

    matchingPlaylist.tracks.forEach((track) =>
        matchingPlaylist.isSelected
            ? model.value.add(track.name)
            : model.value.delete(track.name),
    );
}
</script>
