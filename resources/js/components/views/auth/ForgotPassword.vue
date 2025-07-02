<template>
  <div class="h-screen w-full flex items-center justify-center font-sans bg-gradient-to-br from-[#e3f2fd] via-[#f3e5f5] to-[#fce4ec]">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-2xl flex overflow-hidden">

      <!-- Left Illustration -->
      <div class="hidden md:flex w-1/2 bg-white items-center justify-center p-6">
        <img src="@/assets/forgotpassword.png" alt="Forgot Password" class="max-w-[80%]" />
      </div>

      <!-- Right Form -->
      <div class="w-full md:w-1/2 p-10 bg-gradient-to-b from-[#6ca8dd] to-[#b1d4ef] flex flex-col justify-center">
        <div class="bg-white p-8 rounded-xl shadow-lg">
          <h2 class="text-2xl font-bold text-center mb-2 text-[#1B3A57]">Forgot Password?</h2>
          <p class="text-sm text-gray-600 text-center mb-6 leading-relaxed">
            No worries! Enter your email and we’ll send you a reset link.
          </p>

          <form @submit.prevent="sendResetLink" class="space-y-4">
            <input
              v-model="email"
              type="email"
              placeholder="Enter your email"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#a0c4ff]"
              required
            />

            <button
              type="submit"
              class="w-full py-3 bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold rounded-lg transition duration-300 shadow-md"
              :disabled="loading"
            >
              <span v-if="!loading">Send Reset Link</span>
              <span v-else>Sending<span class="animate-pulse">...</span></span>
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from '@/axios'; // Your configured instance

const email = ref('');
const loading = ref(false);

const sendResetLink = async () => {
  try {
    loading.value = true;

    await axios.post('/forgot-password', { email: email.value });

    alert('✅ A password reset link has been sent to your email.');
  } catch (error) {
    console.error('Error:', error.response?.data?.message || error.message);
    alert('❌ Failed to send reset link. Please try again.');
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
input:focus {
  outline: none;
  border-color: #4072bc;
}
</style>
