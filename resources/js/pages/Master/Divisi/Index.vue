<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { LoaderCircle, Plus, Edit, Trash, IdCard } from 'lucide-vue-next';
import { Table, TableHead, TableRow, TableHeaderCell, TableBody, TableCell } from '@/components/ui/table';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';

const page = usePage<{
  divisies: any,
  search: string,
  perPage: number,
  sortKey: string,
  sortOrder: string
}>();

const divisies = computed(() => page.props.divisies?.data || []);
const currentPage = computed(() => Number(page.props.divisies?.current_page) || 1);
const lastPage = computed(() => Number(page.props.divisies?.last_page) || 1);

const search = ref(page.props.search || '');
const perPage = ref(page.props.perPage || 5);
const sortKey = ref(page.props.sortKey || 'id');
const sortOrder = ref<'asc' | 'desc'>(page.props.sortOrder === 'asc' ? 'asc' : 'desc');

const form = useForm({
  id: null,
  nama_divisi: ''
});
const showForm = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Divisi', href: '/divisi' },
];

const openForm = (divisi: any = null) => {
  if (divisi) {
    form.id = divisi.id;
    form.nama_divisi = divisi.nama_divisi;
  } else {
    form.reset();
  }
  showForm.value = true;
};

const closeForm = () => {
  form.reset();
  showForm.value = false;
};

const submitForm = () => {
  if (form.id) {
    form.put(route('divisi.update', form.id), { onSuccess: closeForm });
  } else {
    form.post(route('divisi.store'), { onSuccess: closeForm });
  }
};

const deleteDivisi = (id: number) => {
  if (confirm('Apakah anda yakin ingin menghapus divisi ini?')) {
    router.delete(route('divisi.destroy', id));
  }
};

const goPage = (targetPage: number) => {
  router.get(route('divisi.index'), {
    page: targetPage,
    search: search.value,
    perPage: perPage.value,
    sortKey: sortKey.value,
    sortOrder: sortOrder.value
  }, { preserveState: true, replace: true });
};

watch([search, perPage, sortKey, sortOrder], () => {
  goPage(1);
});

const pageNumbers = computed(() => {
  const pages = [];
  const start = Math.max(currentPage.value - 2, 1);
  const end = Math.min(start + 4, lastPage.value);
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  return pages;
});
</script>

<template>
  <Head title="Divisi" />
  <AppLayout :breadcrumbs="breadcrumbs" active-menu="divisi">
    <div class="w-5/8 mx-auto px-2 sm:px-4 lg:px-4 py-4">
      <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-2">
          <IdCard class="h-6 w-6 text-green-600" />
          <h1 class="text-xl font-bold">Master Divisi</h1>
        </div>
        <Button @click="openForm" class="flex items-center">
          <Plus class="mr-2" /> Tambah Divisi
        </Button>
      </div>

      <div class="flex justify-end gap-2 mb-2">
        <Input placeholder="Search..." v-model="search" class="w-60"/>
        <select v-model="perPage" class="border rounded px-2">
          <option :value="5">5</option>
          <option :value="10">10</option>
          <option :value="50">50</option>
        </select>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Daftar Divisi</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHead>
              <TableRow>
                <TableHeaderCell class="!py-1 px-2" @click="sortKey='nama_divisi'; sortOrder = sortOrder==='asc' ? 'desc' : 'asc'">Nama Divisi</TableHeaderCell>
                <TableHeaderCell class="!py-1 px-25 flex gap-1">Aksi</TableHeaderCell>
              </TableRow>
            </TableHead>
            <TableBody>
              <TableRow v-for="divisi in divisies" :key="divisi.id"
              :class="['even:bg-gray-50', 'hover:bg-gray-100', 'transition-colors']">
                <TableCell class="!py-1 px-2">{{ divisi.nama_divisi }}</TableCell>
                <TableCell class="!py-1 px-20 flex gap-1">
                  <Button size="sm" variant="outline" @click="openForm(divisi)">
                    <Edit class="h-4 w-4" />
                  </Button>
                  <Button size="sm" variant="destructive" @click="deleteDivisi(divisi.id)">
                    <Trash class="h-4 w-4" />
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>

          <div class="flex justify-center gap-2 mt-4">
            <Button :disabled="currentPage <= 1" @click="goPage(1)">First</Button>
            <Button :disabled="currentPage <= 1" @click="goPage(Math.max(currentPage - 1, 1))">Prev</Button>

            <Button
              v-for="p in pageNumbers"
              :key="p"
              :variant="p === currentPage ? 'default' : 'outline'"
              @click="goPage(p)"
            >{{ p }}</Button>

            <Button :disabled="currentPage >= lastPage" @click="goPage(Math.min(currentPage + 1, lastPage))">Next</Button>
            <Button :disabled="currentPage >= lastPage" @click="goPage(lastPage)">Last</Button>
          </div>
        </CardContent>
      </Card>
    </div>

    <div v-if="showForm" class="fixed inset-0 bg-black/30 flex justify-center items-center z-50">
      <div class="bg-white p-6 rounded shadow w-96">
        <h2 class="text-lg font-semibold mb-4">{{ form.id ? 'Edit' : 'Tambah' }} Divisi</h2>
        <div class="mb-3">
          <label class="block mb-1">Nama Divisi</label>
          <Input v-model="form.nama_divisi" placeholder="Nama Divisi"/>
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <Button variant="secondary" @click="closeForm">Batal</Button>
          <Button @click="submitForm" :disabled="form.processing">
            <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin"/>
            Simpan
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
