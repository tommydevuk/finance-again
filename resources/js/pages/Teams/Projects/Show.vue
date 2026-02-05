<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import draggable from 'vuedraggable';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { LayoutGrid, Users, Settings, Plus, ListTodo } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import TaskItem from '@/components/TaskItem.vue';
import TaskFormSheet from '@/components/TaskFormSheet.vue';
import ActivityLog from '@/components/ActivityLog.vue';
import UserHoverCard from '@/components/UserHoverCard.vue';

interface User {
    id: number;
    uuid: string;
    name: string;
    email: string;
    pivot: {
        role: string;
    };
}

interface Task {
    id: number;
    uuid: string;
    parent_id: number | null;
    name: string;
    description: string | null;
    status: string;
    priority: string;
    children: Task[];
}

interface Project {
    id: number;
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
    tasks: {
        data: Task[];
    };
}>();

// --- Task Management ---

const isSheetOpen = ref(false);
const editingTask = ref<Task | null>(null);

const openCreateTask = (parent: Task | null = null) => {
    editingTask.value = parent ? ({ parent_id: parent.id, name: '', description: '', status: 'todo', priority: 'medium' } as any) : null;
    isSheetOpen.value = true;
};

const openEditTask = (task: Task) => {
    editingTask.value = task;
    isSheetOpen.value = true;
};

const deleteTask = (task: Task) => {
    if (confirm('Are you sure you want to delete this task? All sub-tasks will also be deleted.')) {
        router.delete(route('teams.projects.tasks.destroy', { entity: props.entity.uuid, project: props.project.uuid, task: task.uuid }), {
            preserveScroll: true
        });
    }
};

const onReorder = (event: any) => {
    // Flatten the task structure to sync with backend
    // This is a simplified approach: in a real app, we'd only sync the changed level.
    const tasksToSync: any[] = [];
    
    const flatten = (items: Task[], parentId: number | null = null) => {
        items.forEach((item, index) => {
            tasksToSync.push({
                id: item.id,
                sort_order: index,
                parent_id: parentId
            });
            if (item.children && item.children.length > 0) {
                flatten(item.children, item.id);
            }
        });
    };

    flatten(props.tasks.data);

    router.post(route('teams.projects.tasks.reorder', { entity: props.entity.uuid, project: props.project.uuid }), {
        tasks: tasksToSync
    }, {
        preserveScroll: true,
        only: ['tasks']
    });
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
            <!-- Header -->
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
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-8">
                    <!-- Tasks Section -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <ListTodo class="h-5 w-5 text-primary" />
                                <h2 class="text-xl font-semibold tracking-tight">Tasks</h2>
                            </div>
                            <Button size="sm" @click="openCreateTask()">
                                <Plus class="mr-2 h-4 w-4" />
                                Add Task
                            </Button>
                        </div>

                        <div class="space-y-1 min-h-[100px]">
                            <draggable
                                :list="tasks.data"
                                item-key="id"
                                handle=".drag-handle"
                                group="tasks"
                                @change="onReorder"
                                class="space-y-1"
                            >
                                <template #item="{ element }">
                                    <TaskItem 
                                        :task="element" 
                                        @edit="openEditTask"
                                        @add-subtask="openCreateTask"
                                        @reorder="onReorder"
                                        @delete="deleteTask"
                                    />
                                </template>
                            </draggable>

                            <div v-if="tasks.data.length === 0" class="text-center py-12 border-2 border-dashed rounded-xl text-muted-foreground">
                                No tasks found. Create one to get started!
                            </div>
                        </div>
                    </div>

                    <!-- Activity Feed -->
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
                                            <span class="font-normal text-muted-foreground">{{ activity.causer?.name || 'System' }}:</span>
                                            <ActivityLog :text="activity.description" :entity="entity" />
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
                                    <UserHoverCard :user="user">
                                        <div class="flex items-center gap-3 cursor-pointer">
                                            <div class="h-8 w-8 rounded-full bg-muted flex items-center justify-center text-xs font-medium">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium hover:underline">{{ user.name }}</p>
                                                <p class="text-xs text-muted-foreground capitalize">{{ user.pivot.role }}</p>
                                            </div>
                                        </div>
                                    </UserHoverCard>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <TaskFormSheet 
            v-model:open="isSheetOpen"
            :task="editingTask"
            :project-id="project.uuid"
            :entity-id="entity.uuid"
        />
    </AppLayout>
</template>
