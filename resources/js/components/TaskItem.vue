<script setup lang="ts">
import { ref } from 'vue';
import draggable from 'vuedraggable';
import { MoreHorizontal, ChevronRight, ChevronDown, GripVertical, Plus, Pencil, Trash2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { cn } from '@/lib/utils';

interface Task {
    id: number;
    uuid: string;
    parent_id: number | null;
    name: string;
    description: string | null;
    status: string;
    priority: string;
    children: Task[];
}

const props = defineProps<{
    task: Task;
    level?: number;
}>();

const emit = defineEmits(['edit', 'add-subtask', 'reorder', 'delete']);

const isExpanded = ref(true);
const currentLevel = props.level || 0;

const priorityColors = {
    low: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100',
    medium: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100',
    high: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100',
};

const statusColors = {
    todo: 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-100',
    in_progress: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-100',
    done: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-100',
};

const onReorder = (event: any) => {
    emit('reorder', event);
};
</script>

<template>
    <div class="space-y-1">
        <div 
            :class="cn(
                'group flex items-center justify-between p-2 rounded-lg border bg-card hover:border-primary/50 transition-all',
                currentLevel > 0 && 'ml-6 border-l-4'
            )"
        >
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <div class="cursor-grab active:cursor-grabbing text-muted-foreground/50 group-hover:text-muted-foreground drag-handle px-1">
                    <GripVertical class="h-4 w-4" />
                </div>
                
                <button 
                    v-if="task.children?.length > 0"
                    @click="isExpanded = !isExpanded"
                    class="p-0.5 hover:bg-muted rounded"
                >
                    <ChevronDown v-if="isExpanded" class="h-4 w-4" />
                    <ChevronRight v-else class="h-4 w-4" />
                </button>
                <div v-else class="w-5" />

                <div class="flex flex-col min-w-0">
                    <span :class="cn('text-sm font-medium truncate', task.status === 'done' && 'line-through text-muted-foreground')">
                        {{ task.name }}
                    </span>
                    <span v-if="task.description" class="text-xs text-muted-foreground line-clamp-1">
                        {{ task.description }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3 ml-4 shrink-0">
                <Badge variant="outline" :class="cn('text-[10px] uppercase px-1.5 py-0', priorityColors[task.priority as keyof typeof priorityColors])">
                    {{ task.priority }}
                </Badge>
                
                <Badge variant="outline" :class="cn('text-[10px] uppercase px-1.5 py-0', statusColors[task.status as keyof typeof statusColors])">
                    {{ task.status.replace('_', ' ') }}
                </Badge>

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="ghost" size="icon" class="h-8 w-8 opacity-0 group-hover:opacity-100 transition-opacity">
                            <MoreHorizontal class="h-4 w-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem @click="emit('edit', task)">
                            <Pencil class="mr-2 h-4 w-4" />
                            Edit Task
                        </DropdownMenuItem>
                        <DropdownMenuItem @click="emit('add-subtask', task)">
                            <Plus class="mr-2 h-4 w-4" />
                            Add Sub-task
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem @click="emit('delete', task)" class="text-destructive focus:text-destructive">
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>

        <div v-if="isExpanded && task.children?.length > 0" class="relative">
            <!-- Vertical line for nesting -->
            <div class="absolute left-4 top-0 bottom-0 w-px bg-border ml-0.5" />
            
            <draggable
                :list="task.children || []"
                item-key="id"
                handle=".drag-handle"
                group="tasks"
                @change="onReorder"
                class="space-y-1"
            >
                <template #item="{ element }">
                    <TaskItem 
                        :task="element" 
                        :level="currentLevel + 1"
                        @edit="(t) => emit('edit', t)"
                        @add-subtask="(t) => emit('add-subtask', t)"
                        @reorder="onReorder"
                        @delete="(t) => emit('delete', t)"
                    />
                </template>
            </draggable>
        </div>
    </div>
</template>
