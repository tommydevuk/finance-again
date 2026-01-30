<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';

const breadcrumbs = [
    { title: 'System', href: '/system' },
    { title: 'Users', href: '/system/users' },
    { title: 'Create', href: '/system/users/create' },
];
</script>

<template>
    <Head title="Create User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight">Create User</h1>
                <Button variant="outline" as-child>
                    <Link href="/system/users">Cancel</Link>
                </Button>
            </div>
            
            <div class="rounded-md border p-4">
                <p class="text-muted-foreground">User creation form will go here.</p>
            </div>
        </div>
    </AppLayout>
</template>
