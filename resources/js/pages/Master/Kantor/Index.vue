<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { LoaderCircle, Plus, Edit, Trash, Landmark } from 'lucide-vue-next'; // Import ikon
import { Table, TableHead, TableRow, TableHeaderCell, TableBody, TableCell } from '@/components/ui/table';
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card';

// Akses objek page Inertia secara langsung untuk reaktivitas penuh
const page = usePage<{
  kantors: any,
  search: string,
  perPage: number,
  sortKey: string,
  sortOrder: string
}>();

const kantors = computed(() => page.props.kantors?.data || []);
const currentPage = computed(() => Number(page.props.kantors?.current_page) || 1);
const lastPage = computed(() => Number(page.props.kantors?.last_page) || 1);

const search = ref(page.props.search || '');
const perPage = ref(page.props.perPage || 5);
const sortKey = ref(page.props.sortKey || 'id');
const sortOrder = ref<'asc' | 'desc'>(page.props.sortOrder === 'asc' ? 'asc' : 'desc');

const form = useForm({
  id: null,
  kode_kantor: '',
  nama_kantor: ''
});
const showForm = ref(false);

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Kantor', href: '/master/kantor' },
];

const openForm = (kantor: any = null) => {
  if (kantor) {
    form.id = kantor.id;
    form.kode_kantor = kantor.kode_kantor;
    form.nama_kantor = kantor.nama_kantor;
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
    form.put(route('kantor.update', form.id), { onSuccess: closeForm });
  } else {
    form.post(route('kantor.store'), { onSuccess: closeForm });
  }
};

const deleteKantor = (id: number) => {
  if (confirm('Apakah anda yakin ingin menghapus kantor ini?')) {
    router.delete(route('kantor.destroy', id));
  }
};

const goPage = (targetPage: number) => {
  router.get(route('kantor.index'), {
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
  <Head title="Kantor" />
  <AppLayout :breadcrumbs="breadcrumbs" active-menu="kantor">
    <div class="w-5/8 mx-auto px-2 sm:px-4 lg:px-4 py-4">
      <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-2">
          <Landmark class="h-6 w-6 text-blue-600" />
          <h1 class="text-xl font-bold">Master Kantor</h1>
        </div>
        <Button @click="openForm" class="flex items-center">
          <Plus class="mr-2" /> Tambah Kantor
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
          <CardTitle>Daftar Kantor</CardTitle>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHead>
              <TableRow>
                <TableHeaderCell class="!py-1 px-2" @click="sortKey='kode_kantor'; sortOrder = sortOrder==='asc' ? 'desc' : 'asc'">Kode Kantor</TableHeaderCell>
                <TableHeaderCell class="!py-1 px-2" @click="sortKey='nama_kantor'; sortOrder = sortOrder==='asc' ? 'desc' : 'asc'">Nama Kantor</TableHeaderCell>
                <TableHeaderCell class="!py-1 px-20 flex gap-1">Aksi</TableHeaderCell>
              </TableRow>
            </TableHead>
            <TableBody>
              <TableRow v-for="kantor in kantors" :key="kantor.id"
              :class="['even:bg-gray-50', 'hover:bg-gray-100', 'transition-colors']">
                <TableCell class="!py-1 px-2">{{ kantor.kode_kantor }}</TableCell>
                <TableCell class="!py-1 px-2">{{ kantor.nama_kantor }}</TableCell>
                <TableCell class="!py-1 px-15 flex gap-1">
                  <Button size="sm" variant="outline" @click="openForm(kantor)">
                    <Edit class="h-4 w-4" />
                  </Button>
                  <Button size="sm" variant="destructive" @click="deleteKantor(kantor.id)">
                    <Trash class="h-4 w-4" />
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>

          <div class="flex justify-end gap-1 mt-2">
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
        <h2 class="text-lg font-semibold mb-4">{{ form.id ? 'Edit' : 'Tambah' }} Kantor</h2>
        <div class="mb-3">
          <label class="block mb-1">Kode Kantor</label>
          <Input v-model="form.kode_kantor" placeholder="Kode Kantor"/>
        </div>
        <div class="mb-3">
          <label class="block mb-1">Nama Kantor</label>
          <Input v-model="form.nama_kantor" placeholder="Nama Kantor"/>
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