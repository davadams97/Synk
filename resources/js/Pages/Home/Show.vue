<template>
    <div class="flex items-center p-10">
        <h1 class="flex items-end flex-col mb-4 text-right text-4xl font-bold leading-none
                    tracking-tight md:text-5xl lg:text-6xl text-slate-200">
            <span class="w-max pb-2 text-right">Your Playlist</span>
            <span class="w-max pb-2"> Your Rules</span>
            <span class="w-max pb-2">Any Platform!</span>
        </h1>

        <div class="flex flex-col">

            <h3 class="text-center mb-4 text-ld font-bold text-gray-900 dark:text-white md:text-5xl lg:text-6xl">
                <span class="text-transparent bg-clip-text bg-gradient-to-r to-emerald-600 from-sky-400">
                    Get started with
                </span>
                Synk
            </h3>

            <p class="mb-6 text-lg font-normal text-white lg:text-xl sm:px-16 xl:px-48">
                Sync your tunes effortlessly across platforms with Synk. Groove on Spotify,
                vibe on Apple Music, or jam on any platform – Synk makes it easy.
                Your playlist deserves to dance everywhere, effortlessly. Unleash the power of
                Synk and take your music anywhere life's rhythm takes you!
            </p>

            <Link :href="route('login')" class="inline-flex items-center px-5 py-3 text-base max-w-fit self-center
                      font-medium text-center text-white bg-purple-700 rounded-lg hover:bg-purple-800
                      focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
                Let's get started
                <svg aria-hidden="true" class="w-3.5 h-3.5 ms-2 rtl:rotate-180" fill="none" viewBox="0 0 14 10"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 5h12m0 0L9 1m4 4L9 9" stroke="currentColor" stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="2"/>
                </svg>
            </Link>

        </div>

    </div>

</template>

<script setup>
import { Link } from "@inertiajs/vue3";

</script>
