<template>
  <router-link
    :to="to"
    class="block px-4 py-2 rounded-lg transition font-medium"
    :class="[
      isActive
        ? 'bg-gradient-to-r from-[#f07ba3] to-[#59a8f7] text-white font-semibold'
        : 'hover:bg-gradient-to-r hover:from-[#f07ba3] hover:to-[#59a8f7] text-[#1B3A57]',
      small ? 'text-sm' : ''
    ]"
  >
    {{ label }}
  </router-link>
</template>

<script setup>
defineProps({
  to: String,
  label: String,
  isActive: Boolean,
  small: Boolean
});
</script>
