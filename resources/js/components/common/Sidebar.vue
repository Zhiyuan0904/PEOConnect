<template>
  <aside class="w-1/5 bg-[#f4f4f4] text-[#1B3A57] p-5 flex flex-col justify-between h-screen font-sans shadow-md">
    <div>
      <!-- Logo -->
      <div class="flex items-center mb-10">
        <h2 class="text-2xl font-extrabold">
          <span class="text-[#4072bc]">PEOConnect</span>
        </h2>
      </div>

      <!-- Navigation -->
      <nav class="space-y-2 text-[16px]">
        <!-- Dashboard -->
        <SidebarLink to="/dashboard" label="Dashboard" :isActive="isActive('/dashboard')" />

        <!-- Manage Profile Dropdown -->
        <div class="relative font-semibold">
          <div
            @click="toggleDropdown"
            class="flex justify-between items-center cursor-pointer px-4 py-3 rounded-lg transition"
            :class="isManageProfileOpen ? activeClass : normalClass"
          >
            <span>Manage Profile</span>
            <i :class="[isManageProfileOpen ? 'fas fa-chevron-up' : 'fas fa-chevron-down']" class="text-sm transition duration-300"></i>
          </div>

          <transition name="fade">
            <div v-show="isManageProfileOpen" class="ml-4 mt-2 space-y-1">
              <SidebarLink to="/update/profile" label="Update Profile" :isActive="isActive('/update/profile')" small />
              <SidebarLink to="/view/profile" label="View Profile" :isActive="isActive('/view/profile')" small />
            </div>
          </transition>
        </div>

        <!-- Student -->
        <SidebarLink
          v-if="authStore.user?.role === 'student'"
          to="/respond/surveys"
          label="Respond to Survey"
          :isActive="isActive('/respond/surveys')"
        />

        <!-- Admin -->
        <template v-if="authStore.user?.role === 'admin'">
          <SidebarLink to="/manage/surveys" label="Manage Surveys" :isActive="isActive('/manage/surveys')" />
          <SidebarLink to="/manage/responses" label="Manage Responses" :isActive="isActive('/manage/responses')" />
          <SidebarLink to="/manage/distributions" label="Manage Distributions" :isActive="isActive('/manage/distributions')" />
          <SidebarLink to="/manage-curriculum-content" label="Manage Curriculum Content" :isActive="isActive('/manage-curriculum-content')" />
          <SidebarLink to="/manage/PEOs" label="Manage PEO" :isActive="isActive('/manage/PEOs')" />
          <SidebarLink to="/track/progress" label="Track Progress" :isActive="isActive('/track/progress')" />
          <SidebarLink to="/reports" label="Reports" :isActive="isActive('/reports')" />
        </template>
      </nav>
    </div>

    <!-- Logout -->
    <button @click="handleLogout" class="flex items-center gap-2 text-[#4072bc] hover:text-[#f07ba3] transition font-medium">
      <i class="fas fa-sign-out-alt"></i> Logout
    </button>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import SidebarLink from './SidebarLink.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const isManageProfileManuallyOpen = ref(null);

const isActive = (path) => route.path === path;

const isManageProfileOpen = computed(() =>
  isManageProfileManuallyOpen.value !== null
    ? isManageProfileManuallyOpen.value
    : ['/update/profile', '/view/profile'].includes(route.path)
);

const toggleDropdown = () => {
  isManageProfileManuallyOpen.value = !(isManageProfileManuallyOpen.value ?? ['/update/profile', '/view/profile'].includes(route.path));
};

const handleLogout = async () => {
  await authStore.logout();
  router.push('/home');
};

const activeClass = 'bg-gradient-to-r from-[#f07ba3] to-[#59a8f7] text-white shadow-md'
const normalClass = 'hover:bg-gradient-to-r hover:from-[#f07ba3] hover:to-[#59a8f7] hover:text-white'
</script>
