<template>
  <div class="h-screen flex">
    <!-- Left Section: Image -->
    <div class="w-1/2 bg-white flex justify-center items-center">
      <img src="@/assets/forgotpassword.png" alt="Forgot Password Illustration" class="w-3/4">
    </div>

    <!-- Right Section: Form -->
    <div class="w-1/2 bg-gradient-to-b from-blue-500 to-blue-900 flex justify-center items-center">
      <div class="bg-white p-10 rounded-2xl shadow-lg w-3/4">
        <h2 class="text-2xl font-bold text-center mb-2">Forgot Password ?</h2>
        <p class="text-sm text-gray-600 text-center mb-6">
          No worries, we can help you reset it.<br>
          Please enter your email address below to receive a password reset link.
        </p>

        <form @submit.prevent="sendResetLink">
          <div class="mb-4">
            <input v-model="email" type="email" placeholder="Email" class="w-full p-2 border-b mb-4 focus:outline-none" required />
          </div>

          <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-teal-400 text-white p-3 rounded-lg text-lg" :disabled="loading">
            <span v-if="!loading">Send Reset Link</span>
            <span v-else>Sending...</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { ref } from 'vue';

export default {
  setup() {
    const email = ref('');
    const loading = ref(false);

    const sendResetLink = async () => {
      try {
        loading.value = true;

        const response = await axios.post('http://127.0.0.1:8000/api/forgot-password', {
          email: email.value
        });

        console.log('Reset link sent:', response.data.message);
        alert('A password reset link has been sent to your email.');
      } catch (error) {
        console.error('Error sending reset link:', error.response?.data?.message || 'Unknown error');
        alert('Failed to send reset link. Please check your email.');
      } finally {
        loading.value = false;
      }
    };

    return { email, loading, sendResetLink };
  }
};
</script>

<style scoped>
input:focus {
  outline: none;
  border-color: #1B3A57;
}
</style>
