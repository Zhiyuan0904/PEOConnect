<template>
  <div class="h-screen w-full flex items-center justify-center font-sans bg-gradient-to-br from-[#fce4ec] via-[#f3e5f5] to-[#e3f2fd]">
    <div class="w-full max-w-md bg-white bg-opacity-60 backdrop-blur-lg rounded-2xl shadow-xl p-8">
      
      <!-- Logo -->
      <div class="text-center mb-6">
        <h1 class="text-4xl font-extrabold text-[#4072bc]">Login</h1>
        <p class="text-lg text-gray-600 mt-1">Connecting Education with Excellence</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="loginUser" class="space-y-5">
        
        <!-- Email -->
        <div class="relative">
          <input
            v-model="email"
            type="email"
            placeholder="Email"
            class="w-full px-11 py-3 rounded-lg bg-white bg-opacity-80 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#a0c4ff]"
            required
          />
          <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#4072bc]">
            <i class="fas fa-envelope"></i>
          </span>
        </div>

        <!-- Password -->
        <div class="relative">
          <input
            :type="showPassword ? 'text' : 'password'"
            v-model="password"
            placeholder="Password"
            class="w-full px-11 py-3 rounded-lg bg-white bg-opacity-80 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#a0c4ff]"
            required
          />
          <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#4072bc]">
            <i class="fas fa-lock"></i>
          </span>
          <span @click="showPassword = !showPassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-[#4072bc] cursor-pointer">
            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
          </span>
        </div>

        <!-- Forgot Password -->
        <div class="text-right text-sm text-[#4072bc]">
          <a href="/forgot-password" class="hover:text-[#f07ba3] transition">Forgot password?</a>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="w-full py-3 rounded-lg bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold transition duration-300 shadow-md"
          :disabled="isLoading"
        >
          <span v-if="!isLoading">Get Started</span>
          <span v-else>Loading<span class="animate-pulse">...</span></span>
        </button>

        <!-- Success / Error Message -->
        <p v-if="successMessage" class="text-green-600 text-center text-sm">{{ successMessage }}</p>
        <p v-if="errorMessage" class="text-red-500 text-center text-sm">{{ errorMessage }}</p>
      </form>

      <!-- Signup Link -->
      <p class="mt-6 text-center text-sm text-gray-600">
        Don’t have an account?
        <a href="/sign-up" class="text-[#4072bc] font-semibold hover:text-[#f07ba3]">Sign up</a>
      </p>
    </div>
  </div>
</template>


<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const router = useRouter();
const authStore = useAuthStore();

const isLoading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const loginUser = async () => {
  try {
    isLoading.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    await authStore.login(email.value, password.value);

    const user = authStore.user;
    if (!user || !user.role) {
      throw new Error('No role detected.');
    }

    successMessage.value = 'Login successful!';
    console.log('Logged in user:', user);

    const mustUpdateProfile =
      (!user.enroll_date || !user.expected_graduate_date || !user.actual_graduate_date) &&
      (user.role === 'student' || user.role === 'alumni');

    if (mustUpdateProfile) {
      router.push('/update/profile');
      return;
    }

    router.push('/dashboard');
  } catch (error) {
    console.error('Login failed:', error.response?.data?.message || error.message);
    errorMessage.value = 'Login failed. Please check your credentials.';
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
