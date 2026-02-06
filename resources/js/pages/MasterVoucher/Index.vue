<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import Input from '@/components/ui/input/Input.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import DeleteConfirmation from './DeleteConfirmation.vue';
import VoucherForm from './VoucherForm.vue';
import VoucherTable from './VoucherTable.vue';

type Props = {
    vouchers: {
        data: Array<{
            id: string;
            name: string;
            type: 'fixed' | 'percentage';
            discount_value: number;
            max_discount_amount: number | null;
            start_date: string;
            end_date: string;
            is_active: boolean;
            created_by: number;
            creator?: { name: string };
            insurance_id: string;
            insurance_name?: string;
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
    insurances: Array<{
        id: string;
        name: string;
    }>;
    filters: {
        search?: string;
    }
};

const props = defineProps<Props>();

const page = usePage();
const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedVoucher = ref<Props['vouchers']['data'][0] | null>(null);

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        '/master-vouchers',
        { search: value },
        {
            preserveState: true,
            replace: true,
        }
    );
});


const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Master Voucher',
        href: '/master-vouchers',
    },
];

const openAddModal = () => {
    selectedVoucher.value = null;
    showAddModal.value = true;
};

const openEditModal = (voucher: Props['vouchers']['data'][0]) => {
    selectedVoucher.value = voucher;
    showEditModal.value = true;
};

const openDeleteModal = (voucher: Props['vouchers']['data'][0]) => {
    selectedVoucher.value = voucher;
    showDeleteModal.value = true;
};

const closeModals = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    showDeleteModal.value = false;
    selectedVoucher.value = null;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Master Voucher" />

        <div class="mx-8 my-6 space-y-6">
            <div class="flex">
                <Heading
                    title="Master Voucher"
                    description="Kelola daftar voucher asuransi"
                />
            </div>
            <div class="flex justify-between items-center">
                <div class="relative w-full max-w-sm items-center">
                    <Input id="search" type="text" placeholder="Cari nama voucher..." class="pl-10" v-model="search" />
                    <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
                        <Search class="size-4 text-muted-foreground" />
                    </span>
                </div>
                <Button @click="openAddModal"> + Tambah Voucher </Button>
            </div>

            <div v-if="(page.props.flash as any)?.success" class="space-y-4">
                <div
                    class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200"
                >
                    {{ (page.props.flash as any)?.success }}
                </div>
            </div>

            <VoucherTable
                :vouchers="vouchers.data"
                @edit="openEditModal"
                @delete="openDeleteModal"
            />

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between bg-white p-4 rounded-lg border dark:bg-slate-800 dark:border-slate-700">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Menampilkan <span class="font-medium">{{ vouchers.data.length }}</span> dari
                    <span class="font-medium">{{ vouchers.total }}</span> voucher
                </div>

                <Pagination :links="vouchers.links" />
            </div>
        </div>

        <!-- Add Modal -->
        <Dialog :open="showAddModal" @update:open="closeModals">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Tambah Voucher Baru</DialogTitle>
                    <DialogDescription>
                        Isi form di bawah untuk menambahkan voucher baru
                    </DialogDescription>
                </DialogHeader>
                <VoucherForm :insurances="insurances" @submitted="closeModals" />
            </DialogContent>
        </Dialog>

        <!-- Edit Modal -->
        <Dialog :open="showEditModal" @update:open="closeModals">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Edit Voucher</DialogTitle>
                    <DialogDescription>
                        Perbarui informasi voucher di bawah
                    </DialogDescription>
                </DialogHeader>
                <VoucherForm
                    v-if="selectedVoucher"
                    :voucher="selectedVoucher"
                    :insurances="insurances"
                    @submitted="closeModals"
                />
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Modal -->
        <DeleteConfirmation
            v-if="selectedVoucher"
            :open="showDeleteModal"
            :voucher="selectedVoucher"
            @confirm="closeModals"
            @cancel="closeModals"
        />
    </AppLayout>
</template>
