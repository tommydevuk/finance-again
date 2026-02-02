<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    text: string;
    entity: {
        uuid: string;
    };
}>();

interface Token {
    type: 'text' | 'link';
    content: string;
    href?: string;
    target?: string;
}

const tokens = computed((): Token[] => {
    const regex = /\[(task|project):([a-f\d-]+)\|([^\]]+)\]/g;
    let match;
    const tokens: Token[] = [];
    let lastIndex = 0;

    while ((match = regex.exec(props.text)) !== null) {
        // Add preceding text
        if (match.index > lastIndex) {
            tokens.push({ type: 'text', content: props.text.slice(lastIndex, match.index) });
        }

        const [fullMatch, type, uuid, name] = match;

        let href = '#';
        if (type === 'project') {
            href = route('teams.projects.show', { entity: props.entity.uuid, project: uuid });
        } else if (type === 'task') {
            // We need project UUID for task link, this component assumes it's on a project page
            // This will need enhancement if used outside project context
            // For now, we get it from the current route parameters
            const currentRoute: { params?: { project?: string } } = route() as any;
            if (currentRoute.params?.project) {
                href = route('teams.projects.show', { entity: props.entity.uuid, project: currentRoute.params.project });
            }
        }
        
        tokens.push({
            type: 'link',
            content: name,
            href: href,
            target: '_blank'
        });

        lastIndex = regex.lastIndex;
    }

    // Add remaining text
    if (lastIndex < props.text.length) {
        tokens.push({ type: 'text', content: props.text.slice(lastIndex) });
    }

    return tokens;
});
</script>

<template>
    <span>
        <template v-for="(token, index) in tokens" :key="index">
            <span v-if="token.type === 'text'">{{ token.content }}</span>
            <Link v-else-if="token.type === 'link'" :href="token.href" :target="token.target" class="font-semibold text-primary hover:underline">
                {{ token.content }}
            </Link>
        </template>
    </span>
</template>
