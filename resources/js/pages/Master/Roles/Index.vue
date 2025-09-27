<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { LoaderCircle, Plus, Edit, Trash } from 'lucide-vue-next';

const page = usePage<{
  roles: any,
  permissions: Record<string, Array<{ id: number, name: string }>>,
  search?: string,
  perPage?: number
}>();

const roles = computed(() => page.props.roles.data);
const permissions = computed(() => page.props.permissions);

const search = ref(page.props.search || '');
const perPage = ref(page.props.perPage || 5);

const showForm = ref(false);
const form = useForm({
  id: null,
  name: '',
  permissions: [] as string[]
});

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Role Management', href: '/roles' }
];

const openForm = (role: any = null) => {
  if (role) {
    form.id = role.id;
    form.name = role.name;
    form.permissions = role.permissions?.map((p: any) => p.name) || [];
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
    form.put(route('roles.update', form.id), { onSuccess: closeForm });
  } else {
    form.post(route('roles.store'), { onSuccess: closeForm });
  }
};

const deleteRole = (id: number) => {
  if (confirm('Yakin hapus role ini?')) {
    router.delete(route('roles.destroy', id));
  }
};

const toggleAllPermissions = (checked: boolean) => {
  const all = Object.values(permissions.value).flat().map((p: any) => p.name);
  form.permissions = checked ? all : [];
};
</script>

<template>
  <Head title="Role Management" />
  <AppLayout :breadcrumbs="breadcrumbs" active-menu="roles">
    <div class="w-11/12 mx-auto px-4 py-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">Role Management</h1>
        <Button @click="openForm"><Plus class="mr-2" /> Tambah Role</Button>
      </div>

      <Card>
        <CardHeader class="flex justify-between items-center">
          <CardTitle>Daftar Role</CardTitle>
          <div class="flex gap-2 items-center">
            <Input
              v-model="search"
              placeholder="Cari role..."
              class="w-48"
              @keyup.enter="() => router.get(route('roles.index'), { search, perPage })"
            />
            <select
              v-model="perPage"
              @change="() => router.get(route('roles.index'), { search, perPage })"
              class="border rounded px-2 py-1"
            >
              <option :value="5">5</option>
              <option :value="10">10</option>
              <option :value="50">50</option>
            </select>
          </div>
        </CardHeader>

        <CardContent>
          <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
              <thead>
                <tr class="bg-gray-100">
                  <!-- <th class="border px-4 py-2 w-16">ID</th> -->
                  <th class="border px-4 py-2 w-48">Nama Role</th>
                  <th class="border px-4 py-2 w-1/2">Permissions</th>
                  <th class="border px-4 py-2 w-32">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="role in roles" :key="role.id">
                  <!-- <td class="border px-4 py-2">{{ role.id }}</td> -->
                  <td class="border px-4 py-2">{{ role.name }}</td>
                  <td class="border px-4 py-2 max-w-xs overflow-hidden">
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="p in role.permissions"
                        :key="p.id"
                        class="inline-block bg-gray-200 px-2 py-1 rounded text-xs truncate"
                        :title="p.name"
                      >
                        {{ p.name.length > 20 ? p.name.slice(0, 20) + '...' : p.name }}
                      </span>
                    </div>
                  </td>
                  <td class="border px-4 py-2 text-center whitespace-nowrap">
                    <Button size="sm" class="mr-2" @click="openForm(role)">
                      <Edit />
                    </Button>
                    <Button size="sm" variant="destructive" @click="deleteRole(role.id)">
                      <Trash />
                    </Button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>

      <!-- MODAL -->
      <div
        v-if="showForm"
        class="fixed inset-0 bg-black/30 flex justify-center items-start pt-20 z-50 overflow-auto"
      >
        <div class="bg-white p-6 rounded shadow w-3/4 max-w-3xl">
          <h2 class="text-lg font-semibold mb-4">{{ form.id ? 'Edit' : 'Tambah' }} Role</h2>

          <div class="mb-3">
            <label class="block mb-1">Nama Role</label>
            <Input v-model="form.name" placeholder="Nama Role" />
          </div>

          <div class="mb-3">
            <label class="block mb-1">Permissions</label>
            <div class="mb-2">
              <label class="inline-flex items-center gap-2">
                <input type="checkbox" @change="toggleAllPermissions($event.target.checked)" />
                Pilih Semua
              </label>
            </div>
            <div class="grid grid-cols-2 gap-2 max-h-96 overflow-auto border p-2 rounded">
              <div v-for="(perms, group) in permissions" :key="group">
                <h3 class="font-semibold capitalize mb-1">{{ group }}</h3>
                <div v-for="p in perms" :key="p.id" class="flex items-center gap-2 mb-1">
                  <input type="checkbox" :value="p.name" v-model="form.permissions" />
                  {{ p.name }}
                </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-2 mt-4">
            <Button variant="secondary" @click="closeForm">Batal</Button>
            <Button @click="submitForm" :disabled="form.processing">
              <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
              Simpan
            </Button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
