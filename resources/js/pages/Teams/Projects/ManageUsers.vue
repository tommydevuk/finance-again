<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Save, UserPlus, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { ref } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    pivot?: {
        role: string;
    };
}

interface Project {
    uuid: string;
    name: string;
    users: User[];
}

interface Entity {
    uuid: string;
    name: string;
}

const props = defineProps<{
    entity: Entity;
    project: Project;
    teamUsers: User[];
    availableRoles: string[];
}>();

const form = useForm({
    users: props.project.users.map(u => ({
        id: u.id,
        role: u.pivot?.role || 'member'
    }))
});

const addUser = (userId: any) => {
    if (!userId) return;
    const id = parseInt(userId.toString());
    if (form.users.some(u => u.id === id)) return;
    
    form.users.push({
        id: id,
        role: 'member'
    });
};

const removeUser = (id: number) => {
    form.users = form.users.filter(u => u.id !== id);
};

const getUserName = (id: number) => {
    return props.teamUsers.find(u => u.id === id)?.name || 'Unknown User';
};

const getUserEmail = (id: number) => {
    return props.teamUsers.find(u => u.id === id)?.email || '';
};

const submit = () => {
    form.put(route('teams.projects.users.update', { entity: props.entity.uuid, project: props.project.uuid }));
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: props.entity.name, href: route('teams.show', props.entity.uuid) },
    { title: 'Projects', href: route('teams.projects.index', props.entity.uuid) },
    { title: `Manage Members: ${props.project.name}`, href: '#' },
];
</script>

<template>
    <Head :title="`Members - ${project.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" as-child>
                        <Link :href="route('teams.projects.index', entity.uuid)">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Manage Members</h1>
                        <p class="text-muted-foreground text-sm">Control who can access and edit {{ project.name }}.</p>
                    </div>
                </div>
                <Button @click="submit" :disabled="form.processing" class="gap-2">
                    <Save class="h-4 w-4" />
                    Save Changes
                </Button>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <!-- Add User Sidebar -->
                <Card class="md:col-span-1">
                    <CardHeader>
                        <CardTitle class="text-lg">Add Team Member</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-2">
                            <Select @update:modelValue="addUser">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select a user..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem 
                                        v-for="user in teamUsers" 
                                        :key="user.id" 
                                        :value="user.id.toString()"
                                        :disabled="form.users.some(u => u.id === user.id)"
                                    >
                                        {{ user.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p class="text-xs text-muted-foreground">Only users already in this team can be added to the project.</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Members List -->
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle class="text-lg">Current Members ({{ form.users.length }})</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-if="form.users.length === 0" class="text-center py-8 text-muted-foreground border-2 border-dashed rounded-lg">
                                No members assigned to this project.
                            </div>
                            
                            <div v-for="(userForm, index) in form.users" :key="userForm.id" 
                                class="flex items-center justify-between p-3 rounded-lg border bg-card hover:bg-accent/5 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary font-medium text-xs">
                                        {{ getUserName(userForm.id).charAt(0) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ getUserName(userForm.id) }}</p>
                                        <p class="text-xs text-muted-foreground">{{ getUserEmail(userForm.id) }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <Select v-model="userForm.role">
                                        <SelectTrigger class="w-32 h-8 text-xs">
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="role in availableRoles" :key="role" :value="role" class="capitalize">
                                                {{ role }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    
                                    <Button variant="ghost" size="icon" class="h-8 w-8 text-destructive" @click="removeUser(userForm.id)">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
