<template>
  <div class="flex min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />
    <main class="flex-1 flex items-center justify-center p-6">
      <div class="w-full max-w-2xl bg-white rounded-2xl shadow-lg p-10">

        <!-- Header -->
        <div class="flex flex-col items-center mb-8">
          <h1 class="text-3xl font-bold text-[#4072bc]">View Profile</h1>
        </div>

        <!-- User Info -->
        <div class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-[#6c6f73] mb-1">Name</label>
            <input type="text" :value="user.name" class="w-full bg-gray-100 border border-[#e3e3e3] rounded-lg px-3 py-2" readonly />
          </div>

          <div>
            <label class="block text-sm font-medium text-[#6c6f73] mb-1">Email</label>
            <input type="email" :value="user.email" class="w-full bg-gray-100 border border-[#e3e3e3] rounded-lg px-3 py-2" readonly />
          </div>

          <div>
            <label class="block text-sm font-medium text-[#6c6f73] mb-1">Role</label>
            <input type="text" :value="user.role" class="w-full bg-gray-100 border border-[#e3e3e3] rounded-lg px-3 py-2" readonly />
          </div>
        </div>

        <!-- Back Button -->
        <div class="pt-6">
          <router-link 
            to="/dashboard" 
            class="block w-full bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold px-7 py-3 rounded-full transition duration-300 shadow-md text-center">
            Back
          </router-link>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';
import Sidebar from '@/components/common/Sidebar.vue';
import { useRouter } from 'vue-router';
import { computed, onMounted } from 'vue';

const authStore = useAuthStore();
const router = useRouter();

const user = computed(() => authStore.user);

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
  }
});
</script>
