<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';

const props = defineProps<{
    types: Array<{ label: string; value: string }>;
}>();

const form = useForm({
    name: '',
    website: '',
    type: 'bank',
});

const submit = () => {
    form.post('/system/platforms');
};

const breadcrumbs = [
    { title: 'System', href: '/system' },
    { title: 'Platforms', href: '/system/platforms' },
    { title: 'Create', href: '/system/platforms/create' },
];
</script>

<template>
    <Head title="Create Platform" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <PageHeader>
                <template #title>Create Platform</template>
                <template #description>Add a new platform to the system.</template>
                <template #actions>
                    <Button variant="outline" as-child>
                        <Link href="/system/platforms">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back
                        </Link>
                    </Button>
                </template>
            </PageHeader>

            <Card>
                <CardContent class="pt-6">
                    <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" required />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="website">Website</Label>
                            <Input id="website" v-model="form.website" placeholder="https://example.com" />
                            <InputError :message="form.errors.website" />
                        </div>

                        <div class="space-y-2">
                            <Label for="type">Type</Label>
                            <div class="relative w-full items-center">
                                <select 
                                    id="type" 
                                    v-model="form.type"
                                    class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option v-for="type in types" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                            </div>
                            <InputError :message="form.errors.type" />
                        </div>

                        <div class="flex justify-end">
                            <Button type="submit" :disabled="form.processing">
                                <Save class="mr-2 h-4 w-4" />
                                Create Platform
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
