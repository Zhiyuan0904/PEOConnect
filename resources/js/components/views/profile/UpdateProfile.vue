<template>
  <div class="flex min-h-screen bg-gray-100">
    <Sidebar />
    <main class="flex-1 flex items-center justify-center p-6">
      <div class="w-full max-w-2xl bg-white rounded-lg shadow-md p-10">
        <div class="flex flex-col items-center mb-8">
          <div class="w-24 h-24 rounded-full border-2 border-gray-300 flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0zM19.428 15.341A9.953 9.953 0 0021 12c0-5.523-4.477-10-10-10S1 6.477 1 12c0 2.02.598 3.892 1.63 5.456" />
            </svg>
          </div>
          <h1 class="text-2xl font-semibold text-gray-800">Update Profile</h1>
        </div>

        <form @submit.prevent="updateProfile" class="space-y-5">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input v-model="form.name" id="name" type="text" class="w-full border border-gray-300 rounded px-3 py-2" required />
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input :value="currentUser.email" id="email" type="email" class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 cursor-not-allowed" readonly />
          </div>

          <!-- Enroll Date -->
          <div>
            <label for="enroll_date" class="block text-sm font-medium text-gray-700 mb-1">Enroll Date</label>
            <input v-model="form.enroll_date" id="enroll_date" type="date" class="w-full border border-gray-300 rounded px-3 py-2" required />
          </div>

          <!-- Expected Graduate Date -->
          <div>
            <label for="expected_graduate_date" class="block text-sm font-medium text-gray-700 mb-1">Expected Graduate Date</label>
            <input v-model="form.expected_graduate_date" id="expected_graduate_date" type="date" class="w-full border border-gray-300 rounded px-3 py-2" required />
          </div>

          <!-- Actual Graduate Date -->
          <div>
            <label for="actual_graduate_date" class="block text-sm font-medium text-gray-700 mb-1">Actual Graduate Date (Optional)</label>
            <input v-model="form.actual_graduate_date" id="actual_graduate_date" type="date" class="w-full border border-gray-300 rounded px-3 py-2" />
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input v-model="form.password" id="password" type="password" placeholder="••••••••" class="w-full border border-gray-300 rounded px-3 py-2" />
            <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
          </div>

          <div v-if="form.password">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input v-model="form.password_confirmation" id="password_confirmation" type="password" placeholder="••••••••" class="w-full border border-gray-300 rounded px-3 py-2" />
          </div>

          <!-- Submit Button -->
          <div>
            <button type="submit" class="w-full bg-gradient-to-r from-cyan-500 to-blue-500 text-white py-3 rounded hover:opacity-90" :disabled="isLoading">
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
  name: '',
  enroll_date: '',
  expected_graduate_date: '',
  actual_graduate_date: '',
  password: '',
  password_confirmation: ''
});

const currentUser = computed(() => authStore.user || { name: '', email: '', role: '' });

const isAdmin = computed(() => authStore.user?.role === 'admin'); // 🚀 New computed property

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toISOString().split('T')[0];
};

const loadUserData = async () => {
  try {
    isLoading.value = true;
    await authStore.fetchUser();

    const user = authStore.user;

    if (user) {
      form.value.name = user.name || '';
      if (!isAdmin.value) { // ✨ Only load dates if NOT admin
        form.value.enroll_date = user.enroll_date ? formatDate(user.enroll_date) : '';
        form.value.expected_graduate_date = user.expected_graduate_date ? formatDate(user.expected_graduate_date) : '';
        form.value.actual_graduate_date = user.actual_graduate_date ? formatDate(user.actual_graduate_date) : '';

        // ✨ Alert only if student/alumni missing dates
        if (!form.value.enroll_date || !form.value.expected_graduate_date) {
          alert('⚡ You must complete your Enroll Date and Expected Graduate Date before proceeding to surveys.');
        }
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

    const payload = {
      name: form.value.name,
    };

    // ✨ Only send dates if NOT admin
    if (!isAdmin.value) {
      payload.enroll_date = form.value.enroll_date;
      payload.expected_graduate_date = form.value.expected_graduate_date;
      payload.actual_graduate_date = form.value.actual_graduate_date;
    }

    if (form.value.password) {
      payload.password = form.value.password;
    }

    await axios.put('/profile', payload);
    await authStore.fetchUser();

    successMessage.value = '✅ Profile updated successfully!';
    form.value.password = '';
    form.value.password_confirmation = '';

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

