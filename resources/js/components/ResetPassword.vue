<template>
  <div class="h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-4xl bg-white rounded-lg shadow-lg overflow-hidden flex">
      
      <!-- Left Side Illustration -->
      <div class="hidden md:flex w-1/2 bg-white items-center justify-center">
        <img src="../assets/resetpassword.png" alt="Reset Password Illustration" class="max-w-full">
      </div>

      <!-- Right Side Form -->
      <div class="w-full md:w-1/2 bg-gradient-to-b from-blue-500 to-teal-500 p-8 flex flex-col justify-center">
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-2xl font-bold mb-4 text-center">Enter New Password</h2>
          <p class="text-center text-gray-600 mb-4">Please enter a new password different from the previous one.</p>

          <div v-if="invalidToken" class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            Invalid or expired reset link. Please request a new password reset.
          </div>

          <form @submit.prevent="handleReset" v-else>
            <div class="mb-4">
              <label class="block text-gray-700 mb-2">Email</label>
              <input v-model="form.email" type="email" class="w-full p-2 border rounded" :readonly="true" required>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 mb-2">New Password</label>
              <input v-model="form.password" type="password" class="w-full p-2 border rounded" required minlength="8">
              <span class="text-red-500 text-sm">{{ errors.password?.[0] }}</span>
            </div>

            <div class="mb-6">
              <label class="block text-gray-700 mb-2">Confirm Password</label>
              <input v-model="form.password_confirmation" type="password" class="w-full p-2 border rounded" required>
              <span v-if="form.password !== form.password_confirmation" class="text-red-500 text-sm">
                Passwords do not match
              </span>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600 disabled:bg-blue-300"
              :disabled="isSubmitting || form.password !== form.password_confirmation">
              <span v-if="isSubmitting">Processing...</span>
              <span v-else>Submit</span>
            </button>
          </form>

          <div class="mt-4 text-center">
            <router-link to="/login" class="text-blue-500 hover:underline">Back to Login</router-link>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
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
    const response = await axios.post('/api/reset-password', {
      email: form.value.email,
      token: form.value.token,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation
    });

    alert(response.data.message);
    router.push('/login');
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else if (error.response?.status === 400) {
      invalidToken.value = true;
      alert(error.response.data.message);
    } else {
      alert('An unexpected error occurred. Please try again.');
    }
  } finally {
    isSubmitting.value = false;
  }
};
</script>


