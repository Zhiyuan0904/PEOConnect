<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pink-100 to-blue-100">
    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-md w-full text-center">
      <img src="@/assets/email-verified.png" alt="Verified" class="w-40 sm:w-56 md:w-64 lg:w-72 mx-auto mb-6"/>

      <h2 class="text-3xl font-bold text-[#4072bc] mb-2">Email Verified</h2>
      <p class="text-gray-600 mb-6">{{ statusMessage }}</p>

      <router-link
        to="/login"
        class="block w-full py-3 rounded-lg bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold transition duration-300 shadow-md"
        >
        Go to Login
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const statusMessage = ref('Verifying...')

onMounted(() => {
  const status = route.query.status
  if (status === 'success') {
    statusMessage.value = 'Email verified successfully! You may now log in.'
  } else if (status === 'already-verified') {
    statusMessage.value = 'Your email was already verified. You may now log in.'
  } else {
    statusMessage.value = 'Invalid or expired verification link.'
  }
})
</script>
