<template>
    <section class="h-screen w-full">
        <div class="flex items-center justify-evenly flex-col p-10 h-full overflow-auto">       
            <div>
                <h1
                    class="text-center mb-4 text-6xl font-bold text-gray-900 dark:text-white "
                >
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r to-emerald-600 from-sky-400"
                    >
                        Synk
                    </span>
                </h1>

                <p class="flex items-end flex-col mb-2 font-thin tracking-tight leading-none text-6xl text-slate-200">
                    <span class="w-max pb-2">Your Playlist, Your Rules, Any Platform!</span>
                    
                </p>
            </div>

            <Link
                :href="route('transfer.source')"
                class="inline-flex items-center px-5 py-3 text-base max-w-fit self-center font-medium text-center hover:bg-white/10 text-white rounded-full border border-fuchsia-500"
            >
                Let's get started
                <svg
                    aria-hidden="true"
                    class="w-3.5 h-3.5 ms-2 rtl:rotate-180"
                    fill="none"
                    viewBox="0 0 14 10"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M1 5h12m0 0L9 1m4 4L9 9"
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                    />
                </svg>
            </Link>
        </div>
    </section>

    <section class="w-full m-16">
        <div>
            <p
            class="indent-32 mb-6 font-thin text-4xl text-white"
            >
                Sync your tunes effortlessly across platforms with Synk. Groove
                on Spotify, vibe on Apple Music, or jam on any platform – Synk
                makes it easy. Your playlist deserves to dance everywhere,
                effortlessly. Unleash the power of Synk and take your music
                anywhere life's rhythm takes you!
            </p>
        </div>
    </section>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
</script>
