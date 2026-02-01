<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { LayoutGrid, Users as UsersIcon, X, MoreHorizontal, Eye, Plus } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceGrid from '@/components/ResourceGrid.vue';
import DataSearch from '@/components/DataSearch.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/AppLayout.vue';
import { useQueryFilters } from '@/composables/useQueryFilters';
import { dashboard } from '@/routes';

interface Project {
    id: number;
    uuid: string;
    name: string;
    description: string;
    users_count: number;
    created_at: string;
}

interface Entity {
    uuid: string;
    name: string;
}

interface Props {
    entity: Entity;
    projects: {
        data: Project[];
        meta: any;
    };
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

const { search, isFiltered, resetFilters } = useQueryFilters(
    props.filters,
    { defaultSort: 'created_at', defaultDirection: 'desc' },
    route('teams.projects.index', props.entity.uuid)
);

const breadcrumbs = [
    { title: 'Dashboard', href: dashboard().url },
    { title: props.entity.name, href: route('teams.show', props.entity.uuid) },
    { title: 'Projects', href: route('teams.projects.index', props.entity.uuid) },
];
</script>

<template>
    <Head :title="`${entity.name} Projects`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <PageHeader>
                <template #title>{{ entity.name }} Projects</template>
                <template #description>Manage team projects and user assignments.</template>
                <template #actions>
                    <Button as-child>
                        <Link :href="route('teams.projects.create', entity.uuid)">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Project
                        </Link>
                    </Button>
                </template>
            </PageHeader>

            <div class="flex items-center gap-4">
                <DataSearch v-model="search" />

                <Button v-if="isFiltered" variant="ghost" size="icon" @click="resetFilters" title="Clear Filters">
                    <X class="h-4 w-4" />
                </Button>
            </div>

            <div v-if="projects.data.length === 0" class="flex min-h-[200px] flex-col items-center justify-center rounded-md border border-dashed text-center animate-in fade-in-50">
                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-muted">
                    <LayoutGrid class="h-5 w-5 text-muted-foreground" />
                </div>
                <h3 class="mt-4 text-lg font-semibold">No projects found</h3>
                <p class="mb-4 text-sm text-muted-foreground">
                    You don't have access to any projects or none exist.
                </p>
                <Button variant="outline" @click="resetFilters">Clear Search</Button>
            </div>

            <ResourceGrid v-else>
                <Card v-for="project in projects.data" :key="project.id" class="overflow-hidden transition-all hover:border-primary/50">
                    <CardHeader class="pb-3 border-b flex flex-row items-center justify-between space-y-0">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <LayoutGrid class="h-4 w-4" />
                            </div>
                            <div>
                                <CardTitle class="text-base">{{ project.name }}</CardTitle>
                                <p class="text-xs text-muted-foreground line-clamp-1">{{ project.description || 'No description' }}</p>
                            </div>
                        </div>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon" class="h-8 w-8">
                                    <MoreHorizontal class="h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem as-child>
                                    <Link :href="route('teams.show', entity.uuid)">
                                        <Eye class="mr-2 h-4 w-4" />
                                        View Details
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link :href="route('teams.projects.users.edit', { entity: entity.uuid, project: project.uuid })">
                                        <UsersIcon class="mr-2 h-4 w-4" />
                                        Manage Members
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </CardHeader>
                    <CardContent class="pt-4">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Users Assigned:</span>
                                <div class="flex items-center gap-1.5 font-medium">
                                    <UsersIcon class="h-3.5 w-3.5" />
                                    {{ project.users_count }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Created:</span>
                                <span>{{ new Date(project.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </ResourceGrid>

            <Pagination :meta="projects.meta" />
        </div>
    </AppLayout>
</template>
