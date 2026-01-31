<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Settings2 } from 'lucide-vue-next';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import system from '@/routes/system';
import { type BreadcrumbItem } from '@/types';

defineProps<{
    counts: {
        users: number;
        roles: number;
        entities: number;
        transactions: number;
        platforms: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'System Dashboard',
        href: system.dashboard.url(),
    },
];
</script>

<template>
    <Head title="System Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">System Dashboard</h1>
                    <p class="text-gray-500">Super Admin Access Only</p>
                </div>
                <Button as-child variant="outline" class="gap-2">
                    <Link :href="system.roles.index.url()">
                        <Settings2 class="h-4 w-4" />
                        Manage Roles
                    </Link>
                </Button>
            </div>
            
            <div class="grid auto-rows-min gap-4 md:grid-cols-3 lg:grid-cols-5">
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 bg-sidebar">
                    <h3 class="font-semibold mb-2">Entities</h3>
                    <div class="text-3xl font-bold">{{ counts.entities }}</div>
                </div>
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 bg-sidebar flex flex-col justify-between">
                     <h3 class="font-semibold mb-2">Users</h3>
                    <div class="flex items-center justify-between">
                        <div class="text-3xl font-bold">{{ counts.users }}</div>
                        <Button variant="ghost" size="sm" as-child>
                            <Link :href="system.users.index.url()">View All</Link>
                        </Button>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 bg-sidebar flex flex-col justify-between">
                     <h3 class="font-semibold mb-2">Platforms</h3>
                    <div class="flex items-center justify-between">
                        <div class="text-3xl font-bold">{{ counts.platforms }}</div>
                        <Button variant="ghost" size="sm" as-child>
                            <Link href="/system/platforms">View All</Link>
                        </Button>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 bg-sidebar">
                     <h3 class="font-semibold mb-2">Transactions</h3>
                    <div class="text-3xl font-bold">{{ counts.transactions }}</div>
                </div>
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4 bg-sidebar flex flex-col justify-between">
                     <h3 class="font-semibold mb-2">System Roles</h3>
                    <div class="flex items-center justify-between">
                        <div class="text-3xl font-bold">{{ counts.roles }}</div>
                        <Button variant="ghost" size="sm" as-child>
                            <Link :href="system.roles.index.url()">View All</Link>
                        </Button>
                    </div>
                </div>
            </div>
            
            <div class="relative min-h-[50vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>