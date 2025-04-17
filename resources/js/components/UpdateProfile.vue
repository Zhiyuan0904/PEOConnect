<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <SideBar />

    <!-- Main Content -->
    <main class="flex-1 bg-gray-100 p-10">
      <h1 class="text-xl font-semibold mb-6 text-gray-800">Update Profile</h1>

      <div class="max-w-xl mx-auto bg-white p-8 rounded shadow space-y-6">
        <!-- Profile Icon -->
        <div class="w-24 h-24 mx-auto rounded-full border-2 border-slate-400 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0zM19.428 15.341A9.953 9.953 0 0021 12c0-5.523-4.477-10-10-10S1 6.477 1 12c0 2.02.598 3.892 1.63 5.456" />
          </svg>
        </div>

        <!-- Update Form -->
        <form @submit.prevent="updateProfile" class="space-y-4">
          <!-- Name -->
          <div class="relative">
            <label for="name" class="block text-sm font-medium mb-1">Name</label>
            <input id="name" name="name" v-model="form.name" class="w-full rounded border px-3 py-2" type="text" required />
            <span class="absolute top-8 right-3 text-gray-400">✏️</span>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input id="email" name="email" :value="currentUser.email" class="w-full rounded border px-3 py-2 bg-gray-100 cursor-not-allowed" type="email" readonly />
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium mb-1">New Password</label>
            <input id="password" name="password" v-model="form.password" class="w-full rounded border px-3 py-2" type="password" placeholder="••••••••" />
            <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
          </div>

          <!-- Confirm Password -->
          <div v-if="form.password">
            <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm Password</label>
            <input id="password_confirmation" name="password_confirmation" v-model="form.password_confirmation" class="w-full rounded border px-3 py-2" type="password" placeholder="••••••••" />
          </div>

          <!-- Submit -->
          <div class="pt-4">
            <button type="submit" class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-6 py-2 rounded hover:opacity-90 w-full" :disabled="isLoading">
              <span v-if="!isLoading">Update Profile</span>
              <span v-else>Updating...</span>
            </button>
          </div>

          <!-- Feedback Messages -->
          <div v-if="successMessage" class="p-3 bg-green-100 text-green-700 rounded text-sm">{{ successMessage }}</div>
          <div v-if="errorMessage" class="p-3 bg-red-100 text-red-700 rounded text-sm">{{ errorMessage }}</div>
        </form>
      </div>
    </main>
  </div>
</template>


<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import axios from '@/axios'
import SideBar from './Sidebar.vue'

const authStore = useAuthStore()
const router = useRouter()

const isLoading = ref(true) 
const isDropdownOpen = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const form = ref({
  name: '',
  password: '',
  password_confirmation: ''
})

// Enhanced computed property with debug logging
const currentUser = computed(() => {
  console.log('Current authStore user:', authStore.user)
  return authStore.user || { name: '', email: '' }
})

// Enhanced loadUserData with debugging
const loadUserData = async () => {
  try {
    console.log('Starting user data load...')
    isLoading.value = true
    
    // Debug: Check token existence
    console.log('Current token exists:', !!authStore.token)
    
    // Always fetch fresh data
    console.log('Fetching user from authStore...')
    await authStore.fetchUser()
    
    // Debug: Verify what was stored
    console.log('AuthStore after fetch:', authStore.user)
    
    // Update form only if data exists
    if (authStore.user?.name) {
      form.value.name = authStore.user.name
      console.log('Form updated with name:', form.value.name)
    } else {
      throw new Error('No user name found in store')
    }
  } catch (error) {
    console.error('Error in loadUserData:', {
      error: error.message,
      response: error.response?.data,
      status: error.response?.status
    })
    
    errorMessage.value = error.response?.data?.message || 'Failed to load profile data'
    
    if (error.response?.status === 401) {
      console.log('Unauthorized, redirecting to login')
      router.push('/login')
    }
  } finally {
    isLoading.value = false
    console.log('Finished loading attempt')
  }
}

// Enhanced updateProfile with debugging
const updateProfile = async () => {
  try {
    console.log('Starting profile update...')
    errorMessage.value = ''
    successMessage.value = ''
    isLoading.value = true
    
    // Validate password confirmation
    if (form.value.password && form.value.password !== form.value.password_confirmation) {
      throw new Error('Password confirmation does not match')
    }

    // Prepare payload
    const payload = {
      name: form.value.name
    }

    // Only include password if provided
    if (form.value.password) {
      payload.password = form.value.password
    }

    console.log('Sending update payload:', payload)
    const response = await axios.put('/profile', payload)
    console.log('Update response:', response.data)
    
    // Refresh user data
    console.log('Refreshing user data...')
    await authStore.fetchUser()
    
    successMessage.value = 'Profile updated successfully!'
    console.log('Profile update successful')
    
    // Clear password fields
    form.value.password = ''
    form.value.password_confirmation = ''
    
  } catch (error) {
    console.error('Update error:', {
      error: error.message,
      response: error.response?.data,
      status: error.response?.status
    })
    
    if (error.response?.status === 422) {
      errorMessage.value = Object.values(error.response.data.errors).flat().join('\n')
    } else if (error.response?.status === 401) {
      authStore.logout()
      router.push('/login')
    } else {
      errorMessage.value = error.response?.data?.message || error.message || 'Failed to update profile'
    }
  } finally {
    isLoading.value = false
  }
}

// Load data when component mounts
onMounted(async () => {
  console.log('Component mounted, loading user data...')
  await loadUserData()
})
</script>