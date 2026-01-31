<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Plus, FilePen, Coins, X, Filter } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceGrid from '@/components/ResourceGrid.vue';
import DataSearch from '@/components/DataSearch.vue';
import DataSort from '@/components/DataSort.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardContent, CardFooter } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { useQueryFilters } from '@/composables/useQueryFilters';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';

interface Currency {
    id: number;
    name: string;
    code: string;
    symbol: string | null;
    type: string;
    decimals: number;
    created_at: string;
}

interface Props {
    currencies: {
        data: Currency[];
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
        sort?: string;
        direction?: string;
        type?: string;
    };
    types: Array<{ label: string; value: string }>;
}

const props = defineProps<Props>();

const { search, sort, direction, filters, isFiltered, resetFilters } = useQueryFilters(
    props.filters,
    { defaultSort: 'created_at', defaultDirection: 'desc' },
    '/system/currencies'
);

const sortOptions = {
    'Date Created': 'created_at',
    'Name': 'name',
    'Code': 'code',
    'Type': 'type',
};

const breadcrumbs = [
    { title: 'System', href: '/system' },
    { title: 'Currencies', href: '/system/currencies' },
];
</script>

<template>
    <Head title="System Currencies" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <PageHeader>
                <template #title>System Currencies</template>
                <template #description>Manage currencies (Fiat, Crypto).</template>
                <template #actions>
                    <Button as-child>
                        <Link href="/system/currencies/create">
                            <Plus class="mr-2 h-4 w-4" />
                            Create Currency
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
                            {{ filters.type ? types.find(t => t.value === filters.type)?.label : 'Filter by Type' }}
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <DropdownMenuLabel>Filter by Type</DropdownMenuLabel>
                        <DropdownMenuSeparator />
                        <DropdownMenuRadioGroup v-model="filters.type">
                            <DropdownMenuRadioItem value="">
                                All Types
                            </DropdownMenuRadioItem>
                            <DropdownMenuRadioItem v-for="type in types" :key="type.value" :value="type.value">
                                {{ type.label }}
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

            <div v-if="currencies.data.length === 0" class="flex min-h-[200px] flex-col items-center justify-center rounded-md border border-dashed text-center animate-in fade-in-50">
                <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-muted">
                    <Coins class="h-5 w-5 text-muted-foreground" />
                </div>
                <h3 class="mt-4 text-lg font-semibold">No currencies found</h3>
                <p class="mb-4 text-sm text-muted-foreground">
                    No currencies match your search criteria.
                </p>
                <Button variant="outline" @click="resetFilters">Clear Search</Button>
            </div>

            <ResourceGrid v-else>
                <Card v-for="currency in currencies.data" :key="currency.id" class="overflow-hidden transition-all hover:border-primary/50">
                    <CardHeader class="pb-3 border-b flex flex-row items-center justify-between space-y-0">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <span class="text-sm font-medium">{{ currency.code.charAt(0) }}</span>
                            </div>
                            <div>
                                <CardTitle class="text-base">{{ currency.name }} ({{ currency.code }})</CardTitle>
                                <p class="text-xs text-muted-foreground">{{ currency.type }}</p>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-4">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Symbol:</span>
                                <span>{{ currency.symbol || 'N/A' }}</span>
                            </div>
                             <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Decimals:</span>
                                <span>{{ currency.decimals }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-muted-foreground">Joined:</span>
                                <span>{{ new Date(currency.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="border-t bg-muted/50 p-3 flex justify-end">
                        <Button variant="ghost" size="sm" as-child class="h-8 w-full justify-center">
                            <Link :href="`/system/currencies/${currency.id}/edit`">
                                <FilePen class="mr-2 h-3.5 w-3.5" />
                                Edit
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>
            </ResourceGrid>

            <!-- Pagination -->
            <Pagination :meta="currencies.meta" />
        </div>
    </AppLayout>
</template>
