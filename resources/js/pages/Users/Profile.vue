<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { useInitials } from '@/composables/useInitials';

const props = defineProps<{
    profile: {
        id: number;
        uuid: string;
        name: string;
        email: string;
        created_at: string;
        roles?: Array<{ id: number; name: string }>;
    };
}>();

const { getInitials } = useInitials();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Profile',
        href: `/u/${props.profile.uuid}`,
    },
];

const memberSince = new Date(props.profile.created_at).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
});
</script>

<template>
    <Head :title="profile.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6">
            <Card>
                <CardContent>
                    <div class="min-h-screen dark:text-gray-100 ">
  

  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
    <div class=" flex flex-col items-center sm:flex-row sm:items-end sm:space-x-5">
      
      <div class="relative group">
        <img class="h-32 w-32 rounded-full ring-4 ring-white dark:ring-gray-800 object-cover bg-white" 
             src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" 
             alt="User Avatar">
        <span class="absolute bottom-2 right-2 block h-4 w-4 rounded-full bg-green-400 ring-2 ring-white dark:ring-gray-800"></span>
      </div>

      <div class="mt-6 sm:mt-0 sm:flex-1 sm:min-w-0 sm:flex sm:items-center sm:justify-end sm:space-x-6 pb-1">
        <div class="sm:hidden md:block mt-6 min-w-0 flex-1">
          <h1 class="text-2xl font-bold truncate">Alex Rivera</h1>
          <p class="text-gray-500 dark:text-gray-400">@arivera_dev</p>
        </div>
        <div class="mt-6 flex flex-col justify-stretch space-y-3 sm:flex-row sm:space-y-0 sm:space-x-4">
          <button class="inline-flex justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none">
            Message
          </button>
          <button class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
            Follow
          </button>
        </div>
      </div>
    </div>

    <div class="mt-8 grid grid-cols-3 gap-4 py-4 border-y border-gray-200 dark:border-gray-700 text-center">
      <div>
        <span class="block text-xl font-bold">1.2k</span>
        <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Posts</span>
      </div>
      <div>
        <span class="block text-xl font-bold">45.8k</span>
        <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Followers</span>
      </div>
      <div>
        <span class="block text-xl font-bold">892</span>
        <span class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Following</span>
      </div>
    </div>

    <div class="mt-6">
      <p class="text-lg">
        Full-stack developer & UI enthusiast. Coffee drinker, late-night coder, and occasional hiker. Building things with <span class="text-blue-500">#TailwindCSS</span> and <span class="text-purple-500">#React</span>.
      </p>
      <div class="mt-4 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
        <span class="flex items-center">📍 San Francisco, CA</span>
        <a href="#" class="flex items-center text-blue-500 hover:underline">🔗 arivera.dev</a>
      </div>
    </div>

    <div class="mt-10 grid grid-cols-3 gap-1 md:gap-4 pb-20">
      <div class="aspect-square bg-gray-200 dark:bg-gray-800 rounded-sm md:rounded-lg animate-pulse"></div>
      <div class="aspect-square bg-gray-200 dark:bg-gray-800 rounded-sm md:rounded-lg animate-pulse"></div>
      <div class="aspect-square bg-gray-200 dark:bg-gray-800 rounded-sm md:rounded-lg animate-pulse"></div>
      <div class="aspect-square bg-gray-200 dark:bg-gray-800 rounded-sm md:rounded-lg animate-pulse"></div>
      <div class="aspect-square bg-gray-200 dark:bg-gray-800 rounded-sm md:rounded-lg animate-pulse"></div>
      <div class="aspect-square bg-gray-200 dark:bg-gray-800 rounded-sm md:rounded-lg animate-pulse"></div>
    </div>
  </div>
</div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
