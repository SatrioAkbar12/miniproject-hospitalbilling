<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const page = usePage();

type Props = {
    voucher?: {
        id: string;
        name: string;
        insurance_id: string;
        type: 'fixed' | 'percentage';
        discount_value: number;
        max_discount_amount: number | null;
        start_date: string;
        end_date: string | null;
        is_active: boolean;
    };
    insurances: Array<{
        id: string;
        name: string;
    }>;
};

const props = defineProps<Props>();

const emit = defineEmits<{
    submitted: [];
}>();

const form = ref<any>(
    props.voucher
        ? {
              name: props.voucher.name,
              insurance_id: props.voucher.insurance_id,
              type: props.voucher.type,
              discount_value: props.voucher.discount_value,
              max_discount_amount: props.voucher.max_discount_amount,
              start_date: props.voucher.start_date ? String(props.voucher.start_date).split('T')[0] : '',
              end_date: props.voucher.end_date ? String(props.voucher.end_date).split('T')[0] : '',
              is_active: props.voucher.is_active,
          }
        : {
              name: '',
              insurance_id: '',
              type: '',
              discount_value: '',
              max_discount_amount: '',
              start_date: '',
              end_date: '',
              is_active: true,
          }
);

const useSameAsDiscount = ref(
    props.voucher ? props.voucher.max_discount_amount === props.voucher.discount_value : false
);

// Watch checkbox to auto-sync max_discount_amount with discount_value
watch(useSameAsDiscount, (newValue) => {
    if (newValue) {
        form.value.max_discount_amount = form.value.discount_value;
    }
});

// Watch discount_value to update max_discount_amount if checkbox is enabled
watch(
    () => form.value.discount_value,
    (newValue) => {
        if (useSameAsDiscount.value) {
            form.value.max_discount_amount = newValue;
        }
    }
);

const onSubmit = async () => {
    if (props.voucher) {
        // Edit
        router.patch(
            `/master-vouchers/${props.voucher.id}`,
            form.value,
            {
                onSuccess: () => {
                    emit('submitted');
                },
            }
        );
    } else {
        // Create
        router.post('/master-vouchers', form.value, {
            onSuccess: () => {
                emit('submitted');
            },
        });
    }
};
</script>

<template>
    <form @submit.prevent="onSubmit" class="space-y-4">
        <div>
            <Label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                >Nama Voucher <span class="text-red-500">*</span> </Label
            >
            <Input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Masukkan nama voucher"
                class="mt-1"
            />
            <InputError class="mt-1" :message="page.props.errors.name as string" />
        </div>

        <div>
            <Label for="insurance_id" class="block text-sm font-medium text-gray-700"
                >Asuransi <span class="text-red-500">*</span> </Label
            >
            <select
                v-model="form.insurance_id"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-gray-100"
            >
                <option value="">Pilih asuransi</option>
                <option v-for="insurance in insurances" :key="insurance.id" :value="insurance.id">
                    {{ insurance.name }}
                </option>
            </select>
            <InputError class="mt-1" :message="page.props.errors.insurance_id as string" />
        </div>

        <div>
            <Label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                >Tipe Diskon <span class="text-red-500">*</span> </Label
            >
            <select
                v-model="form.type"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:bg-slate-900 dark:border-slate-700 dark:text-gray-100"
            >
                <option value="">Pilih tipe diskon</option>
                <option value="fixed">Fixed (Jumlah Tetap)</option>
                <option value="percentage">Percentage (Persentase)</option>
            </select>
            <InputError class="mt-1" :message="page.props.errors.type as string" />
        </div>

        <div>
            <Label
                for="discount_value"
                class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                >Nilai Diskon <span class="text-red-500">*</span> </Label
            >
            <Input
                id="discount_value"
                v-model.number="form.discount_value"
                type="number"
                placeholder="Masukkan nilai diskon"
                class="mt-1"
                min="0"
            />
            <InputError class="mt-1" :message="page.props.errors.discount_value as string" />
        </div>

        <div v-if="form.type !== 'fixed'">
            <Label
                for="max_discount_amount"
                class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                >Maksimal Diskon</Label
            >
            <Input
                id="max_discount_amount"
                v-model.number="form.max_discount_amount"
                type="number"
                placeholder="Masukkan maksimal diskon"
                class="mt-1"
                min="0"
                :disabled="useSameAsDiscount"
            />
            <InputError class="mt-1" :message="page.props.errors.max_discount_amount as string" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <Label
                    for="start_date"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >Tanggal Mulai <span class="text-red-500">*</span></Label
                >
                <Input
                    id="start_date"
                    v-model="form.start_date"
                    type="date"
                    class="mt-1"
                />
                <InputError class="mt-1" :message="page.props.errors.start_date as string" />
            </div>

            <div>
                <Label
                    for="end_date"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-200"
                    >Tanggal Akhir</Label
                >
                <Input
                    id="end_date"
                    v-model="form.end_date"
                    type="date"
                    class="mt-1"
                />
                <InputError class="mt-1" :message="page.props.errors.end_date as string" />
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <input
                id="is_active"
                v-model="form.is_active"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600"
            />
            <Label
                for="is_active"
                class="text-sm font-medium text-gray-700 dark:text-gray-200"
                >Aktif</Label
            >
        </div>

        <div class="flex gap-3 pt-4">
            <Button type="submit" class="w-full">
                {{ voucher ? 'Perbarui' : 'Tambahkan' }}
            </Button>
        </div>
    </form>
</template>
