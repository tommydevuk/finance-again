<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

const props = defineProps<{
    entity: {
        id: number;
        uuid: string;
        name: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: props.entity.name,
        href: '#',
    },
];
</script>

<template>
    <Head :title="entity.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">{{ entity.name }}</h1>
                    <p class="text-muted-foreground">Team Overview</p>
                </div>
            </div>
            
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Total Accounts</h3>
                    </div>
                    <div class="text-2xl font-bold">0</div>
                </div>
                 <div class="rounded-xl border bg-card text-card-foreground shadow-sm p-6">
                    <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Pending Transactions</h3>
                    </div>
                    <div class="text-2xl font-bold">0</div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
