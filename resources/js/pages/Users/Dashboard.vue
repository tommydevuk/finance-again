<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import EmptyState from '@/components/EmptyState.vue';
import { Button } from '@/components/ui/button';

defineProps<{
    entities: Array<{
        id: number;
        uuid: string;
        name: string;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col p-4">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">My Teams</h1>
                    <p class="text-muted-foreground">Manage the entities where you have admin access.</p>
                </div>
                <Button as-child>
                    <Link :href="route('teams.create')">Create Team</Link>
                </Button>
            </div>

            <div v-if="entities.length > 0" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link 
                    v-for="entity in entities" 
                    :key="entity.id" 
                    :href="route('teams.show', entity.uuid)"
                    class="group relative flex flex-col justify-between overflow-hidden rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm transition-all hover:bg-accent hover:shadow-md dark:border-sidebar-border"
                >
                    <div class="space-y-2">
                        <h3 class="font-semibold leading-none tracking-tight group-hover:underline">
                            {{ entity.name }}
                        </h3>
                        <p class="text-sm text-muted-foreground">
                            Manage team settings and resources.
                        </p>
                    </div>
                </Link>
            </div>

            <div v-else class="flex flex-1 items-center justify-center rounded-xl border border-sidebar-border/70 bg-muted/10 p-8 dark:border-sidebar-border">
                <EmptyState />
            </div>
        </div>
    </AppLayout>
</template>
