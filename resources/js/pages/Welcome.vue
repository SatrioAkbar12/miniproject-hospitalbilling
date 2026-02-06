<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-br from-gray-50 to-gray-100 p-6 text-gray-800 lg:justify-center lg:p-8 dark:bg-gradient-to-br dark:from-gray-900 dark:to-black"
    >
        <header
            class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <!-- <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-gray-300 px-6 py-2 text-sm leading-normal text-gray-800 hover:border-gray-400 hover:scale-105 transition duration-200 dark:border-gray-700 dark:text-gray-200 dark:hover:border-gray-600"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-6 py-2 text-sm leading-normal text-gray-800 hover:border-gray-300 hover:scale-105 transition duration-200 dark:text-gray-200 dark:hover:border-gray-700"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-block rounded-sm border border-gray-300 px-6 py-2 text-sm leading-normal text-gray-800 hover:border-gray-400 hover:scale-105 transition duration-200 dark:border-gray-700 dark:text-gray-200 dark:hover:border-gray-600"
                    >
                        Register
                    </Link>
                </template>
            </nav> -->
        </header>

        <div class="my-90 flex w-full items-center justify-center">
            <div class="text-center">
                <div class="pb-10">
                    <h1 class="text-6xl font-bold leading-tight text-gray-800 drop-shadow-lg dark:text-white">Hospital Billing and Cashier System</h1>
                    <h1 class="mt-5 text-3xl text-gray-600 drop-shadow-md dark:text-gray-400">Rumah Sakit Delta Surya</h1>
                </div>
                <div class="mt-2">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-block rounded-sm border border-gray-300 px-6 py-2 text-sm leading-normal text-gray-800 hover:border-gray-400 hover:scale-105 transition duration-200 hover:shadow-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:border-gray-600 dark:hover:bg-gray-800"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="inline-block rounded-sm border border-transparent px-6 py-2 text-sm leading-normal text-gray-800 hover:border-gray-300 hover:scale-105 transition duration-200 hover:shadow-md hover:bg-gray-100 dark:text-gray-200 dark:hover:border-gray-700 dark:hover:bg-gray-800"
                        >
                            Log in
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="ml-4 inline-block rounded-sm border border-gray-300 px-6 py-2 text-sm leading-normal text-gray-800 hover:border-gray-400 hover:scale-105 transition duration-200 hover:shadow-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:border-gray-600 dark:hover:bg-gray-800"
                        >
                            Register
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
