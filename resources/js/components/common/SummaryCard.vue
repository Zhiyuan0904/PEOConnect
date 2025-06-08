<template>
  <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center gap-4 hover:scale-[1.02] transition-transform duration-300 cursor-pointer">
    <div
      class="w-14 h-14 flex items-center justify-center text-3xl rounded-full text-white shadow-md"
      :style="{ background: gradientColor }"
    >
      {{ icon }}
    </div>
    <div>
      <h4 class="text-sm font-medium text-[#6c6f73] mb-1">{{ title }}</h4>
      <p class="text-2xl font-bold text-[#4072bc]">{{ value }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  title: String,
  value: [String, Number],
  icon: String,
  color: String,
});

const colorMap = {
  primary: 'linear-gradient(to right, #4072bc, #59a8f7)',
  secondary: 'linear-gradient(to right, #f07ba3, #c4a8e3)',
  success: 'linear-gradient(to right, #6fd97f, #59a8f7)',
  warning: 'linear-gradient(to right, #f7e277, #f48eb4)',
  danger: 'linear-gradient(to right, #ef4444, #f07ba3)',
  neutral: 'linear-gradient(to right, #e3e3e3, #dcd0f7)'
};

const gradientColor = computed(() => colorMap[props.color] || colorMap['primary']);
</script>

<style scoped>
</style>
