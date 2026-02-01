<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

interface Entity {
    uuid: string;
    name: string;
}

const props = defineProps<{
    entity: Entity;
}>();

const form = useForm({
    name: '',
    description: '',
});

const submit = () => {
    form.post(route('teams.projects.store', props.entity.uuid));
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
    { title: props.entity.name, href: route('teams.show', props.entity.uuid) },
    { title: 'Projects', href: route('teams.projects.index', props.entity.uuid) },
    { title: 'New Project', href: '#' },
];
</script>

<template>
    <Head title="Create Project" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 max-w-2xl mx-auto space-y-6">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child>
                    <Link :href="route('teams.projects.index', entity.uuid)">
                        <ArrowLeft class="h-4 w-4" />
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Create New Project</h1>
                    <p class="text-muted-foreground text-sm">Add a new project to {{ entity.name }}.</p>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-2">
                    <Label for="name">Project Name</Label>
                    <Input 
                        id="name" 
                        v-model="form.name" 
                        required 
                        autofocus 
                        placeholder="e.g. Q1 Marketing Campaign"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea 
                        id="description" 
                        v-model="form.description" 
                        placeholder="Briefly describe the purpose of this project..."
                        rows="4"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="flex justify-end gap-3">
                    <Button variant="outline" as-child :disabled="form.processing">
                        <Link :href="route('teams.projects.index', entity.uuid)">Cancel</Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing" class="gap-2">
                        <Save class="h-4 w-4" />
                        Create Project
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
