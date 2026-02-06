<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

type Voucher = {
    id: string;
    name: string;
    type: 'fixed' | 'percentage';
    discount_value: number;
    max_discount_amount: number | null;
    start_date: string;
    end_date: string;
    is_active: boolean;
};

type Props = {
    open: boolean;
    voucher: Voucher;
};

const props = defineProps<Props>();

const emit = defineEmits<{
    confirm: [];
    cancel: [];
}>();

const isLoading = ref(false);

const handleConfirm = () => {
    isLoading.value = true;
    router.delete(`/master-vouchers/${props.voucher.id}`, {
        onSuccess: () => {
            isLoading.value = false;
            emit('confirm');
        },
        onError: () => {
            isLoading.value = false;
        },
    });
};

const handleCancel = () => {
    if (!isLoading.value) {
        emit('cancel');
    }
};
</script>

<template>
    <Dialog :open="open" @update:open="handleCancel">
        <DialogContent class="max-w-sm bg-white dark:bg-slate-800">
            <DialogHeader>
                <DialogTitle>Konfirmasi Hapus Voucher</DialogTitle>
                <DialogDescription>
                    <p class="text-sm text-gray-700 dark:text-gray-200">
                        Anda akan menghapus voucher <strong>{{ voucher.name }}</strong>. Tindakan
                        ini tidak dapat dibatalkan.
                    </p>
                </DialogDescription>
            </DialogHeader>

            <div class="flex gap-3">
                <Button
                    :disabled="isLoading"
                    variant="outline"
                    class="flex-auto"
                    @click="handleCancel"
                >
                    Batal
                </Button>
                <Button
                    :disabled="isLoading"
                    variant="destructive"
                    class="flex-auto"
                    @click="handleConfirm"
                >
                    {{ isLoading ? 'Menghapus...' : 'Hapus' }}
                </Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
