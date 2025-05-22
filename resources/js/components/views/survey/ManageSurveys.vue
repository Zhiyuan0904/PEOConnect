<template>
    <div class="flex min-h-screen bg-gray-100">
      <!-- Sidebar -->
      <Sidebar />
  
      <!-- Main Content -->
      <main class="flex-1 p-10 overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
          <h1 class="text-3xl font-bold text-[#1e3a5f]">List of Survey 📋</h1>
          <router-link
            to="/create/surveys"
            class="bg-[#1e7c99] hover:bg-[#156a84] text-white font-semibold px-5 py-3 rounded-lg transition duration-300"
          >
            + Create New Survey
          </router-link>
        </div>
  
        <div v-if="loading" class="text-center text-gray-500 py-10">Loading surveys...</div>
  
        <div v-else-if="surveys.length === 0" class="text-center text-gray-500 py-10">
          No surveys found. Create one!
        </div>
  
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="survey in surveys"
            :key="survey.id"
            class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition overflow-hidden flex flex-col"
          >
            <img
              src="@/assets/survey.jpg"
              alt="Survey Image"
              class="h-48 w-full object-cover"
            />
  
            <div class="flex-1 flex flex-col p-6">
              <h2 class="text-xl font-bold text-[#1e3a5f] mb-2">{{ survey.title }}</h2>
              <p class="text-gray-600 flex-1">{{ survey.description }}</p>
              <p class="text-gray-400 text-sm mt-4">Total Questions: {{ survey.questions.length }}</p>
            </div>
  
            <div class="flex justify-around items-center bg-gray-50 p-4 border-t space-x-4">
            <router-link
                :to="`/edit/surveys/${survey.id}`"
                class="w-28 flex items-center justify-center gap-2 bg-[#1e7c99] hover:bg-[#156a84] text-white font-semibold px-4 py-2 rounded-lg transition duration-200"
            >
                Edit
            </router-link>
            <button
                @click="deleteSurvey(survey.id)"
                class="w-28 flex items-center justify-center gap-2 bg-red-400 hover:bg-red-500 text-white font-semibold px-4 py-2 rounded-lg transition duration-200"
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
  