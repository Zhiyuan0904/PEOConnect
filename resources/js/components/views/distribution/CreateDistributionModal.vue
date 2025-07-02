<template>
  <!-- Overlay that covers everything including sidebar -->
  <div v-if="visible" class="fixed inset-0 bg-gray-400/40 backdrop-blur-sm flex items-center justify-center z-50">
    <!-- Modal Card -->
    <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6 relative z-50">
      <!-- Modal Header -->
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-[#4072bc]">Schedule Survey Distribution</h2>
        <button @click="closeModal" class="text-gray-500 hover:text-red-500 text-xl">&times;</button>
      </div>

      <!-- Form -->
      <form @submit.prevent="submitForm" class="space-y-4">
        <!-- Survey -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Select Survey</label>
          <select v-model="form.survey_id" required class="w-full border px-3 py-2 rounded">
            <option disabled value="">-- Select a survey --</option>
            <option v-for="survey in availableSurveys" :key="survey.id" :value="survey.id">
              {{ survey.title }}
            </option>
          </select>
        </div>

        <!-- Target Role -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Target Audience</label>
          <select v-model="form.target_role" required class="w-full border px-3 py-2 rounded">
            <option value="student">Student</option>
            <option value="alumni">Alumni</option>
          </select>
        </div>

        <!-- Date Field -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Date Field</label>
          <select v-model="form.date_field" required class="w-full border px-3 py-2 rounded">
            <option value="enroll_date">Enrolment Date</option>
            <option value="expected_graduate_date">Graduation Date</option>
          </select>
        </div>

        <!-- Start & End Date -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
            <input v-model="form.start_date" type="date" class="w-full border px-3 py-2 rounded" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">End Date</label>
            <input v-model="form.end_date" type="date" class="w-full border px-3 py-2 rounded" required />
          </div>
        </div>

        <!-- Scheduled Start & End Date -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Scheduled Active Date</label>
            <input v-model="form.scheduled_active_date" type="date" class="w-full border px-3 py-2 rounded" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Scheduled End Date</label>
            <input v-model="form.scheduled_end_date" type="date" class="w-full border px-3 py-2 rounded" required />
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3 pt-4">
          <button type="button" @click="closeModal"
            class="px-4 py-2 rounded-full bg-gray-300 text-gray-800 font-semibold shadow-lg hover:brightness-110 transition">
            Cancel
          </button>
          <button type="submit"
            :disabled="loading"
            class="px-4 py-2 bg-gradient-to-r from-[#f07ba3] to-[#c4a8e3] hover:from-[#8475d2] hover:to-[#a7c8f8] text-white font-semibold rounded-full shadow-lg hover:brightness-110 transition flex items-center justify-center gap-1">
            Create<span v-if="loading" class="ml-1 animate-pulse">...</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import axios from '@/axios'

// Props & Emits
const props = defineProps({ visible: Boolean })
const emit = defineEmits(['close', 'created'])

const form = ref({
  survey_id: '',
  target_role: 'student',
  date_field: 'enroll_date',
  start_date: '',
  end_date: '',
  scheduled_active_date: '',
  scheduled_end_date: ''
})

const surveys = ref([])
const distributions = ref([]) // already distributed surveys

// Available surveys (not already distributed)
const availableSurveys = computed(() => {
  const usedSurveyIds = distributions.value.map(d => d.survey_id)
  return surveys.value.filter(s => !usedSurveyIds.includes(s.id))
})

const fetchSurveys = async () => {
  try {
    const [surveyRes, distRes] = await Promise.all([
      axios.get('/surveys'),
      axios.get('/survey-distributions')
    ])
    surveys.value = surveyRes.data
    distributions.value = distRes.data
  } catch (error) {
    console.error('Failed to fetch surveys/distributions:', error)
  }
}

const loading = ref(false)

const submitForm = async () => {
  loading.value = true
  try {
    await axios.post('/survey-distributions', {
      ...form.value,
      is_active: true
    })
    alert('Survey distribution created successfully!')
    emit('created')
    closeModal()
  } catch (error) {
    console.error('Failed to create distribution:', error)
    alert('Failed to create. Please try again.')
  } finally {
    loading.value = false
  }
}

const closeModal = () => {
  emit('close')
}

watch(() => props.visible, (newVal) => {
  if (newVal) fetchSurveys()
})
</script>
