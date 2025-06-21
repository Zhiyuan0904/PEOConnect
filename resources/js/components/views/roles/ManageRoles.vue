<template>
  <div class="flex min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd] font-sans">
    <!-- Sidebar -->
    <div class="w-1/5 fixed h-screen z-40">
      <Sidebar />
    </div>

    <!-- Main Content -->
    <div class="ml-[20%] w-[80%] relative">
      <!-- Glow Background -->
      <div class="absolute inset-0 flex justify-center items-center pointer-events-none">
        <div class="w-[550px] h-[550px] md:w-[700px] md:h-[700px] rounded-full blur-[100px] opacity-20
                    bg-gradient-to-br from-[#e6c752] via-[#d98ab3] to-[#7caee6] animate-floating-glow">
        </div>
      </div>

      <main class="p-10 z-10 relative">
        <div class="w-full max-w-6xl bg-white rounded-2xl shadow-xl p-10 mx-auto">
          <!-- Header -->
          <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-[#4072bc]">Manage User Roles</h1>
            <button @click="openModal"
              class="bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8]
                     text-white font-semibold px-7 py-3 rounded-full shadow-md transition">
              + Register New User
            </button>
          </div>

          <!-- Table -->
          <div v-if="loading" class="text-center text-gray-400 py-10">Loading users...</div>
          <div v-else>
            <table class="min-w-full text-sm border border-[#e3e3e3]">
              <thead class="bg-[#f9fbfd]">
                <tr>
                  <th class="py-3 px-4 text-left font-semibold text-[#4072bc] border-b">Name</th>
                  <th class="py-3 px-4 text-left font-semibold text-[#4072bc] border-b">Email</th>
                  <th class="py-3 px-4 text-left font-semibold text-[#4072bc] border-b">Role</th>
                  <th class="py-3 px-4 text-center font-semibold text-[#4072bc] border-b">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id" class="hover:bg-[#f9fbfd]">
                  <td class="py-3 px-4">{{ user.name }}</td>
                  <td class="py-3 px-4">{{ user.email }}</td>
                  <td class="py-3 px-4">
                    <select v-model="user.role" class="border rounded-md px-2 py-1 w-full">
                      <option v-for="role in roles" :key="role" :value="role">{{ capitalize(role) }}</option>
                    </select>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <div class="flex justify-center items-center space-x-2">
                      <button
                        @click="updateRole(user)"
                        class="bg-[#59a8f7] hover:bg-[#3a85c3] text-white font-semibold px-3 py-1 rounded text-xs"
                      >Update</button>
                      <button
                        @click="deleteRole(user.id)"
                        class="bg-red-400 hover:bg-red-500 text-white font-semibold px-3 py-1 rounded text-xs"
                      >Delete</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>

    <!-- Modal -->
    <div v-if="showCreateForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
      <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-3xl">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-[#4072bc]">Register New User</h2>
          <button @click="closeModal"
            class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
        </div>

        <form @submit.prevent="createUser" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <input v-model="newUser.name" type="text" placeholder="Full Name"
                  class="border p-2 rounded-full w-full" required />
            <input v-model="newUser.email" type="email" placeholder="Email"
                  class="border p-2 rounded-full w-full" required />
            <select v-model="newUser.role" class="border p-2 rounded-full w-full" required>
              <option disabled value="">Select Role</option>
              <option v-for="role in allowedRoles" :key="role" :value="role">{{ capitalize(role) }}</option>
            </select>
          </div>

          <div class="flex justify-end gap-3">
            <button type="submit" :disabled="isCreating"
              class="bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] text-white px-6 py-2 rounded-full shadow-md
                    hover:from-[#8475d2] hover:to-[#a7c8f8] disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="isCreating" class="tracking-widest">Creating<span class="dot-animate">...</span></span>
              <span v-else>Create</span>
            </button>
          </div>

          <p v-if="createError" class="text-red-500 mt-2 text-sm">{{ createError }}</p>
          <p v-if="createSuccess" class="text-green-600 mt-2 text-sm">{{ createSuccess }}</p>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import axios from '@/axios';
import Sidebar from '@/components/common/Sidebar.vue';

const authStore = useAuthStore();
const router = useRouter();

const users = ref([]);
const loading = ref(true);
const roles = ['admin', 'lecturer', 'quality team', 'dean', 'student', 'alumni'];
const allowedRoles = ['lecturer', 'quality team', 'dean'];

const newUser = ref({ name: '', email: '', role: '' });
const isCreating = ref(false);
const createError = ref('');
const createSuccess = ref('');
const showCreateForm = ref(false);

const openModal = () => {
  showCreateForm.value = true;
  createSuccess.value = '';
  createError.value = '';
  newUser.value = { name: '', email: '', role: '' };
};

const closeModal = () => {
  showCreateForm.value = false;
  createSuccess.value = '';
  createError.value = '';
};

const fetchUsers = async () => {
  loading.value = true;
  try {
    const res = await axios.get('/manage/users');
    users.value = res.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const updateRole = async (user) => {
  try {
    await axios.put(`/manage/users/${user.id}/role`, { role: user.role });
    alert('Role updated successfully');
  } catch (err) {
    console.error(err);
    alert('Failed to update role');
  }
};

const createUser = async () => {
  isCreating.value = true;
  createError.value = '';
  createSuccess.value = '';

  try {
    const res = await axios.post('/manage/users/create', newUser.value);
    const { email, generatedPassword } = res.data;
    const createdUserName = newUser.value.name;
    const createdUserRole = newUser.value.role;

    createSuccess.value = 'User created and email sent.';
    showCreateForm.value = false;

    users.value.push({
      id: res.data.id || Date.now(),
      name: createdUserName,
      email,
      role: createdUserRole,
      spatie_role: capitalize(createdUserRole)
    });

    axios.post('/manage/users/send-login-email', {
      email,
      name: createdUserName,
      password: generatedPassword
    }).catch(err => {
      console.warn("Email failed:", err);
    });
  } catch (err) {
    createError.value = err.response?.data?.message || 'Failed to create user.';
  } finally {
    isCreating.value = false;
  }
};

const deleteRole = async (userId) => {
  if (!confirm("Are you sure you want to delete this user?")) return;
  try {
    await axios.delete(`/manage/users/${userId}`);
    users.value = users.value.filter((u) => u.id !== userId);
    alert("User deleted successfully.");
  } catch (err) {
    console.error("Delete error:", err);
    alert(err.response?.data?.message || "Failed to delete user.");
  }
};

onMounted(() => {
  if (authStore.user?.role !== 'admin') {
    router.push('/dashboard');
  } else {
    fetchUsers();
  }
});

const capitalize = (text) => {
  return text
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};
</script>

<style scoped>
.dashboard-container {
  font-family: 'Inter', sans-serif;
}

@keyframes floatGlow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

.animate-floating-glow {
  animation: floatGlow 6s ease-in-out infinite;
}
</style>
