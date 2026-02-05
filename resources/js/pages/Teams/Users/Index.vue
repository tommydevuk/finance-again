<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { User as UserIcon, X, MoreHorizontal, Eye } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceGrid from '@/components/ResourceGrid.vue';
import DataSearch from '@/components/DataSearch.vue';
import DataSort from '@/components/DataSort.vue';
import { Badge } from '@/components/ui/badge';
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
import UserHoverCard from '@/components/UserHoverCard.vue';

interface User {
    id: number;
    uuid: string;
    name: string;
    email: string;
    roles: Array<{ name: string }>;
    created_at: string;
}

interface Entity {
    uuid: string;
    name: string;
}

interface Props {
    entity: Entity;
    users: {
        data: User[];
        meta: any;
    };
    filters: {
        search?: string;
        sort?: string;
        direction?: string;
    };
}

const props = defineProps<Props>();

const { search, sort: currentSort, direction, isFiltered, resetFilters } = useQueryFilters(
    props.filters,
    { defaultSort: 'created_at', defaultDirection: 'desc' },
    route('teams.users.index', props.entity.uuid)
);

const sortOptions = {
    'Date Created': 'created_at',
    'Name': 'name',
    'Email': 'email',
};

const breadcrumbs = [
    { title: 'Dashboard', href: dashboard().url },
    { title: props.entity.name, href: route('teams.show', props.entity.uuid) },
    { title: 'Users', href: route('teams.users.index', props.entity.uuid) },
];
</script>

<template>
    <Head :title="`${entity.name} Users`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <PageHeader>
                <template #title>{{ entity.name }} Users</template>
                <template #description>Manage users within this team.</template>
            </PageHeader>

            <div class="flex items-center gap-4">
                <DataSearch v-model="search" />
                
                <DataSort 
                    v-model:sortBy="currentSort" 
                    v-model:direction="direction" 
                    :sort-options="sortOptions" 
                />

                <Button v-if="isFiltered" variant="ghost" size="icon" @click="resetFilters" title="Clear Filters">
                    <X class="h-4 w-4" />
                </Button>
            </div>

            <div v-if="users.data.length === 0" class="flex min-h-[200px] flex-col items-center justify-center rounded-md border border-dashed text-center animate-in fade-in-50">
                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-muted">
                    <UserIcon class="h-5 w-5 text-muted-foreground" />
                </div>
                <h3 class="mt-4 text-lg font-semibold">No users found</h3>
                <p class="mb-4 text-sm text-muted-foreground">
                    No users match your search criteria.
                </p>
                <Button variant="outline" @click="resetFilters">Clear Search</Button>
            </div>


            <ResourceGrid v-else>
                <Card v-for="user in users.data" :key="user.id" class="overflow-hidden transition-all hover:border-primary/50">
                    <CardHeader class="pb-3 border-b flex flex-row items-center justify-between space-y-0">
                        <UserHoverCard :user="user">
                            <div class="flex items-center gap-3 cursor-pointer">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-primary">
                                    <span class="text-sm font-medium">{{ user.name.charAt(0) }}</span>
                                </div>
                                <div>
                                    <CardTitle class="text-base hover:underline">{{ user.name }}</CardTitle>
                                    <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                                </div>
                            </div>
                        </UserHoverCard>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon" class="h-8 w-8">
                                    <MoreHorizontal class="h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuItem as-child>
                                    <Link :href="`/u/${user.uuid}`">
                                        <Eye class="mr-2 h-4 w-4" />
                                        View Profile
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </CardHeader>
                    <CardContent class="pt-4">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Entity Roles:</span>
                                <div class="flex gap-1 flex-wrap justify-end">
                                    <Badge v-for="role in user.roles" :key="role.name" variant="secondary" class="text-xs">
                                        {{ role.name }}
                                    </Badge>
                                    <span v-if="user.roles.length === 0" class="text-muted-foreground italic text-xs">No roles in this team</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Joined:</span>
                                <span>{{ new Date(user.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </ResourceGrid>

            <Pagination :meta="users.meta" />
        </div>
    </AppLayout>
</template>
