<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Plus, FilePen, User as UserIcon, Filter, X } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceGrid from '@/components/ResourceGrid.vue';
import DataSearch from '@/components/DataSearch.vue';
import DataSort from '@/components/DataSort.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/AppLayout.vue';
import { useQueryFilters } from '@/composables/useQueryFilters';

interface User {
    id: number;
    name: string;
    email: string;
    roles: Array<{ name: string }>;
    created_at: string;
}

interface Props {
    users: {
        data: User[];
        meta: {
            current_page: number;
            last_page: number;
            from: number;
            to: number;
            total: number;
            links: Array<{
                url: string | null;
                label: string;
                active: boolean;
            }>;
        };
    };
    filters: {
        search?: string;
        role?: string;
        sort?: string;
        direction?: string;
    };
    roles: Array<{
        id: number;
        name: string;
        guard_name: string;
    }>;
}

const props = defineProps<Props>();

const { search, sort, direction, filters, isFiltered, resetFilters } = useQueryFilters(
    props.filters,
    { defaultSort: 'created_at', defaultDirection: 'desc' },
    '/system/users'
);

const sortOptions = {
    'Date Created': 'created_at',
    'Name': 'name',
    'Email': 'email',
};

const breadcrumbs = [
    { title: 'System', href: '/system' },
    { title: 'Users', href: '/system/users' },
];
</script>

<template>
    <Head title="System Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <PageHeader>
                <template #title>System Users</template>
                <template #description>Manage users and their system access.</template>
                <template #actions>
                    <Button as-child>
                        <Link href="/system/users/create">
                            <Plus class="mr-2 h-4 w-4" />
                            Create User
                        </Link>
                    </Button>
                </template>
            </PageHeader>

            <div class="flex items-center gap-4">
                <DataSearch v-model="search" />

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline" class="gap-2">
                            <Filter class="h-4 w-4" />
                            {{ filters.role ? filters.role : 'Filter by Role' }}
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <DropdownMenuLabel>Filter by Role</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuRadioGroup v-model="filters.role">
                            <DropdownMenuRadioItem value="">
                                All Roles
                            </DropdownMenuRadioItem>
                            <DropdownMenuRadioItem v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name }}
                            </DropdownMenuRadioItem>
                        </DropdownMenuRadioGroup>
                    </DropdownMenuContent>
                </DropdownMenu>
                
                <DataSort 
                    v-model:sort="sort" 
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
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <span class="text-sm font-medium">{{ user.name.charAt(0) }}</span>
                            </div>
                            <div>
                                <CardTitle class="text-base">{{ user.name }}</CardTitle>
                                <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-4">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Roles:</span>
                                <div class="flex gap-1 flex-wrap justify-end">
                                    <Badge v-for="role in user.roles" :key="role.name" variant="secondary" class="text-xs">
                                        {{ role.name }}
                                    </Badge>
                                    <span v-if="user.roles.length === 0" class="text-muted-foreground italic text-xs">No roles</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Joined:</span>
                                <span>{{ new Date(user.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="border-t bg-muted/50 p-3 flex justify-end">
                        <Button variant="ghost" size="sm" as-child class="h-8 w-full justify-center">
                            <Link :href="`/system/users/${user.id}/edit`">
                                <FilePen class="mr-2 h-3.5 w-3.5" />
                                Edit User
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>
            </ResourceGrid>

            <!-- Pagination -->
            <Pagination :meta="users.meta" />
        </div>
    </AppLayout>
</template>
