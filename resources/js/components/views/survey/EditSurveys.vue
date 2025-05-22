<template>
    <main class="flex items-center justify-center min-h-screen bg-gray-100">
      <div class="bg-white p-10 rounded-xl shadow-xl w-full max-w-3xl">
  
        <!-- Loading State -->
        <div v-if="loading" class="text-center text-gray-500 text-lg">
          Loading survey data...
        </div>
  
        <div v-else>
          <h1 class="text-3xl font-bold text-[#1e7c99] mb-4">Edit Survey ✏️</h1>
          <p class="text-gray-500 mb-8">Update your survey details below.</p>
  
          <form @submit.prevent="updateSurvey">
            <!-- Survey Title -->
            <div class="mb-6">
              <label class="block mb-2 text-sm font-medium text-gray-700">Survey Title</label>
              <input
                v-model="form.title"
                type="text"
                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#1e7c99]"
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
              ></textarea>
            </div>
  
            <!-- Questions -->
            <div class="mb-6 space-y-6">
              <h2 class="text-xl font-semibold text-[#1e7c99] mb-2">Questions</h2>
  
              <div
                v-for="(question, index) in form.questions"
                :key="index"
                class="p-5 bg-gray-50 rounded-lg shadow-sm space-y-4 relative"
              >
                <!-- Remove Question Button -->
                <button
                  type="button"
                  @click="removeQuestion(index)"
                  class="absolute top-2 right-2 text-red-400 hover:text-red-600 text-sm"
                  title="Remove Question"
                >
                  ❌
                </button>
  
                <!-- Question Text -->
                <div>
                  <label class="block mb-1 text-sm font-medium text-gray-700">Question Text</label>
                  <input
                    v-model="question.questionText"
                    type="text"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1e7c99]"
                    required
                  />
                </div>
  
                <!-- Question Type -->
                <div>
                  <label class="block mb-1 text-sm font-medium text-gray-700">Question Type</label>
                  <select
                    v-model="question.questionType"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1e7c99]"
                  >
                    <option value="short-answer">Short Answer</option>
                    <option value="multiple-choice">Multiple Choice</option>
                  </select>
                </div>
  
                <!-- Options if Multiple Choice -->
                <div v-if="question.questionType === 'multiple-choice'" class="space-y-2">
                  <label class="block mb-1 text-sm font-medium text-gray-700">Options</label>
  
                  <div
                    v-for="(option, optIndex) in question.options"
                    :key="optIndex"
                    class="flex items-center space-x-2"
                  >
                    <input
                      v-model="question.options[optIndex]"
                      type="text"
                      class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-[#1e7c99]"
                      :placeholder="`Option ${optIndex + 1}`"
                      required
                    />
                    <button
                      type="button"
                      @click="removeOption(index, optIndex)"
                      class="text-red-400 hover:text-red-600 text-sm"
                    >
                      Remove
                    </button>
                  </div>
  
                  <button
                    type="button"
                    @click="addOption(index)"
                    class="mt-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-4 py-2 rounded-lg"
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
                class="bg-[#1e7c99] hover:bg-[#156a84] text-white font-semibold px-6 py-3 rounded-lg"
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
              {{ loading ? 'Updating...' : 'Update Survey' }}
            </button>
          </form>
        </div>
      </div>
    </main>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import { useRoute, useRouter } from 'vue-router'
  import axios from '@/axios'
  
  const route = useRoute()
  const router = useRouter()
  
  const form = ref({
    title: '',
    description: '',
    questions: [],
  })
  
  const loading = ref(true)
  
  const fetchSurvey = async () => {
    try {
      const { data } = await axios.get(`/surveys/${route.params.id}`)
      form.value.title = data.title
      form.value.description = data.description
      form.value.questions = data.questions || []
    } catch (error) {
      console.error(error)
      alert('❌ Failed to load survey.')
    } finally {
      loading.value = false
    }
  }
  
  const updateSurvey = async () => {
    try {
      loading.value = true
      await axios.put(`/surveys/${route.params.id}`, form.value)
      alert('✅ Survey updated successfully!')
      router.push('/manage/surveys')
    } catch (error) {
      console.error(error)
      alert('❌ Failed to update survey. Please try again.')
    } finally {
      loading.value = false
    }
  }
  
  const addQuestion = () => {
    form.value.questions.push({
      questionText: '',
      questionType: 'short-answer',
      options: [],
    })
  }
  
  const removeQuestion = (index) => {
    form.value.questions.splice(index, 1)
  }
  
  const addOption = (qIndex) => {
    form.value.questions[qIndex].options.push('')
  }
  
  const removeOption = (qIndex, optIndex) => {
    form.value.questions[qIndex].options.splice(optIndex, 1)
  }
  
  onMounted(() => {
    fetchSurvey()
  })
  </script>
  