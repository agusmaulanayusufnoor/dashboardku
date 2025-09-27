<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Table, TableHead, TableRow, TableHeaderCell, TableBody, TableCell } from '@/components/ui/table';
import { Clock, CheckCircle, XCircle, Loader } from 'lucide-vue-next';

defineProps<{
    histories: any[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Import History', href: '/import-history' },
];

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'pending':
            return Clock;
        case 'processing':
            return Loader;
        case 'completed':
            return CheckCircle;
        case 'failed':
            return XCircle;
        default:
            return Clock;
    }
};

const getStatusVariant = (status: string) => {
    switch (status) {
        case 'pending':
            return 'secondary';
        case 'processing':
            return 'default';
        case 'completed':
            return 'default';
        case 'failed':
            return 'destructive';
        default:
            return 'secondary';
    }
};
</script>

<template>

    <Head title="Import History" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="w-full mx-auto px-2 sm:px-4 lg:px-4 py-4">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">Riwayat Import Data Kredit</h1>
                <p class="text-gray-600">Daftar proses import data kredit yang telah dilakukan</p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Riwayat Import</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHead>
                            <TableRow>
                                <TableHeaderCell>Tanggal Report</TableHeaderCell>
                                <TableHeaderCell>User</TableHeaderCell>
                                <TableHeaderCell>Status</TableHeaderCell>
                                <TableHeaderCell>Total Baris</TableHeaderCell>
                                <TableHeaderCell>Berhasil</TableHeaderCell>
                                <TableHeaderCell>Gagal</TableHeaderCell>
                                <TableHeaderCell>Tanggal</TableHeaderCell>
                            </TableRow>
                        </TableHead>
                        <TableBody>
                            <TableRow v-for="history in histories" :key="history.id">
                                <TableCell>{{ history.tgl_report }}</TableCell>
                                <TableCell>{{ history.user.name }}</TableCell>
                                <TableCell>
                                    <span :class="getStatusClasses(history.status)" class="flex items-center gap-1">
                                        <component :is="getStatusIcon(history.status)" class="h-3 w-3" />
                                        {{ history.status }}
                                    </span>
                                </TableCell>
                                <TableCell>{{ history.total_rows }}</TableCell>
                                <TableCell>{{ history.success_count }}</TableCell>
                                <TableCell>{{ history.error_count }}</TableCell>
                                <TableCell>{{ new Date(history.created_at).toLocaleString() }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>