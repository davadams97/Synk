<template>
    <div class="bg-gradient-to-r from-blue-950 from-75%% to-fuchsia-950 h-10">
        <a :href="route('home')" class="inline-block mt-4 ml-4 text-2xl white text-white tracking-widest">SYNK</a>
    </div>
    <div
        class="flex flex-wrap bg-gradient-to-r from-blue-950 from-75%% to-fuchsia-950 h-screen px-4 py-8 md:p-12 overflow-y-auto"
    >
        <slot></slot>
    </div>
</template>
