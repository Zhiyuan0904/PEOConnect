<template>
  <div class="h-screen w-full flex items-center justify-center font-sans bg-gradient-to-br from-[#fce4ec] via-[#f3e5f5] to-[#e3f2fd]">
    <div class="w-full max-w-xl bg-white rounded-xl shadow-xl p-10 text-center">
      <h1 class="text-3xl font-extrabold text-[#4072bc] mb-4">🔐 Email Verification</h1>

      <div v-if="loading" class="text-gray-600 text-lg">
        Verifying your email, please wait...
      </div>

      <div v-else-if="success" class="text-green-600 text-lg font-medium">
        ✅ Your email has been successfully verified! <br />
        Redirecting to login...
      </div>

      <div v-else class="text-red-500 text-lg font-medium">
        ❌ Verification failed. The link may be invalid or expired.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from '@/axios';

const loading = ref(true);
const success = ref(false);

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

onMounted(async () => {
  const id = route.params.id;
  const hash = route.params.hash;

  try {
    // 🚀 Publicly call verification endpoint
    await axios.get(`/email/verify/${id}/${hash}`);
    success.value = true;

    // 🔒 Ensure user is logged out to access login page properly
    await authStore.logout();

    // ✅ Redirect after a short delay
    setTimeout(() => {
      router.push({ name: 'login' });
    }, 2000);
  } catch (error) {
    console.error('Verification failed:', error);
    success.value = false;
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
/* Optional: Add animation or pulse effect if desired */
</style>
