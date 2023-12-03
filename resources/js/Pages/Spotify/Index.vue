<template>
    <div>
        <section>
            <h2 class="text-slate-50 w-50 h-12 m-2 p-4">
                Showing {{ userName }} current playlist for Spotify
            </h2>

            <!-- TODO: Can be a component -->
            <table class="text-slate-50">
                <tr>
                    <th>Name of playlist</th>
                </tr>
                <tr v-for="playlist of playlists">
                    <td>
                        <Link
                            :href="route('spotify.playlist.list', playlist.id)"
                            >{{ playlist.name }}</Link
                        >
                    </td>
                </tr>
            </table>
        </section>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
defineProps(["userName", "playlists"]);
</script>
