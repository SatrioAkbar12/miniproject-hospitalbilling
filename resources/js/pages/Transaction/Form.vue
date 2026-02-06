<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Trash2, Plus } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

type Props = {
    generated_transaction_code?: string;
    transaction?: {
        id: string;
        transaction_code: string;
        patient_name: string;
        insurance_id: string;
        voucher_id: string | null;
        total_amount_original: number;
        discount_amount: number;
        total_amount_final: number;
        status: 'pending' | 'completed' | 'cancelled';
        details: Array<{
            id: string;
            procedure_id: string;
            procedure_name: string;
            price: number;
            discount_amount: number;
            final_price: number;
        }>;
    };
    insurances: Array<{
        id: string;
        name: string;
    }>;
    vouchers: Array<{
        id: string;
        insurance_id: string;
        name: string;
        type: string;
        discount_value: number;
        max_discount_amount: number;
    }>;
    procedures: Array<{
        id: string;
        name: string;
        prices: {
            id: string;
            unit_price: number;
        };
    }>;
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Transaksi',
        href: '/transactions',
    },
    {
        title: props.transaction ? 'Edit Transaksi' : 'Tambah Transaksi',
        href: '#',
    },
];

const showStatusConfirmationModal = ref(false)

const isEditing = computed(() => !!props.transaction);

const form = useForm<any>(
    props.transaction
        ? {
              transaction_code: props.transaction.transaction_code,
              patient_name: props.transaction.patient_name,
              insurance_id: props.transaction.insurance_id,
              voucher_id: props.transaction.voucher_id || '',
              total_amount_original: props.transaction.total_amount_original,
              discount_amount: props.transaction.discount_amount,
              total_amount_final: props.transaction.total_amount_final,
              status: props.transaction.status,
              detail_transactions: props.transaction.details.map((d) => ({
                  id: d.id,
                  procedure_id: d.procedure_id,
                  procedure_name: d.procedure_name,
                  price: d.price,
                  discount_amount: d.discount_amount,
                  final_price: d.final_price,
              })),
          }
        : {
              transaction_code: props.generated_transaction_code || '',
              patient_name: '',
              insurance_id: '',
              voucher_id: '',
              total_amount_original: 0,
              discount_amount: 0,
              total_amount_final: 0,
              status: 'pending',
              detail_transactions: [],
          }
);

// Filter vouchers by selected insurance
const filteredVouchers = computed(() => {
    if (!form.insurance_id) return [];
    return props.vouchers.filter((v) => v.insurance_id === form.insurance_id);
});

// Clear voucher when insurance changes
watch(
    () => form.insurance_id,
    () => {
        form.voucher_id = '';
    }
);

// Recalculate discounts for all details when voucher changes
watch(
    () => form.voucher_id,
    () => {
        form.detail_transactions.forEach((detail: any, index: number) => {
            if (detail.procedure_id) {
                const selectedVoucher = filteredVouchers.value.find((v) => v.id === form.voucher_id);
                const voucherType = selectedVoucher?.type || '';
                const voucherDiscount = selectedVoucher?.discount_value || 0;
                const voucherMaxDiscount = selectedVoucher?.max_discount_amount || 0;

                if (voucherType === 'fixed') {
                    detail.discount_amount = voucherDiscount;
                } else if (voucherType === 'percentage') {
                    let calculatedDiscount = (detail.price * voucherDiscount) / 100;
                    if (voucherMaxDiscount && calculatedDiscount > voucherMaxDiscount) {
                        calculatedDiscount = voucherMaxDiscount;
                    }
                    detail.discount_amount = calculatedDiscount;
                } else {
                    detail.discount_amount = 0;
                }
                updateDetailFinalPrice(index);
            }
        });
    }
);

// Calculate total amounts based on details
const calculateTotals = () => {
    if (!form.detail_transactions || form.detail_transactions.length === 0) {
        form.total_amount_original = 0;
        form.discount_amount = 0;
        form.total_amount_final = 0;
        return;
    }

    form.total_amount_original = form.detail_transactions.reduce(
        (sum: number, detail: any) => Number(sum) + (Number(detail.price) || 0),
        0
    );

    form.discount_amount = form.detail_transactions.reduce(
        (sum: number, detail: any) => Number(sum) + (Number(detail.discount_amount) || 0),
        0
    );

    form.total_amount_final = form.detail_transactions.reduce(
        (sum: number, detail: any) => Number(sum) + (Number(detail.final_price) || 0),
        0
    );
};

// Watch for changes in detail_transactions
watch(
    () => form.detail_transactions,
    () => {
        calculateTotals();
    },
    { deep: true }
);

const addDetail = () => {
    form.detail_transactions.push({
        procedure_id: '',
        procedure_name: '',
        price: 0,
        discount_amount: 0,
        final_price: 0,
    });
};

