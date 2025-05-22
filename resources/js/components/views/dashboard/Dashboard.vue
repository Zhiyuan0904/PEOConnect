<template>
  <div class="dashboard-container flex h-screen">
    <!-- Sidebar -->
    <Sidebar/>
    
    <!-- Main Dashboard -->
    <main class="w-4/5 p-10 bg-gray-100">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <div class="text-gray-700">
          Username: {{ authStore.user?.name || 'Guest' }} | Role: {{ authStore.user?.role || 'Unknown' }}
        </div>
      </header>
      
      <!-- Stats Cards -->
      <div class="grid grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
          <h3 class="text-gray-600">Active Surveys</h3>
          <p class="text-2xl font-bold">5</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
          <h3 class="text-gray-600">Respondence</h3>
          <p class="text-2xl font-bold">20</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md text-center">
          <h3 class="text-gray-600">Av. Session Length</h3>
          <p class="text-2xl font-bold">2m 34s</p>
        </div>
      </div>
      
      <!-- Survey Progress -->
      <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h3 class="text-xl font-bold mb-4">Survey Response Progress</h3>
        <div class="mb-4">
          <p>PEO (74% Completed)</p>
          <div class="w-full bg-gray-200 h-2 rounded">
            <div class="bg-black h-2 rounded" style="width: 74%"></div>
          </div>
        </div>
        <div class="mb-4">
          <p>Entry Survey (52% Completed)</p>
          <div class="w-full bg-gray-200 h-2 rounded">
            <div class="bg-black h-2 rounded" style="width: 52%"></div>
          </div>
        </div>
        <div>
          <p>Exit Survey (36% Completed)</p>
          <div class="w-full bg-gray-200 h-2 rounded">
            <div class="bg-black h-2 rounded" style="width: 36%"></div>
          </div>
        </div>
      </div>
      
      <!-- Users Graph Placeholder -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-bold mb-4">Users</h3>
        <p class="text-gray-500">(Graph will be implemented here)</p>
      </div>
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import Sidebar from '@/components/common/Sidebar.vue'

const router = useRouter();
const authStore = useAuthStore();

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
  }
});
</script>

<style scoped>
/* Keep your styling the same */
.dashboard-container {
  font-family: 'Arial', sans-serif;
}

button {
  transition: all 0.2s ease;
}

button:hover {
  transform: translateX(2px);
}

.error-message {
  color: red;
  margin-top: 10px;
}

.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}

.router-link-active, button:hover {
  transition: background-color 0.2s ease;
}

.router-link-active {
  background-color: #008080;
  font-weight: 500;
}

.absolute {
  z-index: 50;
}
</style>
