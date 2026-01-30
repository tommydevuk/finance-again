<script setup lang="ts">
// No script needed for this simple layout component
</script>

<template>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">
                <slot name="title" />
            </h1>
            <p v-if="$slots.description" class="text-muted-foreground text-sm">
                <slot name="description" />
            </p>
        </div>
        <div v-if="$slots.actions">
            <slot name="actions" />
        </div>
    </div>
</template>
