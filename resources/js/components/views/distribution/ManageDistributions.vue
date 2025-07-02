<template>
  <div class="relative">
    <!-- Sidebar + Main Layout -->
    <div class="flex ml-[20%] min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd] relative z-0">
      <Sidebar />

      <main class="flex-1 flex justify-center p-10">
        <div class="w-full max-w-7xl bg-white rounded-3xl shadow-2xl p-10 relative z-10">
          <h1 class="text-4xl font-bold text-[#4072bc] mb-10 text-center">Manage Survey Distributions</h1>

          <!-- Top controls -->
          <div class="flex flex-col md:flex-row justify-between gap-4 mb-8">
            <!-- Search -->
            <input type="text" v-model="search" placeholder="Search by survey title..."
              class="w-full md:w-1/3 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4072bc]">

            <!-- Filter -->
            <div class="flex flex-wrap justify-center gap-2 md:gap-3">
              <button v-for="s in statusOptions" :key="s"
                @click="statusFilter = s"
                :class="['px-4 py-2 rounded-full font-semibold transition',
                  statusFilter === s ? 'bg-[#4072bc] text-white' : 'bg-gray-200 text-gray-700']">
                {{ s }}
              </button>
            </div>

            <!-- Create Button -->
            <div class="flex justify-end">
              <button @click="showCreateModal = true"
                class="bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold px-7 py-3 rounded-full transition duration-300 shadow-md text-center">
                + Create New Distribution
              </button>
            </div>
          </div>

          <!-- Content -->
          <div v-if="loading" class="text-center text-gray-600 text-lg animate-pulse">
            Loading distributions...
          </div>

          <div v-else-if="!filteredDistributions.length" class="text-center text-gray-400 text-lg">
            No survey distributions found.
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div v-for="dist in filteredDistributions" :key="dist.id"
              class="bg-[#f9fbfd] p-8 rounded-2xl border border-[#e3e3e3] shadow relative">
              <h2 class="text-xl font-bold text-[#4072bc] mb-2">{{ dist.survey.title }}</h2>
              <p class="text-sm text-gray-500 mb-3">
                🎯 Target: <strong>{{ capitalize(dist.target_role) }}</strong><br />
                📆 Active: {{ formatDate(dist.scheduled_active_date) }}
              </p>

              <!-- Status Badge -->
              <div class="absolute top-4 right-4">
                <template v-if="isExpired(dist.scheduled_end_date)">
                  <span class="px-3 py-1 text-yellow-700 bg-yellow-100 rounded-full text-xs font-semibold">Expired</span>
                </template>
                <template v-else-if="dist.is_active">
                  <span class="px-3 py-1 text-green-700 bg-green-100 rounded-full text-xs font-semibold">Active</span>
                </template>
                <template v-else>
                  <span class="px-3 py-1 text-red-700 bg-red-100 rounded-full text-xs font-semibold">Inactive</span>
                </template>
              </div>

              <!-- Expand -->
              <button @click="toggleExpand(dist.id)" class="text-[#4072bc] font-semibold text-sm mt-2">
                {{ expandedId === dist.id ? 'Hide Details' : 'View Details' }}
              </button>

              <transition name="fade-slide">
                <div v-if="expandedId === dist.id" class="mt-4 text-sm text-gray-600 space-y-1">
                  <div><b>From:</b> {{ formatDate(dist.start_date) }}</div>
                  <div><b>To:</b> {{ formatDate(dist.end_date) }}</div>
                  <div><b>Date Field:</b> {{ dist.date_field === 'enroll_date' ? 'Enrolment Date' : 'Graduation Date' }}</div>
                  <div><b>Schedule Ends:</b> {{ formatDate(dist.scheduled_end_date) }}</div>
                  <div><b>Created:</b> {{ formatDate(dist.created_at) }}</div>
                </div>
              </transition>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- ✅ Modal -->
    <CreateDistributionModal :visible="showCreateModal" @close="showCreateModal = false" @created="handleCreated"/>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from '@/axios';
import Sidebar from '@/components/common/Sidebar.vue';
import CreateDistributionModal from '@/components/views/distribution/CreateDistributionModal.vue';

const distributions = ref([]);
const loading = ref(true);
const showCreateModal = ref(false);
const expandedId = ref(null);
const search = ref("");
const statusFilter = ref("All");
const statusOptions = ["All", "Active", "Inactive", "Expired"];

const fetchDistributions = async () => {
  try {
    const { data } = await axios.get('/survey-distributions');
    distributions.value = data;
  } catch (error) {
    console.error('Failed to fetch distributions:', error);
    alert('Failed to load distributions');
  } finally {
    loading.value = false;
  }
};

const handleCreated = async () => {
  showCreateModal.value = false;
  await fetchDistributions();
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const capitalize = (text) => {
  if (!text) return '';
  return text.charAt(0).toUpperCase() + text.slice(1);
};

const isExpired = (scheduledEndDate) => {
  if (!scheduledEndDate) return false;
  const now = new Date();
  const end = new Date(scheduledEndDate);
  return now > end;
};

const toggleExpand = (id) => {
  expandedId.value = expandedId.value === id ? null : id;
};

const filteredDistributions = computed(() => {
  return distributions.value.filter(dist => {
    const matchesSearch = dist.survey.title.toLowerCase().includes(search.value.toLowerCase());

    let matchesStatus = true;
    if (statusFilter.value === "Active") matchesStatus = dist.is_active && !isExpired(dist.scheduled_end_date);
    if (statusFilter.value === "Inactive") matchesStatus = !dist.is_active && !isExpired(dist.scheduled_end_date);
    if (statusFilter.value === "Expired") matchesStatus = isExpired(dist.scheduled_end_date);

    return matchesSearch && matchesStatus;
  });
});

onMounted(() => {
  fetchDistributions();
});
</script>

<style scoped>
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.3s ease;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
