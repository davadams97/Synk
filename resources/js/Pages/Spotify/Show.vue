<template>
    Showing playlist data
    <div>
        {{ playlist }}
    </div>
</template>
<script setup>
const { playlist } = defineProps(["playlist"]);
</script>
