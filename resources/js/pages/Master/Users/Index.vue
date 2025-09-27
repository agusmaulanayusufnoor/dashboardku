<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { LoaderCircle, Plus, Edit, Trash } from 'lucide-vue-next';

const page = usePage<{
  users: any,
  roles: any[],
  kantors: any[],
  divisis: any[],
  search?: string,
  perPage?: number
}>();

const users = computed(() => page.props.users.data);
const roles = computed(() => page.props.roles);
const kantors = computed(() => page.props.kantors);
const divisis = computed(() => page.props.divisis);

const search = ref(page.props.search || '');
const perPage = ref(page.props.perPage || 5);

const showForm = ref(false);
const form = useForm({
  id: null,
  username: '',
  name: '',
  email: '',
  password: '',
  kantor_id: '',
  divisi_id: '',
  roles: [] as string[],
});

const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'User Management', href: '/users' }
];

const openForm = (user: any = null) => {
  if (user) {
    form.id = user.id;
    form.username = user.username;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.kantor_id = user.kantor_id;
    form.divisi_id = user.divisi_id;
    form.roles = user.roles?.map((r: any) => r.name) || [];
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
    form.put(route('users.update', form.id), { onSuccess: closeForm });
  } else {
    form.post(route('users.store'), { onSuccess: closeForm });
  }
};

const deleteUser = (id: number) => {
  if (confirm('Yakin hapus user ini?')) {
    router.delete(route('users.destroy', id));
  }
};
</script>

<template>
  <Head title="User Management" />
  <AppLayout :breadcrumbs="breadcrumbs" active-menu="users">
    <div class="w-11/12 mx-auto px-4 py-4">
      <div class="flex justify-between items-center mb-4">
        <h1 class="text-xl font-bold">User Management</h1>
        <Button @click="openForm"><Plus class="mr-2" /> Tambah User</Button>
      </div>

      <Card>
        <CardHeader class="flex justify-between items-center">
          <CardTitle>Daftar User</CardTitle>
          <div class="flex gap-2 items-center">
            <Input
              v-model="search"
              placeholder="Cari user..."
              class="w-48"
              @keyup.enter="() => router.get(route('users.index'), { search, perPage })"
            />
            <select
              v-model="perPage"
              @change="() => router.get(route('users.index'), { search, perPage })"
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
                  <th class="border px-4 py-2">Username</th>
                  <th class="border px-4 py-2">Nama</th>
                  <th class="border px-4 py-2">Email</th>
                  <th class="border px-4 py-2">Kantor</th>
                  <th class="border px-4 py-2">Divisi</th>
                  <th class="border px-4 py-2">Role</th>
                  <th class="border px-4 py-2 w-32">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td class="border px-4 py-2">{{ user.username }}</td>
                  <td class="border px-4 py-2">{{ user.name }}</td>
                  <td class="border px-4 py-2">{{ user.email }}</td>
                  <td class="border px-4 py-2">{{ user.kantor?.nama_kantor ?? '-' }}</td>
                  <td class="border px-4 py-2">{{ user.divisi?.nama_divisi ?? '-' }}</td>
                  <td class="border px-4 py-2">
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="r in user.roles"
                        :key="r.id"
                        class="inline-block bg-gray-200 px-2 py-1 rounded text-xs"
                      >
                        {{ r.name }}
                      </span>
                    </div>
                  </td>
                  <td class="border px-4 py-2 text-center">
                    <Button size="sm" class="mr-2" @click="openForm(user)">
                      <Edit />
                    </Button>
                    <Button size="sm" variant="destructive" @click="deleteUser(user.id)">
                      <Trash />
                    </Button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </CardContent>
      </Card>

      <!-- MODAL FORM -->
      <div
        v-if="showForm"
        class="fixed inset-0 bg-black/30 flex justify-center items-start pt-20 z-50 overflow-auto"
      >
        <div class="bg-white p-6 rounded shadow w-3/4 max-w-3xl">
          <h2 class="text-lg font-semibold mb-4">{{ form.id ? 'Edit' : 'Tambah' }} User</h2>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block mb-1">Username</label>
              <Input v-model="form.username" placeholder="Username" />
            </div>
            <div>
              <label class="block mb-1">Nama</label>
              <Input v-model="form.name" placeholder="Nama Lengkap" />
            </div>
            <div>
              <label class="block mb-1">Email</label>
              <Input v-model="form.email" placeholder="Email" />
            </div>
            <div>
              <label class="block mb-1">Password</label>
              <Input v-model="form.password" type="password" placeholder="Password" />
            </div>
            <div>
              <label class="block mb-1">Kantor</label>
              <select v-model="form.kantor_id" class="border rounded px-2 py-1 w-full">
                <option disabled value="">-- Pilih Kantor --</option>
                <option v-for="k in kantors" :key="k.id" :value="k.id">{{ k.nama_kantor }}</option>
              </select>
            </div>
            <div>
              <label class="block mb-1">Divisi</label>
              <select v-model="form.divisi_id" class="border rounded px-2 py-1 w-full">
                <option disabled value="">-- Pilih Divisi --</option>
                <option v-for="d in divisis" :key="d.id" :value="d.id">{{ d.nama_divisi }}</option>
              </select>
            </div>
            <div class="col-span-2">
              <label class="block mb-1">Roles</label>
              <div class="flex flex-wrap gap-2">
                <label v-for="r in roles" :key="r.id" class="inline-flex items-center gap-1">
                  <input type="checkbox" :value="r.name" v-model="form.roles" />
                  {{ r.name }}
                </label>
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
