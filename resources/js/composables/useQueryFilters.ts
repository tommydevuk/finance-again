import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ref, watch, computed, type Ref } from 'vue';

interface SortOptions {
    defaultSort: string;
    defaultDirection: 'asc' | 'desc';
}

export function useQueryFilters(
    initialFilters: Record<string, any>,
    sortOptions: SortOptions = { defaultSort: 'created_at', defaultDirection: 'desc' },
    routePath: string
) {
    const search = ref(initialFilters.search || '');
    
    // safe access to sort, ensuring it's a string and not Array.prototype.sort
    const rawSort = initialFilters.sort;
    const sortField = ref((typeof rawSort === 'string' ? rawSort : null) || sortOptions.defaultSort);
    
    const direction = ref(initialFilters.direction || sortOptions.defaultDirection);
    
    // Dynamic filters (e.g. role, status)
    const filters = ref({ ...initialFilters });
    // Remove known keys from filters object to avoid duplication if passed in initialFilters
    delete filters.value.search;
    delete filters.value.sort;
    delete filters.value.direction;

    const isFiltered = computed(() => {
        return search.value !== '' || 
               sortField.value !== sortOptions.defaultSort || 
               direction.value !== sortOptions.defaultDirection ||
               Object.values(filters.value).some(val => val !== '' && val !== null && val !== undefined);
    });

    const handleSearch = useDebounceFn(() => {
        router.get(routePath, { 
            search: search.value,
            sort: sortField.value,
            direction: direction.value,
            ...filters.value
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);

    const resetFilters = () => {
        search.value = '';
        sortField.value = sortOptions.defaultSort;
        direction.value = sortOptions.defaultDirection;
        
        // Reset all dynamic filters to empty string or null
        Object.keys(filters.value).forEach(key => {
            filters.value[key] = '';
        });
        
        // handleSearch will be triggered by watcher
    };

    // Watch all reactive state
    watch([search, sortField, direction, filters], () => {
        handleSearch();
    }, { deep: true });

    return {
        search,
        sort: sortField,
        direction,
        filters,
        isFiltered,
        resetFilters,
    };
}
