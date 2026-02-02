<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { Users, ShieldCheck, LayoutGrid } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import ActivityLog from '@/components/ActivityLog.vue';

import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';

interface Project {
    id: number;
    uuid: string;
    name: string;
    description: string;
}

interface Activity {
    id: number;
    description: string;
    event: string;
    created_at: string;
    causer?: {
        name: string;
    };
    properties: Record<string, any>;
}

const props = defineProps<{
    entity: {
        id: number;
        uuid: string;
        name: string;
    };
    projects: Project[];
    activities: Activity[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: props.entity.name,
        href: route('teams.show', props.entity.uuid),
    },
];
</script>

<template>

    <Head :title="entity.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">{{ entity.name }}</h1>
                    <p class="text-muted-foreground">Team Overview and Management</p>
                </div>
                <div class="flex gap-3">
                    <Button variant="outline" as-child class="gap-2">
                        <Link :href="route('teams.projects.index', entity.uuid)">
                            <LayoutGrid class="h-4 w-4" />
                            Manage Projects
                        </Link>
                    </Button>
                    <Button variant="outline" as-child class="gap-2">
                        <Link :href="route('teams.users.index', entity.uuid)">
                            <Users class="h-4 w-4" />
                            Manage Users
                        </Link>
                    </Button>
                    <Button variant="outline" as-child class="gap-2">
                        <Link :href="route('teams.roles.index', entity.uuid)">
                            <ShieldCheck class="h-4 w-4" />
                            Manage Roles
                        </Link>
                    </Button>
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

            <div class="grid gap-8 md:grid-cols-3">
                <div class="md:col-span-6 space-y-8">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold tracking-tight">Active Projects</h2>
                            <Link :href="route('teams.projects.index', entity.uuid)"
                                class="text-sm text-primary hover:underline">View all projects</Link>
                        </div>

                        <div v-if="projects.length > 0" class="grid gap-4 sm:grid-cols-3">
                            <Link v-for="project in projects" :key="project.id"
                                :href="route('teams.projects.show', { entity: entity.uuid, project: project.uuid })"
                                class="block p-5 rounded-xl border bg-card hover:bg-accent transition-colors">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="bg-primary/10 p-2 rounded-lg text-primary">
                                        <LayoutGrid class="h-4 w-4" />
                                    </div>
                                    <h3 class="font-medium">{{ project.name }}</h3>
                                </div>
                                <p class="text-sm text-muted-foreground line-clamp-2">
                                    {{ project.description || 'No description provided.' }}
                                 </p>
                            </Link>
                        </div>
                        <div v-else class="text-center py-12 border-2 border-dashed rounded-xl text-muted-foreground">
                            No active projects found.
                        </div>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Team Activity</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="activities.length > 0" class="space-y-6">
                            <div v-for="activity in activities" :key="activity.id" class="flex gap-4">
                                <div class="mt-1 h-2 w-2 rounded-full bg-primary shrink-0" />
                                <div class="space-y-1">
                                    <p class="text-sm font-medium leading-none">
                                        <span class="font-normal text-muted-foreground">{{ activity.causer?.name || 'System' }}:</span>
                                        <ActivityLog :text="activity.description" :entity="entity" />
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ new Date(activity.created_at).toLocaleString() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div v-else
                            class="text-sm text-muted-foreground py-8 text-center border-2 border-dashed rounded-lg">
                            No activity recorded yet.
                        </div>
                    </CardContent>
                </Card>
            </div>

        </div>
    </AppLayout>
</template>