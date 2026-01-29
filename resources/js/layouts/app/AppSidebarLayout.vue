<script setup lang="ts">
import { toast } from 'vue-sonner'
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItem } from '@/types';
import Toaster from '../../components/ui/sonner/Sonner.vue'                                                                                                                                                
type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
            <Toaster />                                                                                                                                                             
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            
            <slot />
        </AppContent>
    </AppShell>
</template>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        