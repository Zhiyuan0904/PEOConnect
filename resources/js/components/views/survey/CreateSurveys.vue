<template>
  <div class="flex ml-[20%] min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />
    <main class="flex-1 flex items-center justify-center p-10">
      <div class="relative w-full max-w-5xl p-10">
        <div class="absolute inset-0 z-0 rounded-1xl blur-3xl opacity-40 bg-gradient-to-br from-[#e6c752] via-[#d98ab3] to-[#7caee6] animate-floating-glow"></div>

        <div class="relative z-10 bg-white rounded-2xl shadow-lg p-10">
          <h1 class="text-4xl font-bold text-[#4072bc] mb-10 text-center">Create New Survey 📋</h1>

          <!-- Step Indicator Circles -->
          <div class="flex justify-center mb-12">
            <div v-for="(title, index) in stepTitles" :key="index" class="flex flex-col items-center mx-6 cursor-pointer" @click="goToStep(index)">
              <div :class="[currentStep === index ? 'bg-[#4072bc]' : 'bg-gray-300', 'rounded-full w-10 h-10 flex items-center justify-center text-white font-bold text-lg mb-1']">
                {{ index + 1 }}
              </div>
              <div class="text-sm font-medium">
                {{ title }}
              </div>
            </div>
          </div>

          <!-- Step Content -->
          <div v-if="currentStep === 0">
            <div class="mb-8 border-2 border-dashed border-[#59a8f7] bg-[#f9fbfd] rounded-xl p-6 text-center">
              <label class="block mb-3 text-lg font-semibold text-[#4072bc]">Import Survey CSV (Optional)</label>
              <input type="file" accept=".csv" @change="handleCSVUpload" class="mx-auto text-sm file:rounded-lg file:border-none file:bg-[#59a8f7] file:text-white file:py-2 file:px-4 cursor-pointer" />
              <p class="text-xs text-gray-500 mt-2">Format: title, description, question_text, question_type, option1, option2...</p>
            </div>
          </div>

          <div v-else-if="currentStep === 1">
            <label class="block mb-2 text-sm font-semibold text-[#4072bc]">Survey Title</label>
            <input v-model="form.title" type="text" class="w-full border border-[#e3e3e3] rounded-lg px-4 py-3 mb-6" required />
            <label class="block mb-2 text-sm font-semibold text-[#4072bc]">Survey Description</label>
            <textarea v-model="form.description" rows="3" class="w-full border border-[#e3e3e3] rounded-lg px-4 py-3"></textarea>
          </div>

          <div v-else-if="currentStep >= 2 && currentStep < totalSteps - 1">
            <div v-if="form.questions[currentStep - 2]" class="bg-[#f9fbfd] border border-[#e3e3e3] rounded-xl p-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-[#4072bc] text-lg">Question {{ currentStep - 1 }}</h3>
                <button @click="removeQuestion(currentStep - 2)" class="text-red-500 text-sm hover:underline">Remove</button>
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-[#6c6f73]">Question Text</label>
                <input v-model="form.questions[currentStep - 2].questionText" type="text" class="w-full border border-[#e3e3e3] px-3 py-2 rounded-lg" required />
              </div>
              <div class="mb-4">
                <label class="block text-sm font-medium text-[#6c6f73]">Question Type</label>
                <select v-model="form.questions[currentStep - 2].questionType" class="w-full border border-[#e3e3e3] px-3 py-2 rounded-lg">
                  <option value="short-answer">Short Answer</option>
                  <option value="multiple-choice">Multiple Choice</option>
                </select>
              </div>
              <div v-if="form.questions[currentStep - 2].questionType === 'multiple-choice'">
                <label class="block text-sm font-medium text-[#6c6f73]">Options</label>
                <div v-for="(option, optIndex) in form.questions[currentStep - 2].options" :key="optIndex" class="flex gap-2 mb-2">
                  <input v-model="form.questions[currentStep - 2].options[optIndex]" type="text" class="w-full border border-[#e3e3e3] px-3 py-2 rounded-lg" required />
                  <button @click="removeOption(currentStep - 2, optIndex)" class="text-red-400 hover:text-red-600 text-lg rounded-full">&times;</button>
                </div>
                <button @click="addOption(currentStep - 2)" class="bg-gray-200 px-4 py-2 rounded-full font-medium text-sm">+ Add Option</button>
              </div>
            </div>
            <div class="text-center mt-6">
              <button @click="addQuestion" class="bg-[#4072bc] text-white px-8 py-3 rounded-full font-semibold">+ Add Question</button>
            </div>
          </div>

          <div v-else-if="currentStep === totalSteps - 1" class="text-center py-10">
            <h2 class="text-2xl font-bold text-[#4072bc] mb-4">Ready to submit?</h2>
            <p class="text-gray-500 mb-8">Click submit to save your survey to the system.</p>
          </div>

          <div class="flex justify-between items-center mt-12">
            <button v-if="currentStep > 0" @click="currentStep--" class="w-44 py-3 rounded-full bg-gray-300 text-gray-800 font-semibold shadow-lg hover:brightness-110 transition">Back</button>
            <div class="flex-1"></div>
            <button v-if="currentStep < totalSteps - 1" @click="currentStep++" class="w-44 py-3 rounded-full bg-gradient-to-r from-[#4072bc] to-[#59a8f7] text-white font-semibold shadow-lg hover:brightness-110 transition">Next</button>
            <button v-if="currentStep === totalSteps - 1" :disabled="loading" @click="createSurvey" class="w-44 py-3 bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold rounded-full shadow-lg hover:brightness-110 transition">
              {{ loading ? 'Creating...' : 'Create Survey' }}
            </button>
          </div>

          <div v-if="errorMessage" class="p-3 bg-red-100 text-red-700 rounded text-sm mt-4">{{ errorMessage }}</div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from '@/axios';
import Sidebar from '@/components/common/Sidebar.vue';

const form = ref({ title: '', description: '', questions: [] });
const loading = ref(false);
const errorMessage = ref('');
const router = useRouter();
const currentStep = ref(0);
const stepTitles = ['Import CSV', 'Survey Info', 'Questions', 'Review'];
const totalSteps = computed(() => form.value.questions.length + 3);

const addQuestion = () => {
  form.value.questions.push({ questionType: 'short-answer', questionText: '', options: [] });
};

const removeQuestion = (index) => {
  form.value.questions.splice(index, 1);
};

const addOption = (qIndex) => {
  form.value.questions[qIndex].options.push('');
};

const removeOption = (qIndex, optIndex) => {
  form.value.questions[qIndex].options.splice(optIndex, 1);
};

const goToStep = (step) => {
  if (step >= 0 && step < totalSteps.value) {
    currentStep.value = step;
  }
};

const createSurvey = async () => {
  try {
    loading.value = true;
    errorMessage.value = '';
    const title = form.value.title.trim();
    if (!title) {
      errorMessage.value = 'Survey title is required.';
      return;
    }
    const { data } = await axios.get('/surveys/check-title', { params: { title } });
    if (data.exists) {
      errorMessage.value = 'A survey with this title already exists. Please choose a different title.';
      return;
    }
    await axios.post('/surveys', { ...form.value, title });
    alert('Survey created successfully!');
    router.push('/manage/surveys');
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to create survey';
  } finally {
    loading.value = false;
  }
};

const handleCSVUpload = (e) => {
  const file = e.target.files[0];
  if (!file) return;

  const stripQuotes = (str) => {
    return str.replace(/^"(.*)"$/, '$1').replace(/^'(.*)'$/, '$1').replace(/""/g, '"');
  };

  const reader = new FileReader();
  reader.onload = () => {
    const rows = reader.result.split('\n').map(row => row.trim()).filter(row => row !== '');
    if (rows.length < 2) {
      alert("CSV must contain at least 2 rows (header + data)");
      return;
    }
    const dataRows = rows.slice(1);
    form.value.title = '';
    form.value.description = '';
    form.value.questions = [];
    dataRows.forEach((row, index) => {
      const columns = row.split(',').map(col => stripQuotes(col.trim()));
      if (index === 0) {
        form.value.title = columns[0] || '';
        form.value.description = columns[1] || '';
      }
      const questionText = columns[2] || '';
      const questionType = columns[3] || 'short-answer';
      const options = columns.slice(4).filter(opt => opt !== '');
      form.value.questions.push({
        questionText,
        questionType,
        options: questionType === 'multiple-choice' ? options : []
      });
    });
  };
  reader.readAsText(file);
};
</script>

<style scoped>
body { font-family: 'Inter', sans-serif; }
</style>
