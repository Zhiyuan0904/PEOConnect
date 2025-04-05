<template>
  <nav v-if="showNavigation">
    <router-link to="/">Home</router-link>
    <router-link to="/sign-up">Sign Up</router-link>
    <router-link to="/login">Login</router-link>
  </nav>
  <router-view></router-view>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from "vue-router";

const route = useRoute();

const showNavigation = computed(() => {
  const hiddenPaths = ['/login', '/sign-up', '/reset-password', '/forgot-password'];
  return !hiddenPaths.includes(route.path);
});
</script>