<template>
  <div class="h-screen w-full flex items-center justify-center font-sans bg-gradient-to-br from-[#e3f2fd] via-[#f3e5f5] to-[#fce4ec]">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl flex overflow-hidden">

      <!-- Left Illustration -->
      <div class="hidden md:flex w-1/2 bg-white items-center justify-center p-6">
        <img src="@/assets/resetpassword.png" alt="Reset Password" class="max-w" />
      </div>

      <!-- Right Form -->
      <div class="w-full md:w-1/2 p-10 bg-gradient-to-b from-[#4072bc] to-[#5ca1d4] flex flex-col justify-center">
        <div class="bg-white p-8 rounded-xl shadow-lg">
          <h2 class="text-2xl font-bold text-center mb-3 text-[#1B3A57]">Enter New Password</h2>
          <p class="text-sm text-gray-600 text-center mb-6">Please choose a password different from the previous one.</p>

          <!-- Invalid token warning -->
          <div v-if="invalidToken" class="mb-4 p-3 bg-red-100 text-red-700 rounded text-sm text-center">
            Invalid or expired reset link. Please request a new password reset.
          </div>

          <!-- Password Reset Form -->
          <form @submit.prevent="handleReset" v-else class="space-y-4">
            <div>
              <label class="block text-gray-700 mb-1 text-sm">Email</label>
              <input
                v-model="form.email"
                type="email"
                readonly
                class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-500"
              />
            </div>

            <div>
              <label class="block text-gray-700 mb-1 text-sm">New Password</label>
              <input
                v-model="form.password"
                type="password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#a0c4ff]"
                required
                minlength="8"
              />
              <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password[0] }}</p>
            </div>

            <div>
              <label class="block text-gray-700 mb-1 text-sm">Confirm Password</label>
              <input
                v-model="form.password_confirmation"
                type="password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#a0c4ff]"
                required
              />
              <p v-if="form.password !== form.password_confirmation" class="text-red-500 text-xs mt-1">
                Passwords do not match.
              </p>
            </div>

            <button
              type="submit"
              class="w-full py-3 bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold rounded-lg transition duration-300 shadow-md"
              :disabled="isSubmitting || form.password !== form.password_confirmation"
            >
              <span v-if="!isSubmitting">Submit</span>
              <span v-else>Processing<span class="animate-pulse">...</span></span>
            </button>
          </form>

          <div class="mt-4 text-center">
            <router-link to="/login" class="text-sm text-[#4072bc] hover:underline">Back to Login</router-link>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '@/axios';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const errors = ref({});
const invalidToken = ref(false);
const isSubmitting = ref(false);

const form = ref({
  email: '',
  password: '',
  password_confirmation: '',
  token: ''
});

// Extract token and email from URL
onMounted(() => {
  form.value.token = route.query.token || '';
  form.value.email = route.query.email ? decodeURIComponent(route.query.email) : '';

  if (!form.value.token || !form.value.email) {
    invalidToken.value = true;
  }
});

const handleReset = async () => {
  errors.value = {};
  isSubmitting.value = true;

  try {
    await axios.post('/reset-password', {
      email: form.value.email,
      token: form.value.token,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation
    });

    localStorage.removeItem('token');
    if (authStore.logout) await authStore.logout();

    router.push({ name: 'login', query: { reset: 'success' } });

  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else if (error.response?.status === 400) {
      invalidToken.value = true;
    } else {
      alert('An unexpected error occurred. Please try again.');
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
input:focus {
  outline: none;
  border-color: #4072bc;
}
</style>
