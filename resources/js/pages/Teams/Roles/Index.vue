<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Shield, ShieldCheck, Settings2 } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import ResourceGrid from '@/components/ResourceGrid.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface Entity {
    uuid: string;
    name: string;
}

interface Role {
    id: number;
    name: string;
    permissions_count: number;
}

const props = defineProps<{
    entity: Entity;
    roles: Role[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: props.entity.name, href: route('teams.show', props.entity.uuid) },
    { title: 'Roles', href: route('teams.roles.index', props.entity.uuid) },
];
</script>

<template>
    <Head :title="`${entity.name} Roles`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 flex flex-col gap-6">
            <PageHeader>
                <template #title>{{ entity.name }} Roles</template>
                <template #description>Manage roles and permissions for this team.</template>
            </PageHeader>

            <ResourceGrid>
                <Card v-for="role in roles" :key="role.id" class="overflow-hidden transition-all hover:border-primary/50">
                    <CardHeader class="pb-3 border-b ">
                        <div class="flex items-center gap-2">
                            <ShieldCheck v-if="role.name === 'Admin'" class="h-5 w-5 text-primary" />
                            <Shield v-else class="h-5 w-5 text-muted-foreground" />
                            <CardTitle class="text-lg">{{ role.name }}</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-muted-foreground">Permissions Assigned:</span>
                            <span class="font-mono font-medium">{{ role.permissions_count }}</span>
                        </div>
                    </CardContent>
                    <CardFooter class="border-t flex justify-end">
                        <Button variant="ghost" size="sm" as-child class="gap-2">
                            <Link :href="route('teams.roles.permissions.edit', { entity: entity.uuid, role: role.id })">
                                <Settings2 class="h-4 w-4" />
                                Edit Permissions
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>
            </ResourceGrid>
        </div>
    </AppLayout>
</template>