const removeDetail = (index: number) => {
    form.detail_transactions.splice(index, 1);
};

const updateDetailFinalPrice = (index: number) => {
    const detail = form.detail_transactions[index];
    detail.final_price = Math.max(0, (detail.price || 0) - (detail.discount_amount || 0));
};

const updateDetailFromProcedure = (index: number, voucher_type: string, voucher_discount: number, voucher_max_discount: number) => {
    const detail = form.detail_transactions[index];
    const selectedProcedure = props.procedures.find((p) => p.id === detail.procedure_id);

    if (selectedProcedure) {
        detail.procedure_name = selectedProcedure.name;
        // Set price from active price if available
        if (selectedProcedure.prices) {
            detail.price = selectedProcedure.prices.unit_price;
            if (voucher_type === 'fixed') {
                detail.discount_amount = voucher_discount;
            } else if (voucher_type === 'percentage') {
                let calculatedDiscount = (detail.price * voucher_discount) / 100;
                if (voucher_max_discount && calculatedDiscount > voucher_max_discount) {
                    calculatedDiscount = voucher_max_discount;
                }
                detail.discount_amount = calculatedDiscount;
            } else {
                detail.discount_amount = 0;
            }
            updateDetailFinalPrice(index);
        }
    }
};

const submitForm = () => {
    if (isEditing.value) {
        form.patch(`/transactions/${props.transaction?.id}`, form.value);
    } else {
        form.post('/transactions', form.value);
    }
};

const onSubmit = () => {
    if (form.status === 'completed' || form.status === 'cancelled') {
        showStatusConfirmationModal.value = true;
    } else {
        submitForm();
    }
};

const onCancel = () => {
    form.get('/transactions');
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value || 0);
};

