<template>
  <div class="flex min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd] relative">
    <Sidebar />

    <!-- Glow Background -->
    <div class="absolute inset-0 flex justify-center items-center pointer-events-none">
      <div class="w-[550px] h-[550px] md:w-[700px] md:h-[700px] rounded-full blur-3xl opacity-50
                  bg-gradient-to-br from-[#e6c752] via-[#d98ab3] to-[#7caee6]">
      </div>
    </div>

    <main class="flex-1 flex justify-center p-10 z-10">
      <div class="w-full max-w-6xl bg-white rounded-2xl shadow-xl p-10 relative">

        <h1 class="text-4xl font-bold text-[#4072bc] mb-10 text-center">Manage Survey Responses</h1>

        <!-- Search Section -->
        <div class="flex justify-end mb-8">
          <div class="flex items-center gap-2">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by title or user..."
              class="w-80 border border-[#e3e3e3] rounded-full px-4 py-2 text-sm"
            />
            <button @click="applySearch" class="bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white px-4 py-2 rounded-full text-sm flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21 21l-4.35-4.35M17 11A6 6 0 1 0 5 11a6 6 0 0 0 12 0z" />
              </svg>
              Search
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm border border-[#e3e3e3]">
            <thead class="bg-[#f9fbfd]">
              <tr>
                <th class="py-3 px-4 text-left font-semibold text-[#4072bc] border-b">Survey</th>
                <th class="py-3 px-4 text-left font-semibold text-[#4072bc] border-b">User</th>
                <th class="py-3 px-4 text-left font-semibold text-[#4072bc] border-b">Submitted At</th>
                <th class="py-3 px-4 text-center font-semibold text-[#4072bc] border-b">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="response in responses" :key="response.id" class="hover:bg-[#f9fbfd]">
                <td class="py-3 px-4">{{ response.survey_title }}</td>
                <td class="py-3 px-4">{{ response.user_name }}</td>
                <td class="py-3 px-4">{{ formatDate(response.created_at) }}</td>
                <td class="py-3 px-4 text-center">
                  <button @click="viewResponse(response)" class="bg-[#59a8f7] text-white font-semibold px-3 py-1 rounded mr-2 text-xs">View</button>
                  <button @click="deleteResponse(response.id)" class="bg-red-400 text-white font-semibold px-3 py-1 rounded text-xs">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Centered -->
        <div class="flex justify-center mt-8 gap-4">
          <button :disabled="currentPage === 1" @click="changePage(currentPage - 1)"
            class="px-4 py-2 rounded-lg bg-gray-300 text-sm font-semibold">Prev</button>

          <span class="px-4 py-2 font-semibold">{{ currentPage }} / {{ totalPages }}</span>

          <button :disabled="currentPage === totalPages" @click="changePage(currentPage + 1)"
            class="px-4 py-2 rounded-lg bg-gray-300 text-sm font-semibold">Next</button>
        </div>

      </div>
    </main>
  </div>

  <!-- View Modal -->
  <div v-if="selectedResponse" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white w-full max-w-2xl p-8 rounded-lg shadow-lg">
      <h2 class="text-2xl font-bold mb-6 text-[#4072bc]">Survey Answers</h2>
      <div class="space-y-4 max-h-[500px] overflow-y-auto">
        <div v-for="(answer, index) in selectedResponse.answers" :key="index" class="border-b pb-2">
          <p class="font-semibold">{{ index + 1 }}. {{ answer.question }}</p>
          <p class="text-gray-600 ml-2">Answer: {{ answer.response }}</p>
        </div>
      </div>
      <div class="text-right mt-6">
        <button @click="selectedResponse = null" class="bg-[#4072bc] text-white px-5 py-2 rounded-lg">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/axios';
import Sidebar from '@/components/common/Sidebar.vue';

const responses = ref([]);
const selectedResponse = ref(null);
const searchQuery = ref('');
const loading = ref(false);

// Pagination
const currentPage = ref(1);
const totalPages = ref(1);

const fetchResponses = async () => {
  loading.value = true;
  try {
    const { data } = await axios.get(`/survey-responses?page=${currentPage.value}&search=${searchQuery.value}`);
    responses.value = data.data;
    totalPages.value = data.last_page;
  } catch {
    alert('Failed to load responses');
  } finally {
    loading.value = false;
  }
};

const changePage = (page) => {
  currentPage.value = page;
  fetchResponses();
};

const applySearch = () => {
  currentPage.value = 1;
  fetchResponses();
};

const deleteResponse = async (id) => {
  if (!confirm("Are you sure you want to delete this response?")) return;
  try {
    await axios.delete(`/survey-responses/${id}`);
    fetchResponses();
  } catch {
    alert("Failed to delete response");
  }
};

const viewResponse = (response) => {
  selectedResponse.value = response;
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

onMounted(fetchResponses);
</script>

<style scoped>
body { font-family: 'Inter', sans-serif; }
</style>
