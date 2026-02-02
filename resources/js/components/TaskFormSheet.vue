<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetFooter,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';

interface Task {
    id?: number;
    uuid?: string;
    parent_id?: number | null;
    name: string;
    description: string | null;
    status: string;
    priority: string;
}

const props = defineProps<{
    open: boolean;
    task: Task | null;
    projectId: string;
    entityId: string;
}>();

const emit = defineEmits(['update:open', 'success']);

const form = useForm({
    name: '',
    description: '',
    status: 'todo',
    priority: 'medium',
    parent_id: null as number | null,
});

watch(() => props.task, (newTask) => {
    if (newTask) {
        form.name = newTask.name;
        form.description = newTask.description || '';
        form.status = newTask.status;
        form.priority = newTask.priority;
        form.parent_id = newTask.parent_id || null;
    } else {
        form.reset();
    }
}, { immediate: true });

const submit = () => {
    if (props.task?.uuid) {
        form.put(route('teams.projects.tasks.update', { entity: props.entityId, project: props.projectId, task: props.task.uuid }), {
            onSuccess: () => {
                emit('update:open', false);
                emit('success');
            }
        });
    } else {
        form.post(route('teams.projects.tasks.store', { entity: props.entityId, project: props.projectId }), {
            onSuccess: () => {
                emit('update:open', false);
                emit('success');
            }
        });
    }
};
</script>

<template>
    <Sheet :open="open" @update:open="(val) => emit('update:open', val)">
        <SheetContent class="sm:max-w-md">
            <SheetHeader>
                <SheetTitle>{{ task?.uuid ? 'Edit Task' : 'Create New Task' }}</SheetTitle>
                <SheetDescription>
                    {{ task?.uuid ? 'Update the details of your project task.' : 'Add a new task to your project.' }}
                </SheetDescription>
            </SheetHeader>

            <form @submit.prevent="submit" class="space-y-6 py-6">
                <div class="space-y-2">
                    <Label for="task-name">Task Name</Label>
                    <Input id="task-name" v-model="form.name" required placeholder="What needs to be done?" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <Label for="task-description">Description</Label>
                    <Textarea id="task-description" v-model="form.description" placeholder="Add more details..." rows="3" />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label>Priority</Label>
                        <Select v-model="form.priority">
                            <SelectTrigger>
                                <SelectValue placeholder="Priority" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="low">Low</SelectItem>
                                <SelectItem value="medium">Medium</SelectItem>
                                <SelectItem value="high">High</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.priority" />
                    </div>

                    <div class="space-y-2">
                        <Label>Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger>
                                <SelectValue placeholder="Status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="todo">To Do</SelectItem>
                                <SelectItem value="in_progress">In Progress</SelectItem>
                                <SelectItem value="done">Done</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.status" />
                    </div>
                </div>

                <SheetFooter>
                    <Button type="button" variant="outline" @click="emit('update:open', false)">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ task?.uuid ? 'Save Changes' : 'Create Task' }}
                    </Button>
                </SheetFooter>
            </form>
        </SheetContent>
    </Sheet>
</template>
