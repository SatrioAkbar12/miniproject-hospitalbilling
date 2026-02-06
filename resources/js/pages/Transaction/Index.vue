<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import TransactionTable from './TransactionTable.vue';

type Props = {
    transactions: {
        data: Array<{
            id: string;
            transaction_code: string;
            patient_name: string;
            total_amount_original: number;
            discount_amount: number;
            total_amount_final: number;
            status: 'pending' | 'completed' | 'cancelled';
            created_by: number;
            created_at: string;
            creator?: { name: string };
            transaction_details_count?: number;
        }>;
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: Array<{
            url: string | null;
            label: string;
            page: number | null;
            active: boolean;
        }>;
    };
    filters: {
        search?: string;
    };
};

const props = defineProps<Props>();

const page = usePage();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Transaksi',
        href: '/transactions',
    },
];

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        '/transactions',
        { search: value },
        {
            preserveState: true,
            replace: true,
        }
    );
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Transaksi" />

        <div class="mx-8 my-6 space-y-6">
            <div class="flex">
                <Heading
                    title="Transaksi"
                    description="Kelola daftar transaksi pasien"
                />
            </div>
            <div class="flex justify-between items-center">
                <div class="relative w-full max-w-sm items-center">
                    <Input id="search" type="text" placeholder="Cari kode transaksi..." class="pl-10" v-model="search" />
                    <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                        <Search class="size-4 text-muted-foreground" />
                    </span>
                </div>
                <Link href="/transactions/create">
                    <Button> + Tambah Transaksi </Button>
                </Link>
            </div>

            <div v-if="(page.props.flash as any)?.success" class="space-y-4">
                <div
                    class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200"
                >
                    {{ (page.props.flash as any)?.success }}
                </div>
            </div>

            <TransactionTable :transactions="transactions.data" />

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between bg-white p-4 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Menampilkan <span class="font-medium">{{ transactions.data.length }}</span> dari
                    <span class="font-medium">{{ transactions.total }}</span> transaksi
                </div>

                <Pagination :links="transactions.links" />
            </div>
        </div>
    </AppLayout>
</template>
