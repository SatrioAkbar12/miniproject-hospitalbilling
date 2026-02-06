<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Download, Edit, Eye } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

type TransactionData = {
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
};

type Props = {
    transactions: TransactionData[];
};

defineProps<Props>();

const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
};

const getStatusBadge = (
    status: string
): { label: string; class: string } => {
    switch (status) {
        case 'pending':
            return {
                label: 'Pending',
                class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
            };
        case 'completed':
            return {
                label: 'Selesai',
                class: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
            };
        case 'cancelled':
            return {
                label: 'Dibatalkan',
                class: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
            };
        default:
            return {
                label: 'Tidak Diketahui',
                class: 'bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-gray-200',
            };
    }
};
</script>

<template>
    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white dark:bg-slate-800 dark:border-slate-700">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50 dark:bg-slate-700/60">
                    <th class="px-6 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                        Kode Transaksi
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                        Waktu
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                        Nama Pasien
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                        Total Pembayaran
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                        Status
                    </th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="transaction in transactions"
                    :key="transaction.id"
                    class="border-b border-gray-200 hover:bg-gray-50 dark:hover:bg-slate-700/50"
                >
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                        {{ transaction.transaction_code }}
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                        {{ new Date(transaction.created_at).toLocaleString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                        }) }}
                    </td>
                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                        {{ transaction.patient_name }}
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                        {{ formatCurrency(transaction.total_amount_final) }}
                    </td>
                    <td class="px-6 py-4">
                        <span
                            class="rounded-full px-3 py-1 text-xs font-medium"
                            :class="getStatusBadge(transaction.status).class"
                        >
                            {{ getStatusBadge(transaction.status).label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <Link v-if="transaction.status === 'pending'" :href="`/transactions/${transaction.id}/edit`">
                                <Button
                                    variant="ghost"
                                    size="icon-sm"
                                    title="Edit"
                                >
                                    <Edit class="size-4" />
                                </Button>
                            </Link>
                            <Link v-else :href="`/transactions/${transaction.id}/edit`">
                                <Button
                                    variant="ghost"
                                    size="icon-sm"
                                    title="Lihat Detail"
                                >
                                    <Eye class="size-4" />
                                </Button>
                            </Link>
                            <a :href="`/transactions/${transaction.id}/download`" target="_blank">
                                <Button
                                    variant="ghost"
                                    size="icon-sm"
                                    title="Download Invoice"
                                >
                                    <Download class="size-4" />
                                </Button>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
