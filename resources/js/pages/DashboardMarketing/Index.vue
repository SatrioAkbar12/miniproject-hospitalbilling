<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    LineElement,
    PointElement
} from 'chart.js';
import { computed } from 'vue';
import { Bar as BarChart, Line as LineChart } from 'vue-chartjs';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, LineElement, PointElement);

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Dashboard Marketing',
        href: '/marketing',
    },
];

const props = defineProps({
    visitChart: { type: Object as () => any, default: () => ({ labels: [], datasets: [] }) },
    revenueChart: { type: Object as () => any, default: () => ({ labels: [], datasets: [] }) },
    voucherTrendChart: { type: Object as () => any, default: () => ({ labels: [], datasets: [] }) },
});

const visitChartData = computed(() => {
    if (!props.visitChart || !props.visitChart.labels) {
        return { labels: [], datasets: [] };
    }

    const primaryColor = '#3B82F6'; // Blue-500
    const secondaryColor = '#10B981'; // Emerald-500

    return {
        labels: props.visitChart.labels,
        datasets: props.visitChart.datasets.map((dataset: any, index: number) => ({
            ...dataset,
            backgroundColor: [primaryColor, secondaryColor, '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'][index % 7], // A palette of 7 colors
            borderColor: [primaryColor, secondaryColor, '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'][index % 7],
            borderWidth: 1,
        })),
    };
});

const revenueChartData = computed(() => {
    if (!props.revenueChart || !props.revenueChart.labels) {
        return { labels: [], datasets: [] };
    }

    const primaryColor = '#F59E0B'; // Amber-500
    const secondaryColor = '#EF4444'; // Red-500

    return {
        labels: props.revenueChart.labels,
        datasets: props.revenueChart.datasets.map((dataset: any, index: number) => ({
            ...dataset,
            backgroundColor: [primaryColor, secondaryColor, '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#06B6D4'][index % 7], // A palette of 7 colors
            borderColor: [primaryColor, secondaryColor, '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#06B6D4'][index % 7],
            borderWidth: 1,
        })),
    };
});

const voucherTrendChartData = computed(() => {
    if (!props.voucherTrendChart || !props.voucherTrendChart.labels) {
        return { labels: [], datasets: [] };
    }

    // Extract original datasets from the prop
    const originalDatasets = props.voucherTrendChart.datasets;

    // Map them to the format expected by Chart.js
    const datasets = originalDatasets.map((dataset: any) => {
        if (dataset.label === 'Pengguna Voucher') {
            return {
                label: dataset.label,
                data: dataset.dataVouchers,
                backgroundColor: '#3B82F6', // Blue-500
                borderColor: '#3B82F6',
                tension: 0.1,
                fill: false,
            };
        } else if (dataset.label === 'Pengguna Non-Voucher') {
            return {
                label: dataset.label,
                data: dataset.dataNonVouchers,
                backgroundColor: '#EF4444', // Red-500
                borderColor: '#EF4444',
                tension: 0.1,
                fill: false,
            };
        }
        return dataset; // Fallback for any other dataset types
    });

    return {
        labels: props.voucherTrendChart.labels,
        datasets: datasets,
    };
});

const visitChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Jumlah Kunjungan'
            }
        },
        x: {
            title: {
                display: true,
            }
        }
    }
};

const revenueChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Pendapatan (IDR)'
            }
        },
        x: {
            title: {
                display: true,
            }
        }
    }
};

const voucherTrendChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom' as const,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            title: {
                display: true,
                text: 'Jumlah Pengguna'
            }
        },
        x: {
            title: {
                display: true,
            }
        }
    }
};
</script>

<template>
    <Head title="Marketing Dashboard" />
    <AppLayout title="Marketing Dashboard" :breadcrumbs="breadcrumbItems">
        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mb-8">Marketing Dashboard</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Visit Chart Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Jumlah Kunjungan per Asuransi</h3>
                    <div class="h-80">
                        <BarChart :data="visitChartData" :options="visitChartOptions" />
                    </div>
                </div>

                <!-- Revenue Chart Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pendapatan per Asuransi</h3>
                    <div class="h-80">
                        <BarChart :data="revenueChartData" :options="revenueChartOptions" />
                    </div>
                </div>

                <!-- Voucher Trend Chart Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 lg:col-span-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tren Penggunaan Voucher (Bulan Ini)</h3>
                    <div class="h-80">
                        <LineChart :data="voucherTrendChartData" :options="voucherTrendChartOptions" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
