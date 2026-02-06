<script setup lang="ts">
import { Edit, Trash2 } from 'lucide-vue-next';

type VoucherData = {
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
};

type Props = {
    vouchers: VoucherData[];
};

defineProps<Props>();

const emit = defineEmits<{
    edit: [voucher: VoucherData];
    delete: [voucher: VoucherData];
}>();

const formatDate = (date: string): string => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatCurrency = (value: number, type: string): string => {
    if (type === 'percentage') {
        return `${value}%`;
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
};

const isReady = (startDate: string): boolean => {
    const start = new Date(startDate);
    start.setHours(0, 0, 0, 0);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return start > today;
};

const isExpired = (endDate: string): boolean => {
    const end = new Date(endDate);
    end.setHours(0, 0, 0, 0);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return end < today;
};

const getStatusBadge = (
    isActive: boolean,
    startDate: string,
    endDate: string
): { label: string; class: string } => {
    if (!isActive) {
        return {
            label: 'Nonaktif',
            class: 'bg-gray-100 text-gray-800 dark:bg-slate-700 dark:text-gray-200',
        };
    }
    if (endDate !== null && isExpired(endDate)) {
        return {
            label: 'Kadaluarsa',
            class: 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
        };
    }
    if (isReady(startDate)) {
        return {
            label: 'Terjadwal',
            class: 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
        }
    }
    return {
        label: 'Aktif',
        class: 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    };
};
</script>

<template>
    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white dark:bg-slate-800 dark:border-slate-700">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50 dark:bg-slate-700/60">
                    <th class="px-6 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                        Nama Voucher
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                        Asuransi
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                        Tipe
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                        Diskon
                    </th>
                    <th class="px-6 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">
                        Periode
                    </th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">
                        Status
                    </th>
                    <th class="px-6 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="voucher in vouchers"
                    :key="voucher.id"
                    class="border-b border-gray-200 bg-white dark:bg-transparent transition-colors hover:bg-gray-50 dark:hover:bg-slate-800"
                >
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-gray-100">
                                {{ voucher.name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                By: {{ voucher.creator?.name || 'Unknown' }}
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900 dark:text-gray-100">
                            {{ voucher.insurance_name || 'Unknown' }}
                        </p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/40 dark:text-blue-200">
                            {{ voucher.type === 'fixed' ? 'Fixed' : 'Percentage' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ formatCurrency(voucher.discount_value, voucher.type) }}
                        </p>
                        <p v-if="voucher.max_discount_amount" class="text-xs text-gray-500 dark:text-gray-400">
                            Max: {{ formatCurrency(voucher.max_discount_amount, 'fixed') }}
                        </p>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        <p>{{ formatDate(voucher.start_date) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            s/d {{ voucher.end_date === null ? '-' :formatDate(voucher.end_date) }}
                        </p>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span
                            :class="[
                                'inline-block rounded-full px-3 py-1 text-xs font-medium',
                                getStatusBadge(voucher.is_active, voucher.start_date, voucher.end_date).class,
                            ]"
                        >
                            {{ getStatusBadge(voucher.is_active, voucher.start_date, voucher.end_date).label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button
                            @click="emit('edit', voucher)"
                            class="rounded p-2 text-blue-600 transition-colors hover:bg-blue-50 dark:text-blue-300 dark:hover:bg-blue-800/50"
                            title="Edit voucher"
                        >
                            <Edit :size="18" />
                        </button>
                        <button
                            @click="emit('delete', voucher)"
                            class="rounded p-2 text-red-600 transition-colors hover:bg-red-50 dark:text-red-300 dark:hover:bg-red-800/40"
                            title="Delete voucher"
                        >
                            <Trash2 :size="18" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="vouchers.length === 0" class="p-8 text-center">
            <p class="text-gray-500 dark:text-gray-400">Tidak ada data voucher</p>
        </div>
    </div>
</template>
