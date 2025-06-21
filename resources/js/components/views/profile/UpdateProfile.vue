<template>
  <div class="flex ml-[20%] min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />
    <main class="flex-1 flex items-center justify-center p-6">
      <div class="w-full max-w-2xl bg-white rounded-2xl shadow-lg p-10">

        <!-- Just header -->
        <div class="flex flex-col items-center mb-8">
          <h1 class="text-3xl font-bold text-[#4072bc]">Update Profile</h1>
        </div>

        <!-- Form Section -->
        <form @submit.prevent="updateProfile" class="space-y-5">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium text-[#6c6f73] mb-1">Name</label>
            <input v-model="form.name" type="text" class="w-full border border-[#e3e3e3] rounded-lg px-3 py-2 focus:ring-[#59a8f7]" required />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-[#6c6f73] mb-1">Email</label>
            <input :value="currentUser.email" type="email" class="w-full bg-gray-100 border border-[#e3e3e3] rounded-lg px-3 py-2 cursor-not-allowed" readonly />
          </div>

          <!-- Dates (hide for admin) -->
          <div v-if="!isAcademicStaff">
            <div>
              <label class="block text-sm font-medium text-[#6c6f73] mb-1">Enroll Date</label>
              <input v-model="form.enroll_date" type="date" class="w-full border border-[#e3e3e3] rounded-lg px-3 py-2" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-[#6c6f73] mb-1">Expected Graduate Date</label>
              <input v-model="form.expected_graduate_date" type="date" class="w-full border border-[#e3e3e3] rounded-lg px-3 py-2" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-[#6c6f73] mb-1">Actual Graduate Date (Optional)</label>
              <input v-model="form.actual_graduate_date" type="date" class="w-full border border-[#e3e3e3] rounded-lg px-3 py-2" />
            </div>
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium text-[#6c6f73] mb-1">New Password</label>
            <input v-model="form.password" type="password" placeholder="••••••••" class="w-full border border-[#e3e3e3] rounded-lg px-3 py-2" />
            <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
          </div>

          <div v-if="form.password">
            <label class="block text-sm font-medium text-[#6c6f73] mb-1">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" placeholder="••••••••" class="w-full border border-[#e3e3e3] rounded-lg px-3 py-2" />
          </div>

          <!-- Submit Button -->
          <div>
            <button type="submit"
              class="w-full bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold px-7 py-3 rounded-full transition duration-300 shadow-md"
              :disabled="isLoading">
              <span v-if="!isLoading">Update Profile</span>
              <span v-else>Updating...</span>
            </button>
          </div>

          <!-- Messages -->
          <div v-if="successMessage" class="p-3 bg-green-100 text-green-700 rounded text-sm">{{ successMessage }}</div>
          <div v-if="errorMessage" class="p-3 bg-red-100 text-red-700 rounded text-sm">{{ errorMessage }}</div>
        </form>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'vue-router';
import axios from '@/axios';
import Sidebar from '@/components/common/Sidebar.vue';

const authStore = useAuthStore();
const router = useRouter();

const isLoading = ref(true);
const successMessage = ref('');
const errorMessage = ref('');

const form = ref({
  name: '', enroll_date: '', expected_graduate_date: '', actual_graduate_date: '',
  password: '', password_confirmation: ''
});

const currentUser = computed(() => authStore.user || { name: '', email: '', role: '' });
const isAcademicStaff = computed(() =>
  ['admin', 'dean', 'lecturer', 'quality team'].includes(authStore.user?.role)
);

const formatDate = (dateString) => (!dateString ? '' : new Date(dateString).toISOString().split('T')[0]);

const loadUserData = async () => {
  try {
    isLoading.value = true;
    await authStore.fetchUser();
    const user = authStore.user;

    if (user) {
      form.value.name = user.name || '';
      if (!isAcademicStaff.value) {
        form.value.enroll_date = user.enroll_date ? formatDate(user.enroll_date) : '';
        form.value.expected_graduate_date = user.expected_graduate_date ? formatDate(user.expected_graduate_date) : '';
        form.value.actual_graduate_date = user.actual_graduate_date ? formatDate(user.actual_graduate_date) : '';
      }
    }
  } catch (error) {
    console.error('Failed to load user', error);
    router.push('/login');
  } finally {
    isLoading.value = false;
  }
};

const updateProfile = async () => {
  try {
    isLoading.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    if (form.value.password && form.value.password !== form.value.password_confirmation) {
      throw new Error('Password confirmation does not match');
    }

    const payload = new FormData();
    payload.append('name', form.value.name);
    if (!isAcademicStaff.value) {
      payload.append('enroll_date', form.value.enroll_date);
      payload.append('expected_graduate_date', form.value.expected_graduate_date);
      payload.append('actual_graduate_date', form.value.actual_graduate_date);
    }
    if (form.value.password) {
      payload.append('password', form.value.password);
    }

    await axios.post('/profile/update', payload, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    await authStore.fetchUser();

    form.value.password = '';
    form.value.password_confirmation = '';
    successMessage.value = 'Profile updated successfully!';
  } catch (error) {
    errorMessage.value = error.response?.data?.message || error.message;
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  loadUserData();
});
</script>

<style scoped>
.dashboard-container { font-family: 'Inter', sans-serif; }
</style>
