<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Upload, FileText, AlertCircle, CheckCircle } from 'lucide-vue-next';
import Swal from 'sweetalert2';

// Akses objek page Inertia
const page = usePage<{
    flash: {
        success?: string;
        error?: string;
    }
}>();

const form = useForm({
    tgl_report: new Date().toISOString().split('T')[0], // Default ke tanggal hari ini
    file: null,
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Import Kredit', href: '/import-kredit' },
];

// Tampilkan notifikasi saat komponen dimuat
onMounted(() => {
    if (page.props.flash?.success) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: page.props.flash.success,
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    if (page.props.flash?.error) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            html: page.props.flash.error,
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33',
        });
    }
});

const submitForm = () => {
    form.post(route('import-kredit.store'), {
        preserveState: true,
        onSuccess: () => {
            form.reset('file');
            // Notifikasi akan ditampilkan di onMounted karena page akan direload
        },
        onError: (errors) => {
            let errorMessage = 'Terjadi kesalahan saat import data.';
            if (errors.file) {
                errorMessage = errors.file;
            }

            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: errorMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33',
            });
        },
    });
};

// Menghandle file input
const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.file = target.files[0];
    }
};
</script>

<template>

    <Head title="Import Kredit" />
    <AppLayout :breadcrumbs="breadcrumbs" active-menu="import-kredit">
        <div class="w-5/8 mx-auto px-2 sm:px-4 lg:px-4 py-4">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-2">
                    <Upload class="h-6 w-6 text-blue-600" />
                    <h1 class="text-xl font-bold">Import Data Kredit</h1>
                </div>
            </div>

            <!-- Pesan Sukses/Error (untuk fallback jika SweetAlert2 gagal) -->
            <div v-if="page.props.flash?.success"
                class="mb-4 p-4 bg-green-100 text-green-700 rounded-md flex items-center">
                <CheckCircle class="h-5 w-5 mr-2" />
                {{ page.props.flash.success }}
            </div>

            <div v-if="page.props.flash?.error" class="mb-4 p-4 bg-red-100 text-red-700 rounded-md flex items-center">
                <AlertCircle class="h-5 w-5 mr-2" />
                {{ page.props.flash.error }}
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Form Upload -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center">
                            <FileText class="h-5 w-5 mr-2" />
                            Upload File Excel
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submitForm">

                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2" for="tgl_report">
                                    Tanggal Laporan
                                </label>
                                <Input id="tgl_report" type="date" v-model="form.tgl_report" class="w-full" />
                                <div v-if="form.errors.tgl_report" class="text-red-500 text-xs italic mt-2">
                                    {{ form.errors.tgl_report }}
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2" for="file">
                                    Pilih File Excel
                                </label>
                                <Input id="file" type="file" @change="handleFileChange" accept=".xlsx, .xls, .csv"
                                    class="w-full" />
                                <div v-if="form.errors.file" class="text-red-500 text-xs italic mt-2">
                                    {{ form.errors.file }}
                                </div>
                                <p class="text-gray-500 text-xs mt-1">
                                    Format file yang didukung: .xlsx, .xls, .csv (maksimal 100MB)
                                </p>
                            </div>

                            <div class="flex justify-end">
                                <Button type="submit" :disabled="form.processing" class="flex items-center">
                                    <Upload v-if="!form.processing" class="h-4 w-4 mr-2" />
                                    <span v-if="form.processing">Memproses...</span>
                                    <span v-else>Import Data</span>
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <!-- Petunjuk Import -->
                <Card>
                    <CardHeader>
                        <CardTitle>Petunjuk Import Data</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">

                            <div class="flex items-start">
                                <div
                                    class="bg-blue-100 text-blue-800 rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0">
                                    1</div>
                                <p class="text-sm">Pilih tanggal laporan untuk mengelompokkan data yang akan
                                    diimport</p>
                            </div>

                            <div class="flex items-start">
                                <div
                                    class="bg-blue-100 text-blue-800 rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0">
                                    2</div>
                                <p class="text-sm">Download template Excel untuk memastikan format data yang benar
                                </p>
                            </div>
                            <div class="flex items-start">
                                <div
                                    class="bg-blue-100 text-blue-800 rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0">
                                    3</div>
                                <p class="text-sm">Pastikan file Excel memiliki header yang sesuai dengan nama kolom
                                    di
                                    database</p>
                            </div>

                            <div class="flex items-start">
                                <div
                                    class="bg-blue-100 text-blue-800 rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0">
                                    4</div>
                                <p class="text-sm">Format tanggal harus sesuai dengan format Excel (YYYY-MM-DD)</p>
                            </div>

                            <div class="flex items-start">
                                <div
                                    class="bg-blue-100 text-blue-800 rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0">
                                    5</div>
                                <p class="text-sm">Kolom wajib diisi: no_rekening, nama_nasabah, tgl_realisasi,
                                    jml_pinjaman</p>
                            </div>

                            <div class="flex items-start">
                                <div
                                    class="bg-blue-100 text-blue-800 rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0">
                                    6</div>
                                <p class="text-sm">Untuk kolom desimal, gunakan titik (.) sebagai pemisah desimal
                                </p>
                            </div>

                            <div class="flex items-start">
                                <div
                                    class="bg-blue-100 text-blue-800 rounded-full h-6 w-6 flex items-center justify-center mr-2 flex-shrink-0">
                                    7</div>
                                <p class="text-sm">Pastikan tidak ada data duplikat pada kolom no_rekening</p>
                            </div>
                        </div>

                        <div class="mt-6 p-3 bg-yellow-50 rounded-md">
                            <p class="text-sm text-yellow-800">
                                <strong>Catatan:</strong> Proses import mungkin memerlukan waktu beberapa menit
                                tergantung pada jumlah data yang diimport.
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Template Download -->
            <Card class="mt-6">
                <CardHeader>
                    <CardTitle>Template Import Data Kredit</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center justify-between">
                        <a :href="route('import-kredit.template')"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <FileText class="h-4 w-4 mr-2" />
                            Download Template
                        </a>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>