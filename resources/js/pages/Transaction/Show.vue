<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

type Props = {
    transaction: {
        id: string;
        transaction_code: string;
        patient_name: string;
        insurance_id: string;
        voucher_id: string | null;
        total_amount_original: number;
        discount_amount: number;
        total_amount_final: number;
        status: string;
        created_by: string;
        created_at: string;
        creator: {
            name: string;
        };
        voucher: {
            name: string;
        } | null;
        details: Array<{
            id: string;
            procedure_name: string;
            price: number;
            discount_amount: number;
            final_price: number;
        }>;
    };
    insurance: {
        id: string;
        name: string;
    } | null;
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Transactions',
        href: '/transactions',
    },
    {
        title: `Detail Transaksi ${props.transaction.transaction_code}`,
        href: `/transactions/${props.transaction.id}`,
    },
];

const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Detail Transaksi ${transaction.transaction_code}`" />

        <div class="mx-8 my-6 space-y-6">
            <div class="flex items-center justify-between">
                <Heading
                    :title="`Detail Transaksi ${transaction.transaction_code}`"
                    description="Informasi lengkap mengenai transaksi."
                />
                <Button variant="outline" @click="router.visit('/transactions')">
                    <ArrowLeft class="mr-2 size-4" />
                    Kembali
                </Button>
            </div>

            <div class="space-y-4">
                <div class="rounded-lg border bg-white p-6 shadow-sm dark:bg-slate-800 dark:border-slate-700">
                    <h3 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
                        Informasi Umum Transaksi
                    </h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kode Transaksi</p>
                            <p class="text-base text-gray-900 dark:text-white">{{ transaction.transaction_code }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Pasien</p>
                            <p class="text-base text-gray-900 dark:text-white">{{ transaction.patient_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Asuransi</p>
                            <p class="text-base text-gray-900 dark:text-white">
                                {{ insurance ? insurance.name : 'Tidak Ada' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Voucher</p>
                            <p class="text-base text-gray-900 dark:text-white">
                                {{ transaction.voucher ? transaction.voucher.name : 'Tidak Ada' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                            <p class="text-base text-gray-900 dark:text-white">{{ transaction.status }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Dibuat Oleh</p>
                            <p class="text-base text-gray-900 dark:text-white">{{ transaction.creator.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal Transaksi</p>
                            <p class="text-base text-gray-900 dark:text-white">{{ formatDate(transaction.created_at) }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6 shadow-sm dark:bg-slate-800 dark:border-slate-700">
                    <h3 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
                        Ringkasan Biaya
                    </h3>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Biaya Original</p>
                            <p class="text-base text-gray-900 dark:text-white">{{ formatCurrency(transaction.total_amount_original) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Diskon</p>
                            <p class="text-base text-gray-900 dark:text-white">{{ formatCurrency(transaction.discount_amount) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Biaya Final</p>
                            <p class="text-base text-gray-900 dark:text-white">{{ formatCurrency(transaction.total_amount_final) }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border bg-white p-6 shadow-sm dark:bg-slate-800 dark:border-slate-700">
                    <h3 class="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
                        Detail Prosedur
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-slate-700 dark:text-gray-300">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Prosedur</th>
                                    <th scope="col" class="px-6 py-3 text-right">Harga Original</th>
                                    <th scope="col" class="px-6 py-3 text-right">Diskon</th>
                                    <th scope="col" class="px-6 py-3 text-right">Harga Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="detail in transaction.details" :key="detail.id"
                                    class="border-b bg-white dark:border-slate-700 dark:bg-slate-800">
                                    <td class="px-6 py-4">{{ detail.procedure_name }}</td>
                                    <td class="px-6 py-4 text-right">{{ formatCurrency(detail.price) }}</td>
                                    <td class="px-6 py-4 text-right">{{ formatCurrency(detail.discount_amount) }}</td>
                                    <td class="px-6 py-4 text-right">{{ formatCurrency(detail.final_price) }}</td>
                                </tr>
                                <tr v-if="transaction.details.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center">Tidak ada detail prosedur.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
