<script setup lang="ts">
import { FolderOpen } from 'lucide-vue-next';
</script>

<template>
    <div class="flex flex-col items-center justify-center p-12 text-center border-2 border-dashed rounded-xl border-sidebar-border/70 dark:border-sidebar-border">
        <div class="p-4 bg-muted/50 rounded-full mb-4">
            <FolderOpen class="w-8 h-8 text-muted-foreground" />
        </div>
        <h3 class="text-lg font-semibold">No Entities Found</h3>
        <p class="text-sm text-muted-foreground mt-2 max-w-sm">
            You are not an administrator of any entity at this time.
        </p>
    </div>
</template>
