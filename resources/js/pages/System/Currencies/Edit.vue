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

interface Currency {
    id: number;
    name: string;
    code: string;
    symbol: string | null;
    type: string;
    decimals: number;
}

const props = defineProps<{
    currency: { data: Currency };
    types: Array<{ label: string; value: string }>;
}>();

const form = useForm({
    name: props.currency.data.name,
    code: props.currency.data.code,
    symbol: props.currency.data.symbol ?? '',
    type: props.currency.data.type,
    decimals: props.currency.data.decimals,
});

const submit = () => {
    form.put(`/system/currencies/${props.currency.data.id}`);
};

const destroy = () => {
    form.delete(`/system/currencies/${props.currency.data.id}`);
};

const breadcrumbs = [
    { title: 'System', href: '/system' },
    { title: 'Currencies', href: '/system/currencies' },
    { title: 'Edit', href: `/system/currencies/${props.currency.data.id}/edit` },
];
</script>

<template>
    <Head title="Edit Currency" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <PageHeader>
                <template #title>Edit Currency</template>
                <template #description>Edit currency details.</template>
                <template #actions>
                    <Button variant="outline" as-child>
                        <Link href="/system/currencies">
                            <ArrowLeft class="mr-2 h-4 w-4" />
                            Back
                        </Link>
                    </Button>
                </template>
            </PageHeader>

            <Card>
                <CardContent class="pt-6">
                    <form @submit.prevent="submit" class="space-y-6 max-w-2xl">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="name">Name</Label>
                                <Input id="name" v-model="form.name" required />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="space-y-2">
                                <Label for="code">Code</Label>
                                <Input id="code" v-model="form.code" required />
                                <InputError :message="form.errors.code" />
                            </div>
                        </div>

                         <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="symbol">Symbol</Label>
                                <Input id="symbol" v-model="form.symbol" />
                                <InputError :message="form.errors.symbol" />
                            </div>
                             <div class="space-y-2">
                                <Label for="decimals">Decimals</Label>
                                <Input id="decimals" type="number" v-model="form.decimals" required />
                                <InputError :message="form.errors.decimals" />
                            </div>
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

                        <div class="flex justify-between">
                            <Dialog>
                                <DialogTrigger as-child>
                                    <Button variant="destructive" type="button">
                                        <Trash2 class="mr-2 h-4 w-4" />
                                        Delete Currency
                                    </Button>
                                </DialogTrigger>
                                <DialogContent>
                                    <DialogHeader>
                                        <DialogTitle>Are you absolutely sure?</DialogTitle>
                                        <DialogDescription>
                                            This action cannot be undone. This will permanently delete the currency.
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
