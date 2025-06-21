<template>
  <div class="flex ml-[20%] min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd] relative">
    <Sidebar />

    <!-- Glow behind -->
    <div class="absolute inset-0 flex justify-center items-center pointer-events-none z-0">
      <div class="w-[550px] h-[550px] md:w-[700px] md:h-[700px] rounded-full blur-3xl opacity-50
                  bg-gradient-to-br from-[#e6c752] via-[#d98ab3] to-[#7caee6] animate-floating-glow">
      </div>
    </div>

    <main class="flex-1 flex justify-center p-10 relative z-10">
      <div class="w-full max-w-5xl bg-white rounded-2xl shadow-xl p-10">

        <h1 class="text-4xl font-bold text-[#4072bc] mb-10 text-center">📝 Respond to Survey</h1>

        <!-- Loading -->
        <div v-if="loading" class="text-gray-600 text-lg animate-pulse text-center">
          Loading available surveys...
        </div>

        <!-- No Surveys -->
        <div v-else-if="!surveys.length && !selectedSurvey" class="text-center text-gray-500">
          No available surveys right now.
        </div>

        <!-- Survey List -->
        <div v-else-if="!selectedSurvey" class="grid gap-8 md:grid-cols-2">
          <div 
            v-for="survey in surveys" 
            :key="survey.id" 
            class="bg-white p-8 rounded-2xl border border-[#e3e3e3] shadow-md hover:shadow-lg cursor-pointer transition duration-300 flex flex-col items-center text-center"
            @click="selectSurvey(survey)"
          >
            <img src="@/assets/survey.jpg" alt="Survey" class="w-24 mb-4" />
            <h2 class="text-xl font-bold text-[#4072bc]">{{ survey.title }}</h2>
            <p class="text-gray-500 mt-2">{{ survey.description }}</p>
          </div>
        </div>

        <!-- Selected Survey Form -->
        <div v-else class="bg-[#f9fbfd] p-8 rounded-2xl border border-[#e3e3e3] shadow">
          <h2 class="text-3xl font-bold text-[#4072bc] mb-3">{{ selectedSurvey.title }}</h2>
          <p class="text-gray-500 mb-8">{{ selectedSurvey.description }}</p>

          <form @submit.prevent="submitResponse" class="space-y-6">
            <div v-for="(question, index) in selectedSurvey.questions" :key="index" class="space-y-3">
              <label class="block text-[#1e3a5f] font-semibold text-lg">
                {{ index + 1 }}. {{ question.questionText }}
              </label>

              <!-- Short Answer -->
              <input 
                v-if="question.questionType === 'short-answer'"
                v-model="responses[index]"
                type="text"
                placeholder="Your answer here..."
                class="w-full p-3 border border-[#e3e3e3] rounded-lg focus:ring-2 focus:ring-[#4072bc] focus:outline-none"
                required
              />

              <!-- Multiple Choice -->
              <div v-else-if="question.questionType === 'multiple-choice'" class="space-y-2">
                <div v-for="(option, optIndex) in question.options" :key="optIndex" class="flex items-center gap-3">
                  <input
                    type="radio"
                    :name="'question-' + index"
                    :value="option"
                    v-model="responses[index]"
                    required
                    class="text-[#4072bc] focus:ring-[#4072bc]"
                  />
                  <span class="text-gray-700">{{ option }}</span>
                </div>
              </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-10">
              <button type="button" @click="selectedSurvey = null"
                class="px-6 py-3 rounded-full bg-gray-300 text-gray-800 font-semibold shadow-lg hover:brightness-110 transition">
                Cancel
              </button>

              <button type="submit"
                class="px-6 py-3 bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold rounded-full shadow-lg hover:brightness-110 transition">
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
import Sidebar from '@/components/common/Sidebar.vue';
import { useAuthStore } from '@/stores/auth'; // Make sure this is your auth store path

const authStore = useAuthStore();
const surveys = ref([]);
const loading = ref(true);
const selectedSurvey = ref(null);
const responses = ref([]);

const fetchSurveys = async () => {
  try {
    const { data } = await axios.get('/available-surveys');
    
    // Debug logs to confirm user role and survey target roles
    console.log('🔐 User Role:', authStore.user?.role);
    console.log('📋 All Surveys:', data);

    // Match only surveys for current user's role
    const userRole = authStore.user?.role?.toLowerCase();
    surveys.value = data.filter(s => s.target_role?.toLowerCase() === userRole);

    console.log('✅ Filtered Surveys:', surveys.value);
  } catch (error) {
    console.error('❌ Failed to load surveys:', error);
    alert('Unable to load surveys. Please try again later.');
  } finally {
    loading.value = false;
  }
};

const selectSurvey = (survey) => {
  selectedSurvey.value = survey;
  responses.value = Array(survey.questions.length).fill(null);
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
    await fetchSurveys(); // Refresh after submission
  } catch (error) {
    console.error('❌ Survey submission failed:', error);
    alert('Failed to submit survey. Please try again.');
  }
};

onMounted(() => {
  fetchSurveys();
});
</script>

