<template>
    <div>
        <section>
            <h2 class="text-slate-50 w-50 h-12 m-2 p-4">
                Showing playlist data
            </h2>

            <table class="text-slate-50">
                <tr>
                    <th>Song title</th>
                    <th>Album</th>
                </tr>
                <tr v-for="track of playlist">
                    <td>
                        {{ track.name }}
                    </td>
                    <td>{{ track.album ?? "Not available" }}</td>
                </tr>
            </table>
        </section>
    </div>
</template>
<script setup>
defineProps(["playlist"]);
</script>
