<template>
  <aside class="w-1/5 bg-[#1B3A57] text-white p-5 flex flex-col">
    <div class="flex-grow">
      <h2 class="text-2xl font-bold mb-6">PEOConnect</h2>

      <nav>
        <ul class="space-y-2 text-base">
        <!-- Dashboard -->
        <li>
          <router-link
            to="/dashboard"
            class="block p-2 rounded transition-colors duration-200"
            :class="{ 'bg-[#008080] font-semibold': isActive('/dashboard'), 'hover:bg-[#008080]': true }"
          >
            Dashboard
          </router-link>
        </li>

        <!-- Manage Profile Dropdown -->
        <li class="relative">
          <div
            @click="toggleDropdown"
            class="p-2 rounded cursor-pointer flex justify-between items-center transition-colors duration-200"
            :class="{ 'bg-[#008080] font-semibold': isManageProfileManuallyOpen || isInManageProfile(), 'hover:bg-[#008080]': true }"
          >
            <span>Manage Profile</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': isManageProfileManuallyOpen || isInManageProfile() }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
          <transition name="dropdown">
            <div
              v-show="isManageProfileOpen"
              @click.stop
              class="absolute left-0 mt-1 w-full bg-[#1B3A57] rounded-md shadow-lg py-1 z-50 border border-[#008080]"
            >
              <router-link
                to="/update/profile"
                class="block px-4 py-2 transition-colors duration-200"
                :class="{ 'bg-[#008080] font-semibold': isActive('/update/profile'), 'hover:bg-[#008080]': true }"
                @click.capture="handleProfileItemClick"
              >
                Update Profile
              </router-link>
              <router-link
                to="/view/profile"
                class="block px-4 py-2 transition-colors duration-200"
                :class="{ 'bg-[#008080] font-semibold': isActive('/view/profile'), 'hover:bg-[#008080]': true }"
                @click.capture="handleProfileItemClick"
              >
                View Profile
              </router-link>
            </div>
          </transition>
        </li>

        <!-- Student-only Respond to Survey -->
        <li v-if="authStore.user?.role === 'student'">
          <router-link
            to="/respond/surveys"
            class="block p-2 rounded transition-colors duration-200"
            :class="{ 'bg-[#008080] font-semibold': isActive('/respond/survey'), 'hover:bg-[#008080]': true }"
          >
            Respond to Survey
          </router-link>
        </li>

        <!-- Admin-only Menus -->
        <template v-if="authStore.user?.role === 'admin'">
          <li>
            <router-link
              to="/manage/surveys"
              class="block p-2 rounded transition-colors duration-200"
              :class="{ 'bg-[#008080] font-semibold': isActive('/manage/surveys'), 'hover:bg-[#008080]': true }"
            >
              Manage Surveys
            </router-link>
          </li>

          <!-- Manage Responses (Only Admin can see) -->
          <li v-if="authStore.user?.role === 'admin'">
            <router-link
              to="/manage/responses"
              class="block p-2 rounded transition-colors duration-200"
              :class="{ 'bg-[#008080] font-semibold': isActive('/manage/responses'), 'hover:bg-[#008080]': true }"
            >
              Manage Responses
            </router-link>
          </li>

          <!-- Manage Distributions (Only Admin can see) -->
          <li v-if="authStore.user?.role === 'admin'">
            <router-link
              to="/manage/distributions"
              class="block p-2 rounded transition-colors duration-200"
              :class="{ 'bg-[#008080] font-semibold': isActive('/manage/distributions'), 'hover:bg-[#008080]': true }"
            >
              Manage Distributions
            </router-link>
          </li>

          <li>
            <router-link
              to="/curriculum"
              class="block p-2 rounded transition-colors duration-200"
              :class="{ 'bg-[#008080] font-semibold': isActive('/curriculum'), 'hover:bg-[#008080]': true }"
            >
              Manage Curriculum Content
            </router-link>
          </li>

          <li>
            <router-link
              to="/progress"
              class="block p-2 rounded transition-colors duration-200"
              :class="{ 'bg-[#008080] font-semibold': isActive('/progress'), 'hover:bg-[#008080]': true }"
            >
              Track Progress
            </router-link>
          </li>

          <li>
            <router-link
              to="/reports"
              class="block p-2 rounded transition-colors duration-200"
              :class="{ 'bg-[#008080] font-semibold': isActive('/reports'), 'hover:bg-[#008080]': true }"
            >
              Reports
            </router-link>
          </li>
        </template>
      </ul>

      </nav>
    </div>

    <!-- Logout -->
    <button @click="handleLogout" class="mt-auto p-2 text-left rounded flex items-center hover:bg-[#008080] transition-colors duration-200">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
      </svg>
      Logout
    </button>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const isManageProfileManuallyOpen = ref(null)
const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/home')
}

const isActive = (path) => route.path === path
const isInManageProfile = () => ['/update/profile', '/view/profile'].includes(route.path)

const isManageProfileOpen = computed(() => {
  return isManageProfileManuallyOpen.value !== null
    ? isManageProfileManuallyOpen.value
    : isInManageProfile()
})

const toggleDropdown = () => {
  isManageProfileManuallyOpen.value = !(isManageProfileManuallyOpen.value ?? isInManageProfile())
}

const handleProfileItemClick = () => {
  isManageProfileManuallyOpen.value = false
}
</script>
