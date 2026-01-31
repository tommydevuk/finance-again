<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Trash2 } from 'lucide-vue-next';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';

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
    website: props.platform.data.website,
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
                            <Select v-model="form.type">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select a type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="bank">Bank</SelectItem>
                                    <SelectItem value="exchange">Exchange</SelectItem>
                                    <SelectItem value="casino">Casino</SelectItem>
                                    <SelectItem value="wallet">Wallet</SelectItem>
                                    <SelectItem value="payment_processor">Payment Processor</SelectItem>
                                    <SelectItem value="other">Other</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.type" />
                        </div>

                        <div class="flex justify-between">
                            <AlertDialog>
                                <AlertDialogTrigger as-child>
                                    <Button variant="destructive" type="button">
                                        <Trash2 class="mr-2 h-4 w-4" />
                                        Delete Platform
                                    </Button>
                                </AlertDialogTrigger>
                                <AlertDialogContent>
                                    <AlertDialogHeader>
                                        <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                            This action cannot be undone. This will permanently delete the platform.
                                        </AlertDialogDescription>
                                    </AlertDialogHeader>
                                    <AlertDialogFooter>
                                        <AlertDialogCancel>Cancel</AlertDialogCancel>
                                        <AlertDialogAction @click="destroy" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">
                                            Delete
                                        </AlertDialogAction>
                                    </AlertDialogFooter>
                                </AlertDialogContent>
                            </AlertDialog>

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
