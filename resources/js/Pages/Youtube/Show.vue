<template>
    Showing playlist data
    <table>
        <tr>
            <th>Song title</th>
            <th>Album</th>
        </tr>
        <tr v-for="track of playlist.tracks">
            <td>
                {{ track.title }}
            </td>
            <td>{{ track.album?.name ?? 'Not available' }}</td>
        </tr>
    </table>
</template>
<script setup>
defineProps(["playlist"]);
</script>
