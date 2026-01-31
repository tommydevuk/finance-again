<script setup lang="ts">
import { Search } from 'lucide-vue-next';
import { Input } from '@/components/ui/input';

const model = defineModel<string>();
</script>

<template>
    <div class="relative flex-1 max-w-sm">
        <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
        <Input
            v-model="model"
            placeholder="Search..."
            class="pl-8"
        />
    </div>
</template>
