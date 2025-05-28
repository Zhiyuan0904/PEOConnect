<template>
  <div class="dashboard-container flex min-h-screen bg-[#f4f7fa]">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <main class="flex-1 p-8">
      <!-- Header -->
      <header class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-[#1e3a5f]">📊 Dashboard Analytics</h1>
        <div class="text-sm text-gray-600">
          Logged in as <strong>{{ authStore.user?.name || 'Guest' }}</strong> ({{ authStore.user?.role || 'Unknown' }})
        </div>
      </header>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-2xl shadow-md text-center">
          <h3 class="text-gray-500 mb-1">Active Surveys</h3>
          <p class="text-3xl font-semibold text-[#1e3a5f]">{{ activeSurveys }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-md text-center">
          <h3 class="text-gray-500 mb-1">Respondence</h3>
          <p class="text-3xl font-semibold text-[#1e3a5f]">{{ totalRespondents }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-md text-center">
          <h3 class="text-gray-500 mb-1">Avg. Session</h3>
          <p class="text-3xl font-semibold text-[#1e3a5f]">2m 34s</p>
        </div>
      </div>

      <!-- Survey Progress with Filter & Refresh -->
      <div class="bg-white p-6 rounded-2xl shadow-md mb-10">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-bold text-[#1e3a5f]">Survey Response Progress</h3>
          <div class="flex gap-3">
            <select v-model="selectedSurvey" class="border border-gray-300 px-3 py-2 rounded-md text-sm">
              <option value="">All Surveys</option> 
              <option v-for="s in surveyProgress" :key="s.survey_title" :value="s.survey_title">
                {{ s.survey_title }}
              </option>
            </select>
            <button @click="fetchSurveyProgress" class="flex items-center gap-1 px-4 py-2 bg-gradient-to-r from-[#1e7c99] to-[#62c5e2] text-white rounded-md hover:from-[#156a84] hover:to-[#1e7c99] shadow-md transition">
              <span class="text-lg">🗘</span>
              <span class="text-sm font-medium">Refresh</span>
            </button>
          </div>
        </div>

        <div v-if="loadingProgress" class="text-center text-gray-500 py-10">Loading progress data...</div>

        <div v-else class="space-y-6">
          <div
            v-for="item in filteredSurveyProgress"
            :key="item.survey_title"
            class="mb-4"
          >
            <p class="mb-1 text-sm text-gray-700">{{ item.survey_title }} <span class="ml-2 text-xs text-gray-500">({{ item.percentage }}% Completed)</span></p>
            <div class="w-full bg-gray-200 rounded-full h-4">
              <div
                class="bg-gradient-to-r from-[#1e7c99] to-[#62c5e2] h-4 rounded-full transition-all duration-500"
                :style="{ width: item.percentage + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Chart Visualization -->
      <div class="bg-white p-6 rounded-2xl shadow-md">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-bold text-[#1e3a5f]">📈 Survey Completion Overview</h3>
          <span class="text-sm text-gray-400">Updated in real-time</span>
        </div>
        <div class="relative w-full max-w-4xl h-[320px] mx-auto">
          <Bar :data="chartData" :options="chartOptions" />
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import Sidebar from '@/components/common/Sidebar.vue';
import axios from '@/axios';

import { Bar } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const router = useRouter();
const authStore = useAuthStore();

const surveyProgress = ref([]);
const loadingProgress = ref(true);
const selectedSurvey = ref('');

const activeSurveys = computed(() => surveyProgress.value.length);
const totalRespondents = computed(() => surveyProgress.value.reduce((acc, item) => acc + item.responded, 0));

const fetchSurveyProgress = async () => {
  loadingProgress.value = true;
  try {
    const res = await axios.get('/track/progress');
    surveyProgress.value = res.data;
  } catch (error) {
    console.error('Failed to load dashboard survey progress', error);
  } finally {
    loadingProgress.value = false;
  }
};

const filteredSurveyProgress = computed(() => {
  if (!selectedSurvey.value) return surveyProgress.value;
  return surveyProgress.value.filter(s => s.survey_title === selectedSurvey.value);
});

const chartData = computed(() => ({
  labels: surveyProgress.value.map(item => item.survey_title),
  datasets: [
    {
      label: 'Completion %',
      backgroundColor: '#1e7c99',
      data: surveyProgress.value.map(item => item.percentage),
    },
  ],
}));

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        color: '#6b7280',
        font: { size: 14 },
      },
    },
    tooltip: {
      callbacks: {
        label: context => `${context.parsed.y}% completed`
      }
    }
  },
  scales: {
    x: {
      ticks: {
        color: '#6b7280',
        font: { size: 12 },
      },
      grid: { display: false }
    },
    y: {
      beginAtZero: true,
      max: 100,
      ticks: {
        stepSize: 20,
        color: '#6b7280',
        callback: val => `${val}%`,
        font: { size: 12 }
      },
      grid: {
        color: '#e5e7eb',
        borderDash: [4, 4],
      }
    }
  },
};

onMounted(() => {
  if (!authStore.isAuthenticated) {
    router.push('/login');
  } else {
    fetchSurveyProgress();
  }
});
</script>

<style scoped>
.dashboard-container {
  font-family: 'Inter', sans-serif;
}
</style>
