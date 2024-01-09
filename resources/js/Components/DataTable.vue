<template>
    <div class="bg-white rounded-md shadow overflow-x-auto">
        <table class="w-full whitespace-nowrap">
            <tr class="text-left font-bold sticky top-0 bg-white">
                <th v-for="header in headers" class="pb-4 pt-6 px-6">
                    {{ header }}
                </th>
            </tr>
            <tr
                v-for="entry of dataList"
                :key="entry.id"
                class="hover:bg-gray-100 focus-within:bg-gray-100"
            >
                <td
                    v-for="(col, index) in entry.columns"
                    :key="col"
                    class="border-t"
                >
                    <!--                    <Link-->
                    <!--                        v-if="routeData"-->
                    <!--                        class="flex items-center px-6 py-4 focus:text-indigo-500"-->
                    <!--                        :href="route(routeData.name, entry[routeData.params])"-->
                    <!--                    >-->
                    <!--                        {{ col || "Not Available" }}-->
                    <!--                    </Link>-->
                    <span
                        class="flex items-center px-6 py-4 focus:text-indigo-500"
                    >
                        <input
                            class="mr-2.5 rounded focus:ring-0 text-purple-900"
                            v-if="index === 0"
                            type="checkbox"
                        />

                        {{ col || "Not Available" }}
                    </span>

                    <div v-for="el in [2, 3]" class="flex pb-3 mr-3.5 ml-20">
                        <input
                            class="mr-2.5 rounded focus:ring-0 text-purple-900 flex self-center"
                            type="checkbox"
                        />
                        <div class="flex flex-col">
                            <span>{{ "Song Title" }}</span>
                            <span class="text-sm">{{ "Album" }}</span>
                        </div>
                    </div>
                </td>
            </tr>

            <tr v-if="dataList.length === 0">
                <td class="px-6 py-4 border-t" colspan="4">
                    {{ noDataText }}
                </td>
            </tr>
        </table>
    </div>
</template>
<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

interface Entry {
    id: string | number;
    columns: string[];
}

defineProps<{
    dataList: Entry[];
    routeData?: {
        name: string;
        params: string;
    };
    headers: string[];
    noDataText: string;
}>();
</script>
