<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useInitials } from '@/composables/useInitials';

const props = defineProps<{
    profile: {
        id: number;
        uuid: string;
        name: string;
        email: string;
        created_at: string;
        roles?: Array<{ id: number; name: string }>;
    };
}>();

const { getInitials } = useInitials();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Profile',
        href: `/u/${props.profile.uuid}`,
    },
];

const memberSince = new Date(props.profile.created_at).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
});
</script>

<template>
    <Head :title="profile.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <Card>
                <CardHeader>
                    <div class="flex items-center gap-4">
                        <Avatar class="h-20 w-20 text-2xl">
                            <AvatarFallback>{{ getInitials(profile.name) }}</AvatarFallback>
                        </Avatar>
                        <div>
                            <CardTitle class="text-2xl">{{ profile.name }}</CardTitle>
                            <p class="text-muted-foreground">{{ profile.email }}</p>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-muted-foreground">Member since</dt>
                            <dd class="text-sm">{{ memberSince }}</dd>
                        </div>
                        <div v-if="profile.roles && profile.roles.length > 0">
                            <dt class="text-sm font-medium text-muted-foreground">Roles</dt>
                            <dd class="flex flex-wrap gap-1">
                                <span
                                    v-for="role in profile.roles"
                                    :key="role.id"
                                    class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary"
                                >
                                    {{ role.name }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
