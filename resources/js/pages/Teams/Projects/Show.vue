<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { LayoutGrid, Users, Settings } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface User {
    id: number;
    name: string;
    email: string;
    pivot: {
        role: string;
    };
}

interface Project {
    uuid: string;
    name: string;
    description: string;
    users: User[];
}

interface Entity {
    uuid: string;
    name: string;
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
    entity: Entity;
    project: Project;
    activities: Activity[];
}>();

const formatActivity = (activity: Activity) => {
    const user = activity.causer?.name || 'System';
    
    switch (activity.event) {
        case 'created':
            return `${user} created the project`;
        case 'updated':
            const keys = Object.keys(activity.properties?.attributes || {});
            return `${user} updated ${keys.join(', ')}`;
        default:
            return `${user} ${activity.description}`;
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: props.entity.name, href: route('teams.show', props.entity.uuid) },
    { title: 'Projects', href: route('teams.projects.index', props.entity.uuid) },
    { title: props.project.name, href: '#' },
];
</script>

<template>
    <Head :title="project.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 space-y-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-primary/10 p-3 rounded-xl text-primary">
                        <LayoutGrid class="h-8 w-8" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">{{ project.name }}</h1>
                        <p class="text-muted-foreground">{{ project.description || 'No description provided.' }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <Button variant="outline" as-child class="gap-2">
                        <Link :href="route('teams.projects.users.edit', { entity: entity.uuid, project: project.uuid })">
                            <Users class="h-4 w-4" />
                            Manage Members
                        </Link>
                    </Button>
                    <Button variant="outline" size="icon">
                        <Settings class="h-4 w-4" />
                    </Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <div class="md:col-span-2 space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>Project Activity</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="activities.length > 0" class="space-y-6">
                                <div v-for="activity in activities" :key="activity.id" class="flex gap-4">
                                    <div class="mt-1 h-2 w-2 rounded-full bg-primary shrink-0" />
                                    <div class="space-y-1">
                                        <p class="text-sm font-medium leading-none">
                                            {{ formatActivity(activity) }}
                                        </p>
                                        <p class="text-xs text-muted-foreground">
                                            {{ new Date(activity.created_at).toLocaleString() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-sm text-muted-foreground py-8 text-center border-2 border-dashed rounded-lg">
                                No activity recorded yet.
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="text-lg flex items-center gap-2">
                                <Users class="h-5 w-5" />
                                Team Members
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="user in project.users" :key="user.id" class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-muted flex items-center justify-center text-xs font-medium">
                                            {{ user.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium">{{ user.name }}</p>
                                            <p class="text-xs text-muted-foreground capitalize">{{ user.pivot.role }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>