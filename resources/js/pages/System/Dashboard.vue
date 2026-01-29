<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'System Dashboard',
        href: '/system',
    },
];
</script>

<template>
    <Head title="System Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold">System Dashboard</h1>
            <p class="text-gray-500">Super Admin Access Only</p>
            
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 bg-sidebar">
                    <h3 class="font-semibold mb-2">Entities</h3>
                    <div class="text-3xl font-bold">--</div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 bg-sidebar">
                     <h3 class="font-semibold mb-2">Users</h3>
                    <div class="text-3xl font-bold">--</div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 bg-sidebar">
                     <h3 class="font-semibold mb-2">Transactions</h3>
                    <div class="text-3xl font-bold">--</div>
                </div>
            </div>
            
            <div class="relative min-h-[50vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
