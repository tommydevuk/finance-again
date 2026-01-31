<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import PageHeader from '@/components/PageHeader.vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

interface Platform {
    id: number;
    name: string;
    slug: string;
    website: string | null;
    type: string;
}

const props = defineProps<{
    platform: { data: Platform };
}>();

const form = useForm({
    name: props.platform.data.name,
    website: props.platform.data.website ?? '',
    type: props.platform.data.type,
});

const submit = () => {
    form.put(`/system/platforms/${props.platform.data.id}`);
};

const destroy = () => {
    form.delete(`/system/platforms/${props.platform.data.id}`);
};

const breadcrumbs = [
    { title: 'System', href: '/system' },
    { title: 'Platforms', href: '/system/platforms' },
    { title: 'Edit', href: `/system/platforms/${props.platform.data.id}/edit` },
];
</script>

<template>
    <Head title="Edit Platform" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <PageHeader>
                <template #title>Edit Platform</template>
                <template #description>Edit platform details.</template>
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
                                    <option value="bank">Bank</option>
                                    <option value="exchange">Exchange</option>
                                    <option value="casino">Casino</option>
                                    <option value="wallet">Wallet</option>
                                    <option value="payment_processor">Payment Processor</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <InputError :message="form.errors.type" />
                        </div>

                        <div class="flex justify-between">
                            <Dialog>
                                <DialogTrigger as-child>
                                    <Button variant="destructive" type="button">
                                        <Trash2 class="mr-2 h-4 w-4" />
                                        Delete Platform
                                    </Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Are you absolutely sure?</DialogTitle>
                                        <DialogDescription>
                                            This action cannot be undone. This will permanently delete the platform.
                                        </DialogDescription>
                                    </DialogHeader>
                                    <DialogFooter>
                                        <DialogClose as-child>
                                            <Button variant="outline">Cancel</Button>
                                        </DialogClose>
                                        <Button variant="destructive" @click="destroy">
                                            Delete
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>

                            <Button type="submit" :disabled="form.processing">
                                <Save class="mr-2 h-4 w-4" />
                                Save Changes
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>