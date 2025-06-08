<template>
  <div class="flex min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />

    <main class="flex-1 p-10 overflow-y-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-10">
        <h1 class="text-3xl font-bold text-[#4072bc]">Manage Surveys</h1>
        <router-link
          to="/create/surveys"
          class="bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold px-7 py-3 rounded-full transition duration-300 shadow-md text-center transition"
        >
          + Create New Survey
        </router-link>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center text-gray-500 py-10">Loading surveys...</div>

      <!-- No surveys -->
      <div v-else-if="surveys.length === 0" class="text-center text-gray-500 py-10">
        No surveys found. Create one!
      </div>

      <!-- Surveys Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="survey in surveys"
          :key="survey.id"
          class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition overflow-hidden flex flex-col"
        >
          <img src="@/assets/survey.jpg" alt="Survey Image" class="h-48 w-full object-cover" />

          <div class="flex-1 flex flex-col p-6">
            <h2 class="text-xl font-bold text-[#4072bc] mb-2">{{ survey.title }}</h2>
            <p class="text-gray-600 flex-1">{{ survey.description }}</p>
            <p class="text-gray-400 text-sm mt-4">Total Questions: {{ survey.questions.length }}</p>
          </div>

          <div class="flex justify-around items-center bg-gray-50 p-4 border-t space-x-4">
            <router-link
              :to="`/edit/surveys/${survey.id}`"
              class="w-28 flex items-center justify-center gap-2 bg-[#59a8f7] text-white font-semibold px-4 py-2 rounded-lg shadow-md hover:brightness-110 transition"
            >
              Edit
            </router-link>

            <button
              @click="deleteSurvey(survey.id)"
              class="w-28 flex items-center justify-center gap-2 bg-red-400 hover:bg-red-500 text-white font-semibold px-4 py-2 rounded-lg transition"
            >
              Delete
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from '@/components/common/Sidebar.vue'
import axios from '@/axios'

const surveys = ref([])
const loading = ref(false)

const fetchSurveys = async () => {
  loading.value = true
  try {
    const response = await axios.get('/surveys')
    surveys.value = response.data
  } catch (error) {
    console.error(error)
    alert('Failed to load surveys.')
  } finally {
    loading.value = false
  }
}

const deleteSurvey = async (id) => {
  if (!confirm('Are you sure you want to delete this survey?')) return

  try {
    await axios.delete(`/surveys/${id}`)
    surveys.value = surveys.value.filter(s => s.id !== id)
    alert('Survey deleted successfully!')
  } catch (error) {
    console.error(error)
    alert('Failed to delete survey.')
  }
}

onMounted(() => {
  fetchSurveys()
})
</script>
