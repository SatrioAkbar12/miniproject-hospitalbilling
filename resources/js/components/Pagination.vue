<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';

type PaginationLink = {
    url: string | null;
    label: string;
    page: number | null;
    active: boolean;
};

defineProps<{
    links: PaginationLink[];
}>();
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap gap-2">
        <template v-for="(link, key) in links" :key="key">
            <Button
                v-if="link.url === null"
                disabled
                variant="outline"
                size="sm"
            >
                <span v-html="link.label"></span>
            </Button>

            <Link
                v-else
                :href="link.url"
            >
                <Button
                    :variant="link.active ? 'default' : 'outline'"
                    size="sm"
                >
                    <span v-html="link.label"></span>
                </Button>
            </Link>
        </template>
    </div>
</template>
