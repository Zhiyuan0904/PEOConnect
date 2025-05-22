<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main content -->
    <main class="flex-1 bg-gray-100 p-10">
      <h1 class="text-xl font-semibold mb-6 text-gray-800">View Profile</h1>

      <div class="max-w-xl mx-auto bg-white p-8 rounded shadow space-y-6">
        <!-- Profile Icon -->
        <div class="w-24 h-24 mx-auto rounded-full border-2 border-slate-400 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0zM19.428 15.341A9.953 9.953 0 0021 12c0-5.523-4.477-10-10-10S1 6.477 1 12c0 2.02.598 3.892 1.63 5.456" />
          </svg>
        </div>

        <div v-if="user" class="space-y-4">
          <!-- Username -->
          <div>
            <label class="block text-sm text-gray-600 mb-1">Username</label>
            <input type="text" :value="user.name" class="w-full bg-gray-100 border px-3 py-2 rounded cursor-not-allowed" readonly />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm text-gray-600 mb-1">Email</label>
            <input type="email" :value="user.email" class="w-full bg-gray-100 border px-3 py-2 rounded cursor-not-allowed" readonly />
          </div>

          <!-- Role -->
          <div>
            <label class="block text-sm text-gray-600 mb-1">Role</label>
            <input type="text" :value="user.role" class="w-full bg-gray-100 border px-3 py-2 rounded cursor-not-allowed" readonly />
          </div>
        </div>

        <div v-else>
          <p class="text-center text-gray-500">Loading profile...</p>
        </div>

        <!-- Back Button -->
        <div class="pt-4 text-center">
          <router-link to="/dashboard" class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-6 py-2 rounded hover:opacity-90 w-full">
            Back
          </router-link>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import Sidebar from '@/components/common/Sidebar.vue'
import { useRouter } from 'vue-router'
import { computed, onMounted } from 'vue'

const authStore = useAuthStore()
const router = useRouter()

const user = computed(() => authStore.user)

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login')
  }
})
</script>
