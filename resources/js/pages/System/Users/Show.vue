<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ChevronLeft, FilePen } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

interface User {
    id: number;
    uuid: string;
    name: string;
    email: string;
    created_at: string;
    roles: Array<{ name: string }>;
}

const props = defineProps<{
    user: User;
}>();

const breadcrumbs = [
    { title: 'System', href: '/system' },
    { title: 'Users', href: '/system/users' },
    { title: props.user.name, href: `/system/users/${props.user.uuid}` },
];
</script>

<template>
    <Head :title="user.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 max-w-2xl mx-auto w-full">
            <div class="flex items-center gap-2">
                <Button variant="ghost" size="icon" as-child>
                    <Link href="/system/users">
                        <ChevronLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <h1 class="text-2xl font-bold tracking-tight">User Details</h1>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>{{ user.name }}</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm font-medium text-muted-foreground">Name</div>
                            <div class="text-base">{{ user.name }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-muted-foreground">Email</div>
                            <div class="text-base">{{ user.email }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-muted-foreground">UUID</div>
                            <div class="text-sm font-mono bg-muted p-1 rounded">{{ user.uuid }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-muted-foreground">Joined</div>
                            <div class="text-base">{{ new Date(user.created_at).toLocaleDateString() }}</div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm font-medium text-muted-foreground mb-2">Roles</div>
                        <div class="flex flex-wrap gap-2">
                            <Badge v-for="role in user.roles" :key="role.name" variant="secondary">
                                {{ role.name }}
                            </Badge>
                            <span v-if="user.roles.length === 0" class="text-muted-foreground italic text-sm">No roles assigned</span>
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="border-t bg-muted/50 p-4 flex justify-end">
                    <Button as-child>
                        <Link :href="`/system/users/${user.id}/edit`">
                            <FilePen class="mr-2 h-4 w-4" />
                            Edit User
                        </Link>
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template>
