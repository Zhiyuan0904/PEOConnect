<template>
  <div class="dashboard-container ml-[20%] flex min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />

    <main class="flex-1 p-8">
      <header class="flex justify-between items-center mb-10">
        <h1 class="text-4xl font-bold text-[#4072bc]">Dashboard</h1>
        <div class="text-sm text-[#6c6f73]">
          <div v-if="authStore.user">
         Logged in as <strong>{{ authStore.user?.name || 'Guest' }}</strong> ({{ capitalize(authStore.user?.role || 'Unknown') }})
          </div>
        </div>
      </header>

      <!-- Admin / Quality Team / Dean Dashboard -->
      <template v-if="['admin', 'quality team', 'dean'].includes(role)">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-10">
          <SummaryCard title="Total Surveys" :value="activeSurveys" icon="📝" color="primary" />
          <SummaryCard title="Respondents" :value="totalRespondents" icon="👥" color="secondary" />
          <SummaryCard title="Non-Respondents" :value="totalNonRespondents" icon="⏳" color="danger" />
          <SummaryCard title="Response Rate" :value="responseRate + '%'" icon="📈" color="success" />
        </div>

        <!-- Survey Progress -->
        <div class="bg-white p-6 rounded-2xl shadow-lg mb-10">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-[#4072bc]">Survey Response Progress</h3>
            <div class="flex gap-3">
              <select v-model="selectedSurvey" class="border border-gray-300 px-3 py-2 rounded-full text-sm">
                <option value="">All Surveys</option>
                <option v-for="s in surveyProgress" :key="s.survey_title" :value="s.survey_title">
                  {{ s.survey_title }}
                </option>
              </select>
              <button @click="fetchSurveyProgress" class="flex items-center gap-1 px-4 py-2 bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold px-7 py-3 rounded-full transition duration-300 shadow-md text-center">
                <span class="text-lg">🗘</span>
                <span class="text-sm font-medium">Refresh</span>
              </button>
            </div>
          </div>

          <div v-if="loadingProgress" class="text-center text-gray-400 py-10">Loading progress data...</div>

          <div v-else class="space-y-6">
            <div v-for="item in filteredSurveyProgress" :key="item.survey_title" class="mb-4">
              <p class="mb-1 text-sm text-[#6c6f73]">
                {{ item.survey_title }}
                <span class="ml-2 text-xs text-gray-400">({{ item.percentage }}% Completed)</span>
              </p>
              <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-gradient-to-r from-[#59a8f7] to-[#a7c8f8] h-4 rounded-full transition-all duration-500"
                  :style="{ width: item.percentage + '%' }">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Latest Submissions -->
        <!----<div class="bg-white p-6 rounded-2xl shadow-lg">
          <h3 class="text-xl font-bold text-[#4072bc] mb-4">Latest Submissions</h3>
          <table class="w-full text-[#6c6f73] text-sm">
            <thead>
              <tr>
                <th class="text-left py-2">User</th>
                <th class="text-left py-2">Survey</th>
                <th class="text-left py-2">Submitted At</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="submission in latestSubmissions" :key="submission.id">
                <td class="py-2">{{ submission.user }}</td>
                <td class="py-2">{{ submission.survey_title }}</td>
                <td class="py-2">{{ submission.submitted_at }}</td>
              </tr>
            </tbody>
          </table>
        </div> !-->
      </template>

      <!-- Lecturer Dashboard -->
      <template v-else-if="role === 'lecturer'">
        <div class="bg-white p-10 rounded-2xl shadow-lg text-center">
          <h2 class="text-2xl font-bold text-[#4072bc] mb-4">Welcome Lecturer 👨‍🏫</h2>
          <p class="text-gray-600">You can manage curriculum content and view assigned PEOs.</p>
        </div>
      </template>

      <!-- Student Dashboard -->
      <template v-else-if="role === 'student'">
        <div class="bg-white p-10 rounded-2xl shadow-lg text-center">
          <h2 class="text-2xl font-bold text-[#4072bc] mb-4">Welcome Student 🎓</h2>
          <p class="text-gray-600">Please respond to your assigned surveys from the sidebar.</p>
        </div>
      </template>

      <!-- Alumni Dashboard -->
      <template v-else-if="role === 'alumni'">
        <div class="bg-white p-10 rounded-2xl shadow-lg text-center">
          <h2 class="text-2xl font-bold text-[#4072bc] mb-4">Welcome Alumni 🎓</h2>
          <p class="text-gray-600">Thank you for participating in the graduate tracer survey!</p>
        </div>
      </template>

      <!-- Fallback -->
      <template v-else>
        <div class="bg-white p-10 rounded-2xl shadow-lg text-center">
          <h2 class="text-2xl font-bold text-red-500 mb-4">Unknown Role</h2>
          <p class="text-gray-600">Your role is not assigned correctly. Please contact admin.</p>
        </div>
      </template>

    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import Sidebar from '@/components/common/Sidebar.vue';
import SummaryCard from '@/components/common/SummaryCard.vue';
import axios from '@/axios';

const router = useRouter();
const authStore = useAuthStore();
const role = authStore.user?.role;

const surveyProgress = ref([]);
const latestSubmissions = ref([]);
const loadingProgress = ref(true);
const selectedSurvey = ref('');

// Load both API data together
const fetchSurveyProgress = async () => {
  loadingProgress.value = true;
  try {
    const res = await axios.get('/track/progress');
    surveyProgress.value = res.data;
    const res2 = await axios.get('/track/latest-submissions');
    latestSubmissions.value = res2.data;
  } catch (error) {
    console.error('Failed to load dashboard data', error);
  } finally {
    loadingProgress.value = false;
  }
};

// Main computed stats
const activeSurveys = computed(() => surveyProgress.value.length);
const totalRespondents = computed(() => surveyProgress.value.reduce((acc, item) => acc + item.responded, 0));
const totalExpected = computed(() => surveyProgress.value.reduce((acc, item) => acc + item.total, 0));
const totalNonRespondents = computed(() => totalExpected.value - totalRespondents.value);
const responseRate = computed(() => totalExpected.value > 0 ? ((totalRespondents.value / totalExpected.value) * 100).toFixed(1) : 0);

const filteredSurveyProgress = computed(() => {
  if (!selectedSurvey.value) return surveyProgress.value;
  return surveyProgress.value.filter(s => s.survey_title === selectedSurvey.value);
});

onMounted(() => {
  if (!authStore.isAuthenticated) router.push('/login');
  else {
    if (['admin', 'quality team', 'dean'].includes(role)) {
      fetchSurveyProgress();
    }
  }
});

const capitalize = (text) => {
  return text
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};
</script>

<style scoped>
.dashboard-container { font-family: 'Inter', sans-serif; }
</style>
