<template>
    <div class="flex h-screen">
      <!-- Sidebar -->
      <Sidebar />
  
      <!-- Main Content -->
      <main class="w-4/5 p-10 bg-gray-100 overflow-y-auto">
        <div v-if="loading" class="text-gray-600 text-lg animate-pulse text-center">
          Loading available surveys...
        </div>
  
        <div v-else class="w-full max-w-4xl mx-auto">
          <h1 class="text-3xl font-bold text-[#1e7c99] mb-10 text-center">📝 Respond to Survey</h1>
  
          <!-- No surveys -->
          <div v-if="!surveys.length && !selectedSurvey" class="text-center text-gray-500">
            No available surveys right now.
          </div>
  
          <!-- Survey List -->
          <div v-else-if="!selectedSurvey" class="grid gap-6 md:grid-cols-2">
            <div 
              v-for="survey in surveys" 
              :key="survey.id" 
              class="bg-white p-6 rounded-lg shadow hover:shadow-xl cursor-pointer transition duration-300 flex flex-col items-center text-center"
              @click="selectSurvey(survey)"
            >
              <img src="@/assets/survey.jpg" alt="Survey" class="w-24 mb-4" />
              <h2 class="text-xl font-bold text-[#1e3a5f]">{{ survey.title }}</h2>
              <p class="text-gray-500 mt-2">{{ survey.description }}</p>
            </div>
          </div>
  
          <!-- Selected Survey -->
          <div v-else class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-[#1e3a5f] mb-2">{{ selectedSurvey.title }}</h2>
            <p class="text-gray-500 mb-8">{{ selectedSurvey.description }}</p>
  
            <form @submit.prevent="submitResponse" class="space-y-6">
              <div 
                v-for="(question, index) in selectedSurvey.questions" 
                :key="index" 
                class="space-y-2"
              >
                <label class="block text-gray-700 font-semibold">
                  {{ index + 1 }}. {{ question.questionText }}
                </label>
  
                <!-- Short Answer -->
                <input 
                  v-if="question.questionType === 'short-answer'"
                  v-model="responses[index]"
                  type="text"
                  placeholder="Your answer here..."
                  class="w-full p-3 border rounded-md focus:ring-2 focus:ring-[#1e7c99] focus:outline-none"
                  required
                />
  
                <!-- Multiple Choice -->
                <div v-else-if="question.questionType === 'multiple-choice'" class="space-y-2">
                  <div 
                    v-for="(option, optIndex) in question.options" 
                    :key="optIndex"
                    class="flex items-center space-x-3"
                  >
                    <input
                      type="radio"
                      :name="'question-' + index"
                      :value="option"
                      v-model="responses[index]"
                      required
                      class="text-[#1e7c99] focus:ring-[#1e7c99]"
                    />
                    <span class="text-gray-700">{{ option }}</span>
                  </div>
                </div>
              </div>
  
              <div class="flex justify-between items-center mt-10">
                <button 
                  type="button" 
                  @click="selectedSurvey = null" 
                  class="px-6 py-3 rounded-lg bg-gray-400 hover:bg-gray-500 text-white font-semibold transition"
                >
                  Cancel
                </button>
                <button 
                  type="submit" 
                  class="px-6 py-3 rounded-lg bg-[#1e7c99] hover:bg-[#156a84] text-white font-semibold transition"
                >
                  Submit Survey
                </button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from '@/axios';
  import Sidebar from '@/components/common/Sidebar.vue'; // Your Sidebar component
  
  const surveys = ref([]);
  const loading = ref(true);
  const selectedSurvey = ref(null);
  const responses = ref([]);
  
  const fetchSurveys = async () => {
  try {
    const { data } = await axios.get('/available-surveys');
    surveys.value = data; // no more filtering needed here
  } catch (error) {
    console.error('Failed to load surveys:', error);
    alert('Unable to load surveys. Please try again later.');
  } finally {
    loading.value = false;
  }
};


  
  const selectSurvey = (survey) => {
    selectedSurvey.value = survey;
    responses.value = Array(survey.questions.length).fill(null); // <-- Important: use null here
  };
  
  const submitResponse = async () => {
    try {
      await axios.post('/survey-responses', {
        survey_id: selectedSurvey.value.id,
        responses: responses.value,
      });
      alert('🎉 Survey submitted successfully!');
      selectedSurvey.value = null;
      responses.value = [];
      await fetchSurveys(); // Refresh survey list after submitting
    } catch (error) {
      console.error('Survey submission failed:', error);
      alert('Failed to submit survey. Please try again.');
    }
  };
  
  onMounted(() => {
    fetchSurveys();
  });
  </script>
  