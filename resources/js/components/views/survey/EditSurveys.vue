<template>
  <div class="flex min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
    <Sidebar />
    <main class="flex-1 flex items-center justify-center p-10">
      <div class="w-full max-w-5xl bg-white rounded-2xl shadow-lg p-10">

        <div v-if="loading" class="text-center text-gray-500 text-lg">
          Loading survey data...
        </div>

        <div v-else>
          <h1 class="text-4xl font-bold text-[#4072bc] mb-8">Edit Survey ✏️</h1>

          <form @submit.prevent="updateSurvey" class="space-y-6">

            <!-- Survey Title -->
            <div>
              <label class="block mb-2 text-sm font-semibold text-[#4072bc]">Survey Title</label>
              <input v-model="form.title" type="text" class="w-full border border-[#e3e3e3] rounded-lg px-4 py-3" required />
            </div>

            <!-- Survey Description -->
            <div>
              <label class="block mb-2 text-sm font-semibold text-[#4072bc]">Survey Description</label>
              <textarea v-model="form.description" rows="3" class="w-full border border-[#e3e3e3] rounded-lg px-4 py-3"></textarea>
            </div>

            <!-- Dynamic Questions -->
            <div v-for="(question, index) in form.questions" :key="index" class="bg-[#f9fbfd] border border-[#e3e3e3] rounded-xl p-6 mb-6 shadow-sm">
              <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-[#4072bc] text-lg">Question {{ index + 1 }}</h3>
                <button type="button" class="text-red-500 text-sm hover:underline" @click="removeQuestion(index)">Remove</button>
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium text-[#6c6f73] mb-1">Question Text</label>
                <input v-model="question.questionText" type="text" class="w-full border border-[#e3e3e3] px-3 py-2 rounded-lg" required />
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium text-[#6c6f73] mb-1">Question Type</label>
                <select v-model="question.questionType" class="w-full border border-[#e3e3e3] px-3 py-2 rounded-lg">
                  <option value="short-answer">Short Answer</option>
                  <option value="multiple-choice">Multiple Choice</option>
                </select>
              </div>

              <!-- Options -->
              <div v-if="question.questionType === 'multiple-choice'">
                <label class="block text-sm font-medium text-[#6c6f73] mb-1">Options</label>
                <div v-for="(option, optIndex) in question.options" :key="optIndex" class="flex gap-2 mb-2">
                  <input v-model="question.options[optIndex]" type="text" class="w-full border border-[#e3e3e3] px-3 py-2 rounded-lg" required />
                  <button type="button" class="text-red-400 hover:text-red-600 text-sm" @click="removeOption(index, optIndex)">Remove</button>
                </div>
                <button type="button" class="bg-gray-200 px-4 py-2 rounded-full font-medium text-sm" @click="addOption(index)">+ Add Option</button>
              </div>
            </div>

            <div class="max-w-lg mx-auto space-y-4">

            <!-- Add Question -->
            <button type="button" class="w-full bg-[#4072bc] text-white px-8 py-3 rounded-full font-semibold" @click="addQuestion">
              + Add Question
            </button>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold py-3 rounded-full" :disabled="loading">
              {{ loading ? 'Updating...' : 'Update Survey' }}
            </button>

            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from '@/axios'
import Sidebar from '@/components/common/Sidebar.vue'

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
