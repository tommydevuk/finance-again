<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Save, ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import system from '@/routes/system';
import { type BreadcrumbItem } from '@/types';

// --- Interfaces ---

interface Permission {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
}

interface Role {
    id: number;
    name: string;
    guard_name: string;
    entity_id?: number;
}

interface Props {
    role: Role;
    assigned_permissions: number[];
    permissions: Record<string, Permission[]>;
}

interface FormState {
    permissions: Record<number, boolean>;
}

// --- Props & Setup ---

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'System Dashboard', href: system.dashboard.url() },
    { title: 'Roles', href: system.roles.index.url() },
    { title: `Edit ${props.role.name} Permissions`, href: '#' },
];

// --- Form Initialization ---

// Convert the array of assigned IDs into a boolean map for v-model binding
const initialPermissionState: Record<number, boolean> = {};

// Initialize all possible permissions to false (or true if assigned)
// This ensures reactivity for all fields immediately
Object.values(props.permissions).flat().forEach((permission) => {
    initialPermissionState[permission.id] = props.assigned_permissions.includes(permission.id);
});

const form = useForm<FormState>({
    permissions: initialPermissionState,
});

// --- Actions ---

const submit = () => {
    form.transform((data) => {
        // Filter the boolean map to get only the IDs where the value is true
        const selectedIds = Object.entries(data.permissions)
            .filter(([, isChecked]) => isChecked)
            .map(([id]) => parseInt(id));

        return {
            permissions: selectedIds
        };
    }).put(system.roles.permissions.update.url(props.role.id), {
        preserveScroll: true,
    });
};

const togglePermission = (id: number) => {
    form.permissions[id] = !form.permissions[id];
};
</script>

<template>
    <Head :title="`Permissions - ${role.name}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 flex flex-col gap-6 max-w-2xl">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" as-child>
                        <Link :href="system.roles.index.url()">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">{{ role.name }} Permissions</h1>
                        <p class="text-muted-foreground text-sm">Configure granular access levels for this role.</p>
                    </div>
                </div>
                <Button @click="submit" :disabled="form.processing" class="gap-2">
                    <Save class="h-4 w-4" />
                    Save Changes
                </Button>
            </div>

            <!-- Permission Groups -->
            <div class="grid gap-6">
                <Card v-for="(group, groupName) in permissions" :key="groupName">
                    <CardHeader class="pb-3">
                        <CardTitle class="text-lg capitalize">{{ groupName }} Management</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div v-for="permission in group" :key="permission.id" 
                                class="flex items-center space-x-4 p-3 rounded-lg border bg-muted/20 hover:bg-muted/40 transition-colors cursor-pointer"
                                @click="togglePermission(permission.id)">
                                
                                <Checkbox 
                                    :id="`perm-${permission.id}`" 
                                    v-model="form.permissions[permission.id]"
                                    @click.stop
                                />
                                
                                <Label 
                                    :for="`perm-${permission.id}`" 
                                    class="text-sm font-medium leading-none cursor-pointer flex-1"
                                    @click.stop
                                >
                                    {{ permission.name.split(' ')[0] }}
                                </Label>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
            
            <!-- Footer Action -->
            <div class="flex justify-end pt-4">
                 <Button @click="submit" :disabled="form.processing" class="gap-2 px-8">
                    <Save class="h-4 w-4" />
                    Save Changes
                </Button>
            </div>
        </div>
    </AppLayout>
</template>