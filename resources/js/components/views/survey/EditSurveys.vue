<template>
  <div class="flex ml-[20%] min-h-screen bg-gradient-to-tr from-[#f4f4f4] to-[#f9fbfd]">
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

            <!-- Progress Bar -->
            <div class="mb-6">
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-[#4072bc] h-2 rounded-full transition-all duration-300"
                     :style="{ width: ((currentQuestionIndex + 1) / form.questions.length * 100) + '%' }">
                </div>
              </div>
              <p class="text-xs text-right mt-1 text-[#6c6f73]">
                Question {{ currentQuestionIndex + 1 }} of {{ form.questions.length }}
              </p>
            </div>

            <!-- Single Question Pagination -->
            <div v-if="form.questions.length">
              <transition name="fade" mode="out-in">
                <div :key="currentQuestionIndex" class="bg-[#f9fbfd] border border-[#e3e3e3] rounded-xl p-6 mb-6 shadow-sm">
                  <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-[#4072bc] text-lg">Question {{ currentQuestionIndex + 1 }}</h3>
                    <button type="button" class="text-red-500 text-xl hover:text-red-700" @click="removeQuestion(currentQuestionIndex)">&times;</button>
                  </div>

                  <div class="mb-4">
                    <label class="block text-sm font-medium text-[#6c6f73] mb-1">Question Text</label>
                    <input v-model="form.questions[currentQuestionIndex].questionText" type="text" class="w-full border border-[#e3e3e3] px-3 py-2 rounded-lg" required />
                  </div>

                  <div class="mb-4">
                    <label class="block text-sm font-medium text-[#6c6f73] mb-1">Question Type</label>
                    <select v-model="form.questions[currentQuestionIndex].questionType" class="w-full border border-[#e3e3e3] px-3 py-2 rounded-lg">
                      <option value="short-answer">Short Answer</option>
                      <option value="multiple-choice">Multiple Choice</option>
                    </select>
                  </div>

                  <!-- Options -->
                  <div v-if="form.questions[currentQuestionIndex].questionType === 'multiple-choice'">
                    <label class="block text-sm font-medium text-[#6c6f73] mb-1">Options</label>
                    <div v-for="(option, optIndex) in form.questions[currentQuestionIndex].options" :key="optIndex" class="flex gap-2 mb-2">
                      <input v-model="form.questions[currentQuestionIndex].options[optIndex]" type="text" class="w-full border border-[#e3e3e3] px-3 py-2 rounded-lg" required />
                      <button type="button" class="text-red-500 text-xl hover:text-red-700" @click="removeOption(currentQuestionIndex, optIndex)">&times;</button>
                    </div>
                    <button type="button" class="bg-gray-200 px-4 py-2 rounded-full font-medium text-sm" @click="addOption(currentQuestionIndex)">+ Add Option</button>
                  </div>
                </div>
              </transition>

              <!-- Pagination Buttons -->
              <div class="flex justify-between items-center mb-6">
                <button type="button" class="text-sm px-4 py-2 rounded-full font-semibold bg-[#e3e3e3] hover:bg-[#d0d0d0]" @click="prevQuestion" :disabled="currentQuestionIndex === 0">← Previous</button>
                <div class="flex space-x-1">
                  <button
                    v-for="(q, idx) in form.questions"
                    :key="idx"
                    type="button"
                    class="w-3 h-3 rounded-full"
                    :class="{ 'bg-[#4072bc]': currentQuestionIndex === idx, 'bg-gray-300': currentQuestionIndex !== idx }"
                    @click="currentQuestionIndex = idx"
                  ></button>
                </div>
                <button type="button" class="text-sm px-4 py-2 rounded-full font-semibold bg-[#4072bc] text-white hover:bg-[#2f5a99]" @click="nextQuestion" :disabled="currentQuestionIndex === form.questions.length - 1">Next →</button>
              </div>
            </div>

            <!-- Add Question -->
            <div class="max-w-lg mx-auto space-y-4">
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

const currentQuestionIndex = ref(0)
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
  currentQuestionIndex.value = form.value.questions.length - 1
}

const removeQuestion = (index) => {
  form.value.questions.splice(index, 1)
  if (currentQuestionIndex.value > 0) currentQuestionIndex.value--
}

const addOption = (qIndex) => {
  form.value.questions[qIndex].options.push('')
}

const removeOption = (qIndex, optIndex) => {
  form.value.questions[qIndex].options.splice(optIndex, 1)
}

const nextQuestion = () => {
  if (currentQuestionIndex.value < form.value.questions.length - 1) {
    currentQuestionIndex.value++
  }
}

const prevQuestion = () => {
  if (currentQuestionIndex.value > 0) {
    currentQuestionIndex.value--
  }
}

onMounted(() => {
  fetchSurvey()
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