const closeModals = () => {
    showStatusConfirmationModal.value = false;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="isEditing ? 'Edit Transaksi' : 'Tambah Transaksi'" />

        <div class="mx-8 my-6 max-w-4xl space-y-6">
            <Heading
                :title="isEditing ? 'Edit Transaksi' : 'Tambah Transaksi Baru'"
                :description="isEditing ? 'Perbarui informasi transaksi' : 'Buat transaksi baru untuk pasien'"
            />

            <form @submit.prevent="onSubmit" class="space-y-6">
                <!-- Main Transaction Info -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 dark:bg-slate-800 dark:border-slate-700">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Informasi Transaksi
                    </h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Label for="transaction_code" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Kode Transaksi <span class="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="transaction_code"
                                    v-model="form.transaction_code"
                                    type="text"
                                    placeholder="Masukkan kode transaksi"
                                    class="mt-1"
                                    disabled
                                />
                                <InputError class="mt-1" :message="form.errors.transaction_code" />
                            </div>

                            <div>
                                <Label for="patient_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Nama Pasien <span class="text-red-500">*</span>
                                </Label>
                                <Input
                                    id="patient_name"
                                    v-model="form.patient_name"
                                    type="text"
                                    placeholder="Masukkan nama pasien"
                                    class="mt-1"
                                />
                                <InputError class="mt-1" :message="form.errors.patient_name" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <Label for="insurance_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Asuransi <span class="text-red-500">*</span>
                                </Label>
                                <select
                                    id="insurance_id"
                                    v-model="form.insurance_id"
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-gray-100"
                                >
                                    <option value="">Pilih Asuransi</option>
                                    <option v-for="ins in insurances" :key="ins.id" :value="ins.id">{{ ins.name }}</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.insurance_id" />
                            </div>

                            <div v-if="form.insurance_id !== ''">
                                <Label for="voucher_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                    Voucher (Opsional)
                                </Label>
                                <select
                                    id="voucher_id"
                                    v-model="form.voucher_id"
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-gray-100"
                                >
                                    <option value="">Pilih Voucher</option>
                                    <option v-for="v in filteredVouchers" :key="v.id" :value="v.id">{{ v.name }}</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.voucher_id" />
                            </div>
                        </div>

                        <div>
                            <Label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                Status <span class="text-red-500">*</span>
                            </Label>
                            <select
                                v-model="form.status"
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-gray-100"
                            >
                                <option value="pending">Pending</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                            <InputError class="mt-1" :message="form.errors.status" />
                        </div>
                    </div>
                </div>

                <!-- Transaction Details -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 dark:bg-slate-800 dark:border-slate-700">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Detail Tindakan Medis
                        </h3>
                        <Button
                            type="button"
                            @click="addDetail"
                            variant="outline"
                            size="sm"
                        >
                            <Plus class="mr-2 size-4" /> Tambah Tindakan
                        </Button>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-if="form.detail_transactions.length === 0"
                            class="rounded-lg border-2 border-dashed border-gray-300 p-8 text-center dark:border-slate-600"
                        >
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Belum ada detail transaksi. Klik tombol "Tambah Detail" untuk menambahkan.
                            </p>
                        </div>

                        <div
                            v-for="(detail, index) in form.detail_transactions"
                            :key="index"
                            class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:bg-slate-700/50 dark:border-slate-600"
                        >
                            <div class="flex items-center justify-between">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                    Tindakan #{{ Number(index) + 1 }}
                                </h4>
                                <Button
                                    type="button"
                                    @click="removeDetail( Number(index) )"
                                    variant="ghost"
                                    size="icon-sm"
                                    title="Hapus detail"
                                >
                                    <Trash2 class="size-4 text-red-500" />
                                </Button>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <Label :for="`procedure_id_${index}`" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Prosedur <span class="text-red-500">*</span>
                                    </Label>
                                    <select
                                        :id="`procedure_id_${index}`"
                                        v-model="detail.procedure_id"
                                        @change="updateDetailFromProcedure( Number(index), form.voucher_id ? (filteredVouchers.find(v => v.id === form.voucher_id)?.type || '') : '', form.voucher_id ? (filteredVouchers.find(v => v.id === form.voucher_id)?.discount_value || 0) : 0, form.voucher_id ? (filteredVouchers.find(v => v.id === form.voucher_id)?.max_discount_amount || 0) : 0 )"
                                        class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-gray-100"
                                    >
                                        <option value="">Pilih Prosedur</option>
                                        <option v-for="procedure in procedures" :key="procedure.id" :value="procedure.id">{{ procedure.name }}</option>
                                    </select>
                                </div>

                                <div>
                                    <Label :for="`procedure_name_${index}`" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Nama Prosedur <span class="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        :id="`procedure_name_${index}`"
                                        v-model="detail.procedure_name"
                                        type="text"
                                        class="mt-1"
                                        :disabled=true
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <Label :for="`price_${index}`" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Harga <span class="text-red-500">*</span>
                                    </Label>
                                    <Input
                                        :id="`price_${index}`"
                                        v-model.number="detail.price"
                                        type="number"
                                        placeholder="0"
                                        class="mt-1"
                                        min="0"
                                        @change="updateDetailFinalPrice( Number(index) )"
                                        :disabled=true
                                    />
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ formatCurrency(detail.price) }}
                                    </p>
                                </div>

                                <div>
                                    <Label :for="`discount_${index}`" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Diskon
                                    </Label>
                                    <Input
                                        :id="`discount_${index}`"
                                        v-model.number="detail.discount_amount"
                                        type="number"
                                        placeholder="0"
                                        class="mt-1"
                                        min="0"
                                        :disabled=true
                                    />
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ formatCurrency(detail.discount_amount) }}
                                    </p>
                                </div>

                                <div>
                                    <Label :for="`final_price_${index}`" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                                        Total Akhir
                                    </Label>
                                    <Input
                                        :id="`final_price_${index}`"
                                        v-model.number="detail.final_price"
                                        type="number"
                                        placeholder="0"
                                        class="mt-1"
                                        min="0"
                                        disabled
                                    />
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ formatCurrency(detail.final_price) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Totals Summary -->
                <div class="rounded-lg border border-gray-200 bg-white p-6 dark:bg-slate-800 dark:border-slate-700">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Ringkasan Total
                    </h3>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-slate-600">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Awal</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                {{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(form.total_amount_original || 0) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between border-b border-gray-200 pb-3 dark:border-slate-600">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Diskon</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                {{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(form.discount_amount || 0) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900 dark:text-gray-100">Total Akhir</span>
                            <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                {{ new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(form.total_amount_final || 0) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <Button
                        type="submit"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Menyimpan...' : isEditing ? 'Perbarui Transaksi' : 'Buat Transaksi' }}
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        @click="onCancel"
                        :disabled="form.processing"
                    >
                        Batal
                    </Button>
                </div>
            </form>
        </div>


        <Dialog :open="showStatusConfirmationModal" @update:open="closeModals">
            <DialogContent class="max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi</DialogTitle>
                    <DialogDescription>
                        Apakah yakin ingin mengubah status transaksi ini menjadi
                        <span v-if="form.status === 'completed'" class="font-extrabold text-green-500">Selesai</span>
                        <span v-else class="font-extrabold text-red-500">Dibatalkan</span>
                        ?<br />
                        Transaksi tidak akan bisa diubah lagi.
                    </DialogDescription>
                </DialogHeader>
                <div class="space-y-4 flex justify-center">
                    <Button class="mr-2" @click="submitForm">
                        Ya
                    </Button>
                    <Button class="ml-2" variant="outline" @click="closeModals">
                        Tidak
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
