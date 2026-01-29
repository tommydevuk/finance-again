<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import { Search, Plus, MoreHorizontal, FilePen } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';
interface User {
    id: number;
    name: string;
    email: string;
    roles: Array<{ name: string }>;
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Props {
    users: {
        data: User[];
        links: PaginationLink[];
        meta: {
            current_page: number;
            last_page: number;
            from: number;
            to: number;
            total: number;
        };
    };
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');

const handleSearch = useDebounceFn((value: string) => {
    router.get('/system/users', { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch(search, (value) => {
    handleSearch(value);
});

const breadcrumbs = [
    { title: 'System', href: '/system' },
    { title: 'Users', href: '/system/users' },
];
</script>

<template>
    <Head title="System Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">System Users</h1>
                    <p class="text-muted-foreground text-sm">Manage users and their system access.</p>
                </div>
                <Button as-child>
                    <Link href="/system/users/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Create User
                    </Link>
                </Button>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input
                        v-model="search"
                        placeholder="Search users..."
                        class="pl-8"
                    />
                </div>
            </div>

            <div class="rounded-md border bg-card">
                <div class="relative w-full overflow-auto">
                    <table class="w-full caption-bottom text-sm">
                        <thead class="[&_tr]:border-b">
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Name</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Email</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Roles</th>
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Created At</th>
                                <th class="h-12 px-4 text-right align-middle font-medium text-muted-foreground">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="[&_tr:last-child]:border-0">
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="p-4 text-center text-muted-foreground">
                                    No users found.
                                </td>
                            </tr>
                            <tr v-for="user in users.data" :key="user.id" class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <td class="p-4 align-middle font-medium">{{ user.name }}</td>
                                <td class="p-4 align-middle">{{ user.email }}</td>
                                <td class="p-4 align-middle">
                                    <div class="flex gap-1 flex-wrap">
                                        <Badge v-for="role in user.roles" :key="role.name" variant="secondary" class="text-xs">
                                            {{ role.name }}
                                        </Badge>
                                    </div>
                                </td>
                                <td class="p-4 align-middle">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                                <td class="p-4 align-middle text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" class="h-8 w-8 p-0">
                                                <span class="sr-only">Open menu</span>
                                                <MoreHorizontal class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem as-child>
                                                <Link :href="`/system/users/${user.id}/edit`" class="flex items-center cursor-pointer">
                                                    <FilePen class="mr-2 h-4 w-4" />
                                                    Edit
                                                </Link>
                                            </DropdownMenuItem>
                                            <!-- Add Delete action if needed, usually requires confirmation modal -->
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="users.meta.last_page > 1" class="flex items-center justify-between">
                <div class="text-sm text-muted-foreground">
                    Showing {{ users.meta.from }} to {{ users.meta.to }} of {{ users.meta.total }} results
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        v-for="(link, index) in users.meta.links"
                        :key="index"
                        :variant="link.active ? 'default' : 'outline'"
                        :size="'sm'"
                        :disabled="!link.url"
                        as-child
                    >
                        <Link v-if="link.url" :href="link.url">
                            <span v-html="link.label"></span>
                        </Link>
                        <span v-else v-html="link.label"></span>
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
