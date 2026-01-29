<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Shield, ShieldCheck, Settings2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import system from '@/routes/system';
import { type BreadcrumbItem } from '@/types';

interface Role {
    id: number;
    name: string;
    permissions_count: number;
}

defineProps<{
    roles: Role[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'System Dashboard', href: system.dashboard.url() },
    { title: 'Roles', href: system.roles.index.url() },
];
</script>

<template>
    <Head title="System Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">System Roles</h1>
                    <p class="text-muted-foreground text-sm">Manage global role templates and their permissions.</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="role in roles" :key="role.id" class="overflow-hidden transition-all hover:border-primary/50">
                    <CardHeader class="pb-3 border-b ">
                        <div class="flex items-center gap-2">
                            <ShieldCheck v-if="role.name === 'Super Admin'" class="h-5 w-5 text-primary" />
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
                            <Link :href="system.roles.permissions.edit.url(role.id)">
                                <Settings2 class="h-4 w-4" />
                                Edit Permissions
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>