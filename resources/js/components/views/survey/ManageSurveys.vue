<template>
    <main class="p-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-[#1e7c99]">Manage Surveys</h1>
        <router-link
          to="/manage/surveys/create"
          class="bg-[#1e7c99] hover:bg-[#156a84] text-white px-4 py-2 rounded-lg transition duration-300"
        >
          + Create Survey
        </router-link>
      </div>
  
      <!-- Survey List Table -->
      <div v-if="surveys.length > 0" class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="min-w-full">
          <thead class="bg-gray-100">
            <tr>
              <th class="text-left p-4">Title</th>
              <th class="text-left p-4">Description</th>
              <th class="text-center p-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="survey in surveys" :key="survey.id" class="border-t">
              <td class="p-4">{{ survey.title }}</td>
              <td class="p-4">{{ survey.description }}</td>
              <td class="p-4 flex justify-center space-x-2">
                <router-link
                  :to="`/manage/surveys/edit/${survey.id}`"
                  class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded transition"
                >
                  Edit
                </router-link>
                <button
                  @click="deleteSurvey(survey.id)"
                  class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <div v-else class="text-center text-gray-500 mt-12">
        No surveys found. Try creating a new one!
      </div>
    </main>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axios from '@/axios' // adjust if your axios path different
  import { useRouter } from 'vue-router'
  
  const surveys = ref([])
  const router = useRouter()
  
  const fetchSurveys = async () => {
    try {
      const response = await axios.get('/surveys')
      surveys.value = response.data
    } catch (error) {
      console.error(error)
      alert('Failed to load surveys.')
    }
  }
  
  const deleteSurvey = async (id) => {
    if (confirm('Are you sure you want to delete this survey?')) {
      try {
        await axios.delete(`/surveys/${id}`)
        surveys.value = surveys.value.filter(survey => survey.id !== id)
        alert('Survey deleted successfully!')
      } catch (error) {
        console.error(error)
        alert('Failed to delete survey.')
      }
    }
  }
  
  onMounted(() => {
    fetchSurveys()
  })
  </script>
  