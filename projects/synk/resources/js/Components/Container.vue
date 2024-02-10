<template>
    <div class="flex flex-col w-screen p-8 m-12">
        <slot name="header"></slot>
        <div
            class="w-9/12 h-full bg-gray-700/50 flex self-center justify-items-start rounded-2xl p-4 m-8"
        >
            <slot name="main"></slot>
        </div>
        <slot name="footer"></slot>
    </div>
</template>
