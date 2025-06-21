<template>
  <div class="h-screen w-full flex items-center justify-center font-sans bg-gradient-to-br from-[#fce4ec] via-[#f3e5f5] to-[#e3f2fd]">
    <div class="w-full max-w-5xl bg-white rounded-xl shadow-xl flex overflow-hidden">
      <!-- Left Image Side -->
      <div class="w-1/2 relative overflow-hidden">
        <img src="@/assets/signup.png" alt="Sign Up" class="relative z-10 object-cover h-full w-full" />
      </div>

      <!-- Divider -->
      <div class="w-[1px] bg-gray-300"></div>

      <!-- Right Form Side -->
      <div class="w-1/2 p-10 flex flex-col justify-center">
        <div class="text-center mb-6">
          <h1 class="text-4xl font-extrabold text-[#4072bc]">Sign Up</h1>
          <p class="text-md text-gray-600 mt-1">Join us and build your academic future</p>
        </div>

        <form @submit.prevent="registerUser" class="space-y-4">
          <!-- Name -->
          <div class="relative">
            <input v-model="name" type="text" placeholder="Full Name" class="w-full pl-10 pr-4 py-3 rounded-lg bg-white border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#a0c4ff]" required />
            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#4072bc]"><i class="fas fa-user"></i></span>
          </div>

          <!-- Role Selection -->
          <div>
            <select v-model="role" class="w-full px-4 py-3 rounded-lg bg-white border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#a0c4ff]" required>
              <option value="">-- Choose Role --</option>
              <option value="student">Student</option>
              <option value="alumni">Alumni</option>
            </select>
          </div>
          
          <!-- Email -->
          <div class="relative">
            <input v-model="email" type="email" placeholder="Email" class="w-full px-11 py-3 rounded-lg bg-white bg-opacity-80 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#a0c4ff]" required />
            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#4072bc]"><i class="fas fa-envelope"></i></span>
          </div>

          <!-- Password -->
          <div class="relative">
            <input :type="showPassword ? 'text' : 'password'" v-model="password" placeholder="Password" class="w-full px-11 py-3 rounded-lg bg-white bg-opacity-80 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-[#a0c4ff]" required />
            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#4072bc]"><i class="fas fa-lock"></i></span>
            <span @click="showPassword = !showPassword" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-[#4072bc] cursor-pointer text-lg">
              <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
            </span>
          </div>

          <p class="text-xs text-gray-600">
            By signing up, you agree to our 
            <a href="#" class="text-[#4072bc] font-semibold hover:text-[#f07ba3]">Terms</a> and
            <a href="#" class="text-[#4072bc] font-semibold hover:text-[#f07ba3]">Privacy Policy</a>.
          </p>

          <button
            type="submit"
            class="w-full px-7 py-3 rounded-full bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold transition duration-300 shadow-md"
            :disabled="isLoading"
          >
            <span v-if="!isLoading">Register</span>
            <span v-else>Loading<span class="animate-pulse">...</span></span>
          </button>

          <p v-if="successMessage" class="text-green-600 text-sm text-center">{{ successMessage }}</p>
          <p v-if="errorMessage" class="text-red-500 text-sm text-center">{{ errorMessage }}</p>
        </form>

        <p class="mt-6 text-center text-sm text-[#6c6f73]">
          Already have an account?
          <a href="/login" class="text-[#4072bc] font-semibold hover:text-[#f07ba3]">Log in</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from '@/axios';

const name = ref('');
const email = ref('');
const password = ref('');
const role = ref('');
const isLoading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');
const showPassword = ref(false);

const registerUser = async () => {
  errorMessage.value = '';
  successMessage.value = '';

  // Role check
  if (!role.value) {
    errorMessage.value = 'Please select a role.';
    return;
  }

  // Email domain validation
  const studentDomain = '@graduate.utm.my';
  if (role.value === 'student' && !email.value.endsWith(studentDomain)) {
    errorMessage.value = `Student email must end with ${studentDomain}`;
    return;
  } else if (role.value === 'alumni' && email.value.endsWith(studentDomain)) {
    errorMessage.value = `Alumni should not use a ${studentDomain} email`;
    return;
  }

  isLoading.value = true;

  try {
    const response = await axios.post('/sign-up', {
      name: name.value,
      email: email.value,
      password: password.value,
      role: role.value,
    });

    successMessage.value = 'Registration successful! Please check your email to verify your account.';
    name.value = '';
    email.value = '';
    password.value = '';
    role.value = '';
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Registration failed!';
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
