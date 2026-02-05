<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    HoverCard,
    HoverCardContent,
    HoverCardTrigger,
} from '@/components/ui/hover-card';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { useInitials } from '@/composables/useInitials';
import { ExternalLink } from 'lucide-vue-next';

export interface UserHoverCardUser {
    uuid: string;
    name: string;
    email: string;
    created_at?: string;
}

defineProps<{
    user: UserHoverCardUser;
}>();

const { getInitials } = useInitials();
</script>

<template>
    <HoverCard>
        <HoverCardTrigger as-child>
            <slot />
        </HoverCardTrigger>
        <HoverCardContent class="w-64">
            <div class="flex items-center gap-3">
                <Avatar class="h-10 w-10">
                    <AvatarFallback>{{ getInitials(user.name) }}</AvatarFallback>
                </Avatar>
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-semibold truncate">{{ user.name }}</h4>
                    <p class="text-xs text-muted-foreground truncate">{{ user.email }}</p>
                </div>
                <Button as-child variant="ghost" size="icon-sm">
                    <Link :href="`/u/${user.uuid}`">
                        <ExternalLink class="h-4 w-4" />
                        <span class="sr-only">View Profile</span>
                    </Link>
                </Button>
            </div>
        </HoverCardContent>
    </HoverCard>
</template>
