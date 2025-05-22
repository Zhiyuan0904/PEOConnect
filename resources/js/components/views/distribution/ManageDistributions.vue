<template>
  <div class="flex h-screen">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <main class="flex-1 p-10 bg-gray-100 overflow-y-auto">
      <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-[#1e7c99] mb-8 text-center">Manage Survey Distributions</h1>

        <div class="flex justify-end mb-6">
          <button @click="showCreateModal = true" class="bg-[#1e7c99] hover:bg-[#156a84] text-white font-semibold px-6 py-3 rounded-lg transition">
            + Create New Distribution
          </button>
        </div>

        <!-- Loading state -->
        <div v-if="loading" class="text-center text-gray-500 text-lg">Loading distributions...</div>

        <!-- No distributions -->
        <div v-else-if="!distributions.length" class="text-center text-gray-400">
          No survey distributions yet.
        </div>

        <!-- Distributions list -->
        <div v-else class="space-y-6">
          <div 
            v-for="dist in distributions" 
            :key="dist.id" 
            class="bg-white p-6 rounded-lg shadow-md space-y-3"
          >
            <div class="flex justify-between items-center">
              <div>
                <h2 class="text-xl font-bold text-[#1e3a5f]">{{ dist.survey.title }}</h2>
                <p class="text-gray-600 text-sm mt-1">
                  🎯 Target: <strong>{{ capitalize(dist.target_role) }}</strong> |
                  📆 Active Date: <strong>{{ formatDate(dist.scheduled_active_date) }}</strong>
                </p>
              </div>
              <div class="text-sm text-gray-500">
                Created: {{ formatDate(dist.created_at) }}
              </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm text-gray-700">
              <div>
                <span class="font-medium">From:</span> {{ formatDate(dist.start_date) }}
              </div>
              <div>
                <span class="font-medium">To:</span> {{ formatDate(dist.end_date) }}
              </div>
              <div>
                <span class="font-medium">Date Field:</span> {{ dist.date_field === 'enrol_date' ? 'Enrolment Date' : 'Graduation Date' }}
              </div>
              <div>
                <span class="font-medium">Status:</span>
                <span v-if="dist.is_active" class="inline-block px-2 py-1 text-green-700 bg-green-100 rounded-full text-xs font-semibold ml-1">
                  Active
                </span>
                <span v-else class="inline-block px-2 py-1 text-red-700 bg-red-100 rounded-full text-xs font-semibold ml-1">
                  Inactive
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal for Creating Distribution -->
      <CreateDistributionModal :visible="showCreateModal" @close="showCreateModal = false" @created="handleCreated" />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/axios';
import Sidebar from '@/components/common/Sidebar.vue';
import CreateDistributionModal from '@/components/views/distribution/CreateDistributionModal.vue'

const distributions = ref([]);
const loading = ref(true);
const showCreateModal = ref(false);

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
  await fetchDistributions(); // Refresh after create
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const capitalize = (text) => {
  if (!text) return '';
  return text.charAt(0).toUpperCase() + text.slice(1);
};

onMounted(() => {
  fetchDistributions();
});
</script>
