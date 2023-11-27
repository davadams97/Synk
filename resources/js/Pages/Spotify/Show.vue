<template>
    Showing playlist data
    <table>
        <tr>
            <th>Song title</th>
            <th>Album</th>
        </tr>
        <tr v-for="track of playlist">
            <td>
                {{ track.name }}
            </td>
            <td>{{ track.album }}</td>
        </tr>
    </table>
</template>
<script setup>
const { playlist } = defineProps(["playlist"]);
</script>
