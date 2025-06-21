<template>
  <div class="h-screen w-full flex items-center justify-center font-sans bg-gradient-to-br from-[#fce4ec] via-[#f3e5f5] to-[#e3f2fd]">
    <div class="w-full max-w-md bg-white bg-opacity-60 backdrop-blur-lg rounded-2xl shadow-xl p-8">
      <div class="text-center mb-6">
        <h2 class="text-3xl font-extrabold text-[#4072bc]">Reset Password</h2>
        <p class="text-gray-600 text-sm mt-1">This is your first login. Please reset your password.</p>
      </div>

      <form @submit.prevent="submitReset" class="space-y-5">
        <!-- New Password -->
        <div class="relative">
          <input :type="showPassword ? 'text' : 'password'" v-model="newPassword" placeholder="New Password"
            class="w-full px-11 py-3 rounded-lg bg-white bg-opacity-80 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#a0c4ff]"
            required />
          <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#4072bc]">
            <i class="fas fa-lock"></i>
          </span>
          <span @click="showPassword = !showPassword"
            class="absolute right-4 top-1/2 transform -translate-y-1/2 text-[#4072bc] cursor-pointer">
            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
          </span>
        </div>

        <!-- Confirm Password -->
        <div class="relative">
          <input :type="showPassword ? 'text' : 'password'" v-model="confirmPassword" placeholder="Confirm Password"
            class="w-full px-11 py-3 rounded-lg bg-white bg-opacity-80 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#a0c4ff]"
            required />
          <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#4072bc]">
            <i class="fas fa-lock"></i>
          </span>
        </div>

        <!-- Submit Button -->
        <button type="submit"
          class="w-full py-3 rounded-lg bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold transition duration-300 shadow-md"
          :disabled="isLoading">
          <span v-if="!isLoading">Reset Password</span>
          <span v-else>Processing<span class="animate-pulse">...</span></span>
        </button>

        <!-- Messages -->
        <p v-if="successMessage" class="text-green-600 text-center text-sm">{{ successMessage }}</p>
        <p v-if="errorMessage" class="text-red-500 text-center text-sm">{{ errorMessage }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const newPassword = ref('');
const confirmPassword = ref('');
const showPassword = ref(false);
const isLoading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const router = useRouter();
const authStore = useAuthStore();

const submitReset = async () => {
  if (newPassword.value !== confirmPassword.value) {
    errorMessage.value = '❌ Passwords do not match.';
    return;
  }

  try {
    isLoading.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    // ✅ Submit new password
    await axios.post('/api/reset-password-first-time', {
      password: newPassword.value,
    }, {
      headers: {
        Authorization: `Bearer ${authStore.token}`,
      }
    });

    successMessage.value = '✅ Password updated successfully! Redirecting...';

    // ✅ Clear user session and redirect to login
    setTimeout(() => {
      authStore.logout();
      router.push('/login');
    }, 1500);

  } catch (err) {
    errorMessage.value = err.response?.data?.message || '❌ Reset failed. Try again.';
  } finally {
    isLoading.value = false;
  }
};
</script>

<style scoped>
input:focus {
  outline: none;
  border-color: #4072bc;
}
</style>
