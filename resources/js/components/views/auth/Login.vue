<template>
  <div class="h-screen flex">
    <!-- Left Section: Login Form -->
    <div class="w-1/2 bg-white flex flex-col justify-center items-center p-10">
      <div class="w-3/4">
        <h2 class="text-3xl font-bold mb-6">Login</h2>

        <form @submit.prevent="loginUser" class="w-full">
          <div class="mb-4">
            <input v-model="email" type="email" placeholder="Email" class="w-full p-2 border-b mb-4" required />
            <input v-model="password" type="password" placeholder="Password" class="w-full p-2 border-b mb-4" required />
          </div>

          <div class="text-right mb-4">
            <a href="/forgot-password" class="text-blue-500 text-sm">Forgot Password?</a>
          </div>

          <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-teal-400 text-white p-3 rounded-lg text-lg">
            Login
          </button>
        </form>

        
        <p class="mt-6 text-center text-sm">
          Don't have an account? 
          <a href="/sign-up" class="text-blue-500 font-semibold">Sign up</a>
        </p>
      </div>
    </div>

    <!-- Right Section: Image & Branding -->
    <div class="w-1/2 bg-[#008080] text-white flex flex-col justify-center items-center p-10">
      <img src="../assets/login.png" alt="Login Illustration" class="w-3/4 mb-6">
      <h2 class="text-3xl font-bold">PEOConnect</h2>
      <p class="text-lg text-center">Connecting Education with Excellence</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { useRouter } from 'vue-router';
import { ref } from 'vue';

export default {
  setup() {
    const email = ref('');
    const password = ref('');
    const router = useRouter();

    const loginUser = async () => {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
          email: email.value,
          password: password.value
        });

        if (response && response.data) {
          console.log('Login successful:', response.data);
          localStorage.setItem('token', response.data.token);
          router.push('/dashboard');
        }
      } catch (error) {
        console.error('Login failed:', error.response?.data?.message || 'Unknown error');
      }
    };

    return { email, password, loginUser };
  }
};
</script>

<style scoped>
input:focus {
  outline: none;
  border-color: #1B3A57;
}
</style>
