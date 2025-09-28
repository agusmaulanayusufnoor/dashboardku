<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';
import { Upload, FileText, AlertCircle, CheckCircle, Loader } from 'lucide-vue-next';
import Swal from 'sweetalert2';

// Akses objek page Inertia
const page = usePage<{
    flash: {
        success?: string;
        error?: string;
    },
    import_history_id?: number;
}>();

const today = new Date();
const day = String(today.getDate()).padStart(2, '0'); // Hari dengan leading zero (01-31)
const month = String(today.getMonth() + 1).padStart(2, '0'); // Bulan dengan leading zero (01-12)
const year = today.getFullYear(); // Tahun 4 digi
const form = useForm({
    tgl_report: `${year}-${month}-${day}`, // Format YYYY-MM-DD
    file: null,
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Import Kredit', href: '/import-kredit' },
];
// Variabel untuk polling
const polling = ref<any>(null);
const isProcessing = ref(false);
const currentImportId = ref<number | null>(null);

// Fungsi untuk mengecek status import
const checkImportStatus = async (id: number) => {
    try {
        const response = await fetch(route('import.status.check', id));

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const data = await response.json();

        if (data.status === 'completed') {
            // Stop polling
            if (polling.value) {
                clearInterval(polling.value);
                polling.value = null;
            }

            isProcessing.value = false;

            // Tampilkan notifikasi sukses
            Swal.fire({
                icon: 'success',
                title: 'Import Selesai!',
                html: `Data berhasil diimport!<br>
                       Total: ${data.total_rows} baris<br>
                       Berhasil: ${data.success_count} baris<br>
                       Gagal: ${data.error_count} baris`,
                timer: 5000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });

            // Reset form
            form.reset('file');
        } else if (data.status === 'failed') {
            // Stop polling
            if (polling.value) {
                clearInterval(polling.value);
                polling.value = null;
            }

            isProcessing.value = false;

            // Tampilkan notifikasi error
            Swal.fire({
                icon: 'error',
                title: 'Import Gagal!',
                html: `Terjadi kesalahan saat import data.<br>
                       Error: ${data.error_message || 'Unknown error'}`,
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33',
            });
        }
        // Jika status masih 'processing', lanjutkan polling
    } catch (error) {
        console.error('Error checking import status:', error);

        // Stop polling jika terjadi error
        if (polling.value) {
            clearInterval(polling.value);
            polling.value = null;
        }

        isProcessing.value = false;

        // Tampilkan notifikasi error
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal mengecek status import',
            confirmButtonText: 'OK',
            confirmButtonColor: '#d33',
        });
    }
};
// Fungsi untuk memulai polling
const startPolling = (id: number) => {
    currentImportId.value = id;
    isProcessing.value = true;

    // Set timeout untuk mencegah polling berjalan terus-menerus
    const timeout = setTimeout(() => {
        if (polling.value) {
            clearInterval(polling.value);
            polling.value = null;
            isProcessing.value = false;

            Swal.fire({
                icon: 'warning',
                title: 'Timeout',
                text: 'Proses import memakan waktu terlalu lama',
                confirmButtonText: 'OK',
            });
        }
    }, 300000); // 5 menit timeout

    // Cek status setiap 2 detik
    polling.value = setInterval(() => {
        checkImportStatus(id);
    }, 2000);

    // Simpan timeout ID untuk dibersihkan
    polling.value.timeoutId = timeout;
};
const submitForm = () => {
    form.post(route('import-kredit.store'), {
        preserveState: true,
        onSuccess: (page) => {
            form.reset('file');
            // Dapatkan ID import history dari response
            if (page.props.import_history_id) {
                startPolling(page.props.import_history_id);
            }
        },

        // Tampilkan notifikasi error
        onError: (errors) => {
            let errorMessage = 'Terjadi kesalahan saat import data.';
            if (errors.file) {
                errorMessage = errors.file;
            } else if (page.props.flash?.error) {
                errorMessage = page.props.flash.error;
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


// Bersihkan polling saat komponen di-unmount
onUnmounted(() => {
    if (polling.value) {
        clearInterval(polling.value);
        if (polling.value.timeoutId) {
            clearTimeout(polling.value.timeoutId);
        }
    }
});

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


            <!-- Indikator Proses -->
            <div v-if="isProcessing" class="mb-4 p-4 bg-blue-50 text-blue-700 rounded-md flex items-center">
                <Loader class="h-5 w-5 mr-2 animate-spin" />
                <span>Sedang memproses import data... Mohon tunggu sebentar.</span>
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
                                <Input id="tgl_report" type="date" v-model="form.tgl_report" class="w-full"
                                    :disabled="isProcessing" />
                                <div v-if="form.errors.tgl_report" class="text-red-500 text-xs italic mt-2">
                                    {{ form.errors.tgl_report }}
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2" for="file">
                                    Pilih File Excel
                                </label>
                                <Input id="file" type="file" @change="handleFileChange" accept=".xlsx, .xls, .csv"
                                    class="w-full" :disabled="isProcessing" />
                                <div v-if="form.errors.file" class="text-red-500 text-xs italic mt-2">
                                    {{ form.errors.file }}
                                </div>
                                <p class="text-gray-500 text-xs mt-1">
                                    Format file yang didukung: .xlsx, .xls, .csv (maksimal 100MB)
                                </p>
                            </div>

                            <div class="flex justify-end">
                                <Button type="submit" :disabled="form.processing || isProcessing"
                                    class="flex items-center">
                                    <Upload v-if="!form.processing && !isProcessing" class="h-4 w-4 mr-2" />
                                    <Loader v-if="form.processing || isProcessing" class="h-4 w-4 mr-2 animate-spin" />
                                    <span v-if="form.processing">Mengirim...</span>
                                    <span v-else-if="isProcessing">Memproses...</span>
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