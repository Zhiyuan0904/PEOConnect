<template>
  <div class="h-screen flex">
    <!-- Left Section -->
    <div class="w-1/2 bg-[#1B3A57] text-white flex flex-col justify-center items-center p-8">
      <h1 class="text-3xl font-bold mb-4">Welcome to PEOConnect!</h1>
      <p class="text-center text-lg">
        Join our community and start your journey towards educational excellence. Sign up today to access a range of features designed to help you achieve your goals.
      </p>
      <img src="../assets/signup.png" alt="Illustration" class="mt-6 w-3/4">
    </div>

    <!-- Right Section -->
    <div class="w-1/2 bg-white flex flex-col justify-center items-center p-8">
      <h2 class="text-2xl font-bold mb-4">Create an account</h2>
      <form @submit.prevent="registerUser" class="w-3/4">
        <input v-model="name" type="text" placeholder="Full Name" class="w-full p-2 border-b mb-4">
        <input v-model="email" type="email" placeholder="Email" class="w-full p-2 border-b mb-4">
        <input v-model="password" type="password" placeholder="Password" class="w-full p-2 border-b mb-4">

        <p class="text-xs text-gray-600 mb-4">
          By signing up, you agree to our <a href="#" class="text-blue-500">Terms of Service</a> and <a href="#" class="text-blue-500">Privacy Policy</a>.
        </p>

        <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-teal-400 text-white p-3 rounded-lg">
          Create Account
        </button>
      </form>

      <p v-if="errorMessage" class="text-red-500 mt-2">{{ errorMessage }}</p>
      <p v-if="successMessage" class="text-green-500 mt-2">{{ successMessage }}</p>

      <p class="mt-4 text-sm">Already have an account? <a href="/login" class="text-blue-500 font-semibold">Login</a></p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      errorMessage: '',
      successMessage: ''
    };
  },
  methods: {
    async registerUser() {
      this.errorMessage = '';
      this.successMessage = '';

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/sign-up', {
          name: this.name,
          email: this.email,
          password: this.password
        });

        console.log('User registered:', response.data);
        this.successMessage = 'Registration successful!';
      } catch (error) {
        if (error.response && error.response.data) {
          console.error('Registration error:', error.response.data);
          this.errorMessage = error.response.data.message || 'Registration failed!';
        } else {
          this.errorMessage = 'Network error. Please try again.';
        }
      }
    }
  }
};
</script>

<style scoped>
input:focus {
  outline: none;
  border-color: #1B3A57;
}
</style>
