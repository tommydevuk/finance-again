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
    <Head title="Welcome" />

    <div class="flex min-h-screen flex-col items-center bg-white p-6 dark:bg-black">
        <header class="mb-6 w-full max-w-xl text-sm not-has-[nav]:hidden">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-gray-300 px-5 py-1.5 text-sm leading-normal text-gray-900 hover:border-gray-400 dark:border-gray-600 dark:text-gray-100 dark:hover:border-gray-500"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-gray-900 hover:border-gray-300 dark:text-gray-100 dark:hover:border-gray-600"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-block rounded-sm border border-gray-300 px-5 py-1.5 text-sm leading-normal text-gray-900 hover:border-gray-400 dark:border-gray-600 dark:text-gray-100 dark:hover:border-gray-500"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>

        <div class="flex w-full items-center justify-center">
            <div class="w-full max-w-xl rounded-lg border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-black">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Welcome</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Placeholder page. Replace <code class="rounded bg-gray-100 px-1 py-0.5 dark:bg-gray-900">resources/js/pages/Welcome.vue</code>
                with your real landing experience.
            </p>
            </div>
        </div>
    </div>
</template>
