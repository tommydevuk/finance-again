<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Meta {
    current_page: number;
    last_page: number;
    from: number;
    to: number;
    total: number;
    links: PaginationLink[];
}

defineProps<{
    meta: Meta;
}>();
</script>

<template>
    <div v-if="meta.last_page > 1" class="flex items-center justify-between">
        <div class="text-sm text-muted-foreground">
            Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }} results
        </div>
        <div class="flex items-center gap-2">
            <Button
                v-for="(link, index) in meta.links"
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
</template>
