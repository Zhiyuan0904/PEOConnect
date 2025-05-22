<template>
  <main class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-10 rounded-xl shadow-xl w-full max-w-3xl">
      <h1 class="text-3xl font-bold text-[#1e7c99] mb-4">Create New Survey 📋</h1>
      <p class="text-gray-500 mb-8">Design a new survey with multiple questions.</p>

      <form @submit.prevent="createSurvey">
        <!-- Survey Title -->
        <div class="mb-6">
          <label class="block mb-2 text-sm font-medium text-gray-700">Survey Title</label>
          <input
            v-model="form.title"
            type="text"
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#1e7c99]"
            placeholder="Enter survey title"
            required
          />
        </div>

        <!-- Survey Description -->
        <div class="mb-6">
          <label class="block mb-2 text-sm font-medium text-gray-700">Survey Description</label>
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#1e7c99]"
            placeholder="Describe your survey purpose"
          ></textarea>
        </div>

        <!-- Questions Section -->
        <div class="mb-6 space-y-8">
          <h2 class="text-xl font-semibold text-[#1e7c99] mb-2">Questions</h2>

          <div v-for="(question, index) in form.questions" :key="index" class="border p-5 rounded-lg shadow-sm bg-gray-50">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-700">Question {{ index + 1 }}</h3>
              <button
                type="button"
                @click="removeQuestion(index)"
                class="text-red-500 hover:text-red-700 text-sm"
              >
                Remove
              </button>
            </div>

            <!-- Question Text -->
            <div class="mb-4">
              <label class="block mb-1 text-sm font-medium text-gray-600">Question Text</label>
              <input
                v-model="question.questionText"
                type="text"
                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#1e7c99]"
                placeholder="Enter your question"
                required
              />
            </div>

            <!-- Question Type -->
            <div class="mb-4">
              <label class="block mb-1 text-sm font-medium text-gray-600">Question Type</label>
              <select
                v-model="question.questionType"
                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#1e7c99]"
              >
                <option value="short-answer">Short Answer</option>
                <option value="multiple-choice">Multiple Choice</option>
              </select>
            </div>

            <!-- Options (only for multiple choice) -->
            <div v-if="question.questionType === 'multiple-choice'">
              <label class="block mb-2 text-sm font-medium text-gray-600">Options</label>
              <div v-for="(option, optIndex) in question.options" :key="optIndex" class="flex items-center space-x-2 mb-2">
                <input
                  v-model="question.options[optIndex]"
                  type="text"
                  class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#1e7c99]"
                  :placeholder="`Option ${optIndex + 1}`"
                  required
                />
                <button type="button" @click="removeOption(index, optIndex)" class="text-red-400 hover:text-red-600 text-sm">Remove</button>
              </div>
              <button
                type="button"
                @click="addOption(index)"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg transition"
              >
                + Add Option
              </button>
            </div>
          </div>
        </div>

        <!-- Add Question Button -->
        <div class="text-center mb-8">
          <button
            type="button"
            @click="addQuestion"
            class="bg-[#1e7c99] hover:bg-[#156a84] text-white font-semibold px-6 py-3 rounded-lg transition duration-300"
          >
            + Add Question
          </button>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="w-full bg-[#1e7c99] hover:bg-[#156a84] text-white font-semibold py-3 rounded-lg transition duration-300"
          :disabled="loading"
        >
          {{ loading ? 'Creating...' : 'Create Survey' }}
        </button>
      </form>
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router' 
import axios from '@/axios'
import Sidebar from '@/components/common/Sidebar.vue'

const form = ref({
  title: '',
  description: '',
  questions: []
})

const loading = ref(false)
const router = useRouter()  

// Add a new empty question
const addQuestion = () => {
  form.value.questions.push({
    questionType: 'short-answer',
    questionText: '',
    options: []
  })
}

// Remove a question
const removeQuestion = (index) => {
  form.value.questions.splice(index, 1)
}

// Add an option to a multiple choice question
const addOption = (qIndex) => {
  form.value.questions[qIndex].options.push('')
}

// Remove an option from a multiple choice question
const removeOption = (qIndex, optIndex) => {
  form.value.questions[qIndex].options.splice(optIndex, 1)
}

// Submit the survey
const createSurvey = async () => {
  try {
    loading.value = true

    await axios.post('/surveys', form.value)

    alert('Survey created successfully!')
    router.push('/manage/surveys')  
  } catch (error) {
    console.error(error)
    alert('Failed to create survey. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>

