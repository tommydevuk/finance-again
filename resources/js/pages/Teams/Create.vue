<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('teams.store'));
};

const breadcrumbs = [
    { title: 'Dashboard', href: dashboard().url },
    { title: 'Create Team', href: '#' },
];
</script>

<template>
    <Head title="Create Team" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-2xl mx-auto p-6">
            <h1 class="text-2xl font-bold mb-6">Create New Team</h1>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-2">
                    <Label for="name">Team Name</Label>
                    <Input id="name" v-model="form.name" required autofocus />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing">
                        Create Team
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
