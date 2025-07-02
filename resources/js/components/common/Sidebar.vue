<template>
  <aside v-if="authStore.user" class="fixed top-0 left-0 w-1/5 h-screen bg-[#f4f4f4] text-[#1B3A57] p-5 flex flex-col justify-between font-sans shadow-md z-50">
    <div>
      <!-- Logo -->
      <div class="flex items-center mb-10">
        <h2 class="text-2xl font-extrabold"><span class="text-[#4072bc]">PEOConnect</span></h2>
      </div>

      <!-- Navigation -->
      <nav class="space-y-2 text-[16px]">
        <SidebarLink to="/dashboard" label="Dashboard" :isActive="isActive('/dashboard')" />

        <!-- Manage Profile Dropdown -->
        <div class="relative font-semibold">
          <div @click="toggleDropdown" class="flex justify-between items-center cursor-pointer px-4 py-3 rounded-lg transition" :class="isManageProfileOpen ? activeClass : normalClass">
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

        <SidebarLink v-if="authStore.user?.role === 'admin'" to="/manage-roles" label="Manage Roles" :isActive="isActive('/manage-roles')" />
        <SidebarLink v-if="['admin','quality team','dean','lecturer'].includes(authStore.user?.role)" to="/manage/surveys" label="Manage Surveys" :isActive="isActive('/manage/surveys')" />
        <SidebarLink v-if="authStore.user?.role === 'admin'" to="/manage/responses" label="Manage Responses" :isActive="isActive('/manage/responses')" />
        <SidebarLink v-if="['student','alumni'].includes(authStore.user?.role)" to="/respond/surveys" label="Respond to Survey" :isActive="isActive('/respond/surveys')" />
        <SidebarLink v-if="['admin','quality team'].includes(authStore.user?.role)" to="/manage/distributions" label="Manage Distributions" :isActive="isActive('/manage/distributions')" />
                <SidebarLink v-if="['admin','lecturer', 'quality team'].includes(authStore.user?.role)" to="/manage/PEOs" label="Manage PEO" :isActive="isActive('/manage/PEOs')" />
        <SidebarLink v-if="['admin','lecturer','quality team' ].includes(authStore.user?.role)" to="/manage-curriculum-content" label="Manage Curriculum Content" :isActive="isActive('/manage-curriculum-content')" />
        <SidebarLink v-if="['admin','quality team','dean'].includes(authStore.user?.role)" to="/track/progress" label="Track Progress" :isActive="isActive('/track/progress')" />
        <SidebarLink v-if="['admin','quality team','dean'].includes(authStore.user?.role)" to="/reports" label="Reports" :isActive="isActive('/reports')" />
      </nav>
    </div>

    <!-- Logout Button -->
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

// ✅ Wrapped to avoid top-level await
const handleLogout = () => {
  (async () => {
    await authStore.logout();
    router.push('/home');
  })();
};

const activeClass = 'bg-gradient-to-r from-[#f07ba3] to-[#59a8f7] text-white shadow-md';
const normalClass = 'hover:bg-gradient-to-r hover:from-[#f07ba3] hover:to-[#59a8f7] hover:text-white';
</script>
