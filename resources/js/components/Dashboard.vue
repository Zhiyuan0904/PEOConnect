<template>
  <div class="dashboard-container flex h-screen">
    <!-- Sidebar -->
     <SideBar/>
    
    <!-- Main Dashboard -->
    <main class="w-4/5 p-10 bg-gray-100">
      <header class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <div class="text-gray-700">Username: {{ userData.name || 'Admin' }} | Role: {{ userData.role || 'Admin' }}</div>
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import SideBar from './Sidebar.vue';

const router = useRouter();
const authStore = useAuthStore();
const userData = ref({
  name: '',
  role: ''
});
const isLoading = ref(true);
const error = ref(null);
const isDropdownOpen = ref(false);

const fetchUserData = async () => {
  try {
    const response = await axios.get('/api/me', {
      headers: {
        'Authorization': `Bearer ${authStore.token}`
      }
    });
    userData.value = {
      name: response.data.user.name,
      role: response.data.user.role
    };
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to fetch user data';
    console.error('Error fetching user data:', err);
  } finally {
    isLoading.value = false;
  }
};

const handleLogout = async () => {
  try {
    await authStore.logout();
    router.push('/login');
  } catch (err) {
    console.error('Logout failed:', err);
  }
};

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
    return;
  }
  
  await fetchUserData();
});
</script>

<style scoped>
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

/* Dropdown transitions */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}

/* Smooth transitions for all hover effects */
.router-link-active, button:hover {
  transition: background-color 0.2s ease;
}

/* Active route styling */
.router-link-active {
  background-color: #008080;
  font-weight: 500;
}

/* Ensure dropdown appears above other elements */
.absolute {
  z-index: 50;
}
</style>