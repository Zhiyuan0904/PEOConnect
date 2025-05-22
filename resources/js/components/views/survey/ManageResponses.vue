<template>
    <div class="flex h-screen">
      <!-- Sidebar -->
      <Sidebar />
  
      <!-- Main Content -->
      <main class="w-4/5 p-10 bg-gray-100 overflow-y-auto">
        <h1 class="text-3xl font-bold text-[#1e7c99] mb-8 text-center">📋 Submitted Survey Responses</h1>
  
        <div v-if="loading" class="text-center text-gray-500 text-lg">Loading responses...</div>
  
        <div v-else>
          <div v-if="responses.length === 0" class="text-center text-gray-400">
            No survey responses yet.
          </div>
  
          <div v-else class="space-y-8">
            <div 
              v-for="(response, index) in responses" 
              :key="response.id" 
              class="bg-white p-6 rounded-lg shadow-md"
            >
              <h2 class="text-xl font-bold text-[#1e3a5f] mb-2">
                {{ response.survey_title }} (User: {{ response.user_name }})
              </h2>
  
              <div class="space-y-4">
                <div 
                  v-for="(answer, qIndex) in response.answers" 
                  :key="qIndex" 
                  class="border-b pb-2"
                >
                  <p class="font-semibold text-gray-700">
                    {{ qIndex + 1 }}. {{ answer.question }}
                  </p>
                  <p class="text-gray-600 ml-2 mt-1">
                    ➔ Answer: {{ answer.response }}
                  </p>
                </div>
              </div>
  
              <div class="text-right mt-4 text-gray-400 text-xs">
                Submitted on {{ formatDate(response.created_at) }}
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from '@/axios';
  import Sidebar from '@/components/common/Sidebar.vue';
  
  const responses = ref([]);
  const loading = ref(true);
  
  const fetchResponses = async () => {
    try {
      const { data } = await axios.get('/survey-responses');
      responses.value = data.map(item => ({
        id: item.id,
        survey_title: item.survey.title,
        user_name: item.user.name,
        answers: Object.values(JSON.parse(item.responses)).map((resp, index) => ({
          question: item.survey.questions[index]?.questionText || `Question ${index + 1}`,
          response: resp
        })),
        created_at: item.created_at,
      }));
    } catch (error) {
      console.error('Failed to fetch responses:', error);
      alert('Failed to load responses');
    } finally {
      loading.value = false;
    }
  };
  
  const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
  };
  
  onMounted(() => {
    fetchResponses();
  });
  </script>
  